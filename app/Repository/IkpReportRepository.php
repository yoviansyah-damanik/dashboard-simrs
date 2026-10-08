<?php

namespace App\Repository;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

interface IkpReportInterface
{
    public static function getSummary(int $year, ?int $month = null): array;
    public static function getIncidentList(int $year, ?int $month = null, ?string $jenis = null, int $limit = 50): array;
    public static function getRiskGradingMatrix(): array;
}

class IkpReportRepository implements IkpReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Mengambil ringkasan data Insiden Keselamatan Pasien (IKP)
     */
    public static function getSummary(int $year, ?int $month = null): array
    {
        $conn = DB::connection(self::KONEKSI);

        // Ambil data kejadian IKP jika tabel terisi, atau kombinasikan dengan katalog insiden master
        $incidents = self::getIncidentList($year, $month, null, 100);

        $counts = [
            'total' => count($incidents),
            'ktd' => 0,      // Kejadian Tidak Diharapkan
            'knc' => 0,      // Kejadian Nyaris Cedera
            'ktc' => 0,      // Kejadian Tidak Cedera
            'kpc' => 0,      // Kondisi Potensial Cedera
            'sentinel' => 0, // Sentinel
        ];

        $gradingCounts = [
            'biru' => 0,   // Risiko Rendah (Investigasi sederhana 1 minggu)
            'hijau' => 0,  // Risiko Sedang (Investigasi sederhana 2 minggu)
            'kuning' => 0, // Risiko Tinggi (RCA maks 45 hari)
            'merah' => 0,  // Risiko Ekstrim (RCA maks 45 hari)
        ];

        foreach ($incidents as $inc) {
            $jenis = strtolower($inc['jenis_insiden'] ?? '');
            if (isset($counts[$jenis])) {
                $counts[$jenis]++;
            }

            $grade = strtolower($inc['grading_risiko'] ?? 'biru');
            if (isset($gradingCounts[$grade])) {
                $gradingCounts[$grade]++;
            }
        }

        // Hitung persentase grading dan status tindak lanjut
        $selesaiInvestigasi = count(array_filter($incidents, fn($i) => !empty($i['rtl']) || $i['status'] === 'Selesai'));
        $persenSelesai = $counts['total'] > 0 ? round(($selesaiInvestigasi / $counts['total']) * 100, 1) : 100;

        return [
            'year' => $year,
            'month' => $month,
            'counts' => $counts,
            'grading' => $gradingCounts,
            'selesai_investigasi' => $selesaiInvestigasi,
            'persen_selesai' => $persenSelesai,
            'incidents' => $incidents,
        ];
    }

    /**
     * Mendapatkan daftar insiden keselamatan pasien dengan grading dan kronologis
     */
    public static function getIncidentList(int $year, ?int $month = null, ?string $jenis = null, int $limit = 50): array
    {
        $conn = DB::connection(self::KONEKSI);

        $list = [];

        if (Schema::connection(self::KONEKSI)->hasTable('insiden_keselamatan_pasien')) {
            $query = $conn->table('insiden_keselamatan_pasien as ikp')
                ->leftJoin('insiden_keselamatan as ik', 'ikp.kode_insiden', '=', 'ik.kode_insiden')
                ->select(
                    'ikp.no_rawat',
                    'ikp.tgl_kejadian',
                    'ikp.jam_kejadian',
                    'ikp.tgl_lapor',
                    'ikp.kode_insiden',
                    'ik.nama_insiden',
                    'ik.jenis_insiden',
                    'ik.dampak',
                    'ikp.lokasi',
                    'ikp.kronologis',
                    'ikp.unit_terkait',
                    'ikp.akibat',
                    'ikp.tindakan_insiden',
                    'ikp.identifikasi_masalah',
                    'ikp.rtl'
                )
                ->whereYear('ikp.tgl_kejadian', $year);

            if ($month) {
                $query->whereMonth('ikp.tgl_kejadian', $month);
            }

            if ($jenis) {
                $query->where('ik.jenis_insiden', strtoupper($jenis));
            }

            $dbRecords = $query->orderByDesc('ikp.tgl_kejadian')->limit($limit)->get();

            foreach ($dbRecords as $row) {
                $grade = self::determineGrading($row->jenis_insiden, $row->dampak);
                $list[] = [
                    'id' => $row->kode_insiden,
                    'no_rawat' => $row->no_rawat,
                    'tanggal' => $row->tgl_kejadian,
                    'jam' => $row->jam_kejadian,
                    'tgl_lapor' => $row->tgl_lapor,
                    'nama_insiden' => $row->nama_insiden ?? 'Insiden Keselamatan',
                    'jenis_insiden' => strtoupper($row->jenis_insiden ?? 'KTC'),
                    'dampak' => $row->dampak ?? 'Minor',
                    'lokasi' => $row->lokasi ?? 'Unit Pelayanan',
                    'kronologis' => $row->kronologis ?? '-',
                    'unit_terkait' => $row->unit_terkait ?? '-',
                    'tindakan' => $row->tindakan_insiden ?? '-',
                    'rtl' => $row->rtl ?? '-',
                    'grading_risiko' => $grade,
                    'status' => !empty($row->rtl) ? 'Selesai' : 'Dalam Investigasi',
                ];
            }
        }

        return $list;
    }

    /**
     * Menentukan grading matriks risiko (Biru, Hijau, Kuning, Merah)
     */
    private static function determineGrading(?string $jenis, ?string $dampak): string
    {
        $jenis = strtoupper($jenis ?? '');
        $dampakStr = strtolower($dampak ?? '');

        if ($jenis === 'SENTINEL' || str_contains($dampakStr, 'katastropik') || str_contains($dampakStr, 'mayor')) {
            return 'merah';
        }
        if ($jenis === 'KTD' || str_contains($dampakStr, 'moderat')) {
            return 'kuning';
        }
        if ($jenis === 'KNC' || str_contains($dampakStr, 'minor')) {
            return 'hijau';
        }
        return 'biru';
    }

    /**
     * Definisi Matriks Grading Risiko Standar Kemenkes RI
     */
    public static function getRiskGradingMatrix(): array
    {
        return [
            'merah' => [
                'nama' => 'Ekstrim (Merah)',
                'tindakan' => 'RCA (Root Cause Analysis) maks 45 hari. Memerlukan tindakan segera dan perhatian Direksi.',
                'color' => 'bg-red-500 text-white',
            ],
            'kuning' => [
                'nama' => 'Tinggi (Kuning)',
                'tindakan' => 'RCA maks 45 hari. Investigasi komprehensif oleh Komite Mutu & Keselamatan Pasien.',
                'color' => 'bg-amber-500 text-white',
            ],
            'hijau' => [
                'nama' => 'Sedang (Hijau)',
                'tindakan' => 'Investigasi sederhana maks 2 minggu oleh kepala unit kerja/ruangan terkait.',
                'color' => 'bg-emerald-500 text-white',
            ],
            'biru' => [
                'nama' => 'Rendah (Biru)',
                'tindakan' => 'Investigasi sederhana maks 1 minggu oleh kepala unit kerja/ruangan terkait.',
                'color' => 'bg-blue-500 text-white',
            ],
        ];
    }
}
