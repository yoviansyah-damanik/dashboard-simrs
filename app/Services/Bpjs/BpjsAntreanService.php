<?php

namespace App\Services\Bpjs;

use App\Helpers\LZString;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpjsAntreanService
{
    protected string $baseUrl;
    protected string $consId;
    protected string $secretKey;
    protected string $userKey;
    protected int $requestTimeout = 30;

    public function __construct()
    {
        $this->resolveCredentials();
    }

    /**
     * Resolusi kredensial BPJS Antrean dari config atau tabel configurations DB terapi
     */
    protected function resolveCredentials(): void
    {
        $this->baseUrl = config('bpjs.antrian_online.base_url') ?? 'https://apijkn.bpjs-kesehatan.go.id/antreanrs';
        $this->consId = (string) (config('bpjs.antrian_online.cons_id') ?? '16555');
        $this->secretKey = (string) (config('bpjs.antrian_online.secret_key') ?? 'jTo7lhJsjr');
        $this->userKey = (string) (config('bpjs.antrian_online.user_key') ?? 'f4b4225536e4d1a30b175d11608b41b0');

        // Jika salah satu kosong, coba ambil langsung dari database terapi
        if (empty($this->consId) || empty($this->secretKey) || empty($this->userKey)) {
            try {
                $terapiAppKey = base64_decode('Inwy+cw1XY7nzpXMfnpXWC4z6bFvzzHsk4pB7dqY0Js=');
                $encrypter = new Encrypter($terapiAppKey, 'AES-256-CBC');

                $configs = DB::connection('terapi')->table('configurations')
                    ->where('key', 'like', 'bpjs.antrian_online%')
                    ->get()
                    ->keyBy('key');

                if (isset($configs['bpjs.antrian_online.base_url'])) {
                    $this->baseUrl = $configs['bpjs.antrian_online.base_url']->value;
                }
                if (isset($configs['bpjs.antrian_online.cons_id'])) {
                    $this->consId = $configs['bpjs.antrian_online.cons_id']->value;
                }
                if (isset($configs['bpjs.antrian_online.secret_key'])) {
                    $row = $configs['bpjs.antrian_online.secret_key'];
                    $this->secretKey = $row->is_encrypted ? $encrypter->decryptString($row->value) : $row->value;
                }
                if (isset($configs['bpjs.antrian_online.user_key'])) {
                    $row = $configs['bpjs.antrian_online.user_key'];
                    $this->userKey = $row->is_encrypted ? $encrypter->decryptString($row->value) : $row->value;
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal memuat kredensial BPJS dari database terapi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Header autentikasi HMAC-SHA256 untuk BPJS
     */
    protected function generateHeaders(int $timestamp): array
    {
        $signature = base64_encode(
            hash_hmac('sha256', $this->consId . '&' . $timestamp, $this->secretKey, true)
        );

        return [
            'X-cons-id' => $this->consId,
            'X-timestamp' => (string) $timestamp,
            'X-signature' => $signature,
            'user_key' => $this->userKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Dekripsi respon BPJS AES-256-CBC dilanjutkan dekompresi LZString
     */
    protected function decryptResponse(string $encrypted, int $timestamp): ?array
    {
        $key = $this->consId . $this->secretKey . $timestamp;
        $keyHash = hex2bin(hash('sha256', $key));
        $iv = substr($keyHash, 0, 16);

        $decrypted = openssl_decrypt(
            base64_decode($encrypted),
            'aes-256-cbc',
            $keyHash,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false || $decrypted === null) {
            return null;
        }

        // Coba JSON decode langsung
        $json = json_decode($decrypted, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return $json;
        }

        // Dekompresi LZString jika masih terkompresi
        $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);
        if ($decompressed) {
            $parsed = json_decode($decompressed, true);
            if (is_array($parsed)) {
                return $parsed;
            }
        }

        return null;
    }

    /**
     * Ambil data antrean pendaftaran per tanggal dari API BPJS
     * GET /antrean/pendaftaran/tanggal/{tanggal}
     */
    public function getAntreanByTanggal(string $date): array
    {
        $url = rtrim($this->baseUrl, '/') . '/antrean/pendaftaran/tanggal/' . $date;
        $timestamp = time();

        try {
            $response = Http::withHeaders($this->generateHeaders($timestamp))
                ->timeout($this->requestTimeout)
                ->get($url);

            $body = $response->json();
            $metaCode = (string) ($body['metadata']['code'] ?? $body['metaData']['code'] ?? $response->status());
            $metaMessage = (string) ($body['metadata']['message'] ?? $body['metaData']['message'] ?? '');

            $responseData = $body['response'] ?? null;

            if (is_string($responseData) && !empty($responseData)) {
                $decrypted = $this->decryptResponse($responseData, $timestamp);
                $responseData = $decrypted ?? [];
            }

            return [
                'code' => $metaCode,
                'message' => $metaMessage,
                'response' => is_array($responseData) ? $responseData : [],
            ];
        } catch (\Throwable $e) {
            return [
                'code' => '500',
                'message' => $e->getMessage(),
                'response' => [],
            ];
        }
    }

    /**
     * Tarik dan simpan data antrean per tanggal ke database terapi.bpjs_antrean_bookings
     */
    public function pullDate(string $date): array
    {
        $result = $this->getAntreanByTanggal($date);

        if (($result['code'] ?? '') !== '200') {
            return [
                'date' => $date,
                'success' => false,
                'count' => 0,
                'message' => $result['message'] ?? 'Respon bukan 200',
            ];
        }

        $items = is_array($result['response'] ?? null) ? $result['response'] : [];

        $upsertData = collect($items)
            ->filter(fn($item) => !empty($item['kodebooking']))
            ->map(fn($item) => [
                'kode_booking' => $item['kodebooking'],
                'tanggal' => $date,
                'kd_poli' => $item['kodepoli'] ?? null,
                'kd_dokter' => $item['kodedokter'] ?? null,
                'jam_praktek' => $item['jampraktek'] ?? null,
                'nik' => $item['nik'] ?? null,
                'no_kartu' => $item['nokapst'] ?? null,
                'no_hp' => $item['nohp'] ?? null,
                'no_rm' => $item['norekammedis'] ?? null,
                'jenis_kunjungan' => (string) ($item['jeniskunjungan'] ?? ''),
                'no_referensi' => $item['nomorreferensi'] ?? null,
                'sumber_data' => $item['sumberdata'] ?? null,
                'is_peserta' => (bool) ($item['ispeserta'] ?? false),
                'no_antrean' => $item['noantrean'] ?? null,
                'estimasi_timestamp' => isset($item['estimasidilayani']) && $item['estimasidilayani'] ? (int) $item['estimasidilayani'] : null,
                'status' => $item['status'] ?? null,
                'created_time_timestamp' => isset($item['createdtime']) && $item['createdtime'] ? (int) $item['createdtime'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values()
            ->toArray();

        if (!empty($upsertData)) {
            DB::connection('terapi')->table('bpjs_antrean_bookings')->upsert(
                $upsertData,
                ['kode_booking'],
                [
                    'tanggal',
                    'kd_poli',
                    'kd_dokter',
                    'jam_praktek',
                    'nik',
                    'no_kartu',
                    'no_hp',
                    'no_rm',
                    'jenis_kunjungan',
                    'no_referensi',
                    'sumber_data',
                    'is_peserta',
                    'no_antrean',
                    'estimasi_timestamp',
                    'status',
                    'created_time_timestamp',
                    'updated_at'
                ]
            );
        }

        return [
            'date' => $date,
            'success' => true,
            'count' => count($upsertData),
            'message' => 'Berhasil menyimpan ' . count($upsertData) . ' antrean',
        ];
    }

    /**
     * Tarik dan simpan data antrean untuk rentang tanggal
     */
    public function pullRange(string $startDate, string $endDate): array
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $totalPulled = 0;
        $successDates = 0;
        $failedDates = 0;
        $details = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $res = $this->pullDate($dateStr);
            $details[] = $res;

            if ($res['success']) {
                $successDates++;
                $totalPulled += $res['count'];
            } else {
                $failedDates++;
            }
        }

        return [
            'total_pulled' => $totalPulled,
            'success_dates' => $successDates,
            'failed_dates' => $failedDates,
            'details' => $details,
        ];
    }
}

