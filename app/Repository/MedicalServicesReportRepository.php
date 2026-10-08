<?php

namespace App\Repository;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

interface MedicalServicesReportInterface {}

class MedicalServicesReportRepository implements MedicalServicesReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Mengambil ringkasan komparatif layanan medis (Rawat Jalan / Poli, Gawat Darurat / IGD, dan Rawat Inap / Ranap)
     * dalam rentang tanggal tertentu.
     */
    public static function getSummary(string $startDate, string $endDate): array
    {
        $conn = DB::connection(self::KONEKSI);

        // 1. Agregasi Utama Registrasi Periksa
        $regSummary = $conn->table('reg_periksa as rp')
            ->leftJoin('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'rp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('rp.status_lanjut', '!=', 'Ralan')
                  ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
            })
            ->selectRaw("
                count(*) as total_kunjungan,
                count(distinct rp.no_rkm_medis) as total_pasien,
                sum(case when rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli_kunjungan,
                count(distinct case when rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then rp.no_rkm_medis end) as poli_pasien,
                sum(case when rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd_kunjungan,
                count(distinct case when rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then rp.no_rkm_medis end) as igd_pasien,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap_kunjungan,
                count(distinct case when rp.status_lanjut = 'Ranap' then rp.no_rkm_medis end) as ranap_pasien,
                sum(case when rp.stts_daftar = 'Baru' then 1 else 0 end) as pasien_baru,
                sum(case when rp.stts_daftar = 'Lama' then 1 else 0 end) as pasien_lama,
                sum(case when rp.status_poli = 'Baru' then 1 else 0 end) as poli_baru,
                sum(case when rp.status_poli = 'Lama' then 1 else 0 end) as poli_lama,
                sum(case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then 1 else 0 end) as dinas_total,
                sum(case when (pt.no_rkm_medis is not null or pp.no_rkm_medis is not null) and rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as dinas_poli,
                sum(case when (pt.no_rkm_medis is not null or pp.no_rkm_medis is not null) and rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as dinas_igd,
                sum(case when (pt.no_rkm_medis is not null or pp.no_rkm_medis is not null) and rp.status_lanjut = 'Ranap' then 1 else 0 end) as dinas_ranap,
                sum(case when pt.no_rkm_medis is not null then 1 else 0 end) as dinas_tni,
                sum(case when pt.no_rkm_medis is not null and rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as dinas_tni_poli,
                sum(case when pt.no_rkm_medis is not null and rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as dinas_tni_igd,
                sum(case when pt.no_rkm_medis is not null and rp.status_lanjut = 'Ranap' then 1 else 0 end) as dinas_tni_ranap,
                sum(case when pp.no_rkm_medis is not null then 1 else 0 end) as dinas_polri,
                sum(case when pp.no_rkm_medis is not null and rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as dinas_polri_poli,
                sum(case when pp.no_rkm_medis is not null and rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as dinas_polri_igd,
                sum(case when pp.no_rkm_medis is not null and rp.status_lanjut = 'Ranap' then 1 else 0 end) as dinas_polri_ranap,
                count(distinct case when pt.no_rkm_medis is not null or pp.no_rkm_medis is not null then rp.no_rkm_medis end) as dinas_pasien
            ")
            ->first();

        // 2. Data Ranap Masuk (Admissions) & Ranap Keluar (Discharges) dari kamar_inap
        $ranapAdmissions = $conn->table('kamar_inap')
            ->whereBetween('tgl_masuk', [$startDate, $endDate])
            ->count();

        $ranapDischarges = $conn->table('kamar_inap')
            ->whereBetween('tgl_keluar', [$startDate, $endDate])
            ->selectRaw("
                count(*) as total_keluar,
                sum(case when stts_pulang in ('Sembuh', 'Membaik', 'Atas Persetujuan Dokter') then 1 else 0 end) as sembuh,
                sum(case when stts_pulang in ('Atas Permintaan Sendiri', 'Pulang Paksa') then 1 else 0 end) as pulang_paksa,
                sum(case when stts_pulang = 'Rujuk' then 1 else 0 end) as dirujuk,
                sum(case when stts_pulang in ('Meninggal', 'Meninggal < 48 Jam', 'Meninggal >= 48 Jam') then 1 else 0 end) as meninggal,
                sum(lama) as total_los
            ")
            ->first();

        // 3. Pasien Aktif Dirawat Saat Ini di Ranap
        $ranapActive = $conn->table('kamar_inap')
            ->where('stts_pulang', '-')
            ->count();

        // 4. Breakdown Poliklinik Terbanyak
        $poliBreakdown = $conn->table('reg_periksa as rp')
            ->join('poliklinik as p', 'rp.kd_poli', '=', 'p.kd_poli')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where('rp.status_lanjut', 'Ralan')
            ->where('rp.kd_poli', '!=', 'IGDK')
            ->whereNotIn('rp.stts', ['Batal', 'Belum'])
            ->selectRaw("p.nm_poli, count(*) as total, count(distinct rp.no_rkm_medis) as pasien")
            ->groupBy('p.nm_poli')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // 5. Breakdown Penjamin / Cara Bayar Komparatif (Poli, IGD, Ranap)
        $caraBayarRows = $conn->table('reg_periksa as rp')
            ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('rp.status_lanjut', '!=', 'Ralan')
                  ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
            })
            ->selectRaw("
                pj.png_jawab as cara_bayar,
                count(*) as total,
                sum(case when rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap
            ")
            ->groupBy('pj.png_jawab')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // 6. Outcome / Status Pulang IGD
        $igdOutcome = $conn->table('reg_periksa as rp')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where('rp.status_lanjut', 'Ralan')
            ->where('rp.kd_poli', 'IGDK')
            ->selectRaw("
                sum(case when rp.stts in ('Sudah', 'Selesai') then 1 else 0 end) as pulang,
                sum(case when rp.stts = 'Dirawat' then 1 else 0 end) as dirawat,
                sum(case when rp.stts = 'Dirujuk' then 1 else 0 end) as dirujuk,
                sum(case when rp.stts = 'Meninggal' then 1 else 0 end) as meninggal,
                sum(case when rp.stts = 'Batal' then 1 else 0 end) as batal
            ")
            ->first();

        // 7. Breakdown Kamar / Bangsal Ranap
        $bangsalBreakdown = $conn->table('kamar_inap as ki')
            ->join('kamar as k', 'ki.kd_kamar', '=', 'k.kd_kamar')
            ->join('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
            ->whereBetween('ki.tgl_masuk', [$startDate, $endDate])
            ->selectRaw("b.nm_bangsal, count(*) as total, count(distinct ki.no_rawat) as pasien")
            ->groupBy('b.nm_bangsal')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // 8. Breakdown Pasien Dinas: Kategori Golongan & Satuan
        $dinasCategories = $conn->table('reg_periksa as rp')
            ->leftJoin('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'rp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->leftJoin('golongan_tni as gt', 'pt.golongan_tni', '=', 'gt.id')
            ->leftJoin('golongan_polri as gp', 'pp.golongan_polri', '=', 'gp.id')
            ->where(function ($q) {
                $q->whereNotNull('pt.no_rkm_medis')
                  ->orWhereNotNull('pp.no_rkm_medis');
            })
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('rp.status_lanjut', '!=', 'Ralan')
                  ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
            })
            ->selectRaw("
                CASE 
                    WHEN gt.id IN (1, 2, 3) OR gp.id = 1 THEN 'Militer / Anggota Aktif'
                    WHEN gt.id IN (8, 9, 10) OR gp.id = 2 THEN 'ASN / PNS'
                    WHEN gt.id IN (5, 6, 7) OR gp.id = 3 THEN 'Keluarga Personel'
                    WHEN gt.id IN (4, 11, 12) OR gp.id = 4 THEN 'Purnawirawan'
                    ELSE 'Lainnya'
                END as kategori,
                COUNT(*) as total,
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND (rp.kd_poli != 'IGDK' OR rp.kd_poli IS NULL) THEN 1 ELSE 0 END) as poli,
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as igd,
                SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap,
                SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as tni,
                SUM(CASE WHEN pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as polri
            ")
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $dinasSatuan = $conn->table('reg_periksa as rp')
            ->join('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('satuan_tni as st', 'pt.satuan_tni', '=', 'st.id')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('rp.status_lanjut', '!=', 'Ralan')
                  ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
            })
            ->selectRaw("
                COALESCE(st.nama_satuan, 'Lainnya') as nama_satuan,
                COUNT(*) as total,
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND (rp.kd_poli != 'IGDK' OR rp.kd_poli IS NULL) THEN 1 ELSE 0 END) as poli,
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as igd,
                SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap
            ")
            ->groupBy('nama_satuan')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // 9. Tren Harian (Daily Points)
        $dailyPoints = $conn->table('reg_periksa as rp')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('rp.status_lanjut', '!=', 'Ralan')
                  ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
            })
            ->selectRaw("
                rp.tgl_registrasi as tanggal,
                count(*) as total,
                sum(case when rp.status_lanjut = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when rp.status_lanjut = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when rp.status_lanjut = 'Ranap' then 1 else 0 end) as ranap
            ")
            ->groupBy('rp.tgl_registrasi')
            ->orderBy('rp.tgl_registrasi')
            ->get()
            ->keyBy('tanggal');

        // Normalisasi tanggal untuk tren harian kontinu
        $periodRange = CarbonPeriod::create($startDate, $endDate);
        $trendLabels = [];
        $trendTotal = [];
        $trendPoli = [];
        $trendIgd = [];
        $trendRanap = [];

        foreach ($periodRange as $date) {
            $dStr = $date->format('Y-m-d');
            $dLabel = $date->format('d/m');
            $row = $dailyPoints->get($dStr);

            $trendLabels[] = $dLabel;
            $trendTotal[] = $row ? (int) $row->total : 0;
            $trendPoli[] = $row ? (int) $row->poli : 0;
            $trendIgd[] = $row ? (int) $row->igd : 0;
            $trendRanap[] = $row ? (int) $row->ranap : 0;
        }

        // Hitung Metrik Eksekutif
        $totPoli = (int) ($regSummary->poli_kunjungan ?? 0);
        $totIgd = (int) ($regSummary->igd_kunjungan ?? 0);
        $totRanap = (int) ($regSummary->ranap_kunjungan ?? 0);
        $totAll = $totPoli + $totIgd + $totRanap;

        $pctPoli = $totAll > 0 ? round(($totPoli / $totAll) * 100, 1) : 0;
        $pctIgd = $totAll > 0 ? round(($totIgd / $totAll) * 100, 1) : 0;
        $pctRanap = $totAll > 0 ? round(($totRanap / $totAll) * 100, 1) : 0;

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
            ],
            'summary' => [
                'total_layanan' => $totAll,
                'total_pasien' => (int) ($regSummary->total_pasien ?? 0),
                'poli_kunjungan' => $totPoli,
                'poli_pasien' => (int) ($regSummary->poli_pasien ?? 0),
                'igd_kunjungan' => $totIgd,
                'igd_pasien' => (int) ($regSummary->igd_pasien ?? 0),
                'ranap_kunjungan' => $totRanap,
                'ranap_pasien' => (int) ($regSummary->ranap_pasien ?? 0),
                'pasien_baru' => (int) ($regSummary->pasien_baru ?? 0),
                'pasien_lama' => (int) ($regSummary->pasien_lama ?? 0),
                'pct_poli' => $pctPoli,
                'pct_igd' => $pctIgd,
                'pct_ranap' => $pctRanap,
                'ranap_admissions' => $ranapAdmissions,
                'ranap_discharges' => (int) ($ranapDischarges->total_keluar ?? 0),
                'ranap_active' => $ranapActive,
                'ranap_los_total' => (int) ($ranapDischarges->total_los ?? 0),
                'ranap_alos' => ($ranapDischarges && $ranapDischarges->total_keluar > 0)
                    ? round($ranapDischarges->total_los / $ranapDischarges->total_keluar, 1)
                    : 0,
            ],
            'outpatient' => [
                'total' => $totPoli,
                'pasien' => (int) ($regSummary->poli_pasien ?? 0),
                'top_clinics' => $poliBreakdown,
            ],
            'emergency' => [
                'total' => $totIgd,
                'pasien' => (int) ($regSummary->igd_pasien ?? 0),
                'pulang' => (int) ($igdOutcome->pulang ?? 0),
                'dirawat' => (int) ($igdOutcome->dirawat ?? 0),
                'dirujuk' => (int) ($igdOutcome->dirujuk ?? 0),
                'meninggal' => (int) ($igdOutcome->meninggal ?? 0),
                'batal' => (int) ($igdOutcome->batal ?? 0),
            ],
            'inpatient' => [
                'total' => $totRanap,
                'admissions' => $ranapAdmissions,
                'discharges' => (int) ($ranapDischarges->total_keluar ?? 0),
                'active' => $ranapActive,
                'sembuh' => (int) ($ranapDischarges->sembuh ?? 0),
                'pulang_paksa' => (int) ($ranapDischarges->pulang_paksa ?? 0),
                'dirujuk' => (int) ($ranapDischarges->dirujuk ?? 0),
                'meninggal' => (int) ($ranapDischarges->meninggal ?? 0),
                'top_wards' => $bangsalBreakdown,
            ],
            'dinas' => [
                'total' => (int) ($regSummary->dinas_total ?? 0),
                'pasien' => (int) ($regSummary->dinas_pasien ?? 0),
                'poli' => (int) ($regSummary->dinas_poli ?? 0),
                'igd' => (int) ($regSummary->dinas_igd ?? 0),
                'ranap' => (int) ($regSummary->dinas_ranap ?? 0),
                'tni' => (int) ($regSummary->dinas_tni ?? 0),
                'tni_poli' => (int) ($regSummary->dinas_tni_poli ?? 0),
                'tni_igd' => (int) ($regSummary->dinas_tni_igd ?? 0),
                'tni_ranap' => (int) ($regSummary->dinas_tni_ranap ?? 0),
                'polri' => (int) ($regSummary->dinas_polri ?? 0),
                'polri_poli' => (int) ($regSummary->dinas_polri_poli ?? 0),
                'polri_igd' => (int) ($regSummary->dinas_polri_igd ?? 0),
                'polri_ranap' => (int) ($regSummary->dinas_polri_ranap ?? 0),
                'pct_dinas' => $totAll > 0 ? round(((int) ($regSummary->dinas_total ?? 0) / $totAll) * 100, 1) : 0,
                'categories' => $dinasCategories,
                'satuan' => $dinasSatuan,
            ],
            'cara_bayar' => $caraBayarRows,
            'charts' => [
                'trend' => [
                    'labels' => $trendLabels,
                    'datasets' => [
                        [
                            'label' => 'Total Layanan Medis',
                            'data' => $trendTotal,
                            'borderColor' => '#059669',
                            'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                            'fill' => true,
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Rawat Jalan (Poli)',
                            'data' => $trendPoli,
                            'borderColor' => '#0284c7',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Gawat Darurat (IGD)',
                            'data' => $trendIgd,
                            'borderColor' => '#ef4444',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Rawat Inap (Ranap)',
                            'data' => $trendRanap,
                            'borderColor' => '#f59e0b',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                    ],
                ],
                'proportion' => [
                    'labels' => ['Rawat Jalan (Poli)', 'Gawat Darurat (IGD)', 'Rawat Inap (Ranap)'],
                    'datasets' => [[
                        'data' => [$totPoli, $totIgd, $totRanap],
                        'backgroundColor' => ['#0284c7', '#ef4444', '#f59e0b'],
                    ]],
                ],
                'cara_bayar' => [
                    'labels' => $caraBayarRows->pluck('cara_bayar')->toArray(),
                    'datasets' => [[
                        'label' => 'Total Pasien',
                        'data' => $caraBayarRows->pluck('total')->map(fn($v) => (int) $v)->toArray(),
                        'backgroundColor' => ['#059669', '#0284c7', '#f59e0b', '#8b5cf6', '#ec4899', '#3b82f6', '#64748b', '#14b8a6'],
                        'borderRadius' => 6,
                    ]],
                ],
                'clinics' => [
                    'labels' => $poliBreakdown->pluck('nm_poli')->toArray(),
                    'datasets' => [[
                        'label' => 'Kunjungan Poliklinik',
                        'data' => $poliBreakdown->pluck('total')->map(fn($v) => (int) $v)->toArray(),
                        'backgroundColor' => '#0284c7',
                        'borderRadius' => 6,
                    ]],
                ],
            ],
        ];
    }
}
