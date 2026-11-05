<?php

namespace App\Services\Bpjs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TerapiAntreanApiClient
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected int $timeout;
    protected string $cacheTokenKey = 'terapi_api_bearer_token';

    public function __construct()
    {
        $this->baseUrl = rtrim(env('TERAPI_BASE_URL', 'https://terapi.rumkittnipsp.com'), '/');
        $this->username = env('TERAPI_USERNAME', 'dashboard');
        $this->password = env('TERAPI_PASSWORD', 'dAshbo4rd2026');
        $this->timeout = (int) env('TERAPI_TIMEOUT', 60);
    }

    /**
     * Dapatkan token autentikasi bearer (dengan cache 12 jam)
     */
    public function getToken(bool $forceRefresh = false): ?string
    {
        if (!$forceRefresh) {
            $cachedToken = Cache::get($this->cacheTokenKey);
            if ($cachedToken) {
                return $cachedToken;
            }
        }

        try {
            $url = "{$this->baseUrl}/api/v1/auth/token";
            $response = Http::withHeaders([
                'x-username' => $this->username,
                'x-password' => $this->password,
                'Accept' => 'application/json',
            ])->timeout(30)->post($url, [
                'name' => 'Dashboard SIMRS Queue Compliance Token',
                'expires_in_hours' => 24,
            ]);

            if ($response->successful()) {
                $token = $response->json('data.token');
                if ($token) {
                    Cache::put($this->cacheTokenKey, $token, now()->addHours(12));
                    return $token;
                }
            }

            Log::error('TerapiAntreanApiClient: Gagal membuat token API', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('TerapiAntreanApiClient: Exception saat request token', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Request HTTP client yang sudah diautentikasi ke API Terapi
     */
    protected function client()
    {
        $token = $this->getToken();
        return Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->timeout($this->timeout);
    }

    /**
     * Ringkasan bulanan: total, kesesuaian, kanal, pemanfaatan, dan waktu proses
     * GET /api/v1/bpjs/antrean/report?month=YYYY-MM
     */
    public function getSummary(string $month): array
    {
        $cacheKey = "terapi_antrean_summary_{$month}";
        return Cache::remember($cacheKey, 300, function () use ($month) {
            try {
                $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report", [
                    'month' => $month,
                ]);

                if ($res->status() === 401) {
                    $this->getToken(true);
                    $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report", [
                        'month' => $month,
                    ]);
                }

                return $res->json('data') ?? [];
            } catch (\Throwable $e) {
                Log::error('TerapiAntreanApiClient: Gagal mengambil summary', ['month' => $month, 'error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Rekap harian: sesuai/tidak sesuai dan kanal per tanggal
     * GET /api/v1/bpjs/antrean/report/daily?month=YYYY-MM
     */
    public function getDaily(string $month): array
    {
        $cacheKey = "terapi_antrean_daily_{$month}";
        return Cache::remember($cacheKey, 300, function () use ($month) {
            try {
                $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/daily", [
                    'month' => $month,
                ]);

                if ($res->status() === 401) {
                    $this->getToken(true);
                    $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/daily", [
                        'month' => $month,
                    ]);
                }

                return $res->json('data') ?? [];
            } catch (\Throwable $e) {
                Log::error('TerapiAntreanApiClient: Gagal mengambil daily', ['month' => $month, 'error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Rekap bulanan per poliklinik (agregat semua dokter)
     * GET /api/v1/bpjs/antrean/report/polyclinic?month=YYYY-MM&sort=total
     */
    public function getPolyclinic(string $month, string $sort = 'total'): array
    {
        $cacheKey = "terapi_antrean_polyclinic_{$month}_{$sort}";
        return Cache::remember($cacheKey, 300, function () use ($month, $sort) {
            try {
                $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/polyclinic", [
                    'month' => $month,
                    'sort' => $sort,
                ]);

                if ($res->status() === 401) {
                    $this->getToken(true);
                    $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/polyclinic", [
                        'month' => $month,
                        'sort' => $sort,
                    ]);
                }

                return $res->json('data') ?? [];
            } catch (\Throwable $e) {
                Log::error('TerapiAntreanApiClient: Gagal mengambil polyclinic', ['month' => $month, 'error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Rekap bulanan per dokter
     * GET /api/v1/bpjs/antrean/report/doctor?month=YYYY-MM&sort=total
     */
    public function getDoctor(string $month, string $sort = 'total'): array
    {
        $cacheKey = "terapi_antrean_doctor_{$month}_{$sort}";
        return Cache::remember($cacheKey, 300, function () use ($month, $sort) {
            try {
                $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/doctor", [
                    'month' => $month,
                    'sort' => $sort,
                ]);

                if ($res->status() === 401) {
                    $this->getToken(true);
                    $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/doctor", [
                        'month' => $month,
                        'sort' => $sort,
                    ]);
                }

                return $res->json('data') ?? [];
            } catch (\Throwable $e) {
                Log::error('TerapiAntreanApiClient: Gagal mengambil doctor', ['month' => $month, 'error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Rinci per kombinasi poliklinik + dokter
     * GET /api/v1/bpjs/antrean/report/poly-doctor?month=YYYY-MM&sort=total
     */
    public function getPolyDoctor(string $month, string $sort = 'total'): array
    {
        $cacheKey = "terapi_antrean_poly_doctor_{$month}_{$sort}";
        return Cache::remember($cacheKey, 300, function () use ($month, $sort) {
            try {
                $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/poly-doctor", [
                    'month' => $month,
                    'sort' => $sort,
                ]);

                if ($res->status() === 401) {
                    $this->getToken(true);
                    $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/poly-doctor", [
                        'month' => $month,
                        'sort' => $sort,
                    ]);
                }

                return $res->json('data') ?? [];
            } catch (\Throwable $e) {
                Log::error('TerapiAntreanApiClient: Gagal mengambil poly-doctor', ['month' => $month, 'error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Daftar antrean tidak sesuai (mismatches) beserta alasannya
     * GET /api/v1/bpjs/antrean/report/mismatches?month=YYYY-MM&page=1&per_page=100
     */
    public function getMismatches(string $month, int $page = 1, int $perPage = 100): array
    {
        $cacheKey = "terapi_antrean_mismatches_{$month}_{$page}_{$perPage}";
        return Cache::remember($cacheKey, 300, function () use ($month, $page, $perPage) {
            try {
                $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/mismatches", [
                    'month' => $month,
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

                if ($res->status() === 401) {
                    $this->getToken(true);
                    $res = $this->client()->get("{$this->baseUrl}/api/v1/bpjs/antrean/report/mismatches", [
                        'month' => $month,
                        'page' => $page,
                        'per_page' => $perPage,
                    ]);
                }

                return [
                    'data' => $res->json('data') ?? [],
                    'meta' => $res->json('meta') ?? [],
                ];
            } catch (\Throwable $e) {
                Log::error('TerapiAntreanApiClient: Gagal mengambil mismatches', ['month' => $month, 'error' => $e->getMessage()]);
                return ['data' => [], 'meta' => []];
            }
        });
    }

    /**
     * Bersihkan cache data antrean untuk bulan tertentu
     */
    public function clearMonthCache(string $month): void
    {
        Cache::forget("terapi_antrean_summary_{$month}");
        Cache::forget("terapi_antrean_daily_{$month}");
        Cache::forget("terapi_antrean_polyclinic_{$month}_total");
        Cache::forget("terapi_antrean_doctor_{$month}_total");
        Cache::forget("terapi_antrean_poly_doctor_{$month}_total");
        Cache::forget("terapi_antrean_mismatches_{$month}_1_100");
    }
}

