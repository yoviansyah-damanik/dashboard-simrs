<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\SirsHelper;

interface OutpatientReportInterface {}

class OutpatientReportRepository implements OutpatientReportInterface
{
    const CONNECTION = 'simrs';
    const LIMIT_DEFAULT = 25;

    /**
     * Membangun base query laporan pasien rawat jalan.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $poly
     * @param string|null $payType
     * @param string|null $doctor
     * @param string|null $gender
     * @param string|null $sttsDaftar
     * @param string|null $search
     * @return \Illuminate\Database\Query\Builder
     */
    private static function buildQuery(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null,
        ?string $sttsDaftar = null,
        ?string $search = null,
        ?string $dinasFilter = null
    ) {
        $query = DB::connection(self::CONNECTION)
            ->table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('poliklinik as poli', 'rp.kd_poli', '=', 'poli.kd_poli')
            ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('pasien_tni as pt', 'p.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'p.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where('rp.status_lanjut', 'Ralan')
            ->where('rp.kd_poli', '!=', 'IGDK')
            ->whereNotIn('rp.stts', ['Batal', 'Belum']);

        // Filter Rentang Tanggal Registrasi
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('rp.tgl_registrasi', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('rp.tgl_registrasi', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('rp.tgl_registrasi', '<=', $endDate);
        }

        // Filter Poliklinik
        if (!empty($poly) && $poly !== 'semua') {
            $query->where('rp.kd_poli', $poly);
        }

        // Filter Penanggung Jawab / Jenis Bayar (Murni kd_pj dari tabel penjab, tanpa hardcode)
        if (!empty($payType) && $payType !== 'semua') {
            $query->where('rp.kd_pj', $payType);
        }

        // Filter Pasien Dinas (TNI / POLRI murni dari relasi pasien_tni dan pasien_polri)
        if (!empty($dinasFilter) && $dinasFilter !== 'semua') {
            if ($dinasFilter === 'TNI') {
                $query->whereNotNull('pt.no_rkm_medis');
            } elseif ($dinasFilter === 'POLRI') {
                $query->whereNotNull('pp.no_rkm_medis');
            } elseif ($dinasFilter === 'DINAS') {
                $query->where(function ($q) {
                    $q->whereNotNull('pt.no_rkm_medis')
                      ->orWhereNotNull('pp.no_rkm_medis');
                });
            } elseif ($dinasFilter === 'NON_DINAS' || $dinasFilter === 'UMUM') {
                $query->whereNull('pt.no_rkm_medis')
                      ->whereNull('pp.no_rkm_medis');
            }
        }

        // Filter Dokter
        if (!empty($doctor) && $doctor !== 'semua') {
            $query->where('rp.kd_dokter', $doctor);
        }

        // Filter Jenis Kelamin
        if (!empty($gender) && $gender !== 'semua') {
            $query->where('p.jk', $gender);
        }

        // Filter Status Daftar (Baru / Lama)
        if (!empty($sttsDaftar) && $sttsDaftar !== 'semua') {
            $query->where('rp.stts_daftar', $sttsDaftar);
        }

        // Filter Pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('rp.no_rawat', 'like', "%{$search}%")
                  ->orWhere('rp.no_rkm_medis', 'like', "%{$search}%")
                  ->orWhere('p.nm_pasien', 'like', "%{$search}%")
                  ->orWhere('d.nm_dokter', 'like', "%{$search}%")
                  ->orWhere('poli.nm_poli', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Mengambil ringkasan metrik utama pasien rawat jalan.
     */
    public static function getSummary(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null,
        ?string $sttsDaftar = null,
        ?string $search = null,
        ?string $dinasFilter = null
    ): array {
        $row = self::buildQuery($startDate, $endDate, $poly, $payType, $doctor, $gender, $sttsDaftar, $search, $dinasFilter)
            ->selectRaw("
                count(*) as total_pasien,
                sum(case when p.jk = 'L' then 1 else 0 end) as total_pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as total_wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as total_baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as total_lama,
                sum(case when pj.png_jawab like '%BPJS%' then 1 else 0 end) as total_bpjs,
                sum(case when pj.png_jawab like '%UMUM%' then 1 else 0 end) as total_umum,
                sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as total_dinas,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as total_tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as total_polri,
                sum(case when rp.stts = 'Sudah' then 1 else 0 end) as total_sudah,
                sum(case when rp.stts = 'Belum' then 1 else 0 end) as total_belum
            ")
            ->first();

        $total = $row ? (int) $row->total_pasien : 0;
        $pria = $row ? (int) $row->total_pria : 0;
        $wanita = $row ? (int) $row->total_wanita : 0;

        return [
            'total_pasien' => $total,
            'total_pria' => $pria,
            'total_wanita' => $wanita,
            'rasio_pria' => $total > 0 ? round(($pria / $total) * 100, 1) : 0,
            'rasio_wanita' => $total > 0 ? round(($wanita / $total) * 100, 1) : 0,
            'total_baru' => $row ? (int) $row->total_baru : 0,
            'total_lama' => $row ? (int) $row->total_lama : 0,
            'total_bpjs' => $row ? (int) $row->total_bpjs : 0,
            'total_umum' => $row ? (int) $row->total_umum : 0,
            'total_dinas' => $row ? (int) $row->total_dinas : 0,
            'total_tni' => $row ? (int) $row->total_tni : 0,
            'total_polri' => $row ? (int) $row->total_polri : 0,
            'total_sudah' => $row ? (int) $row->total_sudah : 0,
            'total_belum' => $row ? (int) $row->total_belum : 0,
        ];
    }

    /**
     * Mengambil data rekap jumlah pasien per Poliklinik / Unit.
     */
    public static function getPolyBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $gender = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, null, $payType, null, $gender)
            ->selectRaw("
                poli.kd_poli,
                poli.nm_poli,
                count(*) as total,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama,
                sum(case when pj.png_jawab like '%BPJS%' then 1 else 0 end) as bpjs,
                sum(case when pj.png_jawab like '%UMUM%' then 1 else 0 end) as umum,
                sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as dinas,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when rp.stts = 'Sudah' then 1 else 0 end) as sudah,
                sum(case when rp.stts = 'Belum' then 1 else 0 end) as belum
            ")
            ->groupBy('poli.kd_poli', 'poli.nm_poli')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $rows->sum('total') ?: 1;

        return $rows->map(function ($r) use ($grandTotal) {
            return [
                'kd_poli' => $r->kd_poli,
                'nm_poli' => $r->nm_poli,
                'total' => (int) $r->total,
                'percent' => round(($r->total / $grandTotal) * 100, 1),
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'baru' => (int) $r->baru,
                'lama' => (int) $r->lama,
                'bpjs' => (int) $r->bpjs,
                'umum' => (int) $r->umum,
                'dinas' => (int) $r->dinas,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'sudah' => (int) $r->sudah,
                'belum' => (int) $r->belum,
            ];
        })->toArray();
    }

    /**
     * Mengambil data rekap jumlah pasien per Jenis Bayar / Penjamin.
     */
    public static function getPayTypeBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $gender = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, $poly, null, null, $gender)
            ->selectRaw("
                COALESCE(pj.kd_pj, '-') as kd_pj,
                COALESCE(pj.png_jawab, 'Tidak Diketahui') as png_jawab,
                count(*) as total,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama
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
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'baru' => (int) $r->baru,
                'lama' => (int) $r->lama,
            ];
        })->toArray();
    }

    /**
     * Mengambil data rekap kelompok umur rawat jalan berdasarkan simrs.kelompok_umur.
     */
    public static function getAgeGroupBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $payType = null
    ): array {
        $categories = SirsHelper::getAgeGroupCategories();
        $caseSql = SirsHelper::ageGroupCategoryCaseSql('p.tgl_lahir', 'rp.tgl_registrasi');

        $rows = self::buildQuery($startDate, $endDate, $poly, $payType)
            ->selectRaw("
                {$caseSql} as kode_kelompok,
                count(*) as total,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita
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
     * Mengambil daftar pasien rawat jalan secara terperinci.
     */
    public static function getPatients(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null,
        ?string $sttsDaftar = null,
        ?string $search = null,
        int $limit = self::LIMIT_DEFAULT,
        ?string $dinasFilter = null
    ) {
        $query = self::buildQuery($startDate, $endDate, $poly, $payType, $doctor, $gender, $sttsDaftar, $search, $dinasFilter)
            ->leftJoin('pangkat_tni as pkt', 'pt.pangkat_tni', '=', 'pkt.id')
            ->leftJoin('satuan_tni as sat', 'pt.satuan_tni', '=', 'sat.id')
            ->select([
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'p.jk',
                'p.tgl_lahir',
                'p.alamat',
                'rp.umurdaftar',
                'rp.sttsumur',
                'rp.tgl_registrasi',
                'rp.jam_reg',
                'poli.kd_poli',
                'poli.nm_poli',
                'd.kd_dokter',
                'd.nm_dokter',
                'pj.kd_pj',
                'pj.png_jawab',
                'rp.stts_daftar',
                'rp.stts',
                'pkt.nama_pangkat',
                'sat.nama_satuan',
                DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas")
            ])
            ->orderByDesc('rp.tgl_registrasi')
            ->orderByDesc('rp.jam_reg');

        if ($limit === 0) {
            return $query->get();
        }

        return $query->paginate($limit);
    }

    /**
     * Mengambil tren kunjungan pasien rawat jalan per tanggal.
     */
    public static function getTrend(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null,
        ?string $sttsDaftar = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, $poly, $payType, $doctor, $gender, $sttsDaftar)
            ->selectRaw("
                rp.tgl_registrasi as tgl,
                count(*) as total,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita
            ")
            ->groupBy('rp.tgl_registrasi')
            ->orderBy('rp.tgl_registrasi')
            ->get();

        return [
            'labels' => $rows->map(fn($r) => \Carbon\Carbon::parse($r->tgl)->format('d/m'))->toArray(),
            'total' => $rows->map(fn($r) => (int) $r->total)->toArray(),
            'pria' => $rows->map(fn($r) => (int) $r->pria)->toArray(),
            'wanita' => $rows->map(fn($r) => (int) $r->wanita)->toArray(),
        ];
    }

    /**
     * Mengambil rekapitulasi data pasien dinas rawat jalan (per Poliklinik, Kategori Personel, dan Satuan).
     */
    public static function getDinasBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $gender = null
    ): array {
        $baseQuery = self::buildQuery($startDate, $endDate, $poly, 'DINAS', null, $gender);

        $summaryRow = (clone $baseQuery)
            ->selectRaw("
                count(*) as total,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama,
                sum(case when rp.stts = 'Sudah' then 1 else 0 end) as sudah,
                sum(case when rp.stts = 'Belum' then 1 else 0 end) as belum
            ")
            ->first();

        $totalDinas = $summaryRow ? (int) $summaryRow->total : 0;

        $polyRows = (clone $baseQuery)
            ->selectRaw("
                poli.kd_poli,
                poli.nm_poli,
                count(*) as total,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama,
                sum(case when rp.stts = 'Sudah' then 1 else 0 end) as sudah,
                sum(case when rp.stts = 'Belum' then 1 else 0 end) as belum
            ")
            ->groupBy('poli.kd_poli', 'poli.nm_poli')
            ->orderByDesc('total')
            ->get();

        $polyclinics = $polyRows->map(function ($r) use ($totalDinas) {
            return [
                'kd_poli' => $r->kd_poli,
                'nm_poli' => $r->nm_poli,
                'total' => (int) $r->total,
                'percent' => $totalDinas > 0 ? round(($r->total / $totalDinas) * 100, 1) : 0,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'baru' => (int) $r->baru,
                'lama' => (int) $r->lama,
                'sudah' => (int) $r->sudah,
                'belum' => (int) $r->belum,
            ];
        })->toArray();

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
                count(*) as total,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama
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
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'baru' => (int) $r->baru,
                'lama' => (int) $r->lama,
            ];
        })->toArray();

        $satuanRows = (clone $baseQuery)
            ->whereNotNull('pt.no_rkm_medis')
            ->leftJoin('satuan_tni as sat', 'pt.satuan_tni', '=', 'sat.id')
            ->selectRaw("COALESCE(sat.nama_satuan, 'Lainnya / Tidak Tercatat') as nama_satuan, count(*) as total")
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
                'pria' => $summaryRow ? (int) $summaryRow->pria : 0,
                'wanita' => $summaryRow ? (int) $summaryRow->wanita : 0,
                'baru' => $summaryRow ? (int) $summaryRow->baru : 0,
                'lama' => $summaryRow ? (int) $summaryRow->lama : 0,
                'sudah' => $summaryRow ? (int) $summaryRow->sudah : 0,
                'belum' => $summaryRow ? (int) $summaryRow->belum : 0,
            ],
            'polyclinics' => $polyclinics,
            'categories' => $categories,
            'satuan' => $satuan,
        ];
    }

    /**
     * Mengambil rekapitulasi diagnosa pasien (ICD-10) rawat jalan poliklinik.
     */
    public static function getDiagnosisBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $poly = null,
        ?string $doctor = null,
        ?string $payType = null,
        ?string $gender = null,
        int $limit = 50,
        ?string $dinasFilter = null
    ): array {
        $query = DB::connection(self::CONNECTION)
            ->table('diagnosa_pasien as dp')
            ->join('penyakit as p', 'dp.kd_penyakit', '=', 'p.kd_penyakit')
            ->join('reg_periksa as rp', 'dp.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('pasien_tni as pt', 'ps.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'ps.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where('rp.status_lanjut', 'Ralan')
            ->where('rp.kd_poli', '!=', 'IGDK')
            ->whereNotIn('rp.stts', ['Batal', 'Belum']);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('rp.tgl_registrasi', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('rp.tgl_registrasi', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('rp.tgl_registrasi', '<=', $endDate);
        }

        if (!empty($poly) && $poly !== 'semua') {
            $query->where('rp.kd_poli', $poly);
        }

        if (!empty($doctor) && $doctor !== 'semua') {
            $query->where('rp.kd_dokter', $doctor);
        }

        if (!empty($gender) && $gender !== 'semua') {
            $query->where('ps.jk', $gender);
        }

        if (!empty($payType) && $payType !== 'semua') {
            $query->where('rp.kd_pj', $payType);
        }

        if (!empty($dinasFilter) && $dinasFilter !== 'semua') {
            if ($dinasFilter === 'TNI') {
                $query->whereNotNull('pt.no_rkm_medis');
            } elseif ($dinasFilter === 'POLRI') {
                $query->whereNotNull('pp.no_rkm_medis');
            } elseif ($dinasFilter === 'DINAS') {
                $query->where(function ($q) {
                    $q->whereNotNull('pt.no_rkm_medis')
                      ->orWhereNotNull('pp.no_rkm_medis');
                });
            } elseif ($dinasFilter === 'NON_DINAS' || $dinasFilter === 'UMUM') {
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
            sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as dinas
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
                'dinas' => (int) $r->dinas,
            ];
        })->toArray();
    }
}
