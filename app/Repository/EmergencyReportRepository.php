<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmergencyReportInterface {}

class EmergencyReportRepository implements EmergencyReportInterface
{
    const CONNECTION = 'simrs';
    const LIMIT_DEFAULT = 25;
    const KODE_IGD = 'IGDK';

    /**
     * Membangun base query laporan pasien gawat darurat (IGD).
     */
    private static function buildQuery(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusLanjut = null,
        ?string $statusPelayanan = null,
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
            ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('pasien_tni as pt', 'p.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'p.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where('rp.kd_poli', self::KODE_IGD)
            ->where('rp.stts', '!=', 'Batal');

        // Filter Rentang Tanggal Registrasi
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('rp.tgl_registrasi', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('rp.tgl_registrasi', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('rp.tgl_registrasi', '<=', $endDate);
        }

        // Filter Status Lanjut (Ranap / Ralan)
        if (!empty($statusLanjut) && $statusLanjut !== 'semua') {
            $query->where('rp.status_lanjut', $statusLanjut);
        }

        // Filter Status Pelayanan
        if (!empty($statusPelayanan) && $statusPelayanan !== 'semua') {
            $query->where('rp.stts', $statusPelayanan);
        }

        // Filter Penanggung Jawab / Cara Bayar (Murni kd_pj dari tabel penjab, tanpa hardcode)
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
                  ->orWhere('d.nm_dokter', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Mengambil ringkasan metrik utama pasien IGD.
     */
    public static function getSummary(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusLanjut = null,
        ?string $statusPelayanan = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null,
        ?string $sttsDaftar = null,
        ?string $search = null,
        ?string $dinasFilter = null
    ): array {
        $row = self::buildQuery($startDate, $endDate, $statusLanjut, $statusPelayanan, $payType, $doctor, $gender, $sttsDaftar, $search, $dinasFilter)
            ->selectRaw("
                count(*) as total_pasien,
                sum(case when p.jk = 'L' then 1 else 0 end) as total_pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as total_wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as total_baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as total_lama,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as total_ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as total_ralan,
                sum(case when rp.stts = 'Dirujuk' then 1 else 0 end) as total_dirujuk,
                sum(case when rp.stts = 'Meninggal' then 1 else 0 end) as total_meninggal,
                sum(case when rp.stts = 'Pulang Paksa' then 1 else 0 end) as total_pulang_paksa,
                sum(case when rp.stts = 'Sudah' then 1 else 0 end) as total_sudah,
                sum(case when rp.stts = 'Belum' then 1 else 0 end) as total_belum,
                sum(case when pj.png_jawab like '%BPJS%' then 1 else 0 end) as total_bpjs,
                sum(case when pj.png_jawab like '%UMUM%' then 1 else 0 end) as total_umum,
                sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as total_dinas,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as total_tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as total_polri
            ")
            ->first();

        $total = $row ? (int) $row->total_pasien : 0;
        $pria = $row ? (int) $row->total_pria : 0;
        $wanita = $row ? (int) $row->total_wanita : 0;
        $ranap = $row ? (int) $row->total_ranap : 0;
        $ralan = $row ? (int) $row->total_ralan : 0;

        return [
            'total_pasien' => $total,
            'total_pria' => $pria,
            'total_wanita' => $wanita,
            'rasio_pria' => $total > 0 ? round(($pria / $total) * 100, 1) : 0,
            'rasio_wanita' => $total > 0 ? round(($wanita / $total) * 100, 1) : 0,
            'total_baru' => $row ? (int) $row->total_baru : 0,
            'total_lama' => $row ? (int) $row->total_lama : 0,
            'total_ranap' => $ranap,
            'total_ralan' => $ralan,
            'rasio_ranap' => $total > 0 ? round(($ranap / $total) * 100, 1) : 0,
            'rasio_ralan' => $total > 0 ? round(($ralan / $total) * 100, 1) : 0,
            'total_dirujuk' => $row ? (int) $row->total_dirujuk : 0,
            'total_meninggal' => $row ? (int) $row->total_meninggal : 0,
            'total_pulang_paksa' => $row ? (int) $row->total_pulang_paksa : 0,
            'total_sudah' => $row ? (int) $row->total_sudah : 0,
            'total_belum' => $row ? (int) $row->total_belum : 0,
            'total_bpjs' => $row ? (int) $row->total_bpjs : 0,
            'total_umum' => $row ? (int) $row->total_umum : 0,
            'total_dinas' => $row ? (int) $row->total_dinas : 0,
            'total_tni' => $row ? (int) $row->total_tni : 0,
            'total_polri' => $row ? (int) $row->total_polri : 0,
        ];
    }

    /**
     * Mengambil data rekap tindak lanjut & status pelayanan IGD.
     */
    public static function getStatusBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $doctor = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, null, null, $payType, $doctor)
            ->selectRaw("
                rp.stts,
                rp.status_lanjut,
                count(*) as total
            ")
            ->groupBy('rp.stts', 'rp.status_lanjut')
            ->orderByDesc('total')
            ->get();

        $byStatus = [];
        $byLanjut = ['Ranap' => 0, 'Ralan' => 0];

        foreach ($rows as $r) {
            $stts = $r->stts ?: 'Belum';
            $byStatus[$stts] = ($byStatus[$stts] ?? 0) + (int) $r->total;

            $lanjut = $r->status_lanjut ?: 'Ralan';
            $byLanjut[$lanjut] = ($byLanjut[$lanjut] ?? 0) + (int) $r->total;
        }

        return [
            'by_status' => $byStatus,
            'by_lanjut' => $byLanjut,
        ];
    }

    /**
     * Mengambil data rekap per dokter jaga IGD.
     */
    public static function getDoctorBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, null, null, $payType)
            ->selectRaw("
                d.kd_dokter,
                d.nm_dokter,
                count(*) as total,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts = 'Dirujuk' then 1 else 0 end) as dirujuk,
                sum(case when rp.stts = 'Meninggal' then 1 else 0 end) as meninggal
            ")
            ->groupBy('d.kd_dokter', 'd.nm_dokter')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $rows->sum('total') ?: 1;

        return $rows->map(function ($r) use ($grandTotal) {
            return [
                'kd_dokter' => $r->kd_dokter,
                'nm_dokter' => $r->nm_dokter,
                'total' => (int) $r->total,
                'percent' => round(($r->total / $grandTotal) * 100, 1),
                'ranap' => (int) $r->ranap,
                'ralan' => (int) $r->ralan,
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'dirujuk' => (int) $r->dirujuk,
                'meninggal' => (int) $r->meninggal,
            ];
        })->toArray();
    }

    /**
     * Mengambil data rekap per cara bayar / penanggung jawab di IGD.
     */
    public static function getPayTypeBreakdown(
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate)
            ->selectRaw("
                coalesce(pj.png_jawab, 'Umum / Mandiri') as png_jawab,
                count(*) as total,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan
            ")
            ->groupBy('pj.png_jawab')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $rows->sum('total') ?: 1;

        return $rows->map(function ($r) use ($grandTotal) {
            return [
                'png_jawab' => $r->png_jawab,
                'total' => (int) $r->total,
                'percent' => round(($r->total / $grandTotal) * 100, 1),
                'ranap' => (int) $r->ranap,
                'ralan' => (int) $r->ralan,
            ];
        })->toArray();
    }

    /**
     * Mengambil tren kunjungan pasien IGD per tanggal.
     */
    public static function getTrend(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusLanjut = null,
        ?string $payType = null,
        ?string $doctor = null
    ): array {
        $rows = self::buildQuery($startDate, $endDate, $statusLanjut, null, $payType, $doctor)
            ->selectRaw("
                rp.tgl_registrasi as tgl,
                count(*) as total,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan
            ")
            ->groupBy('rp.tgl_registrasi')
            ->orderBy('rp.tgl_registrasi')
            ->get();

        return [
            'labels' => $rows->map(fn($r) => \Carbon\Carbon::parse($r->tgl)->format('d/m'))->toArray(),
            'total' => $rows->map(fn($r) => (int) $r->total)->toArray(),
            'ranap' => $rows->map(fn($r) => (int) $r->ranap)->toArray(),
            'ralan' => $rows->map(fn($r) => (int) $r->ralan)->toArray(),
        ];
    }

    /**
     * Mengambil daftar pasien IGD (paginasi atau seluruhnya untuk ekspor).
     */
    public static function getPatients(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusLanjut = null,
        ?string $statusPelayanan = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null,
        ?string $sttsDaftar = null,
        ?string $search = null,
        int $limit = self::LIMIT_DEFAULT,
        ?string $dinasFilter = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $query = self::buildQuery(
            $startDate, $endDate, $statusLanjut, $statusPelayanan,
            $payType, $doctor, $gender, $sttsDaftar, $search, $dinasFilter
        )
        ->select([
            'rp.no_rawat',
            'rp.no_reg',
            'rp.tgl_registrasi',
            'rp.jam_reg',
            'rp.no_rkm_medis',
            'p.nm_pasien',
            'p.jk',
            'p.no_ktp',
            'p.tgl_lahir',
            'rp.umurdaftar',
            'rp.sttsumur',
            'rp.stts_daftar',
            'rp.stts',
            'rp.status_lanjut',
            'd.nm_dokter',
            'pj.png_jawab',
            \Illuminate\Support\Facades\DB::raw("CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 'TNI' WHEN pp.no_rkm_medis IS NOT NULL THEN 'POLRI' ELSE 'UMUM' END as status_dinas"),
            'p.alamat',
        ])
        ->orderByDesc('rp.tgl_registrasi')
        ->orderByDesc('rp.jam_reg');

        if ($limit > 0) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * Mengambil rekapitulasi data pasien dinas di IGD (per Kategori Personel, Dokter Jaga, dan Satuan).
     */
    public static function getDinasBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $doctor = null,
        ?string $gender = null
    ): array {
        $baseQuery = self::buildQuery($startDate, $endDate, null, null, 'DINAS', $doctor, $gender);

        $summaryRow = (clone $baseQuery)
            ->selectRaw("
                count(*) as total,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as lama,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when rp.stts = 'Dirujuk' then 1 else 0 end) as dirujuk,
                sum(case when rp.stts = 'Meninggal' then 1 else 0 end) as meninggal
            ")
            ->first();

        $totalDinas = $summaryRow ? (int) $summaryRow->total : 0;

        // Breakdown per Dokter Jaga IGD
        $doctorRows = (clone $baseQuery)
            ->selectRaw("
                d.kd_dokter,
                d.nm_dokter,
                count(*) as total,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan
            ")
            ->groupBy('d.kd_dokter', 'd.nm_dokter')
            ->orderByDesc('total')
            ->get();

        $doctors = $doctorRows->map(function ($r) use ($totalDinas) {
            return [
                'kd_dokter' => $r->kd_dokter,
                'nm_dokter' => $r->nm_dokter,
                'total' => (int) $r->total,
                'percent' => $totalDinas > 0 ? round(($r->total / $totalDinas) * 100, 1) : 0,
                'tni' => (int) $r->tni,
                'polri' => (int) $r->polri,
                'ranap' => (int) $r->ranap,
                'ralan' => (int) $r->ralan,
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
                count(*) as total,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as tni,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as polri,
                sum(case when p.jk = 'L' then 1 else 0 end) as pria,
                sum(case when p.jk = 'P' then 1 else 0 end) as wanita,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan
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
                'ranap' => (int) $r->ranap,
                'ralan' => (int) $r->ralan,
            ];
        })->toArray();

        // Top Satuan Personel TNI
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
                'ranap' => $summaryRow ? (int) $summaryRow->ranap : 0,
                'ralan' => $summaryRow ? (int) $summaryRow->ralan : 0,
                'dirujuk' => $summaryRow ? (int) $summaryRow->dirujuk : 0,
                'meninggal' => $summaryRow ? (int) $summaryRow->meninggal : 0,
            ],
            'doctors' => $doctors,
            'categories' => $categories,
            'satuan' => $satuan,
        ];
    }

    /**
     * Mengambil rekapitulasi diagnosa pasien (ICD-10) di Instalasi Gawat Darurat (IGD).
     */
    public static function getDiagnosisBreakdown(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $statusLanjut = null,
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
            ->where('rp.kd_poli', self::KODE_IGD)
            ->where('rp.stts', '!=', 'Batal');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('rp.tgl_registrasi', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('rp.tgl_registrasi', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('rp.tgl_registrasi', '<=', $endDate);
        }

        if (!empty($statusLanjut) && $statusLanjut !== 'semua') {
            $query->where('rp.status_lanjut', $statusLanjut);
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
            sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap,
            sum(case when rp.status_lanjut = 'Ralan' then 1 else 0 end) as ralan,
            sum(case when rp.stts = 'Dirujuk' then 1 else 0 end) as dirujuk,
            sum(case when rp.stts = 'Meninggal' then 1 else 0 end) as meninggal
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
                'total_kasus' => (int) $r->total_kasus,
                'percent' => round(($r->total_kasus / $grandTotal) * 100, 1),
                'primer' => (int) $r->primer,
                'sekunder' => (int) $r->sekunder,
                'pria' => (int) $r->pria,
                'wanita' => (int) $r->wanita,
                'ranap' => (int) $r->ranap,
                'ralan' => (int) $r->ralan,
                'dirujuk' => (int) $r->dirujuk,
                'meninggal' => (int) $r->meninggal,
            ];
        })->toArray();
    }
}
