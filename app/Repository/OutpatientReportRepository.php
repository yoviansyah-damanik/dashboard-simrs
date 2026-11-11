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

    /**
     * Mengambil data analisis pertumbuhan Month-over-Month (MoM Growth) Pasien Rawat Jalan:
     * - Harian (1 s.d. 28/29/30/31 hari) jika periode Bulanan
     * - Bulanan (Januari s.d. Desember) jika periode Tahunan
     * Termasuk pemisahan eksplisit Poliklinik & IGD, serta Matriks per Poliklinik dan per Dokter DPJP.
     */
    public static function getOutpatientMomGrowth(array $params = []): array
    {
        $period = $params['period'] ?? 'monthly';
        $year = (int) ($params['year'] ?? date('Y'));
        $month = isset($params['month']) && $params['month'] !== '' ? (int) $params['month'] : (int) date('n');

        $filters = [
            'poly' => $params['poly'] ?? 'semua',
            'payType' => $params['payType'] ?? 'semua',
            'doctor' => $params['doctor'] ?? 'semua',
            'gender' => $params['gender'] ?? 'semua',
            'sttsDaftar' => $params['sttsDaftar'] ?? 'semua',
            'dinasFilter' => $params['dinasFilter'] ?? 'semua',
        ];

        $isMonthlyPeriod = ($period === 'monthly' || $period !== 'yearly');

        if ($isMonthlyPeriod) {
            return self::getDailyMomGrowthForMonth($year, $month, $filters);
        } else {
            return self::getMonthlyMomGrowthForYear($year, $filters);
        }
    }

    /**
     * Membangun kueri dasar untuk analisis MoM Growth Rawat Jalan (mencakup Poliklinik & IGD).
     */
    private static function buildMomBaseQuery(string $startDate, string $endDate, array $filters = [])
    {
        $query = DB::connection(self::CONNECTION)
            ->table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('poliklinik as poli', 'rp.kd_poli', '=', 'poli.kd_poli')
            ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('pasien_tni as pt', 'p.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'p.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where('rp.status_lanjut', 'Ralan')
            ->whereNotIn('rp.stts', ['Batal', 'Belum'])
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate]);

        if (!empty($filters['poly']) && $filters['poly'] !== 'semua') {
            $query->where('rp.kd_poli', $filters['poly']);
        }
        if (!empty($filters['payType']) && $filters['payType'] !== 'semua') {
            $query->where('rp.kd_pj', $filters['payType']);
        }
        if (!empty($filters['doctor']) && $filters['doctor'] !== 'semua') {
            $query->where('rp.kd_dokter', $filters['doctor']);
        }
        if (!empty($filters['gender']) && $filters['gender'] !== 'semua') {
            $query->where('p.jk', $filters['gender']);
        }
        if (!empty($filters['sttsDaftar']) && $filters['sttsDaftar'] !== 'semua') {
            $query->where('rp.stts_daftar', $filters['sttsDaftar']);
        }
        if (!empty($filters['dinasFilter']) && $filters['dinasFilter'] !== 'semua') {
            if ($filters['dinasFilter'] === 'TNI') {
                $query->whereNotNull('pt.no_rkm_medis');
            } elseif ($filters['dinasFilter'] === 'POLRI') {
                $query->whereNotNull('pp.no_rkm_medis');
            } elseif ($filters['dinasFilter'] === 'DINAS') {
                $query->where(function ($q) {
                    $q->whereNotNull('pt.no_rkm_medis')->orWhereNotNull('pp.no_rkm_medis');
                });
            } elseif ($filters['dinasFilter'] === 'NON_DINAS' || $filters['dinasFilter'] === 'UMUM') {
                $query->whereNull('pt.no_rkm_medis')->whereNull('pp.no_rkm_medis');
            }
        }

        return $query;
    }

    /**
     * Menghitung MoM Growth Harian untuk Periode Bulanan.
     */
    private static function getDailyMomGrowthForMonth(int $year, int $month, array $filters = []): array
    {
        $carbonCur = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $carbonCur->daysInMonth;
        $startDate = $carbonCur->startOfMonth()->format('Y-m-d');
        $endDate = $carbonCur->endOfMonth()->format('Y-m-d');

        $carbonPrev = \Carbon\Carbon::create($year, $month, 1)->subMonth();
        $prevStartDate = $carbonPrev->copy()->startOfMonth()->format('Y-m-d');
        $prevEndDate = $carbonPrev->copy()->endOfMonth()->format('Y-m-d');
        $prevDaysInMonth = $carbonPrev->daysInMonth;

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $monthNames[$month] ?? date('F');
        $prevMonthName = $monthNames[(int) $carbonPrev->format('n')] ?? $carbonPrev->format('F');

        // 1. Data Timeline Kunjungan Harian Bulan Ini
        $curRows = self::buildMomBaseQuery($startDate, $endDate, $filters)
            ->selectRaw("
                DAY(rp.tgl_registrasi) as hari,
                rp.tgl_registrasi as tanggal,
                count(*) as total,
                sum(case when rp.kd_poli != 'IGDK' then 1 else 0 end) as poli,
                sum(case when rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when pj.png_jawab like '%BPJS%' then 1 else 0 end) as bpjs,
                sum(case when pj.png_jawab like '%UMUM%' then 1 else 0 end) as umum,
                sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as dinas
            ")
            ->groupBy('rp.tgl_registrasi')
            ->orderBy('rp.tgl_registrasi')
            ->get()
            ->keyBy('hari');

        // 2. Data Timeline Kunjungan Harian Bulan Lalu (Pembanding MoM Tanggal yang sama)
        $prevRows = self::buildMomBaseQuery($prevStartDate, $prevEndDate, $filters)
            ->selectRaw("
                DAY(rp.tgl_registrasi) as hari,
                count(*) as total,
                sum(case when rp.kd_poli != 'IGDK' then 1 else 0 end) as poli,
                sum(case when rp.kd_poli = 'IGDK' then 1 else 0 end) as igd
            ")
            ->groupBy('rp.tgl_registrasi')
            ->get()
            ->keyBy('hari');

        // 3. Susun Timeline Harian
        $timeline = [];
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $prevDayVal = null;
        $totalCur = 0;
        $totalPoli = 0;
        $totalIgd = 0;
        $totalBaru = 0;
        $totalLama = 0;
        $totalPria = 0;
        $totalWanita = 0;
        $totalBpjs = 0;
        $totalUmum = 0;
        $totalDinas = 0;

        $peakDay = ['day' => null, 'date' => null, 'total' => 0];
        $lowestDay = ['day' => null, 'date' => null, 'total' => PHP_INT_MAX];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $row = $curRows->get($d);
            $curDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayOfWeek = (int) date('w', strtotime($curDate));
            $dayName = $dayNames[$dayOfWeek] ?? '';

            $val = $row ? (int) $row->total : 0;
            $poli = $row ? (int) $row->poli : 0;
            $igd = $row ? (int) $row->igd : 0;
            $baru = $row ? (int) $row->baru : 0;
            $lama = $row ? (int) $row->lama : 0;
            $pria = $row ? (int) $row->pria : 0;
            $wanita = $row ? (int) $row->wanita : 0;
            $bpjs = $row ? (int) $row->bpjs : 0;
            $umum = $row ? (int) $row->umum : 0;
            $dinas = $row ? (int) $row->dinas : 0;

            $totalCur += $val;
            $totalPoli += $poli;
            $totalIgd += $igd;
            $totalBaru += $baru;
            $totalLama += $lama;
            $totalPria += $pria;
            $totalWanita += $wanita;
            $totalBpjs += $bpjs;
            $totalUmum += $umum;
            $totalDinas += $dinas;

            if ($val > $peakDay['total']) {
                $peakDay = ['day' => $d, 'date' => $curDate, 'day_name' => $dayName, 'total' => $val];
            }
            if ($val > 0 && $val < $lowestDay['total']) {
                $lowestDay = ['day' => $d, 'date' => $curDate, 'day_name' => $dayName, 'total' => $val];
            }

            // DoD Growth (Pertumbuhan terhadap Hari Sebelumnya)
            $dodGrowth = null;
            if ($prevDayVal !== null) {
                if ($prevDayVal > 0) {
                    $dodGrowth = round((($val - $prevDayVal) / $prevDayVal) * 100, 1);
                } elseif ($val > 0) {
                    $dodGrowth = 100.0;
                } else {
                    $dodGrowth = 0.0;
                }
            }

            // MoM Date Growth (Pertumbuhan terhadap Tanggal yang Sama di Bulan Lalu)
            $prevMonthRow = $prevRows->get($d);
            $prevMonthVal = $prevMonthRow ? (int) $prevMonthRow->total : 0;
            $momDateGrowth = null;
            if ($prevMonthVal > 0) {
                $momDateGrowth = round((($val - $prevMonthVal) / $prevMonthVal) * 100, 1);
            } elseif ($val > 0 && $d <= $prevDaysInMonth) {
                $momDateGrowth = 100.0;
            }

            $timeline[$d] = [
                'day' => $d,
                'date' => $curDate,
                'short_date' => sprintf('%02d/%02d', $d, $month),
                'day_name' => $dayName,
                'is_sunday' => ($dayOfWeek === 0),
                'is_saturday' => ($dayOfWeek === 6),
                'total' => $val,
                'poli' => $poli,
                'igd' => $igd,
                'baru' => $baru,
                'lama' => $lama,
                'pria' => $pria,
                'wanita' => $wanita,
                'bpjs' => $bpjs,
                'umum' => $umum,
                'dinas' => $dinas,
                'prev_day_val' => $prevDayVal,
                'dod_growth' => $dodGrowth,
                'prev_month_date_val' => $prevMonthVal,
                'mom_date_growth' => $momDateGrowth,
            ];

            $prevDayVal = $val;
        }

        if ($lowestDay['total'] === PHP_INT_MAX) {
            $lowestDay = ['day' => null, 'date' => null, 'day_name' => '-', 'total' => 0];
        }

        $totalPrevMonth = $prevRows->sum('total');
        $totalPrevPoli = $prevRows->sum('poli');
        $totalPrevIgd = $prevRows->sum('igd');

        $growthTotalMoM = ($totalPrevMonth > 0)
            ? round((($totalCur - $totalPrevMonth) / $totalPrevMonth) * 100, 1)
            : ($totalCur > 0 ? 100.0 : 0.0);
        $growthPoliMoM = ($totalPrevPoli > 0)
            ? round((($totalPoli - $totalPrevPoli) / $totalPrevPoli) * 100, 1)
            : ($totalPoli > 0 ? 100.0 : 0.0);
        $growthIgdMoM = ($totalPrevIgd > 0)
            ? round((($totalIgd - $totalPrevIgd) / $totalPrevIgd) * 100, 1)
            : ($totalIgd > 0 ? 100.0 : 0.0);

        // 4. Matriks Poliklinik & IGD Harian
        $polyMatrix = self::buildDailyPolyclinicMatrix($startDate, $endDate, $prevStartDate, $prevEndDate, $daysInMonth, $filters);

        // 5. Matriks Dokter DPJP Harian
        $docMatrix = self::buildDailyDoctorMatrix($startDate, $endDate, $prevStartDate, $prevEndDate, $daysInMonth, $filters);

        return [
            'period_type' => 'daily',
            'period_label' => "{$monthName} {$year} (Analisis Harian)",
            'year' => $year,
            'month' => $month,
            'month_name' => $monthName,
            'prev_month_name' => $prevMonthName,
            'days_in_month' => $daysInMonth,
            'columns' => range(1, $daysInMonth),
            'kpi' => [
                'total_current' => $totalCur,
                'total_prev' => $totalPrevMonth,
                'growth_total' => $growthTotalMoM,
                'poli_current' => $totalPoli,
                'poli_prev' => $totalPrevPoli,
                'growth_poli' => $growthPoliMoM,
                'igd_current' => $totalIgd,
                'igd_prev' => $totalPrevIgd,
                'growth_igd' => $growthIgdMoM,
                'baru_current' => $totalBaru,
                'lama_current' => $totalLama,
                'pria_current' => $totalPria,
                'wanita_current' => $totalWanita,
                'bpjs_current' => $totalBpjs,
                'umum_current' => $totalUmum,
                'dinas_current' => $totalDinas,
                'avg_per_day' => $daysInMonth > 0 ? round($totalCur / $daysInMonth, 1) : 0,
                'peak_point' => $peakDay,
                'lowest_point' => $lowestDay,
            ],
            'timeline' => $timeline,
            'polyclinic_matrix' => $polyMatrix,
            'doctor_matrix' => $docMatrix,
            'chart_data' => [
                'labels' => collect($timeline)->pluck('short_date')->toArray(),
                'total' => collect($timeline)->pluck('total')->toArray(),
                'poli' => collect($timeline)->pluck('poli')->toArray(),
                'igd' => collect($timeline)->pluck('igd')->toArray(),
                'growth' => collect($timeline)->pluck('dod_growth')->toArray(),
            ],
        ];
    }

    /**
     * Menghitung MoM Growth Bulanan untuk Periode Tahunan (12 Bulan).
     */
    private static function getMonthlyMomGrowthForYear(int $year, array $filters = []): array
    {
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        $prevYear = $year - 1;
        $prevStartDate = "{$prevYear}-01-01";
        $prevEndDate = "{$prevYear}-12-31";

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // 1. Data Timeline Bulanan Tahun Ini
        $curRows = self::buildMomBaseQuery($startDate, $endDate, $filters)
            ->selectRaw("
                MONTH(rp.tgl_registrasi) as bulan,
                count(*) as total,
                sum(case when rp.kd_poli != 'IGDK' then 1 else 0 end) as poli,
                sum(case when rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when pj.png_jawab like '%BPJS%' then 1 else 0 end) as bpjs,
                sum(case when pj.png_jawab like '%UMUM%' then 1 else 0 end) as umum,
                sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as dinas
            ")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        // 2. Data Timeline Bulanan Tahun Lalu
        $prevRows = self::buildMomBaseQuery($prevStartDate, $prevEndDate, $filters)
            ->selectRaw("
                MONTH(rp.tgl_registrasi) as bulan,
                count(*) as total,
                sum(case when rp.kd_poli != 'IGDK' then 1 else 0 end) as poli,
                sum(case when rp.kd_poli = 'IGDK' then 1 else 0 end) as igd
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $totalCurYear = $curRows->sum('total');
        $prevTotalYear = $prevRows->sum('total');

        $totalCurPoli = $curRows->sum('poli');
        $prevTotalPoli = $prevRows->sum('poli');
        $totalCurIgd = $curRows->sum('igd');
        $prevTotalIgd = $prevRows->sum('igd');

        $growthYearTotal = ($prevTotalYear > 0)
            ? round((($totalCurYear - $prevTotalYear) / $prevTotalYear) * 100, 1)
            : ($totalCurYear > 0 ? 100.0 : 0.0);
        $growthYearPoli = ($prevTotalPoli > 0)
            ? round((($totalCurPoli - $prevTotalPoli) / $prevTotalPoli) * 100, 1)
            : ($totalCurPoli > 0 ? 100.0 : 0.0);
        $growthYearIgd = ($prevTotalIgd > 0)
            ? round((($totalCurIgd - $prevTotalIgd) / $prevTotalIgd) * 100, 1)
            : ($totalCurIgd > 0 ? 100.0 : 0.0);

        // 3. Susun Timeline 12 Bulan
        $timeline = [];
        $prevMonthVal = null;
        // Pembanding Januari adalah Desember tahun lalu jika ada
        $decPrevYear = $prevRows->get(12);
        if ($decPrevYear && $decPrevYear->total > 0) {
            $prevMonthVal = (int) $decPrevYear->total;
        }

        $peakMonth = ['month' => null, 'name' => null, 'total' => 0];
        $lowestMonth = ['month' => null, 'name' => null, 'total' => PHP_INT_MAX];

        $totalBaru = 0;
        $totalLama = 0;
        $totalPria = 0;
        $totalWanita = 0;
        $totalBpjs = 0;
        $totalUmum = 0;
        $totalDinas = 0;

        for ($m = 1; $m <= 12; $m++) {
            $row = $curRows->get($m);
            $val = $row ? (int) $row->total : 0;
            $poli = $row ? (int) $row->poli : 0;
            $igd = $row ? (int) $row->igd : 0;
            $baru = $row ? (int) $row->baru : 0;
            $lama = $row ? (int) $row->lama : 0;
            $pria = $row ? (int) $row->pria : 0;
            $wanita = $row ? (int) $row->wanita : 0;
            $bpjs = $row ? (int) $row->bpjs : 0;
            $umum = $row ? (int) $row->umum : 0;
            $dinas = $row ? (int) $row->dinas : 0;

            $totalBaru += $baru;
            $totalLama += $lama;
            $totalPria += $pria;
            $totalWanita += $wanita;
            $totalBpjs += $bpjs;
            $totalUmum += $umum;
            $totalDinas += $dinas;

            if ($val > $peakMonth['total']) {
                $peakMonth = ['month' => $m, 'name' => $monthNames[$m], 'total' => $val];
            }
            if ($val > 0 && $val < $lowestMonth['total']) {
                $lowestMonth = ['month' => $m, 'name' => $monthNames[$m], 'total' => $val];
            }

            // MoM Growth terhadap Bulan Sebelumnya
            $momGrowth = null;
            if ($prevMonthVal !== null) {
                if ($prevMonthVal > 0) {
                    $momGrowth = round((($val - $prevMonthVal) / $prevMonthVal) * 100, 1);
                } elseif ($val > 0) {
                    $momGrowth = 100.0;
                } else {
                    $momGrowth = 0.0;
                }
            }

            // YoY Growth terhadap Bulan yang Sama di Tahun Lalu
            $prevYearMonthRow = $prevRows->get($m);
            $prevYearMonthVal = $prevYearMonthRow ? (int) $prevYearMonthRow->total : 0;
            $yoyMonthGrowth = null;
            if ($prevYearMonthVal > 0) {
                $yoyMonthGrowth = round((($val - $prevYearMonthVal) / $prevYearMonthVal) * 100, 1);
            }

            $timeline[$m] = [
                'month' => $m,
                'name' => $monthNames[$m],
                'short_name' => substr($monthNames[$m], 0, 3),
                'total' => $val,
                'poli' => $poli,
                'igd' => $igd,
                'baru' => $baru,
                'lama' => $lama,
                'pria' => $pria,
                'wanita' => $wanita,
                'bpjs' => $bpjs,
                'umum' => $umum,
                'dinas' => $dinas,
                'prev_month_val' => $prevMonthVal,
                'mom_growth' => $momGrowth,
                'prev_year_month_val' => $prevYearMonthVal,
                'yoy_growth' => $yoyMonthGrowth,
                'share_pct' => $totalCurYear > 0 ? round(($val / $totalCurYear) * 100, 1) : 0,
            ];

            $prevMonthVal = $val;
        }

        if ($lowestMonth['total'] === PHP_INT_MAX) {
            $lowestMonth = ['month' => null, 'name' => '-', 'total' => 0];
        }

        // 4. Matriks Poliklinik & IGD Bulanan
        $polyMatrix = self::buildMonthlyPolyclinicMatrix($startDate, $endDate, $prevStartDate, $prevEndDate, $filters);

        // 5. Matriks Dokter DPJP Bulanan
        $docMatrix = self::buildMonthlyDoctorMatrix($startDate, $endDate, $prevStartDate, $prevEndDate, $filters);

        return [
            'period_type' => 'monthly',
            'period_label' => "Tahun {$year} (Analisis 12 Bulan)",
            'year' => $year,
            'prev_year' => $prevYear,
            'columns' => range(1, 12),
            'column_names' => array_map(fn($m) => substr($monthNames[$m], 0, 3), range(1, 12)),
            'kpi' => [
                'total_current' => $totalCurYear,
                'total_prev' => $prevTotalYear,
                'growth_total' => $growthYearTotal,
                'poli_current' => $totalCurPoli,
                'poli_prev' => $prevTotalPoli,
                'growth_poli' => $growthYearPoli,
                'igd_current' => $totalCurIgd,
                'igd_prev' => $prevTotalIgd,
                'growth_igd' => $growthYearIgd,
                'baru_current' => $totalBaru,
                'lama_current' => $totalLama,
                'pria_current' => $totalPria,
                'wanita_current' => $totalWanita,
                'bpjs_current' => $totalBpjs,
                'umum_current' => $totalUmum,
                'dinas_current' => $totalDinas,
                'avg_per_month' => round($totalCurYear / 12, 1),
                'peak_point' => $peakMonth,
                'lowest_point' => $lowestMonth,
            ],
            'timeline' => $timeline,
            'polyclinic_matrix' => $polyMatrix,
            'doctor_matrix' => $docMatrix,
            'chart_data' => [
                'labels' => collect($timeline)->pluck('short_name')->toArray(),
                'total' => collect($timeline)->pluck('total')->toArray(),
                'poli' => collect($timeline)->pluck('poli')->toArray(),
                'igd' => collect($timeline)->pluck('igd')->toArray(),
                'growth' => collect($timeline)->pluck('mom_growth')->toArray(),
            ],
        ];
    }

    /**
     * Membangun matriks harian per Poliklinik & IGD.
     */
    private static function buildDailyPolyclinicMatrix(string $startDate, string $endDate, string $prevStartDate, string $prevEndDate, int $daysInMonth, array $filters = []): array
    {
        $rows = self::buildMomBaseQuery($startDate, $endDate, $filters)
            ->selectRaw("
                rp.kd_poli,
                poli.nm_poli,
                DAY(rp.tgl_registrasi) as hari,
                count(*) as total
            ")
            ->groupBy('rp.kd_poli', 'poli.nm_poli', 'hari')
            ->get();

        $prevTotals = self::buildMomBaseQuery($prevStartDate, $prevEndDate, $filters)
            ->selectRaw("rp.kd_poli, count(*) as total")
            ->groupBy('rp.kd_poli')
            ->pluck('total', 'rp.kd_poli')
            ->toArray();

        $grouped = [];
        foreach ($rows as $r) {
            $kd = $r->kd_poli;
            if (!isset($grouped[$kd])) {
                $grouped[$kd] = [
                    'kd_poli' => $kd,
                    'nm_poli' => $r->nm_poli,
                    'jenis_layanan' => ($kd === 'IGDK') ? 'IGD' : 'Poli',
                    'points' => array_fill(1, $daysInMonth, 0),
                    'total' => 0,
                ];
            }
            $h = (int) $r->hari;
            $grouped[$kd]['points'][$h] = (int) $r->total;
            $grouped[$kd]['total'] += (int) $r->total;
        }

        // Hitung growth harian di setiap titik
        $items = [];
        foreach ($grouped as $kd => $item) {
            $prevTotal = $prevTotals[$kd] ?? 0;
            $growthTotal = ($prevTotal > 0)
                ? round((($item['total'] - $prevTotal) / $prevTotal) * 100, 1)
                : ($item['total'] > 0 ? 100.0 : 0.0);

            $pointGrowth = [];
            $prevP = null;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $curP = $item['points'][$d];
                if ($prevP !== null) {
                    if ($prevP > 0) {
                        $pointGrowth[$d] = round((($curP - $prevP) / $prevP) * 100, 1);
                    } elseif ($curP > 0) {
                        $pointGrowth[$d] = 100.0;
                    } else {
                        $pointGrowth[$d] = 0.0;
                    }
                } else {
                    $pointGrowth[$d] = null;
                }
                $prevP = $curP;
            }

            $item['growth_pct'] = $growthTotal;
            $item['point_growth'] = $pointGrowth;
            $items[] = $item;
        }

        usort($items, fn($a, $b) => $b['total'] <=> $a['total']);

        return [
            'columns' => range(1, $daysInMonth),
            'items' => $items,
        ];
    }

    /**
     * Membangun matriks harian per Dokter DPJP.
     */
    private static function buildDailyDoctorMatrix(string $startDate, string $endDate, string $prevStartDate, string $prevEndDate, int $daysInMonth, array $filters = []): array
    {
        $rows = self::buildMomBaseQuery($startDate, $endDate, $filters)
            ->selectRaw("
                rp.kd_dokter,
                d.nm_dokter,
                DAY(rp.tgl_registrasi) as hari,
                count(*) as total
            ")
            ->groupBy('rp.kd_dokter', 'd.nm_dokter', 'hari')
            ->get();

        $prevTotals = self::buildMomBaseQuery($prevStartDate, $prevEndDate, $filters)
            ->selectRaw("rp.kd_dokter, count(*) as total")
            ->groupBy('rp.kd_dokter')
            ->pluck('total', 'rp.kd_dokter')
            ->toArray();

        $grouped = [];
        foreach ($rows as $r) {
            $kd = $r->kd_dokter;
            if (!isset($grouped[$kd])) {
                $grouped[$kd] = [
                    'kd_dokter' => $kd,
                    'nm_dokter' => $r->nm_dokter,
                    'points' => array_fill(1, $daysInMonth, 0),
                    'total' => 0,
                ];
            }
            $h = (int) $r->hari;
            $grouped[$kd]['points'][$h] = (int) $r->total;
            $grouped[$kd]['total'] += (int) $r->total;
        }

        $items = [];
        foreach ($grouped as $kd => $item) {
            $prevTotal = $prevTotals[$kd] ?? 0;
            $growthTotal = ($prevTotal > 0)
                ? round((($item['total'] - $prevTotal) / $prevTotal) * 100, 1)
                : ($item['total'] > 0 ? 100.0 : 0.0);

            $pointGrowth = [];
            $prevP = null;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $curP = $item['points'][$d];
                if ($prevP !== null) {
                    if ($prevP > 0) {
                        $pointGrowth[$d] = round((($curP - $prevP) / $prevP) * 100, 1);
                    } elseif ($curP > 0) {
                        $pointGrowth[$d] = 100.0;
                    } else {
                        $pointGrowth[$d] = 0.0;
                    }
                } else {
                    $pointGrowth[$d] = null;
                }
                $prevP = $curP;
            }

            $item['growth_pct'] = $growthTotal;
            $item['point_growth'] = $pointGrowth;
            $items[] = $item;
        }

        usort($items, fn($a, $b) => $b['total'] <=> $a['total']);

        return [
            'columns' => range(1, $daysInMonth),
            'items' => array_slice($items, 0, 50),
        ];
    }

    /**
     * Membangun matriks bulanan (12 Bulan) per Poliklinik & IGD.
     */
    private static function buildMonthlyPolyclinicMatrix(string $startDate, string $endDate, string $prevStartDate, string $prevEndDate, array $filters = []): array
    {
        $rows = self::buildMomBaseQuery($startDate, $endDate, $filters)
            ->selectRaw("
                rp.kd_poli,
                poli.nm_poli,
                MONTH(rp.tgl_registrasi) as bulan,
                count(*) as total
            ")
            ->groupBy('rp.kd_poli', 'poli.nm_poli', 'bulan')
            ->get();

        $prevTotals = self::buildMomBaseQuery($prevStartDate, $prevEndDate, $filters)
            ->selectRaw("rp.kd_poli, count(*) as total")
            ->groupBy('rp.kd_poli')
            ->pluck('total', 'rp.kd_poli')
            ->toArray();

        $grouped = [];
        foreach ($rows as $r) {
            $kd = $r->kd_poli;
            if (!isset($grouped[$kd])) {
                $grouped[$kd] = [
                    'kd_poli' => $kd,
                    'nm_poli' => $r->nm_poli,
                    'jenis_layanan' => ($kd === 'IGDK') ? 'IGD' : 'Poli',
                    'points' => array_fill(1, 12, 0),
                    'total' => 0,
                ];
            }
            $m = (int) $r->bulan;
            $grouped[$kd]['points'][$m] = (int) $r->total;
            $grouped[$kd]['total'] += (int) $r->total;
        }

        $items = [];
        foreach ($grouped as $kd => $item) {
            $prevTotal = $prevTotals[$kd] ?? 0;
            $growthTotal = ($prevTotal > 0)
                ? round((($item['total'] - $prevTotal) / $prevTotal) * 100, 1)
                : ($item['total'] > 0 ? 100.0 : 0.0);

            $pointGrowth = [];
            $prevP = null;
            for ($m = 1; $m <= 12; $m++) {
                $curP = $item['points'][$m];
                if ($prevP !== null) {
                    if ($prevP > 0) {
                        $pointGrowth[$m] = round((($curP - $prevP) / $prevP) * 100, 1);
                    } elseif ($curP > 0) {
                        $pointGrowth[$m] = 100.0;
                    } else {
                        $pointGrowth[$m] = 0.0;
                    }
                } else {
                    $pointGrowth[$m] = null;
                }
                $prevP = $curP;
            }

            $item['growth_pct'] = $growthTotal;
            $item['point_growth'] = $pointGrowth;
            $items[] = $item;
        }

        usort($items, fn($a, $b) => $b['total'] <=> $a['total']);

        return [
            'columns' => range(1, 12),
            'items' => $items,
        ];
    }

    /**
     * Membangun matriks bulanan (12 Bulan) per Dokter DPJP.
     */
    private static function buildMonthlyDoctorMatrix(string $startDate, string $endDate, string $prevStartDate, string $prevEndDate, array $filters = []): array
    {
        $rows = self::buildMomBaseQuery($startDate, $endDate, $filters)
            ->selectRaw("
                rp.kd_dokter,
                d.nm_dokter,
                MONTH(rp.tgl_registrasi) as bulan,
                count(*) as total
            ")
            ->groupBy('rp.kd_dokter', 'd.nm_dokter', 'bulan')
            ->get();

        $prevTotals = self::buildMomBaseQuery($prevStartDate, $prevEndDate, $filters)
            ->selectRaw("rp.kd_dokter, count(*) as total")
            ->groupBy('rp.kd_dokter')
            ->pluck('total', 'rp.kd_dokter')
            ->toArray();

        $grouped = [];
        foreach ($rows as $r) {
            $kd = $r->kd_dokter;
            if (!isset($grouped[$kd])) {
                $grouped[$kd] = [
                    'kd_dokter' => $kd,
                    'nm_dokter' => $r->nm_dokter,
                    'points' => array_fill(1, 12, 0),
                    'total' => 0,
                ];
            }
            $m = (int) $r->bulan;
            $grouped[$kd]['points'][$m] = (int) $r->total;
            $grouped[$kd]['total'] += (int) $r->total;
        }

        $items = [];
        foreach ($grouped as $kd => $item) {
            $prevTotal = $prevTotals[$kd] ?? 0;
            $growthTotal = ($prevTotal > 0)
                ? round((($item['total'] - $prevTotal) / $prevTotal) * 100, 1)
                : ($item['total'] > 0 ? 100.0 : 0.0);

            $pointGrowth = [];
            $prevP = null;
            for ($m = 1; $m <= 12; $m++) {
                $curP = $item['points'][$m];
                if ($prevP !== null) {
                    if ($prevP > 0) {
                        $pointGrowth[$m] = round((($curP - $prevP) / $prevP) * 100, 1);
                    } elseif ($curP > 0) {
                        $pointGrowth[$m] = 100.0;
                    } else {
                        $pointGrowth[$m] = 0.0;
                    }
                } else {
                    $pointGrowth[$m] = null;
                }
                $prevP = $curP;
            }

            $item['growth_pct'] = $growthTotal;
            $item['point_growth'] = $pointGrowth;
            $items[] = $item;
        }

        usort($items, fn($a, $b) => $b['total'] <=> $a['total']);

        return [
            'columns' => range(1, 12),
            'items' => array_slice($items, 0, 50),
        ];
    }
}
