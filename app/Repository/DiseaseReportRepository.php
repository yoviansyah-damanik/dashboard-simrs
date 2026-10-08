<?php

namespace App\Repository;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

interface DiseaseReportInterface
{
    public static function getAvailableYears(): array;
    public static function getSummary(array $filters): array;
    public static function getTopDiseases(array $filters, int $limit = 10): array;
    public static function getDiseasesPaginated(array $filters, int $perPage = 25): LengthAwarePaginator;
    public static function getAllDiseasesForExport(array $filters, int $limit = 100): array;
    public static function getPatientDrilldown(string $diseaseCode, array $filters, int $limit = 50): array;
}

class DiseaseReportRepository implements DiseaseReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Definisi pengelompokan kasus penyakit surveilans / klinis spesifik (ICD-10)
     */
    const SPECIAL_CASES = [
        'isk' => [
            'label' => 'Infeksi Saluran Kemih (ISK / UTI)',
            'short_label' => 'ISK',
            'patterns' => ['N39.0%', 'N390%', 'N30%', 'N10%', 'N11%', 'N12%', 'N34%', 'T83.5%', 'O23%', 'P39.3%'],
            'description' => 'N39.0 (ISK Tak Spesifik), N30 (Sistitis), N10-N12 (Pielonefritis), N34 (Uretritis), T83.5 (CAUTI), O23',
        ],
        'ispa' => [
            'label' => 'ISPA & Pneumonia',
            'short_label' => 'ISPA',
            'patterns' => ['J00%', 'J01%', 'J02%', 'J03%', 'J04%', 'J05%', 'J06%', 'J12%', 'J13%', 'J14%', 'J15%', 'J16%', 'J17%', 'J18%', 'J20%', 'J21%', 'J22%'],
            'description' => 'J00-J06 (ISPA Atas), J12-J18 (Pneumonia), J20-J22 (Bronkitis)',
        ],
        'tb' => [
            'label' => 'Tuberkulosis (TBC)',
            'short_label' => 'TBC',
            'patterns' => ['A15%', 'A16%', 'A17%', 'A18%', 'A19%'],
            'description' => 'A15-A19 (TB Paru & TB Ekstra Paru)',
        ],
        'diare' => [
            'label' => 'Diare & Gastroenteritis (GEA)',
            'short_label' => 'Diare',
            'patterns' => ['A09%', 'A00%', 'A01%', 'A02%', 'A03%', 'A04%', 'A05%', 'A06%', 'A07%', 'A08%', 'K52.9%'],
            'description' => 'A09 (Diare Akut), A00-A08 (Infeksi Saluran Cerna), K52.9',
        ],
        'dbd' => [
            'label' => 'Demam Berdarah Dengue (DBD / Dengue)',
            'short_label' => 'DBD',
            'patterns' => ['A90%', 'A91%'],
            'description' => 'A90 (Demam Dengue), A91 (DHF / DBD)',
        ],
        'tifoid' => [
            'label' => 'Demam Tifoid & Paratifoid',
            'short_label' => 'Tifoid',
            'patterns' => ['A01%'],
            'description' => 'A01.0 - A01.4 (Typhoid & Paratyphoid Fever)',
        ],
        'hipertensi' => [
            'label' => 'Hipertensi',
            'short_label' => 'Hipertensi',
            'patterns' => ['I10%', 'I11%', 'I12%', 'I13%', 'I14%', 'I15%'],
            'description' => 'I10 (Esensial), I11-I15 (Penyakit Hipertensi)',
        ],
        'dm' => [
            'label' => 'Diabetes Melitus (DM)',
            'short_label' => 'Diabetes',
            'patterns' => ['E10%', 'E11%', 'E12%', 'E13%', 'E14%'],
            'description' => 'E10-E14 (Diabetes Melitus Tipe 1, 2, dsb)',
        ],
        'dispepsia' => [
            'label' => 'Dispepsia & Gastritis',
            'short_label' => 'Dispepsia',
            'patterns' => ['K29%', 'K30%'],
            'description' => 'K29 (Gastritis & Duodenitis), K30 (Dispepsia)',
        ],
        'ido' => [
            'label' => 'Infeksi Daerah Operasi (IDO / SSI)',
            'short_label' => 'IDO',
            'patterns' => ['T81.4%'],
            'description' => 'T81.4 (Infeksi Luka Operasi / Pasca Bedah)',
        ],
    ];

    /**
     * Mengambil daftar kasus khusus untuk dropdown filter
     */
    public static function getSpecialCases(): array
    {
        return self::SPECIAL_CASES;
    }

    /**
     * Mengambil daftar tahun yang tersedia dari tabel registrasi
     */
    public static function getAvailableYears(): array
    {
        $years = DB::connection(self::KONEKSI)
            ->table('reg_periksa')
            ->selectRaw('YEAR(tgl_registrasi) as yr')
            ->groupBy('yr')
            ->orderByDesc('yr')
            ->pluck('yr')
            ->toArray();

        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        return array_map('intval', $years);
    }

    /**
     * Membangun kueri dasar dengan seluruh filter yang dipilih
     */
    private static function buildBaseQuery(array $filters)
    {
        $query = DB::connection(self::KONEKSI)
            ->table('diagnosa_pasien as dp')
            ->join('penyakit as p', 'dp.kd_penyakit', '=', 'p.kd_penyakit')
            ->join('reg_periksa as rp', 'dp.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis');

        // Filter Status Periksa: Jika Ralan, pastikan stts not in ('Batal', 'Belum')
        $query->where(function ($q) {
            $q->where('rp.status_lanjut', '!=', 'Ralan')
              ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
        });

        // Filter Rentang Tanggal atau Bulan/Tahun
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('rp.tgl_registrasi', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['year'])) {
            $query->whereYear('rp.tgl_registrasi', $filters['year']);
            if (!empty($filters['month'])) {
                $query->whereMonth('rp.tgl_registrasi', $filters['month']);
            }
        }

        // Filter Status Pelayanan (Poli / IGD / Ralan / Ranap)
        if (!empty($filters['service_status']) && $filters['service_status'] !== 'all') {
            if ($filters['service_status'] === 'IGD') {
                $query->where('rp.kd_poli', 'IGDK');
            } elseif ($filters['service_status'] === 'Poli') {
                $query->where('rp.status_lanjut', 'Ralan')
                      ->where('rp.kd_poli', '!=', 'IGDK');
            } elseif ($filters['service_status'] === 'Ralan') {
                $query->where('rp.status_lanjut', 'Ralan');
            } elseif ($filters['service_status'] === 'Ranap') {
                $query->where('rp.status_lanjut', 'Ranap');
            }
        }

        // Filter Prioritas Diagnosa (1 = Primer, >1 = Sekunder)
        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            if ($filters['priority'] === '1') {
                $query->where('dp.prioritas', 1);
            } elseif ($filters['priority'] === '2') {
                $query->where('dp.prioritas', '>', 1);
            }
        }

        // Filter Status Kasus (Baru / Lama)
        if (!empty($filters['case_type']) && $filters['case_type'] !== 'all') {
            $query->where('dp.status_penyakit', $filters['case_type']);
        }

        // Filter Jenis Kelamin
        if (!empty($filters['gender']) && $filters['gender'] !== 'all') {
            $query->where('ps.jk', $filters['gender']);
        }

        // Filter Kasus Khusus (Special Disease Category: ISK, ISPA, TBC, Diare, DBD, dsb)
        if (!empty($filters['special_case']) && $filters['special_case'] !== 'all' && isset(self::SPECIAL_CASES[$filters['special_case']])) {
            $patterns = self::SPECIAL_CASES[$filters['special_case']]['patterns'];
            $query->where(function ($q) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $q->orWhere('dp.kd_penyakit', 'like', $pattern);
                }
            });
        }

        // Filter Pencarian
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('dp.kd_penyakit', 'like', "%{$search}%")
                  ->orWhere('p.nm_penyakit', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Mengambil ringkasan metrik statistik morbiditas
     */
    public static function getSummary(array $filters): array
    {
        $query = self::buildBaseQuery($filters);

        $stats = $query->selectRaw("
            COUNT(*) as total_kasus,
            COUNT(DISTINCT dp.kd_penyakit) as total_penyakit_unik,
            COUNT(DISTINCT rp.no_rkm_medis) as total_pasien_unik,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' THEN 1 ELSE 0 END) as kasus_baru,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' THEN 1 ELSE 0 END) as kasus_lama,
            SUM(CASE WHEN dp.prioritas = 1 THEN 1 ELSE 0 END) as primer,
            SUM(CASE WHEN dp.prioritas > 1 THEN 1 ELSE 0 END) as sekunder,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as wanita,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as ralan,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap
        ")->first();

        $totalKasus = (int) ($stats->total_kasus ?? 0);

        return [
            'total_kasus' => $totalKasus,
            'total_penyakit_unik' => (int) ($stats->total_penyakit_unik ?? 0),
            'total_pasien_unik' => (int) ($stats->total_pasien_unik ?? 0),
            'kasus_baru' => (int) ($stats->kasus_baru ?? 0),
            'kasus_lama' => (int) ($stats->kasus_lama ?? 0),
            'primer' => (int) ($stats->primer ?? 0),
            'sekunder' => (int) ($stats->sekunder ?? 0),
            'pria' => (int) ($stats->pria ?? 0),
            'wanita' => (int) ($stats->wanita ?? 0),
            'poli' => (int) ($stats->poli ?? 0),
            'igd' => (int) ($stats->igd ?? 0),
            'ralan' => (int) ($stats->ralan ?? 0),
            'ranap' => (int) ($stats->ranap ?? 0),
        ];
    }

    /**
     * Mengambil Top N Penyakit Terbanyak untuk visualisasi grafik
     */
    public static function getTopDiseases(array $filters, int $limit = 10): array
    {
        $query = self::buildBaseQuery($filters);

        $results = $query->selectRaw("
            dp.kd_penyakit,
            p.nm_penyakit,
            COUNT(*) as total_kasus,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as total_pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as total_wanita,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' THEN 1 ELSE 0 END) as kasus_baru,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' THEN 1 ELSE 0 END) as kasus_lama,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap
        ")
        ->groupBy('dp.kd_penyakit', 'p.nm_penyakit')
        ->orderByDesc('total_kasus')
        ->limit($limit)
        ->get();

        $labels = [];
        $dataTotal = [];
        $dataPria = [];
        $dataWanita = [];
        $items = [];

        foreach ($results as $idx => $r) {
            $shortName = mb_strimwidth($r->nm_penyakit, 0, 25, '...');
            $labels[] = "{$r->kd_penyakit} ({$shortName})";
            $dataTotal[] = (int) $r->total_kasus;
            $dataPria[] = (int) $r->total_pria;
            $dataWanita[] = (int) $r->total_wanita;

            $items[] = [
                'rank' => $idx + 1,
                'code' => $r->kd_penyakit,
                'name' => $r->nm_penyakit,
                'total' => (int) $r->total_kasus,
                'pria' => (int) $r->total_pria,
                'wanita' => (int) $r->total_wanita,
                'baru' => (int) $r->kasus_baru,
                'lama' => (int) $r->kasus_lama,
                'poli' => (int) $r->total_poli,
                'igd' => (int) $r->total_igd,
                'ranap' => (int) $r->total_ranap,
            ];
        }

        return [
            'labels' => $labels,
            'data_total' => $dataTotal,
            'data_pria' => $dataPria,
            'data_wanita' => $dataWanita,
            'items' => $items,
        ];
    }

    /**
     * Mengambil data penyakit secara berpaginasi untuk tabel utama
     */
    public static function getDiseasesPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $baseQuery = self::buildBaseQuery($filters);

        // Agregasi per kode penyakit
        $aggregateQuery = $baseQuery->selectRaw("
            dp.kd_penyakit,
            p.nm_penyakit,
            COUNT(*) as total_kasus,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' AND ps.jk = 'L' THEN 1 ELSE 0 END) as baru_pria,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' AND ps.jk = 'P' THEN 1 ELSE 0 END) as baru_wanita,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' AND ps.jk = 'L' THEN 1 ELSE 0 END) as lama_pria,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' AND ps.jk = 'P' THEN 1 ELSE 0 END) as lama_wanita,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as total_pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as total_wanita,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' THEN 1 ELSE 0 END) as total_baru,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' THEN 1 ELSE 0 END) as total_lama,
            SUM(CASE WHEN dp.prioritas = 1 THEN 1 ELSE 0 END) as total_primer,
            SUM(CASE WHEN dp.prioritas > 1 THEN 1 ELSE 0 END) as total_sekunder,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as total_ralan,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap
        ")
        ->groupBy('dp.kd_penyakit', 'p.nm_penyakit')
        ->orderByDesc('total_kasus');

        return $aggregateQuery->paginate($perPage);
    }

    /**
     * Mengambil daftar seluruh penyakit untuk diekspor ke PDF (hingga limit tertentu)
     */
    public static function getAllDiseasesForExport(array $filters, int $limit = 100): array
    {
        $baseQuery = self::buildBaseQuery($filters);

        $rows = $baseQuery->selectRaw("
            dp.kd_penyakit,
            p.nm_penyakit,
            COUNT(*) as total_kasus,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' AND ps.jk = 'L' THEN 1 ELSE 0 END) as baru_pria,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' AND ps.jk = 'P' THEN 1 ELSE 0 END) as baru_wanita,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' AND ps.jk = 'L' THEN 1 ELSE 0 END) as lama_pria,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' AND ps.jk = 'P' THEN 1 ELSE 0 END) as lama_wanita,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as total_pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as total_wanita,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' THEN 1 ELSE 0 END) as total_baru,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' THEN 1 ELSE 0 END) as total_lama,
            SUM(CASE WHEN dp.prioritas = 1 THEN 1 ELSE 0 END) as total_primer,
            SUM(CASE WHEN dp.prioritas > 1 THEN 1 ELSE 0 END) as total_sekunder,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as total_ralan,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap
        ")
        ->groupBy('dp.kd_penyakit', 'p.nm_penyakit')
        ->orderByDesc('total_kasus')
        ->limit($limit)
        ->get();

        $totalAll = $rows->sum('total_kasus') ?: 1;

        $items = [];
        foreach ($rows as $idx => $r) {
            $items[] = [
                'rank' => $idx + 1,
                'code' => $r->kd_penyakit,
                'name' => $r->nm_penyakit,
                'baru_pria' => (int) $r->baru_pria,
                'baru_wanita' => (int) $r->baru_wanita,
                'lama_pria' => (int) $r->lama_pria,
                'lama_wanita' => (int) $r->lama_wanita,
                'total_pria' => (int) $r->total_pria,
                'total_wanita' => (int) $r->total_wanita,
                'total_baru' => (int) $r->total_baru,
                'total_lama' => (int) $r->total_lama,
                'total_primer' => (int) $r->total_primer,
                'total_sekunder' => (int) $r->total_sekunder,
                'total_poli' => (int) $r->total_poli,
                'total_igd' => (int) $r->total_igd,
                'total_ralan' => (int) $r->total_ralan,
                'total_ranap' => (int) $r->total_ranap,
                'total_kasus' => (int) $r->total_kasus,
                'percent' => round(($r->total_kasus / $totalAll) * 100, 2),
            ];
        }

        return [
            'total_all' => $totalAll,
            'items' => $items,
        ];
    }

    /**
     * Mengambil daftar rincian kunjungan pasien untuk penyakit tertentu (Modal Drilldown)
     */
    public static function getPatientDrilldown(string $diseaseCode, array $filters, int $limit = 50): array
    {
        $query = self::buildBaseQuery($filters);

        $query->where('dp.kd_penyakit', $diseaseCode);

        $rows = $query->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('poliklinik as pl', 'rp.kd_poli', '=', 'pl.kd_poli')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->select([
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'rp.kd_poli',
                'ps.nm_pasien',
                'ps.jk',
                'ps.tgl_lahir',
                'rp.tgl_registrasi',
                'rp.status_lanjut',
                'dp.prioritas',
                'dp.status_penyakit',
                'd.nm_dokter',
                'pl.nm_poli',
                'pj.png_jawab as penjamin',
            ])
            ->orderByDesc('rp.tgl_registrasi')
            ->limit($limit)
            ->get();

        $items = [];
        foreach ($rows as $r) {
            $age = $r->tgl_lahir ? Carbon::parse($r->tgl_lahir)->age : '-';
            $isIgd = ($r->kd_poli === 'IGDK');
            $layananLabel = $r->status_lanjut === 'Ranap' ? 'Ranap' : ($isIgd ? 'IGD' : 'Poli');
            $layananDetail = $r->status_lanjut === 'Ranap' ? 'Rawat Inap' : ($isIgd ? 'Gawat Darurat (IGD)' : 'Poliklinik Rawat Jalan');

            $items[] = [
                'no_rawat' => $r->no_rawat,
                'no_rkm_medis' => $r->no_rkm_medis,
                'nama' => $r->nm_pasien,
                'jk' => $r->jk,
                'umur' => $age,
                'tanggal' => Carbon::parse($r->tgl_registrasi)->format('d/m/Y'),
                'layanan' => $layananLabel,
                'layanan_detail' => $layananDetail,
                'prioritas' => $r->prioritas == 1 ? 'Primer' : 'Sekunder',
                'kasus' => $r->status_penyakit,
                'dokter' => $r->nm_dokter ?? '-',
                'unit' => $r->nm_poli ?? '-',
                'penjamin' => $r->penjamin ?? '-',
            ];
        }

        return $items;
    }
}
