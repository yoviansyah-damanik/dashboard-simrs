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
    public static function getMonthlyBreakdown(array $filters): array;
    public static function getVisitsPaginated(array $filters, int $perPage = 25): LengthAwarePaginator;
    public static function getVisitorsPaginated(array $filters, int $perPage = 25): LengthAwarePaginator;
    public static function getAllVisitsForExport(array $filters, int $limit = 500): array;
    public static function getAllVisitorsForExport(array $filters, int $limit = 500): array;
    public static function maskMedicalRecord(?string $rm): string;
    public static function maskPatientName(?string $name): string;
    public static function getDiseaseMonthlyMatrix(array $filters, int $limit = 50): array;
    public static function getPolyclinicMonthlyMatrix(array $filters): array;
    public static function getDoctorMonthlyMatrix(array $filters, int $limit = 30): array;
    public static function getMomSummaryKpi(array $filters): array;
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
        'jantung' => [
            'label' => 'Penyakit Jantung',
            'short_label' => 'Jantung',
            'patterns' => [
                'I05%', 'I06%', 'I07%', 'I08%', 'I09%',
                'I11%',
                'I20%', 'I21%', 'I22%', 'I23%', 'I24%', 'I25%',
                'I26%', 'I27%', 'I28%',
                'I30%', 'I31%', 'I32%', 'I33%', 'I34%', 'I35%', 'I36%', 'I37%', 'I38%',
                'I40%', 'I41%', 'I42%', 'I43%',
                'I44%', 'I45%', 'I46%', 'I47%', 'I48%', 'I49%',
                'I50%', 'I51%', 'I52%',
            ],
            'description' => 'I20-I25 (PJK/Iskemik), I50 (Gagal Jantung), I11 (HHD), I44-I49 (Aritmia), I05-I09, I30-I43',
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
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis')
            ->leftJoin('pasien_tni as pt', 'ps.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'ps.no_rkm_medis', '=', 'pp.no_rkm_medis');

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

        // Filter Pasien Dinas (TNI / POLRI murni dari relasi pasien_tni dan pasien_polri)
        if (!empty($filters['dinas_filter']) && !in_array($filters['dinas_filter'], ['all', 'semua'])) {
            if ($filters['dinas_filter'] === 'DINAS') {
                $query->where(function ($q) {
                    $q->whereNotNull('pt.no_rkm_medis')
                      ->orWhereNotNull('pp.no_rkm_medis');
                });
            } elseif ($filters['dinas_filter'] === 'TNI') {
                $query->whereNotNull('pt.no_rkm_medis');
            } elseif ($filters['dinas_filter'] === 'POLRI') {
                $query->whereNotNull('pp.no_rkm_medis');
            } elseif ($filters['dinas_filter'] === 'NON_DINAS' || $filters['dinas_filter'] === 'UMUM') {
                $query->whereNull('pt.no_rkm_medis')
                      ->whereNull('pp.no_rkm_medis');
            }
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
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap,
            SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL OR pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as total_dinas,
            SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as total_tni,
            SUM(CASE WHEN pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as total_polri
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
            'total_dinas' => (int) ($stats->total_dinas ?? 0),
            'total_tni' => (int) ($stats->total_tni ?? 0),
            'total_polri' => (int) ($stats->total_polri ?? 0),
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
                \Illuminate\Support\Facades\DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas"),
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
                'no_rkm_medis_masked' => self::maskMedicalRecord($r->no_rkm_medis),
                'nama' => $r->nm_pasien,
                'nama_masked' => self::maskPatientName($r->nm_pasien),
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
                'status_dinas' => $r->status_dinas ?? 'UMUM',
            ];
        }

        return $items;
    }

    /**
     * Mengambil rekapitulasi data penyakit per bulan (Januari s.d. Desember) untuk periode tahunan
     */
    public static function getMonthlyBreakdown(array $filters): array
    {
        $monthlyFilters = $filters;
        $monthlyFilters['month'] = null;
        unset($monthlyFilters['start_date'], $monthlyFilters['end_date']);

        $query = self::buildBaseQuery($monthlyFilters);

        $rows = $query->selectRaw("
            MONTH(rp.tgl_registrasi) as bulan,
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
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap,
            SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL OR pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as total_dinas,
            SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as total_tni,
            SUM(CASE WHEN pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as total_polri,
            SUM(CASE WHEN pt.no_rkm_medis IS NULL AND pp.no_rkm_medis IS NULL THEN 1 ELSE 0 END) as total_umum
        ")
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $months = [];
        $totals = [
            'total_kasus' => 0,
            'kasus_baru' => 0,
            'kasus_lama' => 0,
            'primer' => 0,
            'sekunder' => 0,
            'pria' => 0,
            'wanita' => 0,
            'poli' => 0,
            'igd' => 0,
            'ralan' => 0,
            'ranap' => 0,
            'total_dinas' => 0,
            'total_tni' => 0,
            'total_polri' => 0,
            'total_umum' => 0,
        ];

        $yearTotal = $rows->sum('total_kasus') ?: 1;
        $prevTotal = null;
        $prevBaru = null;
        $prevLama = null;
        $prevPoli = null;
        $prevIgd = null;
        $prevRanap = null;

        for ($m = 1; $m <= 12; $m++) {
            $row = $rows->get($m);
            $totalKasus = (int) ($row->total_kasus ?? 0);
            $kasusBaru = (int) ($row->kasus_baru ?? 0);
            $kasusLama = (int) ($row->kasus_lama ?? 0);
            $primer = (int) ($row->primer ?? 0);
            $sekunder = (int) ($row->sekunder ?? 0);
            $pria = (int) ($row->pria ?? 0);
            $wanita = (int) ($row->wanita ?? 0);
            $poli = (int) ($row->poli ?? 0);
            $igd = (int) ($row->igd ?? 0);
            $ralan = (int) ($row->ralan ?? 0);
            $ranap = (int) ($row->ranap ?? 0);
            $totalDinas = (int) ($row->total_dinas ?? 0);
            $totalTni = (int) ($row->total_tni ?? 0);
            $totalPolri = (int) ($row->total_polri ?? 0);
            $totalUmum = (int) ($row->total_umum ?? 0);
            $penyakitUnik = (int) ($row->total_penyakit_unik ?? 0);
            $pasienUnik = (int) ($row->total_pasien_unik ?? 0);

            // Pertumbuhan MoM (Month-over-Month %) terhadap bulan sebelumnya
            $growthTotal = ($prevTotal !== null && $prevTotal > 0)
                ? round((($totalKasus - $prevTotal) / $prevTotal) * 100, 1)
                : null;
            $growthBaru = ($prevBaru !== null && $prevBaru > 0)
                ? round((($kasusBaru - $prevBaru) / $prevBaru) * 100, 1)
                : null;
            $growthLama = ($prevLama !== null && $prevLama > 0)
                ? round((($kasusLama - $prevLama) / $prevLama) * 100, 1)
                : null;
            $growthPoli = ($prevPoli !== null && $prevPoli > 0)
                ? round((($poli - $prevPoli) / $prevPoli) * 100, 1)
                : null;
            $growthIgd = ($prevIgd !== null && $prevIgd > 0)
                ? round((($igd - $prevIgd) / $prevIgd) * 100, 1)
                : null;
            $growthRanap = ($prevRanap !== null && $prevRanap > 0)
                ? round((($ranap - $prevRanap) / $prevRanap) * 100, 1)
                : null;

            $months[$m] = [
                'bulan' => $m,
                'nama_bulan' => $monthNames[$m],
                'short_bulan' => substr($monthNames[$m], 0, 3),
                'total_kasus' => $totalKasus,
                'total_penyakit_unik' => $penyakitUnik,
                'total_pasien_unik' => $pasienUnik,
                'kasus_baru' => $kasusBaru,
                'kasus_lama' => $kasusLama,
                'primer' => $primer,
                'sekunder' => $sekunder,
                'pria' => $pria,
                'wanita' => $wanita,
                'poli' => $poli,
                'igd' => $igd,
                'ralan' => $ralan,
                'ranap' => $ranap,
                'total_dinas' => $totalDinas,
                'total_tni' => $totalTni,
                'total_polri' => $totalPolri,
                'total_umum' => $totalUmum,
                'growth_total' => $growthTotal,
                'growth_baru' => $growthBaru,
                'growth_lama' => $growthLama,
                'growth_poli' => $growthPoli,
                'growth_igd' => $growthIgd,
                'growth_ranap' => $growthRanap,
                'persentase' => round(($totalKasus / $yearTotal) * 100, 1),
            ];

            // Update penanda bulan sebelumnya jika ada data/periode berjalan
            if ($totalKasus > 0 || $m <= (int) date('m')) {
                $prevTotal = $totalKasus;
                $prevBaru = $kasusBaru;
                $prevLama = $kasusLama;
                $prevPoli = $poli;
                $prevIgd = $igd;
                $prevRanap = $ranap;
            }

            $totals['total_kasus'] += $totalKasus;
            $totals['kasus_baru'] += $kasusBaru;
            $totals['kasus_lama'] += $kasusLama;
            $totals['primer'] += $primer;
            $totals['sekunder'] += $sekunder;
            $totals['pria'] += $pria;
            $totals['wanita'] += $wanita;
            $totals['poli'] += $poli;
            $totals['igd'] += $igd;
            $totals['ralan'] += $ralan;
            $totals['ranap'] += $ranap;
            $totals['total_dinas'] += $totalDinas;
            $totals['total_tni'] += $totalTni;
            $totals['total_polri'] += $totalPolri;
            $totals['total_umum'] += $totalUmum;
        }

        // Ambil data unik tahunan sebenarnya dari getSummary
        $yearSummary = self::getSummary($monthlyFilters);
        $totals['total_penyakit_unik'] = $yearSummary['total_penyakit_unik'];
        $totals['total_pasien_unik'] = $yearSummary['total_pasien_unik'];
        $totals['avg_kasus_per_bulan'] = round($totals['total_kasus'] / 12, 1);

        return [
            'year' => (int) ($filters['year'] ?? date('Y')),
            'months' => $months,
            'totals' => $totals,
        ];
    }

    /**
     * Sensor / masking No. Rekam Medis (3 digit belakang diganti bintang)
     * Contoh: 054123 -> 054***
     */
    public static function maskMedicalRecord(?string $rm): string
    {
        if (!$rm) {
            return '-';
        }
        $len = strlen($rm);
        if ($len <= 3) {
            return str_repeat('*', $len);
        }
        return substr($rm, 0, $len - 3) . '***';
    }

    /**
     * Sensor / masking Nama Pasien (bagian tengah tiap kata diganti bintang, awal dan akhir tetap tampak)
     * Contoh: YOVIANSYAH RIZKI -> Y********H R***I
     */
    public static function maskPatientName(?string $name): string
    {
        if (!$name) {
            return '-';
        }
        return preg_replace('/(?<=[A-Za-z])[A-Za-z](?=[A-Za-z])/', '*', $name);
    }

    /**
     * Mengambil daftar data kunjungan pasien (dengan pagination) sesuai filter aktif
     */
    public static function getVisitsPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = self::buildBaseQuery($filters);

        $paginator = $query->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
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
                'dp.kd_penyakit',
                'p.nm_penyakit',
                'dp.prioritas',
                'dp.status_penyakit',
                'd.nm_dokter',
                'pl.nm_poli',
                'pj.png_jawab as penjamin',
                DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas"),
            ])
            ->orderByDesc('rp.tgl_registrasi')
            ->paginate($perPage, ['*'], 'visits_page');

        $paginator->getCollection()->transform(function ($r) {
            $age = $r->tgl_lahir ? Carbon::parse($r->tgl_lahir)->age : '-';
            $isIgd = ($r->kd_poli === 'IGDK');
            $layananLabel = $r->status_lanjut === 'Ranap' ? 'Ranap' : ($isIgd ? 'IGD' : 'Poli');
            $layananDetail = $r->status_lanjut === 'Ranap' ? 'Rawat Inap' : ($isIgd ? 'Gawat Darurat (IGD)' : 'Poliklinik Rawat Jalan');

            return [
                'no_rawat' => $r->no_rawat,
                'no_rkm_medis' => $r->no_rkm_medis,
                'no_rkm_medis_masked' => self::maskMedicalRecord($r->no_rkm_medis),
                'nama' => $r->nm_pasien,
                'nama_masked' => self::maskPatientName($r->nm_pasien),
                'jk' => $r->jk,
                'umur' => $age,
                'tanggal' => Carbon::parse($r->tgl_registrasi)->format('d/m/Y'),
                'tgl_raw' => $r->tgl_registrasi,
                'kd_penyakit' => $r->kd_penyakit,
                'nm_penyakit' => $r->nm_penyakit,
                'diagnosa' => $r->kd_penyakit . ' - ' . $r->nm_penyakit,
                'layanan' => $layananLabel,
                'layanan_detail' => $layananDetail,
                'prioritas' => $r->prioritas == 1 ? 'Primer' : 'Sekunder',
                'kasus' => $r->status_penyakit,
                'dokter' => $r->nm_dokter ?? '-',
                'unit' => $r->nm_poli ?? '-',
                'penjamin' => $r->penjamin ?? '-',
                'status_dinas' => $r->status_dinas ?? 'UMUM',
            ];
        });

        return $paginator;
    }

    /**
     * Mengambil daftar data pengunjung unik pasien (dengan pagination) sesuai filter aktif
     */
    public static function getVisitorsPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = self::buildBaseQuery($filters);

        $paginator = $query->select([
                'ps.no_rkm_medis',
                'ps.nm_pasien',
                'ps.jk',
                'ps.tgl_lahir',
                DB::raw("COUNT(DISTINCT rp.no_rawat) as total_kunjungan"),
                DB::raw("MIN(rp.tgl_registrasi) as kunjungan_pertama"),
                DB::raw("MAX(rp.tgl_registrasi) as kunjungan_terakhir"),
                DB::raw("GROUP_CONCAT(DISTINCT dp.kd_penyakit ORDER BY dp.kd_penyakit SEPARATOR ', ') as riwayat_diagnosa"),
                DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas"),
            ])
            ->groupBy('ps.no_rkm_medis', 'ps.nm_pasien', 'ps.jk', 'ps.tgl_lahir', 'pt.no_rkm_medis', 'pp.no_rkm_medis')
            ->orderByDesc(DB::raw('MAX(rp.tgl_registrasi)'))
            ->paginate($perPage, ['*'], 'visitors_page');

        $paginator->getCollection()->transform(function ($r) {
            $age = $r->tgl_lahir ? Carbon::parse($r->tgl_lahir)->age : '-';

            return [
                'no_rkm_medis' => $r->no_rkm_medis,
                'no_rkm_medis_masked' => self::maskMedicalRecord($r->no_rkm_medis),
                'nama' => $r->nm_pasien,
                'nama_masked' => self::maskPatientName($r->nm_pasien),
                'jk' => $r->jk,
                'umur' => $age,
                'total_kunjungan' => (int) $r->total_kunjungan,
                'kunjungan_pertama' => $r->kunjungan_pertama ? Carbon::parse($r->kunjungan_pertama)->format('d/m/Y') : '-',
                'kunjungan_terakhir' => $r->kunjungan_terakhir ? Carbon::parse($r->kunjungan_terakhir)->format('d/m/Y') : '-',
                'riwayat_diagnosa' => $r->riwayat_diagnosa ?? '-',
                'status_dinas' => $r->status_dinas ?? 'UMUM',
            ];
        });

        return $paginator;
    }

    /**
     * Mengambil seluruh data kunjungan untuk keperluan ekspor PDF
     */
    public static function getAllVisitsForExport(array $filters, int $limit = 500): array
    {
        $query = self::buildBaseQuery($filters);

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
                'dp.kd_penyakit',
                'p.nm_penyakit',
                'dp.prioritas',
                'dp.status_penyakit',
                'd.nm_dokter',
                'pl.nm_poli',
                'pj.png_jawab as penjamin',
                DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas"),
            ])
            ->orderByDesc('rp.tgl_registrasi')
            ->limit($limit)
            ->get();

        $items = [];
        foreach ($rows as $r) {
            $age = $r->tgl_lahir ? Carbon::parse($r->tgl_lahir)->age : '-';
            $isIgd = ($r->kd_poli === 'IGDK');
            $layananLabel = $r->status_lanjut === 'Ranap' ? 'Ranap' : ($isIgd ? 'IGD' : 'Poli');

            $items[] = [
                'no_rawat' => $r->no_rawat,
                'no_rkm_medis' => $r->no_rkm_medis,
                'no_rkm_medis_masked' => self::maskMedicalRecord($r->no_rkm_medis),
                'nama' => $r->nm_pasien,
                'nama_masked' => self::maskPatientName($r->nm_pasien),
                'jk' => $r->jk,
                'umur' => $age,
                'tanggal' => Carbon::parse($r->tgl_registrasi)->format('d/m/Y'),
                'kd_penyakit' => $r->kd_penyakit,
                'nm_penyakit' => $r->nm_penyakit,
                'diagnosa' => $r->kd_penyakit . ' - ' . $r->nm_penyakit,
                'layanan' => $layananLabel,
                'prioritas' => $r->prioritas == 1 ? 'Primer' : 'Sekunder',
                'kasus' => $r->status_penyakit,
                'dokter' => $r->nm_dokter ?? '-',
                'unit' => $r->nm_poli ?? '-',
                'penjamin' => $r->penjamin ?? '-',
                'status_dinas' => $r->status_dinas ?? 'UMUM',
            ];
        }

        return $items;
    }

    /**
     * Mengambil seluruh data pengunjung unik untuk keperluan ekspor PDF
     */
    public static function getAllVisitorsForExport(array $filters, int $limit = 500): array
    {
        $query = self::buildBaseQuery($filters);

        $rows = $query->select([
                'ps.no_rkm_medis',
                'ps.nm_pasien',
                'ps.jk',
                'ps.tgl_lahir',
                DB::raw("COUNT(DISTINCT rp.no_rawat) as total_kunjungan"),
                DB::raw("MIN(rp.tgl_registrasi) as kunjungan_pertama"),
                DB::raw("MAX(rp.tgl_registrasi) as kunjungan_terakhir"),
                DB::raw("GROUP_CONCAT(DISTINCT dp.kd_penyakit ORDER BY dp.kd_penyakit SEPARATOR ', ') as riwayat_diagnosa"),
                DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas"),
            ])
            ->groupBy('ps.no_rkm_medis', 'ps.nm_pasien', 'ps.jk', 'ps.tgl_lahir', 'pt.no_rkm_medis', 'pp.no_rkm_medis')
            ->orderByDesc(DB::raw('MAX(rp.tgl_registrasi)'))
            ->limit($limit)
            ->get();

        $items = [];
        foreach ($rows as $r) {
            $age = $r->tgl_lahir ? Carbon::parse($r->tgl_lahir)->age : '-';

            $items[] = [
                'no_rkm_medis' => $r->no_rkm_medis,
                'no_rkm_medis_masked' => self::maskMedicalRecord($r->no_rkm_medis),
                'nama' => $r->nm_pasien,
                'nama_masked' => self::maskPatientName($r->nm_pasien),
                'jk' => $r->jk,
                'umur' => $age,
                'total_kunjungan' => (int) $r->total_kunjungan,
                'kunjungan_pertama' => $r->kunjungan_pertama ? Carbon::parse($r->kunjungan_pertama)->format('d/m/Y') : '-',
                'kunjungan_terakhir' => $r->kunjungan_terakhir ? Carbon::parse($r->kunjungan_terakhir)->format('d/m/Y') : '-',
                'riwayat_diagnosa' => $r->riwayat_diagnosa ?? '-',
                'status_dinas' => $r->status_dinas ?? 'UMUM',
            ];
        }

        return $items;
    }

    /**
     * Mengambil Matriks 12 Bulan dan Pertumbuhan MoM per Diagnosa Penyakit (ICD-10)
     */
    public static function getDiseaseMonthlyMatrix(array $filters, int $limit = 50): array
    {
        $matrixFilters = $filters;
        $matrixFilters['month'] = null;
        unset($matrixFilters['start_date'], $matrixFilters['end_date']);

        $query = self::buildBaseQuery($matrixFilters);

        $rows = $query->selectRaw("
            dp.kd_penyakit,
            p.nm_penyakit,
            COUNT(*) as total_kasus,
            SUM(CASE WHEN dp.status_penyakit = 'Baru' THEN 1 ELSE 0 END) as total_baru,
            SUM(CASE WHEN dp.status_penyakit = 'Lama' THEN 1 ELSE 0 END) as total_lama,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 1 THEN 1 ELSE 0 END) as m1,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 2 THEN 1 ELSE 0 END) as m2,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 3 THEN 1 ELSE 0 END) as m3,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 4 THEN 1 ELSE 0 END) as m4,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 5 THEN 1 ELSE 0 END) as m5,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 6 THEN 1 ELSE 0 END) as m6,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 7 THEN 1 ELSE 0 END) as m7,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 8 THEN 1 ELSE 0 END) as m8,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 9 THEN 1 ELSE 0 END) as m9,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 10 THEN 1 ELSE 0 END) as m10,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 11 THEN 1 ELSE 0 END) as m11,
            SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 12 THEN 1 ELSE 0 END) as m12
        ")
        ->groupBy('dp.kd_penyakit', 'p.nm_penyakit')
        ->orderByDesc('total_kasus')
        ->limit($limit)
        ->get();

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // Tentukan bulan acuan terkini yang memiliki aktivitas data (misal Oktober atau November)
        $currentMonth = (int) date('m');
        $activeYear = (int) ($filters['year'] ?? date('Y'));
        $targetMonth = ($activeYear < (int) date('Y')) ? 12 : $currentMonth;

        $items = [];
        foreach ($rows as $r) {
            $monthlyValues = [];
            for ($m = 1; $m <= 12; $m++) {
                $prop = "m{$m}";
                $monthlyValues[$m] = (int) ($r->$prop ?? 0);
            }

            // Hitung persentase pertumbuhan MoM untuk setiap bulan
            $monthlyGrowth = [];
            for ($m = 1; $m <= 12; $m++) {
                if ($m === 1) {
                    $monthlyGrowth[1] = null;
                } else {
                    $pVal = $monthlyValues[$m - 1];
                    $cVal = $monthlyValues[$m];
                    if ($pVal > 0) {
                        $monthlyGrowth[$m] = round((($cVal - $pVal) / $pVal) * 100, 1);
                    } elseif ($cVal > 0) {
                        $monthlyGrowth[$m] = 100.0;
                    } else {
                        $monthlyGrowth[$m] = null;
                    }
                }
            }

            // Hitung MoM terkini: komparasi targetMonth terhadap targetMonth - 1
            $curVal = $monthlyValues[$targetMonth] ?? 0;
            $prevMonth = $targetMonth > 1 ? $targetMonth - 1 : 12;
            $prevVal = $monthlyValues[$prevMonth] ?? 0;

            // Jika bulan target 0 (misal awal bulan baru), mundur 1 bulan jika ada data
            if ($curVal === 0 && $prevVal > 0 && $targetMonth > 1) {
                $compTarget = $prevMonth;
                $compPrev = $prevMonth > 1 ? $prevMonth - 1 : 12;
                $curVal = $monthlyValues[$compTarget] ?? 0;
                $prevVal = $monthlyValues[$compPrev] ?? 0;
            }

            $growthPct = ($prevVal > 0)
                ? round((($curVal - $prevVal) / $prevVal) * 100, 1)
                : ($curVal > 0 ? 100.0 : 0.0);

            $diffCases = $curVal - $prevVal;

            $items[] = [
                'kd_penyakit' => $r->kd_penyakit,
                'nm_penyakit' => $r->nm_penyakit,
                'total_kasus' => (int) $r->total_kasus,
                'total_baru' => (int) $r->total_baru,
                'total_lama' => (int) $r->total_lama,
                'total_poli' => (int) $r->total_poli,
                'total_igd' => (int) $r->total_igd,
                'total_ranap' => (int) $r->total_ranap,
                'months' => $monthlyValues,
                'monthly_growth' => $monthlyGrowth,
                'latest_val' => $curVal,
                'prev_val' => $prevVal,
                'growth_pct' => $growthPct,
                'diff_cases' => $diffCases,
                'is_surge' => ($growthPct >= 35.0 && $diffCases >= 15),
            ];
        }

        return [
            'month_names' => $monthNames,
            'reference_month' => $targetMonth,
            'items' => $items,
        ];
    }

    /**
     * Mengambil Matriks 12 Bulan dan Pertumbuhan MoM Kasus Penyakit per Poliklinik & IGD
     */
    public static function getPolyclinicMonthlyMatrix(array $filters): array
    {
        $matrixFilters = $filters;
        $matrixFilters['month'] = null;
        unset($matrixFilters['start_date'], $matrixFilters['end_date']);

        $query = self::buildBaseQuery($matrixFilters);

        $rows = $query->join('poliklinik as pl', 'rp.kd_poli', '=', 'pl.kd_poli')
            ->selectRaw("
                rp.kd_poli,
                pl.nm_poli,
                CASE WHEN rp.kd_poli = 'IGDK' THEN 'IGD' ELSE 'Poli' END as jenis_layanan,
                COUNT(*) as total_kasus,
                COUNT(DISTINCT dp.kd_penyakit) as total_penyakit_unik,
                COUNT(DISTINCT rp.no_rkm_medis) as total_pasien_unik,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 1 THEN 1 ELSE 0 END) as m1,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 2 THEN 1 ELSE 0 END) as m2,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 3 THEN 1 ELSE 0 END) as m3,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 4 THEN 1 ELSE 0 END) as m4,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 5 THEN 1 ELSE 0 END) as m5,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 6 THEN 1 ELSE 0 END) as m6,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 7 THEN 1 ELSE 0 END) as m7,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 8 THEN 1 ELSE 0 END) as m8,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 9 THEN 1 ELSE 0 END) as m9,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 10 THEN 1 ELSE 0 END) as m10,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 11 THEN 1 ELSE 0 END) as m11,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 12 THEN 1 ELSE 0 END) as m12
            ")
            ->groupBy('rp.kd_poli', 'pl.nm_poli')
            ->orderByDesc('total_kasus')
            ->get();

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $currentMonth = (int) date('m');
        $activeYear = (int) ($filters['year'] ?? date('Y'));
        $targetMonth = ($activeYear < (int) date('Y')) ? 12 : $currentMonth;

        $items = [];
        foreach ($rows as $r) {
            $monthlyValues = [];
            for ($m = 1; $m <= 12; $m++) {
                $prop = "m{$m}";
                $monthlyValues[$m] = (int) ($r->$prop ?? 0);
            }

            // Hitung persentase pertumbuhan MoM untuk setiap bulan
            $monthlyGrowth = [];
            for ($m = 1; $m <= 12; $m++) {
                if ($m === 1) {
                    $monthlyGrowth[1] = null;
                } else {
                    $pVal = $monthlyValues[$m - 1];
                    $cVal = $monthlyValues[$m];
                    if ($pVal > 0) {
                        $monthlyGrowth[$m] = round((($cVal - $pVal) / $pVal) * 100, 1);
                    } elseif ($cVal > 0) {
                        $monthlyGrowth[$m] = 100.0;
                    } else {
                        $monthlyGrowth[$m] = null;
                    }
                }
            }

            $curVal = $monthlyValues[$targetMonth] ?? 0;
            $prevMonth = $targetMonth > 1 ? $targetMonth - 1 : 12;
            $prevVal = $monthlyValues[$prevMonth] ?? 0;

            if ($curVal === 0 && $prevVal > 0 && $targetMonth > 1) {
                $compTarget = $prevMonth;
                $compPrev = $prevMonth > 1 ? $prevMonth - 1 : 12;
                $curVal = $monthlyValues[$compTarget] ?? 0;
                $prevVal = $monthlyValues[$compPrev] ?? 0;
            }

            $growthPct = ($prevVal > 0)
                ? round((($curVal - $prevVal) / $prevVal) * 100, 1)
                : ($curVal > 0 ? 100.0 : 0.0);

            $items[] = [
                'kd_poli' => $r->kd_poli,
                'nm_poli' => $r->nm_poli,
                'jenis_layanan' => $r->jenis_layanan,
                'total_kasus' => (int) $r->total_kasus,
                'total_penyakit_unik' => (int) $r->total_penyakit_unik,
                'total_pasien_unik' => (int) $r->total_pasien_unik,
                'months' => $monthlyValues,
                'monthly_growth' => $monthlyGrowth,
                'latest_val' => $curVal,
                'prev_val' => $prevVal,
                'growth_pct' => $growthPct,
                'diff_cases' => $curVal - $prevVal,
            ];
        }

        return [
            'month_names' => $monthNames,
            'reference_month' => $targetMonth,
            'items' => $items,
        ];
    }

    /**
     * Mengambil Matriks 12 Bulan dan Pertumbuhan MoM Kasus Penyakit per Dokter DPJP
     */
    public static function getDoctorMonthlyMatrix(array $filters, int $limit = 30): array
    {
        $matrixFilters = $filters;
        $matrixFilters['month'] = null;
        unset($matrixFilters['start_date'], $matrixFilters['end_date']);

        $query = self::buildBaseQuery($matrixFilters);

        $rows = $query->join('dokter as dr', 'rp.kd_dokter', '=', 'dr.kd_dokter')
            ->selectRaw("
                rp.kd_dokter,
                dr.nm_dokter,
                COUNT(*) as total_kasus,
                COUNT(DISTINCT dp.kd_penyakit) as total_penyakit_unik,
                COUNT(DISTINCT rp.no_rkm_medis) as total_pasien_unik,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 1 THEN 1 ELSE 0 END) as m1,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 2 THEN 1 ELSE 0 END) as m2,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 3 THEN 1 ELSE 0 END) as m3,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 4 THEN 1 ELSE 0 END) as m4,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 5 THEN 1 ELSE 0 END) as m5,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 6 THEN 1 ELSE 0 END) as m6,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 7 THEN 1 ELSE 0 END) as m7,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 8 THEN 1 ELSE 0 END) as m8,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 9 THEN 1 ELSE 0 END) as m9,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 10 THEN 1 ELSE 0 END) as m10,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 11 THEN 1 ELSE 0 END) as m11,
                SUM(CASE WHEN MONTH(rp.tgl_registrasi) = 12 THEN 1 ELSE 0 END) as m12
            ")
            ->groupBy('rp.kd_dokter', 'dr.nm_dokter')
            ->orderByDesc('total_kasus')
            ->limit($limit)
            ->get();

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $currentMonth = (int) date('m');
        $activeYear = (int) ($filters['year'] ?? date('Y'));
        $targetMonth = ($activeYear < (int) date('Y')) ? 12 : $currentMonth;

        $items = [];
        foreach ($rows as $r) {
            $monthlyValues = [];
            for ($m = 1; $m <= 12; $m++) {
                $prop = "m{$m}";
                $monthlyValues[$m] = (int) ($r->$prop ?? 0);
            }

            // Hitung persentase pertumbuhan MoM untuk setiap bulan
            $monthlyGrowth = [];
            for ($m = 1; $m <= 12; $m++) {
                if ($m === 1) {
                    $monthlyGrowth[1] = null;
                } else {
                    $pVal = $monthlyValues[$m - 1];
                    $cVal = $monthlyValues[$m];
                    if ($pVal > 0) {
                        $monthlyGrowth[$m] = round((($cVal - $pVal) / $pVal) * 100, 1);
                    } elseif ($cVal > 0) {
                        $monthlyGrowth[$m] = 100.0;
                    } else {
                        $monthlyGrowth[$m] = null;
                    }
                }
            }

            $curVal = $monthlyValues[$targetMonth] ?? 0;
            $prevMonth = $targetMonth > 1 ? $targetMonth - 1 : 12;
            $prevVal = $monthlyValues[$prevMonth] ?? 0;

            if ($curVal === 0 && $prevVal > 0 && $targetMonth > 1) {
                $compTarget = $prevMonth;
                $compPrev = $prevMonth > 1 ? $prevMonth - 1 : 12;
                $curVal = $monthlyValues[$compTarget] ?? 0;
                $prevVal = $monthlyValues[$compPrev] ?? 0;
            }

            $growthPct = ($prevVal > 0)
                ? round((($curVal - $prevVal) / $prevVal) * 100, 1)
                : ($curVal > 0 ? 100.0 : 0.0);

            $items[] = [
                'kd_dokter' => $r->kd_dokter,
                'nm_dokter' => $r->nm_dokter,
                'total_kasus' => (int) $r->total_kasus,
                'total_penyakit_unik' => (int) $r->total_penyakit_unik,
                'total_pasien_unik' => (int) $r->total_pasien_unik,
                'months' => $monthlyValues,
                'monthly_growth' => $monthlyGrowth,
                'latest_val' => $curVal,
                'prev_val' => $prevVal,
                'growth_pct' => $growthPct,
                'diff_cases' => $curVal - $prevVal,
            ];
        }

        return [
            'month_names' => $monthNames,
            'reference_month' => $targetMonth,
            'items' => $items,
        ];
    }

    /**
     * Mengambil Ringkasan KPI Pertumbuhan MoM Rumah Sakit & Deteksi Lonjakan Kasus
     */
    public static function getMomSummaryKpi(array $filters): array
    {
        $mb = self::getMonthlyBreakdown($filters);
        $months = $mb['months'];

        $currentMonth = (int) date('m');
        $activeYear = (int) ($filters['year'] ?? date('Y'));
        $targetMonth = ($activeYear < (int) date('Y')) ? 12 : $currentMonth;

        // Cari bulan terisi terakhir yang valid
        $curData = $months[$targetMonth] ?? null;
        $prevMonth = $targetMonth > 1 ? $targetMonth - 1 : 12;
        $prevData = $months[$prevMonth] ?? null;

        if ($curData && $curData['total_kasus'] === 0 && $targetMonth > 1 && ($prevData['total_kasus'] ?? 0) > 0) {
            $targetMonth = $prevMonth;
            $prevMonth = $targetMonth > 1 ? $targetMonth - 1 : 12;
            $curData = $months[$targetMonth];
            $prevData = $months[$prevMonth] ?? null;
        }

        $curTotal = $curData['total_kasus'] ?? 0;
        $prevTotal = $prevData['total_kasus'] ?? 0;
        $growthTotal = ($prevTotal > 0) ? round((($curTotal - $prevTotal) / $prevTotal) * 100, 1) : 0.0;

        $curBaru = $curData['kasus_baru'] ?? 0;
        $prevBaru = $prevData['kasus_baru'] ?? 0;
        $growthBaru = ($prevBaru > 0) ? round((($curBaru - $prevBaru) / $prevBaru) * 100, 1) : 0.0;

        $curPoli = $curData['poli'] ?? 0;
        $prevPoli = $prevData['poli'] ?? 0;
        $growthPoli = ($prevPoli > 0) ? round((($curPoli - $prevPoli) / $prevPoli) * 100, 1) : 0.0;

        $curIgd = $curData['igd'] ?? 0;
        $prevIgd = $prevData['igd'] ?? 0;
        $growthIgd = ($prevIgd > 0) ? round((($curIgd - $prevIgd) / $prevIgd) * 100, 1) : 0.0;

        $curRanap = $curData['ranap'] ?? 0;
        $prevRanap = $prevData['ranap'] ?? 0;
        $growthRanap = ($prevRanap > 0) ? round((($curRanap - $prevRanap) / $prevRanap) * 100, 1) : 0.0;

        // Ambil Top 5 Diagnosa dengan lonjakan kasus tertinggi bulan ini
        $diseaseMatrix = self::getDiseaseMonthlyMatrix($filters, 100);
        $spikes = collect($diseaseMatrix['items'])
            ->filter(fn($it) => $it['diff_cases'] > 0)
            ->sortByDesc('diff_cases')
            ->take(5)
            ->values()
            ->all();

        return [
            'reference_month' => $targetMonth,
            'current_month_name' => $curData['nama_bulan'] ?? '-',
            'prev_month_name' => $prevData['nama_bulan'] ?? '-',
            'current_total' => $curTotal,
            'prev_total' => $prevTotal,
            'diff_total' => $curTotal - $prevTotal,
            'growth_total' => $growthTotal,
            'current_baru' => $curBaru,
            'prev_baru' => $prevBaru,
            'growth_baru' => $growthBaru,
            'current_poli' => $curPoli,
            'prev_poli' => $prevPoli,
            'growth_poli' => $growthPoli,
            'current_igd' => $curIgd,
            'prev_igd' => $prevIgd,
            'growth_igd' => $growthIgd,
            'current_ranap' => $curRanap,
            'prev_ranap' => $prevRanap,
            'growth_ranap' => $growthRanap,
            'top_spikes' => $spikes,
        ];
    }
}
