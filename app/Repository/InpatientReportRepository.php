<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\SirsHelper;

interface InpatientReportInterface {}

class InpatientReportRepository implements InpatientReportInterface
{
    const CONNECTION = 'simrs';
    const LIMIT_DEFAULT = 25;

    /**
     * Membangun base query laporan pasien rawat inap sesuai referensi query SIMRS.
     *
     * @param string|null $startDate Tanggal awal masuk (Y-m-d)
     * @param string|null $endDate Tanggal akhir masuk (Y-m-d)
     * @param string|null $payType Kode penanggung jawab / cara bayar (kd_pj)
     * @param string|null $statusPulang Status keluar ('semua', 'sudah_pulang', 'masih_dirawat')
     * @param string|null $ward Kode bangsal (kd_bangsal)
     * @param string|null $search Kata kunci pencarian (No Rawat, No RM, atau Nama Pasien)
     * @return \Illuminate\Database\Query\Builder
     */
    private static function buildQuery(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null,
        ?string $dinasFilter = null
    ) {
        $query = DB::connection(self::CONNECTION)
            ->table('reg_periksa as rp')
            ->join('kamar_inap as ki', 'rp.no_rawat', '=', 'ki.no_rawat')
            ->join('kamar as k', 'k.kd_kamar', '=', 'ki.kd_kamar')
            ->join('bangsal as b', 'b.kd_bangsal', '=', 'k.kd_bangsal')
            ->join('pasien as p', 'p.no_rkm_medis', '=', 'rp.no_rkm_medis')
            ->leftJoin('penjab as pj', 'pj.kd_pj', '=', 'rp.kd_pj')
            ->leftJoin('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'rp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where('rp.status_lanjut', 'Ranap');

        // Filter Penjab (Penanggung Jawab / Cara Bayar murni dari tabel penjab)
        if (!empty($payType) && $payType !== 'semua') {
            if ($payType === 'BPJS') {
                $query->where('pj.png_jawab', 'like', '%BPJS%');
            } elseif ($payType === 'UMUM') {
                $query->where('pj.png_jawab', 'like', '%UMUM%');
            } else {
                $query->where('rp.kd_pj', $payType);
            }
        }

        // Filter Jenis Pasien (Status Kedinasan TNI, POLRI, atau Sipil / Non-Dinas)
        if (!empty($dinasFilter) && $dinasFilter !== 'semua') {
            if ($dinasFilter === 'DINAS') {
                $query->where(function ($q) {
                    $q->whereNotNull('pt.no_rkm_medis')
                      ->orWhereNotNull('pp.no_rkm_medis');
                });
            } elseif ($dinasFilter === 'TNI') {
                $query->whereNotNull('pt.no_rkm_medis');
            } elseif ($dinasFilter === 'POLRI') {
                $query->whereNotNull('pp.no_rkm_medis');
            } elseif ($dinasFilter === 'NON_DINAS') {
                $query->whereNull('pt.no_rkm_medis')
                      ->whereNull('pp.no_rkm_medis');
            }
        }

        // Filter Rentang Tanggal Masuk
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('ki.tgl_masuk', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('ki.tgl_masuk', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('ki.tgl_masuk', '<=', $endDate);
        }

        // Filter Status Pulang / Keluar
        if ($statusPulang === 'sudah_pulang') {
            $query->where('ki.tgl_keluar', '<>', '0000-00-00')
                  ->whereNotNull('ki.tgl_keluar');
        } elseif ($statusPulang === 'masih_dirawat') {
            $query->where(function ($q) {
                $q->where('ki.tgl_keluar', '=', '0000-00-00')
                  ->orWhereNull('ki.tgl_keluar');
            });
        }

        // Filter Bangsal
        if (!empty($ward) && $ward !== 'semua') {
            $query->where('b.kd_bangsal', $ward);
        }

        // Filter Pencarian (No Rawat, No RM, atau Nama Pasien)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('rp.no_rawat', 'like', "%{$search}%")
                  ->orWhere('rp.no_rkm_medis', 'like', "%{$search}%")
                  ->orWhere('p.nm_pasien', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Mengambil data laporan pasien rawat inap beserta DPJP dan penanggung jawab.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $payType
     * @param string|null $statusPulang
     * @param string|null $ward
     * @param string|null $search
     * @param int $limit Jika 0, mengembalikan seluruh baris
     * @return LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public static function getPatients(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null,
        int $limit = self::LIMIT_DEFAULT,
        ?string $dinasFilter = null
    ) {
        $query = self::buildQuery($startDate, $endDate, $payType, $statusPulang, $ward, $search, $dinasFilter)
            ->groupBy('ki.no_rawat')
            ->select([
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'b.nm_bangsal',
                'ki.tgl_masuk',
                'ki.tgl_keluar',
                'pj.png_jawab',
                DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM / SIPIL' END as status_dinas"),
                DB::raw("(
                    SELECT GROUP_CONCAT(DISTINCT d.nm_dokter ORDER BY d.nm_dokter SEPARATOR ', ')
                    FROM dpjp_ranap dpjp
                    JOIN dokter d ON d.kd_dokter = dpjp.kd_dokter
                    WHERE dpjp.no_rawat = rp.no_rawat
                ) AS dpjp_ranap")
            ])
            ->orderByDesc('ki.tgl_masuk')
            ->orderByDesc('rp.no_rawat');

        if ($limit > 0) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * Menghitung ringkasan metrik untuk header laporan.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $payType
     * @param string|null $statusPulang
     * @param string|null $ward
     * @param string|null $search
     * @param string|null $dinasFilter
     * @return array
     */
    public static function getSummary(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null,
        ?string $dinasFilter = null
    ): array {
        // Query keseluruhan tanpa filter status_pulang untuk menghitung komposisi pulang vs dirawat
        $baseQuery = self::buildQuery($startDate, $endDate, $payType, 'semua', $ward, $search, $dinasFilter);

        $stats = $baseQuery
            ->selectRaw("
                COUNT(DISTINCT ki.no_rawat) AS total_pasien,
                COUNT(DISTINCT CASE WHEN ki.tgl_keluar <> '0000-00-00' AND ki.tgl_keluar IS NOT NULL THEN ki.no_rawat END) AS sudah_pulang,
                COUNT(DISTINCT CASE WHEN ki.tgl_keluar = '0000-00-00' OR ki.tgl_keluar IS NULL THEN ki.no_rawat END) AS masih_dirawat,
                COUNT(DISTINCT CASE WHEN pt.no_rkm_medis IS NOT NULL OR pp.no_rkm_medis IS NOT NULL THEN ki.no_rawat END) AS total_dinas,
                COUNT(DISTINCT CASE WHEN pt.no_rkm_medis IS NOT NULL THEN ki.no_rawat END) AS total_tni,
                COUNT(DISTINCT CASE WHEN pp.no_rkm_medis IS NOT NULL THEN ki.no_rawat END) AS total_polri
            ")
            ->first();

        $totalPasien = (int) ($stats->total_pasien ?? 0);
        $totalDinas = (int) ($stats->total_dinas ?? 0);
        $totalSipil = max(0, $totalPasien - $totalDinas);

        return [
            'total_pasien' => $totalPasien,
            'sudah_pulang' => (int) ($stats->sudah_pulang ?? 0),
            'masih_dirawat' => (int) ($stats->masih_dirawat ?? 0),
            'total_dinas' => $totalDinas,
            'total_tni' => (int) ($stats->total_tni ?? 0),
            'total_polri' => (int) ($stats->total_polri ?? 0),
            'total_sipil' => $totalSipil,
        ];
    }

    /**
     * Mengambil data tren masuk pasien rawat inap per tanggal.
     */
    public static function getTrend(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, $payType, $statusPulang, $ward, $search)
            ->selectRaw("
                ki.tgl_masuk as tgl,
                count(distinct ki.no_rawat) as total,
                count(distinct case when p.jk = 'L' then ki.no_rawat end) as pria,
                count(distinct case when p.jk = 'P' then ki.no_rawat end) as wanita
            ")
            ->groupBy('ki.tgl_masuk')
            ->orderBy('ki.tgl_masuk')
            ->get();

        return [
            'labels' => $rows->map(fn($r) => \Carbon\Carbon::parse($r->tgl)->format('d/m'))->toArray(),
            'total' => $rows->map(fn($r) => (int) $r->total)->toArray(),
            'pria' => $rows->map(fn($r) => (int) $r->pria)->toArray(),
            'wanita' => $rows->map(fn($r) => (int) $r->wanita)->toArray(),
        ];
    }

    /**
     * Mengambil data sebaran pasien per bangsal / ruangan rawat inap.
     */
    public static function getWardBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $search = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, $payType, $statusPulang, null, $search)
            ->selectRaw("
                b.kd_bangsal,
                b.nm_bangsal,
                count(distinct ki.no_rawat) as total,
                count(distinct case when pt.no_rkm_medis is not null then ki.no_rawat end) as tni,
                count(distinct case when pp.no_rkm_medis is not null then ki.no_rawat end) as polri,
                count(distinct case when p.jk = 'L' then ki.no_rawat end) as pria,
                count(distinct case when p.jk = 'P' then ki.no_rawat end) as wanita,
                count(distinct case when ki.tgl_keluar = '0000-00-00' or ki.tgl_keluar is null then ki.no_rawat end) as masih_dirawat,
                count(distinct case when ki.tgl_keluar <> '0000-00-00' and ki.tgl_keluar is not null then ki.no_rawat end) as sudah_pulang
            ")
            ->groupBy('b.kd_bangsal', 'b.nm_bangsal')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $rows->sum('total') ?: 1;

        return $rows->map(function ($r) use ($grandTotal) {
            return [
                'kd_bangsal' => $r->kd_bangsal,
                'nm_bangsal' => $r->nm_bangsal,
                'total' => (int) $r->total,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'masih_dirawat' => (int) $r->masih_dirawat,
                'sudah_pulang' => (int) $r->sudah_pulang,
                'percent' => round(($r->total / $grandTotal) * 100, 1),
            ];
        })->toArray();
    }

    /**
     * Mengambil data proporsi cara bayar / penjamin pasien rawat inap.
     */
    public static function getPayTypeBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, null, $statusPulang, $ward, $search)
            ->selectRaw("
                COALESCE(pj.kd_pj, '-') as kd_pj,
                COALESCE(pj.png_jawab, 'Tidak Diketahui') as png_jawab,
                count(distinct ki.no_rawat) as total
            ")
            ->groupBy('pj.kd_pj', 'pj.png_jawab')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $rows->sum('total') ?: 1;

        return $rows->map(function ($r) use ($grandTotal) {
            return [
                'kd_pj' => $r->kd_pj,
                'png_jawab' => $r->png_jawab,
                'total' => (int) $r->total,
                'percent' => round(($r->total / $grandTotal) * 100, 1),
            ];
        })->toArray();
    }

    /**
     * Mengambil data kelompok umur standar SIRS Kemkes untuk pasien rawat inap.
     */
    public static function getAgeGroupBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ): array {
        $categories = SirsHelper::getAgeGroupCategories();
        $caseSql = SirsHelper::ageGroupCategoryCaseSql('p.tgl_lahir', 'ki.tgl_masuk');

        $rows = self::buildQuery($startDate, $endDate, $payType, $statusPulang, $ward, $search)
            ->selectRaw("
                {$caseSql} as kode_kelompok,
                count(distinct ki.no_rawat) as total,
                count(distinct case when p.jk = 'L' then ki.no_rawat end) as pria,
                count(distinct case when p.jk = 'P' then ki.no_rawat end) as wanita
            ")
            ->groupBy('kode_kelompok')
            ->get()
            ->keyBy('kode_kelompok');

        $grandTotal = $rows->sum('total') ?: 1;
        $items = [];

        foreach ($categories as $kode => $info) {
            $row = $rows->get($kode);
            $total = $row ? (int) $row->total : 0;
            $pria = $row ? (int) $row->pria : 0;
            $wanita = $row ? (int) $row->wanita : 0;

            $items[] = [
                'kode' => $kode,
                'nama' => $info['nama'],
                'total' => $total,
                'pria' => $pria,
                'wanita' => $wanita,
                'percent' => round(($total / $grandTotal) * 100, 1),
            ];
        }

        return $items;
    }

    /**
     * Mengambil rekapitulasi data pasien dinas rawat inap (per Bangsal, Kategori Personel, dan Satuan).
     */
    public static function getDinasBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ): array {
        $baseQuery = self::buildQuery($startDate, $endDate, null, $statusPulang, $ward, $search, 'DINAS');

        // Ringkasan
        $summaryRow = (clone $baseQuery)
            ->selectRaw("
                count(distinct ki.no_rawat) as total,
                count(distinct case when pt.no_rkm_medis is not null then ki.no_rawat end) as tni,
                count(distinct case when pp.no_rkm_medis is not null then ki.no_rawat end) as polri,
                count(distinct case when ki.tgl_keluar = '0000-00-00' or ki.tgl_keluar is null then ki.no_rawat end) as masih_dirawat,
                count(distinct case when ki.tgl_keluar <> '0000-00-00' and ki.tgl_keluar is not null then ki.no_rawat end) as sudah_pulang
            ")
            ->first();

        $totalDinas = $summaryRow ? (int) $summaryRow->total : 0;

        // Sebaran per Bangsal
        $wardRows = (clone $baseQuery)
            ->selectRaw("
                b.kd_bangsal,
                b.nm_bangsal,
                count(distinct ki.no_rawat) as total,
                count(distinct case when pt.no_rkm_medis is not null then ki.no_rawat end) as tni,
                count(distinct case when pp.no_rkm_medis is not null then ki.no_rawat end) as polri,
                count(distinct case when ki.tgl_keluar = '0000-00-00' or ki.tgl_keluar is null then ki.no_rawat end) as masih_dirawat,
                count(distinct case when ki.tgl_keluar <> '0000-00-00' and ki.tgl_keluar is not null then ki.no_rawat end) as sudah_pulang
            ")
            ->groupBy('b.kd_bangsal', 'b.nm_bangsal')
            ->orderByDesc('total')
            ->get();

        $wards = $wardRows->map(function ($r) use ($totalDinas) {
            return [
                'kd_bangsal' => $r->kd_bangsal,
                'nm_bangsal' => $r->nm_bangsal,
                'total' => (int) $r->total,
                'percent' => $totalDinas > 0 ? round(($r->total / $totalDinas) * 100, 1) : 0,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'masih_dirawat' => (int) $r->masih_dirawat,
                'sudah_pulang' => (int) $r->sudah_pulang,
            ];
        })->toArray();

        // Kategori Personel
        $categoryRows = (clone $baseQuery)
            ->leftJoin('golongan_tni as gt', 'pt.golongan_tni', '=', 'gt.id')
            ->leftJoin('golongan_polri as gp', 'pp.golongan_polri', '=', 'gp.id')
            ->selectRaw("
                CASE 
                    WHEN gt.id IN (1, 2, 3) OR gp.id = 1 THEN 'Militer / Anggota Aktif'
                    WHEN gt.id IN (8, 9, 10) OR gp.id = 2 THEN 'ASN / PNS'
                    WHEN gt.id IN (5, 6, 7) OR gp.id = 3 THEN 'Keluarga Personel'
                    WHEN gt.id IN (4, 11, 12) OR gp.id = 4 THEN 'Purnawirawan'
                    ELSE 'Lainnya'
                END as kategori,
                count(distinct ki.no_rawat) as total,
                count(distinct case when pt.no_rkm_medis is not null then ki.no_rawat end) as tni,
                count(distinct case when pp.no_rkm_medis is not null then ki.no_rawat end) as polri,
                count(distinct case when ki.tgl_keluar = '0000-00-00' or ki.tgl_keluar is null then ki.no_rawat end) as masih_dirawat,
                count(distinct case when ki.tgl_keluar <> '0000-00-00' and ki.tgl_keluar is not null then ki.no_rawat end) as sudah_pulang
            ")
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $categories = $categoryRows->map(function ($r) use ($totalDinas) {
            return [
                'kategori' => $r->kategori,
                'total' => (int) $r->total,
                'percent' => $totalDinas > 0 ? round(($r->total / $totalDinas) * 100, 1) : 0,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'masih_dirawat' => (int) $r->masih_dirawat,
                'sudah_pulang' => (int) $r->sudah_pulang,
            ];
        })->toArray();

        // Top Satuan Pasien TNI
        $satuanRows = (clone $baseQuery)
            ->whereNotNull('pt.no_rkm_medis')
            ->leftJoin('satuan_tni as sat', 'pt.satuan_tni', '=', 'sat.id')
            ->selectRaw("COALESCE(sat.nama_satuan, 'Lainnya / Tidak Tercatat') as nama_satuan, count(distinct ki.no_rawat) as total")
            ->groupBy('nama_satuan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $satuan = $satuanRows->map(function ($r) {
            return [
                'nama_satuan' => (string) $r->nama_satuan,
                'total' => (int) $r->total,
            ];
        })->toArray();

        return [
            'summary' => [
                'total' => $totalDinas,
                'tni' => $summaryRow ? (int) $summaryRow->tni : 0,
                'polri' => $summaryRow ? (int) $summaryRow->polri : 0,
                'masih_dirawat' => $summaryRow ? (int) $summaryRow->masih_dirawat : 0,
                'sudah_pulang' => $summaryRow ? (int) $summaryRow->sudah_pulang : 0,
            ],
            'wards' => $wards,
            'categories' => $categories,
            'satuan' => $satuan,
        ];
    }

    /**
     * Mengambil proporsi jenis pasien rawat inap (Dinas TNI, Dinas POLRI, Pasien Sipil / Non-Dinas).
     */
    public static function getPatientTypeBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ): array {
        $baseQuery = self::buildQuery($startDate, $endDate, null, $statusPulang, $ward, $search);

        $row = $baseQuery
            ->selectRaw("
                count(distinct ki.no_rawat) as total,
                count(distinct case when pt.no_rkm_medis is not null then ki.no_rawat end) as tni,
                count(distinct case when pp.no_rkm_medis is not null then ki.no_rawat end) as polri,
                count(distinct case when pt.no_rkm_medis is null and pp.no_rkm_medis is null then ki.no_rawat end) as non_dinas
            ")
            ->first();

        $total = (int) ($row->total ?? 0);
        $tni = (int) ($row->tni ?? 0);
        $polri = (int) ($row->polri ?? 0);
        $nonDinas = (int) ($row->non_dinas ?? 0);

        return [
            'total' => $total,
            'items' => [
                [
                    'label' => 'Dinas TNI',
                    'total' => $tni,
                    'percent' => $total > 0 ? round(($tni / $total) * 100, 1) : 0,
                    'color' => '#10b981',
                ],
                [
                    'label' => 'Dinas POLRI',
                    'total' => $polri,
                    'percent' => $total > 0 ? round(($polri / $total) * 100, 1) : 0,
                    'color' => '#3b82f6',
                ],
                [
                    'label' => 'Pasien Sipil / Non-Dinas',
                    'total' => $nonDinas,
                    'percent' => $total > 0 ? round(($nonDinas / $total) * 100, 1) : 0,
                    'color' => '#6b7280',
                ],
            ],
        ];
    }

    /**
     * Mengambil rekapitulasi diagnosa pasien (ICD-10) rawat inap.
     */
    public static function getDiagnosisBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $ward = null,
        ?string $doctor = null,
        ?string $payType = null,
        ?string $gender = null,
        int $limit = 50,
        ?string $dinasFilter = null
    ): array {
        $query = DB::connection(self::CONNECTION)
            ->table('diagnosa_pasien as dp')
            ->join('penyakit as p', 'dp.kd_penyakit', '=', 'p.kd_penyakit')
            ->join('kamar_inap as ki', 'dp.no_rawat', '=', 'ki.no_rawat')
            ->join('reg_periksa as rp', 'ki.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis')
            ->join('kamar as k', 'ki.kd_kamar', '=', 'k.kd_kamar')
            ->join('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'rp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where('b.kd_bangsal', '!=', 'TRANS');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('ki.tgl_masuk', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('ki.tgl_masuk', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('ki.tgl_masuk', '<=', $endDate);
        }

        if (!empty($ward) && $ward !== 'semua') {
            $query->where('b.kd_bangsal', $ward);
        }

        if (!empty($doctor) && $doctor !== 'semua') {
            $query->where('rp.kd_dokter', $doctor);
        }

        if (!empty($gender) && $gender !== 'semua') {
            $query->where('ps.jk', $gender);
        }

        // Filter Penjab murni
        if (!empty($payType) && $payType !== 'semua') {
            if ($payType === 'BPJS') {
                $query->where('pj.png_jawab', 'like', '%BPJS%');
            } elseif ($payType === 'UMUM') {
                $query->where('pj.png_jawab', 'like', '%UMUM%');
            } else {
                $query->where('rp.kd_pj', $payType);
            }
        }

        // Filter Jenis Pasien (Status Kedinasan TNI, POLRI, Non-Dinas)
        if (!empty($dinasFilter) && $dinasFilter !== 'semua') {
            if ($dinasFilter === 'DINAS') {
                $query->where(function ($q) {
                    $q->whereNotNull('pt.no_rkm_medis')
                      ->orWhereNotNull('pp.no_rkm_medis');
                });
            } elseif ($dinasFilter === 'TNI') {
                $query->whereNotNull('pt.no_rkm_medis');
            } elseif ($dinasFilter === 'POLRI') {
                $query->whereNotNull('pp.no_rkm_medis');
            } elseif ($dinasFilter === 'NON_DINAS') {
                $query->whereNull('pt.no_rkm_medis')
                      ->whereNull('pp.no_rkm_medis');
            }
        }

        $rows = $query->selectRaw("
            dp.kd_penyakit,
            p.nm_penyakit,
            count(*) as total_kasus,
            sum(case when dp.prioritas = 1 then 1 else 0 end) as primer,
            sum(case when dp.prioritas > 1 then 1 else 0 end) as sekunder,
            sum(case when ps.jk = 'L' then 1 else 0 end) as pria,
            sum(case when ps.jk = 'P' then 1 else 0 end) as wanita,
            sum(case when dp.status_penyakit = 'Baru' then 1 else 0 end) as kasus_baru,
            sum(case when dp.status_penyakit = 'Lama' then 1 else 0 end) as kasus_lama,
            sum(case when pj.png_jawab like '%BPJS%' then 1 else 0 end) as bpjs,
            sum(case when pj.png_jawab like '%UMUM%' then 1 else 0 end) as umum,
            sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
            sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
            sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as dinas,
            sum(case when ki.stts_pulang = 'Meninggal' then 1 else 0 end) as meninggal,
            sum(case when ki.stts_pulang in ('Sembuh', 'Membaik') then 1 else 0 end) as sembuh
        ")
        ->groupBy('dp.kd_penyakit', 'p.nm_penyakit')
        ->orderByDesc('total_kasus')
        ->limit($limit)
        ->get();

        $grandTotal = $rows->sum('total_kasus') ?: 1;

        return $rows->map(function ($r, $idx) use ($grandTotal) {
            return [
                'rank' => $idx + 1,
                'kd_penyakit' => $r->kd_penyakit,
                'nm_penyakit' => $r->nm_penyakit,
                'total' => (int) $r->total_kasus,
                'total_kasus' => (int) $r->total_kasus,
                'percent' => round(($r->total_kasus / $grandTotal) * 100, 1),
                'primer' => (int) $r->primer,
                'sekunder' => (int) $r->sekunder,
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'baru' => (int) $r->kasus_baru,
                'lama' => (int) $r->kasus_lama,
                'kasus_baru' => (int) $r->kasus_baru,
                'kasus_lama' => (int) $r->kasus_lama,
                'bpjs' => (int) $r->bpjs,
                'umum' => (int) $r->umum,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'dinas' => (int) $r->dinas,
                'meninggal' => (int) $r->meninggal,
                'sembuh' => (int) $r->sembuh,
            ];
        })->toArray();
    }
}
