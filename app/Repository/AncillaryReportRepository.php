<?php

namespace App\Repository;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

interface AncillaryReportInterface {}

class AncillaryReportRepository implements AncillaryReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Mengambil matriks indikator tahunan layanan penunjang gabungan:
     * Laboratorium, Radiologi, Farmasi, dan Gizi (12 bulan).
     */
    public static function getYearlyMatrix(int|string|null $year = null): array
    {
        $year = (int) ($year ?: date('Y'));

        // 1. Query agregat per bulan untuk Laboratorium
        $labRows = DB::connection(self::KONEKSI)->table('periksa_lab as pl')
            ->leftJoin('reg_periksa as rp', 'pl.no_rawat', '=', 'rp.no_rawat')
            ->whereYear('pl.tgl_periksa', $year)
            ->selectRaw("
                MONTH(pl.tgl_periksa) as bulan,
                count(*) as total_pemeriksaan,
                count(distinct pl.no_rawat) as total_pasien,
                sum(case when pl.status = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when pl.status = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when pl.status = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when pl.status = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when pl.kategori = 'PK' then 1 else 0 end) as pk,
                sum(case when pl.kategori = 'PA' then 1 else 0 end) as pa,
                sum(case when pl.kategori = 'MB' then 1 else 0 end) as mb
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // 2. Query agregat per bulan untuk Radiologi
        $radRows = DB::connection(self::KONEKSI)->table('periksa_radiologi as pr')
            ->leftJoin('reg_periksa as rp', 'pr.no_rawat', '=', 'rp.no_rawat')
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->leftJoin('mapping_radiologi_modality as mrm', 'pr.kd_jenis_prw', '=', 'mrm.kd_jenis_prw')
            ->whereYear('pr.tgl_periksa', $year)
            ->selectRaw("
                MONTH(pr.tgl_periksa) as bulan,
                count(*) as total_pemeriksaan,
                count(distinct pr.no_rawat) as total_pasien,
                sum(case when pr.status = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when pr.status = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when pr.status = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when pr.status = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'CR' THEN 1 ELSE 0 END) as cr,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'CT' THEN 1 ELSE 0 END) as ct,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'US' THEN 1 ELSE 0 END) as us,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'MR' THEN 1 ELSE 0 END) as mr,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'PX' THEN 1 ELSE 0 END) as px,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'MG' THEN 1 ELSE 0 END) as mg
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // 3. Query agregat per bulan untuk Farmasi (resep_obat & resep_pulang)
        $farmasiRows = DB::connection(self::KONEKSI)->table('resep_obat as ro')
            ->leftJoin('reg_periksa as rp', 'ro.no_rawat', '=', 'rp.no_rawat')
            ->whereYear('ro.tgl_perawatan', $year)
            ->selectRaw("
                MONTH(ro.tgl_perawatan) as bulan,
                count(*) as total_resep,
                count(distinct ro.no_rawat) as total_pasien,
                sum(case when ro.status = 'ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when ro.status = 'ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when ro.status = 'ralan' then 1 else 0 end) as ralan,
                sum(case when ro.status = 'ranap' then 1 else 0 end) as ranap_harian,
                sum(case when ro.jenis_resep = 'Biasa' then 1 else 0 end) as biasa,
                sum(case when ro.jenis_resep = 'Kronis' then 1 else 0 end) as kronis,
                sum(case when ro.jenis_resep = 'CITO' then 1 else 0 end) as cito,
                sum(case when ro.jenis_resep = 'PRB' then 1 else 0 end) as prb,
                sum(case when ro.tgl_penyerahan is not null and ro.tgl_penyerahan != '0000-00-00' then 1 else 0 end) as diserahkan,
                sum(case when ro.tgl_penyerahan is null or ro.tgl_penyerahan = '0000-00-00' then 1 else 0 end) as belum_diserahkan,
                ROUND(AVG(
                    CASE 
                        WHEN ro.tgl_peresepan is not null AND ro.jam_peresepan is not null 
                             AND ro.tgl_penyerahan is not null AND ro.jam_penyerahan is not null
                             AND ro.tgl_penyerahan != '0000-00-00' AND ro.tgl_peresepan != '0000-00-00'
                             AND TIMESTAMPDIFF(MINUTE, CONCAT(ro.tgl_peresepan, ' ', ro.jam_peresepan), CONCAT(ro.tgl_penyerahan, ' ', ro.jam_penyerahan)) BETWEEN 0 AND 240
                        THEN TIMESTAMPDIFF(MINUTE, CONCAT(ro.tgl_peresepan, ' ', ro.jam_peresepan), CONCAT(ro.tgl_penyerahan, ' ', ro.jam_penyerahan))
                        ELSE NULL 
                    END
                ), 1) as avg_waktu_tunggu
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $resepPulangRows = DB::connection(self::KONEKSI)->table('resep_pulang as rp')
            ->whereYear('rp.tanggal', $year)
            ->selectRaw("
                MONTH(rp.tanggal) as bulan,
                count(distinct concat(rp.no_rawat, ' ', rp.tanggal, ' ', rp.jam)) as total_resep_pulang,
                count(distinct rp.no_rawat) as total_pasien_pulang
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // 4. Query agregat per bulan untuk Gizi (detail_beri_diet & asuhan_gizi/catatan_adime_gizi)
        $dietRows = DB::connection(self::KONEKSI)->table('detail_beri_diet as dbd')
            ->leftJoin('reg_periksa as rp', 'dbd.no_rawat', '=', 'rp.no_rawat')
            ->whereYear('dbd.tanggal', $year)
            ->selectRaw("
                MONTH(dbd.tanggal) as bulan,
                count(*) as total_porsi,
                count(distinct dbd.no_rawat) as total_pasien,
                sum(case when dbd.waktu like 'Pagi%' then 1 else 0 end) as pagi,
                sum(case when dbd.waktu like 'Siang%' then 1 else 0 end) as siang,
                sum(case when dbd.waktu like 'Sore%' or dbd.waktu like 'Malam%' then 1 else 0 end) as sore,
                sum(case when rp.status_lanjut = 'Ranap' or dbd.kd_kamar is not null then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' and dbd.kd_kamar is null then 1 else 0 end) as ralan,
                sum(case when rp.status_lanjut = 'Ralan' and dbd.kd_kamar is null and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when rp.status_lanjut = 'Ralan' and dbd.kd_kamar is null and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $adimeRows = DB::connection(self::KONEKSI)->table('catatan_adime_gizi as cag')
            ->whereYear('cag.tanggal', $year)
            ->selectRaw("
                MONTH(cag.tanggal) as bulan,
                count(*) as total_adime,
                count(distinct cag.no_rawat) as total_pasien_adime
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $topDiets = DB::connection(self::KONEKSI)->table('detail_beri_diet as dbd')
            ->join('diet as d', 'dbd.kd_diet', '=', 'd.kd_diet')
            ->whereYear('dbd.tanggal', $year)
            ->selectRaw("d.nama_diet, count(*) as total_porsi")
            ->groupBy('d.nama_diet')
            ->orderByDesc('total_porsi')
            ->take(8)
            ->get()
            ->map(fn($d) => [
                'nama_diet' => $d->nama_diet,
                'total_porsi' => (int) $d->total_porsi,
            ])
            ->toArray();

        $topDietBangsal = DB::connection(self::KONEKSI)->table('detail_beri_diet as dbd')
            ->leftJoin('kamar as k', 'dbd.kd_kamar', '=', 'k.kd_kamar')
            ->leftJoin('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
            ->whereYear('dbd.tanggal', $year)
            ->selectRaw("coalesce(b.nm_bangsal, dbd.kd_kamar) as nm_bangsal, count(*) as total_porsi")
            ->groupBy('nm_bangsal')
            ->orderByDesc('total_porsi')
            ->take(8)
            ->get()
            ->map(fn($d) => [
                'nm_bangsal' => $d->nm_bangsal,
                'total_porsi' => (int) $d->total_porsi,
            ])
            ->toArray();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $shortMonthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $months = [];
        $totalPelayananList = [];

        for ($m = 1; $m <= 12; $m++) {
            $lab = $labRows->get($m);
            $rad = $radRows->get($m);
            $far = $farmasiRows->get($m);
            $pul = $resepPulangRows->get($m);
            $gizi = $dietRows->get($m);
            $adm = $adimeRows->get($m);

            // Laboratorium
            $labPem = $lab ? (int) $lab->total_pemeriksaan : 0;
            $labPas = $lab ? (int) $lab->total_pasien : 0;
            $labPoli = $lab ? (int) $lab->poli : 0;
            $labIgd = $lab ? (int) $lab->igd : 0;
            $labRalan = $lab ? (int) $lab->ralan : ($labPoli + $labIgd);
            $labRanap = $lab ? (int) $lab->ranap : 0;
            $labPk = $lab ? (int) $lab->pk : 0;
            $labPa = $lab ? (int) $lab->pa : 0;
            $labMb = $lab ? (int) $lab->mb : 0;

            // Radiologi
            $radPem = $rad ? (int) $rad->total_pemeriksaan : 0;
            $radPas = $rad ? (int) $rad->total_pasien : 0;
            $radPoli = $rad ? (int) $rad->poli : 0;
            $radIgd = $rad ? (int) $rad->igd : 0;
            $radRalan = $rad ? (int) $rad->ralan : ($radPoli + $radIgd);
            $radRanap = $rad ? (int) $rad->ranap : 0;
            $radCr = $rad ? (int) $rad->cr : 0;
            $radCt = $rad ? (int) $rad->ct : 0;
            $radUs = $rad ? (int) $rad->us : 0;
            $radMr = $rad ? (int) $rad->mr : 0;
            $radPx = $rad ? (int) $rad->px : 0;
            $radMg = $rad ? (int) $rad->mg : 0;

            // Farmasi
            $farPoli = $far ? (int) $far->poli : 0;
            $farIgd = $far ? (int) $far->igd : 0;
            $farRalan = $far ? (int) $far->ralan : ($farPoli + $farIgd);
            $farRanapHarian = $far ? (int) $far->ranap_harian : 0;
            $farResepPulang = $pul ? (int) $pul->total_resep_pulang : 0;
            $farRanap = $farRanapHarian + $farResepPulang;
            $farResep = $farRalan + $farRanap;
            $farPas = $far ? (int) $far->total_pasien : 0;
            $farBiasa = $far ? (int) $far->biasa : 0;
            $farKronis = $far ? (int) $far->kronis : 0;
            $farCito = $far ? (int) $far->cito : 0;
            $farPrb = $far ? (int) $far->prb : 0;
            $farDiserahkan = ($far ? (int) $far->diserahkan : 0) + $farResepPulang;
            $farBelumDiserahkan = $far ? (int) $far->belum_diserahkan : 0;
            $farWaktuTunggu = $far && $far->avg_waktu_tunggu !== null ? (float) $far->avg_waktu_tunggu : 0;

            // Gizi
            $giziPorsi = $gizi ? (int) $gizi->total_porsi : 0;
            $giziPas = $gizi ? (int) $gizi->total_pasien : 0;
            $giziPagi = $gizi ? (int) $gizi->pagi : 0;
            $giziSiang = $gizi ? (int) $gizi->siang : 0;
            $giziSore = $gizi ? (int) $gizi->sore : 0;
            $giziRanap = $gizi ? (int) $gizi->ranap : 0;
            $giziRalan = $gizi ? (int) $gizi->ralan : 0;
            $giziPoli = $gizi ? (int) $gizi->poli : 0;
            $giziIgd = $gizi ? (int) $gizi->igd : 0;
            $giziAdime = $adm ? (int) $adm->total_adime : 0;
            $giziPasienAdime = $adm ? (int) $adm->total_pasien_adime : 0;

            // Gabungan 4 Layanan
            $totPem = $labPem + $radPem + $farResep + $giziPorsi;
            $totPas = $labPas + $radPas + $farPas + $giziPas;
            $totPoli = $labPoli + $radPoli + $farPoli + $giziPoli;
            $totIgd = $labIgd + $radIgd + $farIgd + $giziIgd;
            $totRalan = $labRalan + $radRalan + $farRalan + $giziRalan;
            $totRanap = $labRanap + $radRanap + $farRanap + $giziRanap;

            $ratioLab = $totPem > 0 ? round(($labPem / $totPem) * 100, 1) : 0;
            $ratioRad = $totPem > 0 ? round(($radPem / $totPem) * 100, 1) : 0;
            $ratioFar = $totPem > 0 ? round(($farResep / $totPem) * 100, 1) : 0;
            $ratioGizi = $totPem > 0 ? round(($giziPorsi / $totPem) * 100, 1) : 0;

            $totalPelayananList[$m] = $totPem;

            $months[$m] = [
                'bulan' => $m,
                'nama_bulan' => $monthNames[$m],
                'nama_pendek' => $shortMonthNames[$m],
                'laboratorium' => [
                    'pemeriksaan' => $labPem,
                    'pasien' => $labPas,
                    'poli' => $labPoli,
                    'igd' => $labIgd,
                    'ralan' => $labRalan,
                    'ranap' => $labRanap,
                    'pk' => $labPk,
                    'pa' => $labPa,
                    'mb' => $labMb,
                ],
                'radiologi' => [
                    'pemeriksaan' => $radPem,
                    'pasien' => $radPas,
                    'poli' => $radPoli,
                    'igd' => $radIgd,
                    'ralan' => $radRalan,
                    'ranap' => $radRanap,
                    'cr' => $radCr,
                    'ct' => $radCt,
                    'us' => $radUs,
                    'mr' => $radMr,
                    'px' => $radPx,
                    'mg' => $radMg,
                ],
                'farmasi' => [
                    'resep' => $farResep,
                    'pemeriksaan' => $farResep, // Alias untuk konsistensi struktur penunjang
                    'pasien' => $farPas,
                    'poli' => $farPoli,
                    'igd' => $farIgd,
                    'ralan' => $farRalan,
                    'ranap_harian' => $farRanapHarian,
                    'resep_pulang' => $farResepPulang,
                    'ranap' => $farRanap,
                    'biasa' => $farBiasa,
                    'kronis' => $farKronis,
                    'cito' => $farCito,
                    'prb' => $farPrb,
                    'diserahkan' => $farDiserahkan,
                    'belum_diserahkan' => $farBelumDiserahkan,
                    'waktu_tunggu' => $farWaktuTunggu,
                ],
                'gizi' => [
                    'porsi' => $giziPorsi,
                    'pelayanan' => $giziPorsi,
                    'pemeriksaan' => $giziPorsi, // Alias konsistensi
                    'pasien' => $giziPas,
                    'pagi' => $giziPagi,
                    'siang' => $giziSiang,
                    'sore' => $giziSore,
                    'poli' => $giziPoli,
                    'igd' => $giziIgd,
                    'ralan' => $giziRalan,
                    'ranap' => $giziRanap,
                    'asuhan_adime' => $giziAdime,
                    'pasien_adime' => $giziPasienAdime,
                ],
                'gabungan' => [
                    'pemeriksaan' => $totPem,
                    'total_pelayanan' => $totPem,
                    'pasien' => $totPas,
                    'poli' => $totPoli,
                    'igd' => $totIgd,
                    'ralan' => $totRalan,
                    'ranap' => $totRanap,
                    'rasio_lab_persen' => $ratioLab,
                    'rasio_rad_persen' => $ratioRad,
                    'rasio_farmasi_persen' => $ratioFar,
                    'rasio_gizi_persen' => $ratioGizi,
                ],
            ];
        }

        // Akumulasi total setahun
        $totals = [
            'laboratorium' => [
                'pemeriksaan' => array_sum(array_column(array_column($months, 'laboratorium'), 'pemeriksaan')),
                'pasien' => array_sum(array_column(array_column($months, 'laboratorium'), 'pasien')),
                'poli' => array_sum(array_column(array_column($months, 'laboratorium'), 'poli')),
                'igd' => array_sum(array_column(array_column($months, 'laboratorium'), 'igd')),
                'ralan' => array_sum(array_column(array_column($months, 'laboratorium'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'laboratorium'), 'ranap')),
                'pk' => array_sum(array_column(array_column($months, 'laboratorium'), 'pk')),
                'pa' => array_sum(array_column(array_column($months, 'laboratorium'), 'pa')),
                'mb' => array_sum(array_column(array_column($months, 'laboratorium'), 'mb')),
            ],
            'radiologi' => [
                'pemeriksaan' => array_sum(array_column(array_column($months, 'radiologi'), 'pemeriksaan')),
                'pasien' => array_sum(array_column(array_column($months, 'radiologi'), 'pasien')),
                'poli' => array_sum(array_column(array_column($months, 'radiologi'), 'poli')),
                'igd' => array_sum(array_column(array_column($months, 'radiologi'), 'igd')),
                'ralan' => array_sum(array_column(array_column($months, 'radiologi'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'radiologi'), 'ranap')),
                'cr' => array_sum(array_column(array_column($months, 'radiologi'), 'cr')),
                'ct' => array_sum(array_column(array_column($months, 'radiologi'), 'ct')),
                'us' => array_sum(array_column(array_column($months, 'radiologi'), 'us')),
                'mr' => array_sum(array_column(array_column($months, 'radiologi'), 'mr')),
                'px' => array_sum(array_column(array_column($months, 'radiologi'), 'px')),
                'mg' => array_sum(array_column(array_column($months, 'radiologi'), 'mg')),
            ],
            'farmasi' => [
                'resep' => array_sum(array_column(array_column($months, 'farmasi'), 'resep')),
                'pemeriksaan' => array_sum(array_column(array_column($months, 'farmasi'), 'resep')),
                'pasien' => array_sum(array_column(array_column($months, 'farmasi'), 'pasien')),
                'poli' => array_sum(array_column(array_column($months, 'farmasi'), 'poli')),
                'igd' => array_sum(array_column(array_column($months, 'farmasi'), 'igd')),
                'ralan' => array_sum(array_column(array_column($months, 'farmasi'), 'ralan')),
                'ranap_harian' => array_sum(array_column(array_column($months, 'farmasi'), 'ranap_harian')),
                'resep_pulang' => array_sum(array_column(array_column($months, 'farmasi'), 'resep_pulang')),
                'ranap' => array_sum(array_column(array_column($months, 'farmasi'), 'ranap')),
                'biasa' => array_sum(array_column(array_column($months, 'farmasi'), 'biasa')),
                'kronis' => array_sum(array_column(array_column($months, 'farmasi'), 'kronis')),
                'cito' => array_sum(array_column(array_column($months, 'farmasi'), 'cito')),
                'prb' => array_sum(array_column(array_column($months, 'farmasi'), 'prb')),
                'diserahkan' => array_sum(array_column(array_column($months, 'farmasi'), 'diserahkan')),
                'belum_diserahkan' => array_sum(array_column(array_column($months, 'farmasi'), 'belum_diserahkan')),
            ],
            'gizi' => [
                'porsi' => array_sum(array_column(array_column($months, 'gizi'), 'porsi')),
                'pelayanan' => array_sum(array_column(array_column($months, 'gizi'), 'porsi')),
                'pasien' => array_sum(array_column(array_column($months, 'gizi'), 'pasien')),
                'pagi' => array_sum(array_column(array_column($months, 'gizi'), 'pagi')),
                'siang' => array_sum(array_column(array_column($months, 'gizi'), 'siang')),
                'sore' => array_sum(array_column(array_column($months, 'gizi'), 'sore')),
                'poli' => array_sum(array_column(array_column($months, 'gizi'), 'poli')),
                'igd' => array_sum(array_column(array_column($months, 'gizi'), 'igd')),
                'ralan' => array_sum(array_column(array_column($months, 'gizi'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'gizi'), 'ranap')),
                'asuhan_adime' => array_sum(array_column(array_column($months, 'gizi'), 'asuhan_adime')),
                'pasien_adime' => array_sum(array_column(array_column($months, 'gizi'), 'pasien_adime')),
            ],
            'gabungan' => [
                'pemeriksaan' => array_sum(array_column(array_column($months, 'gabungan'), 'pemeriksaan')),
                'total_pelayanan' => array_sum(array_column(array_column($months, 'gabungan'), 'total_pelayanan')),
                'pasien' => array_sum(array_column(array_column($months, 'gabungan'), 'pasien')),
                'poli' => array_sum(array_column(array_column($months, 'gabungan'), 'poli')),
                'igd' => array_sum(array_column(array_column($months, 'gabungan'), 'igd')),
                'ralan' => array_sum(array_column(array_column($months, 'gabungan'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'gabungan'), 'ranap')),
            ],
        ];

        // Hitung rata-rata per bulan
        $averages = [
            'laboratorium' => [
                'pemeriksaan' => round($totals['laboratorium']['pemeriksaan'] / 12, 1),
                'pasien' => round($totals['laboratorium']['pasien'] / 12, 1),
                'poli' => round($totals['laboratorium']['poli'] / 12, 1),
                'igd' => round($totals['laboratorium']['igd'] / 12, 1),
                'ralan' => round($totals['laboratorium']['ralan'] / 12, 1),
                'ranap' => round($totals['laboratorium']['ranap'] / 12, 1),
            ],
            'radiologi' => [
                'pemeriksaan' => round($totals['radiologi']['pemeriksaan'] / 12, 1),
                'pasien' => round($totals['radiologi']['pasien'] / 12, 1),
                'poli' => round($totals['radiologi']['poli'] / 12, 1),
                'igd' => round($totals['radiologi']['igd'] / 12, 1),
                'ralan' => round($totals['radiologi']['ralan'] / 12, 1),
                'ranap' => round($totals['radiologi']['ranap'] / 12, 1),
            ],
            'farmasi' => [
                'resep' => round($totals['farmasi']['resep'] / 12, 1),
                'pemeriksaan' => round($totals['farmasi']['resep'] / 12, 1),
                'pasien' => round($totals['farmasi']['pasien'] / 12, 1),
                'poli' => round($totals['farmasi']['poli'] / 12, 1),
                'igd' => round($totals['farmasi']['igd'] / 12, 1),
                'ralan' => round($totals['farmasi']['ralan'] / 12, 1),
                'ranap_harian' => round($totals['farmasi']['ranap_harian'] / 12, 1),
                'resep_pulang' => round($totals['farmasi']['resep_pulang'] / 12, 1),
                'ranap' => round($totals['farmasi']['ranap'] / 12, 1),
                'biasa' => round($totals['farmasi']['biasa'] / 12, 1),
                'kronis' => round($totals['farmasi']['kronis'] / 12, 1),
                'cito' => round($totals['farmasi']['cito'] / 12, 1),
                'prb' => round($totals['farmasi']['prb'] / 12, 1),
                'diserahkan' => round($totals['farmasi']['diserahkan'] / 12, 1),
                'belum_diserahkan' => round($totals['farmasi']['belum_diserahkan'] / 12, 1),
            ],
            'gizi' => [
                'porsi' => round($totals['gizi']['porsi'] / 12, 1),
                'pelayanan' => round($totals['gizi']['porsi'] / 12, 1),
                'pasien' => round($totals['gizi']['pasien'] / 12, 1),
                'pagi' => round($totals['gizi']['pagi'] / 12, 1),
                'siang' => round($totals['gizi']['siang'] / 12, 1),
                'sore' => round($totals['gizi']['sore'] / 12, 1),
                'poli' => round($totals['gizi']['poli'] / 12, 1),
                'igd' => round($totals['gizi']['igd'] / 12, 1),
                'ralan' => round($totals['gizi']['ralan'] / 12, 1),
                'ranap' => round($totals['gizi']['ranap'] / 12, 1),
                'asuhan_adime' => round($totals['gizi']['asuhan_adime'] / 12, 1),
                'pasien_adime' => round($totals['gizi']['pasien_adime'] / 12, 1),
            ],
            'gabungan' => [
                'pemeriksaan' => round($totals['gabungan']['pemeriksaan'] / 12, 1),
                'total_pelayanan' => round($totals['gabungan']['total_pelayanan'] / 12, 1),
                'pasien' => round($totals['gabungan']['pasien'] / 12, 1),
                'poli' => round($totals['gabungan']['poli'] / 12, 1),
                'igd' => round($totals['gabungan']['igd'] / 12, 1),
                'ralan' => round($totals['gabungan']['ralan'] / 12, 1),
                'ranap' => round($totals['gabungan']['ranap'] / 12, 1),
            ],
        ];

        // Cari bulan puncak (Peak Month)
        $peakMonthNum = 1;
        $maxVal = -1;
        foreach ($totalPelayananList as $mNum => $val) {
            if ($val > $maxVal) {
                $maxVal = $val;
                $peakMonthNum = $mNum;
            }
        }

        // Rasio kontribusi tahunan
        $totAncillary = $totals['gabungan']['total_pelayanan'];
        $contribLab = $totAncillary > 0 ? round(($totals['laboratorium']['pemeriksaan'] / $totAncillary) * 100, 1) : 0;
        $contribRad = $totAncillary > 0 ? round(($totals['radiologi']['pemeriksaan'] / $totAncillary) * 100, 1) : 0;
        $contribFar = $totAncillary > 0 ? round(($totals['farmasi']['resep'] / $totAncillary) * 100, 1) : 0;
        $contribGizi = $totAncillary > 0 ? round(($totals['gizi']['porsi'] / $totAncillary) * 100, 1) : 0;

        return [
            'year' => $year,
            'months' => $months,
            'totals' => $totals,
            'averages' => $averages,
            'top_diets' => $topDiets,
            'top_bangsal' => $topDietBangsal,
            'summary' => [
                'total_pemeriksaan' => $totals['gabungan']['pemeriksaan'],
                'total_pelayanan' => $totals['gabungan']['total_pelayanan'],
                'total_lab' => $totals['laboratorium']['pemeriksaan'],
                'total_rad' => $totals['radiologi']['pemeriksaan'],
                'total_farmasi' => $totals['farmasi']['resep'],
                'total_gizi' => $totals['gizi']['porsi'],
                'total_pasien' => $totals['gabungan']['pasien'],
                'total_poli' => $totals['gabungan']['poli'],
                'total_igd' => $totals['gabungan']['igd'],
                'total_ralan' => $totals['gabungan']['ralan'],
                'total_ranap' => $totals['gabungan']['ranap'],
                'avg_per_month' => $averages['gabungan']['pemeriksaan'],
                'peak_month_name' => $monthNames[$peakMonthNum] ?? '-',
                'peak_month_value' => $maxVal > 0 ? $maxVal : 0,
                'contrib_lab_percent' => $contribLab,
                'contrib_rad_percent' => $contribRad,
                'contrib_farmasi_percent' => $contribFar,
                'contrib_gizi_percent' => $contribGizi,
            ],
            'charts' => self::buildChartPayload($months, $totals, $topDiets),
        ];
    }

    /**
     * Membangun payload dataset untuk visualisasi grafik tren bulanan & distribusi.
     */
    protected static function buildChartPayload(array $months, array $totals, array $topDiets = []): array
    {
        $labels = array_map(fn($m) => $m['nama_pendek'], $months);

        $dataTot = array_map(fn($m) => $m['gabungan']['total_pelayanan'], $months);
        $dataLab = array_map(fn($m) => $m['laboratorium']['pemeriksaan'], $months);
        $dataRad = array_map(fn($m) => $m['radiologi']['pemeriksaan'], $months);
        $dataFar = array_map(fn($m) => $m['farmasi']['resep'], $months);
        $dataGizi = array_map(fn($m) => $m['gizi']['porsi'], $months);

        $dataPoli = array_map(fn($m) => $m['gabungan']['poli'], $months);
        $dataIgd = array_map(fn($m) => $m['gabungan']['igd'], $months);
        $dataRalan = array_map(fn($m) => $m['gabungan']['ralan'], $months);
        $dataRanap = array_map(fn($m) => $m['gabungan']['ranap'], $months);

        return [
            'trend_monthly' => [
                'labels' => array_values($labels),
                'datasets' => [
                    [
                        'label' => 'Total Penunjang',
                        'data' => array_values($dataTot),
                        'borderColor' => '#059669',
                        'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                        'fill' => true,
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Farmasi',
                        'data' => array_values($dataFar),
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'transparent',
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Laboratorium',
                        'data' => array_values($dataLab),
                        'borderColor' => '#0284c7',
                        'backgroundColor' => 'transparent',
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Radiologi',
                        'data' => array_values($dataRad),
                        'borderColor' => '#8b5cf6',
                        'backgroundColor' => 'transparent',
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Gizi (Porsi)',
                        'data' => array_values($dataGizi),
                        'borderColor' => '#f59e0b',
                        'backgroundColor' => 'transparent',
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Akumulasi Ralan',
                        'data' => array_values($dataRalan),
                        'borderColor' => '#0ea5e9',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [3, 3],
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Rawat Inap (Ranap)',
                        'data' => array_values($dataRanap),
                        'borderColor' => '#d97706',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [4, 4],
                        'tension' => 0.35,
                    ],
                ],
            ],
            'service_ratio' => [
                'labels' => ['Laboratorium', 'Radiologi', 'Farmasi', 'Gizi'],
                'datasets' => [[
                    'data' => [
                        (int) $totals['laboratorium']['pemeriksaan'],
                        (int) $totals['radiologi']['pemeriksaan'],
                        (int) $totals['farmasi']['resep'],
                        (int) $totals['gizi']['porsi'],
                    ],
                    'backgroundColor' => ['#0284c7', '#8b5cf6', '#10b981', '#f59e0b'],
                ]],
            ],
            'care_setting' => [
                'labels' => ['Poli (Rawat Jalan)', 'IGD (Gawat Darurat)', 'Rawat Inap (Ranap)'],
                'datasets' => [[
                    'data' => [
                        (int) $totals['gabungan']['poli'],
                        (int) $totals['gabungan']['igd'],
                        (int) $totals['gabungan']['ranap'],
                    ],
                    'backgroundColor' => ['#8b5cf6', '#f43f5e', '#f59e0b'],
                ]],
            ],
            'rad_modality' => [
                'labels' => ['CR / X-Ray', 'CT Scan', 'USG', 'MRI', 'Panoramic', 'Mammography'],
                'datasets' => [[
                    'label' => 'Pemeriksaan Radiologi',
                    'data' => [
                        (int) $totals['radiologi']['cr'],
                        (int) $totals['radiologi']['ct'],
                        (int) $totals['radiologi']['us'],
                        (int) $totals['radiologi']['mr'],
                        (int) $totals['radiologi']['px'],
                        (int) $totals['radiologi']['mg'],
                    ],
                    'backgroundColor' => ['#06b6d4', '#8b5cf6', '#10b981', '#f59e0b', '#ec4899', '#3b82f6'],
                    'borderRadius' => 6,
                ]],
            ],
            'lab_kategori' => [
                'labels' => ['Patologi Klinik (PK)', 'Patologi Anatomi (PA)', 'Mikrobiologi (MB)'],
                'datasets' => [[
                    'label' => 'Pemeriksaan Lab',
                    'data' => [
                        (int) $totals['laboratorium']['pk'],
                        (int) $totals['laboratorium']['pa'],
                        (int) $totals['laboratorium']['mb'],
                    ],
                    'backgroundColor' => ['#059669', '#0284c7', '#f59e0b'],
                    'borderRadius' => 6,
                ]],
            ],
            'farmasi_jenis' => [
                'labels' => ['Biasa (Reguler)', 'Kronis (Rutin)', 'CITO (Darurat)', 'PRB (Rujuk Balik)'],
                'datasets' => [[
                    'label' => 'Lembar Resep',
                    'data' => [
                        (int) $totals['farmasi']['biasa'],
                        (int) $totals['farmasi']['kronis'],
                        (int) $totals['farmasi']['cito'],
                        (int) $totals['farmasi']['prb'],
                    ],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#ef4444', '#8b5cf6'],
                    'borderRadius' => 6,
                ]],
            ],
            'gizi_waktu' => [
                'labels' => ['Sarapan Pagi', 'Makan Siang', 'Sore / Malam'],
                'datasets' => [[
                    'label' => 'Porsi Makanan Disajikan',
                    'data' => [
                        (int) $totals['gizi']['pagi'],
                        (int) $totals['gizi']['siang'],
                        (int) $totals['gizi']['sore'],
                    ],
                    'backgroundColor' => ['#f59e0b', '#06b6d4', '#8b5cf6'],
                    'borderRadius' => 6,
                ]],
            ],
            'top_diets' => [
                'labels' => array_column($topDiets, 'nama_diet'),
                'datasets' => [[
                    'label' => 'Distribusi Jenis Diet',
                    'data' => array_column($topDiets, 'total_porsi'),
                    'backgroundColor' => ['#10b981', '#06b6d4', '#f59e0b', '#8b5cf6', '#ec4899', '#3b82f6', '#64748b', '#14b8a6'],
                    'borderRadius' => 6,
                ]],
            ],
        ];
    }

    /**
     * Mengambil ringkasan komparatif layanan penunjang (Farmasi, Laboratorium, Radiologi, dan Gizi)
     * dalam rentang tanggal tertentu.
     */
    public static function getSummary(string $startDate, string $endDate): array
    {
        $conn = DB::connection(self::KONEKSI);

        // 1. Laboratorium
        $labSummary = $conn->table('periksa_lab as pl')
            ->leftJoin('reg_periksa as rp', 'pl.no_rawat', '=', 'rp.no_rawat')
            ->whereBetween('pl.tgl_periksa', [$startDate, $endDate])
            ->selectRaw("
                count(*) as total_pemeriksaan,
                count(distinct pl.no_rawat) as total_pasien,
                sum(case when pl.status = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when pl.status = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when pl.status = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when pl.status = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when pl.kategori = 'PK' then 1 else 0 end) as pk,
                sum(case when pl.kategori = 'PA' then 1 else 0 end) as pa,
                sum(case when pl.kategori = 'MB' then 1 else 0 end) as mb
            ")
            ->first();

        $topLabTests = $conn->table('periksa_lab as pl')
            ->join('jns_perawatan_lab as jpl', 'pl.kd_jenis_prw', '=', 'jpl.kd_jenis_prw')
            ->whereBetween('pl.tgl_periksa', [$startDate, $endDate])
            ->selectRaw("jpl.nm_perawatan, count(*) as total")
            ->groupBy('jpl.nm_perawatan')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // 2. Radiologi
        $radSummary = $conn->table('periksa_radiologi as pr')
            ->leftJoin('reg_periksa as rp', 'pr.no_rawat', '=', 'rp.no_rawat')
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->leftJoin('mapping_radiologi_modality as mrm', 'pr.kd_jenis_prw', '=', 'mrm.kd_jenis_prw')
            ->whereBetween('pr.tgl_periksa', [$startDate, $endDate])
            ->selectRaw("
                count(*) as total_pemeriksaan,
                count(distinct pr.no_rawat) as total_pasien,
                sum(case when pr.status = 'Ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when pr.status = 'Ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when pr.status = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when pr.status = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'CR' THEN 1 ELSE 0 END) as cr,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'CT' THEN 1 ELSE 0 END) as ct,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'US' THEN 1 ELSE 0 END) as us,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'MR' THEN 1 ELSE 0 END) as mr,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'PX' THEN 1 ELSE 0 END) as px,
                sum(case when COALESCE(mrm.modality_code, CASE WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT' WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US' WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR' WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX' WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG' ELSE 'CR' END) = 'MG' THEN 1 ELSE 0 END) as mg
            ")
            ->first();

        $topRadTests = $conn->table('periksa_radiologi as pr')
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->whereBetween('pr.tgl_periksa', [$startDate, $endDate])
            ->selectRaw("jpr.nm_perawatan, count(*) as total")
            ->groupBy('jpr.nm_perawatan')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // 3. Farmasi
        $farmasiSummary = $conn->table('resep_obat as ro')
            ->leftJoin('reg_periksa as rp', 'ro.no_rawat', '=', 'rp.no_rawat')
            ->whereBetween('ro.tgl_perawatan', [$startDate, $endDate])
            ->selectRaw("
                count(*) as total_resep,
                count(distinct ro.no_rawat) as total_pasien,
                sum(case when ro.status = 'ralan' and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when ro.status = 'ralan' and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd,
                sum(case when ro.status = 'ralan' then 1 else 0 end) as ralan,
                sum(case when ro.status = 'ranap' then 1 else 0 end) as ranap_harian,
                sum(case when ro.jenis_resep = 'Biasa' then 1 else 0 end) as biasa,
                sum(case when ro.jenis_resep = 'Kronis' then 1 else 0 end) as kronis,
                sum(case when ro.jenis_resep = 'CITO' then 1 else 0 end) as cito,
                sum(case when ro.jenis_resep = 'PRB' then 1 else 0 end) as prb,
                sum(case when ro.tgl_penyerahan is not null and ro.tgl_penyerahan != '0000-00-00' then 1 else 0 end) as diserahkan,
                sum(case when ro.tgl_penyerahan is null or ro.tgl_penyerahan = '0000-00-00' then 1 else 0 end) as belum_diserahkan,
                ROUND(AVG(
                    CASE 
                        WHEN ro.tgl_peresepan is not null AND ro.jam_peresepan is not null 
                             AND ro.tgl_penyerahan is not null AND ro.jam_penyerahan is not null
                             AND ro.tgl_penyerahan != '0000-00-00' AND ro.tgl_peresepan != '0000-00-00'
                             AND TIMESTAMPDIFF(MINUTE, CONCAT(ro.tgl_peresepan, ' ', ro.jam_peresepan), CONCAT(ro.tgl_penyerahan, ' ', ro.jam_penyerahan)) BETWEEN 0 AND 240
                        THEN TIMESTAMPDIFF(MINUTE, CONCAT(ro.tgl_peresepan, ' ', ro.jam_peresepan), CONCAT(ro.tgl_penyerahan, ' ', ro.jam_penyerahan))
                        ELSE NULL 
                    END
                ), 1) as avg_waktu_tunggu
            ")
            ->first();

        $pulangSummary = $conn->table('resep_pulang as rp')
            ->whereBetween('rp.tanggal', [$startDate, $endDate])
            ->selectRaw("
                count(distinct concat(rp.no_rawat, ' ', rp.tanggal, ' ', rp.jam)) as total_resep_pulang,
                count(distinct rp.no_rawat) as total_pasien_pulang
            ")
            ->first();

        $farPoli = (int) ($farmasiSummary->poli ?? 0);
        $farIgd = (int) ($farmasiSummary->igd ?? 0);
        $farRalan = (int) ($farmasiSummary->ralan ?? 0);
        $farRanapHarian = (int) ($farmasiSummary->ranap_harian ?? 0);
        $farResepPulang = (int) ($pulangSummary->total_resep_pulang ?? 0);
        $farRanap = $farRanapHarian + $farResepPulang;
        $farTotalResep = $farRalan + $farRanap;
        $farDiserahkan = ((int) ($farmasiSummary->diserahkan ?? 0)) + $farResepPulang;

        // 4. Gizi
        $dietSummary = $conn->table('detail_beri_diet as dbd')
            ->leftJoin('reg_periksa as rp', 'dbd.no_rawat', '=', 'rp.no_rawat')
            ->whereBetween('dbd.tanggal', [$startDate, $endDate])
            ->selectRaw("
                count(*) as total_porsi,
                count(distinct dbd.no_rawat) as total_pasien,
                sum(case when dbd.waktu like 'Pagi%' then 1 else 0 end) as pagi,
                sum(case when dbd.waktu like 'Siang%' then 1 else 0 end) as siang,
                sum(case when dbd.waktu like 'Sore%' or dbd.waktu like 'Malam%' then 1 else 0 end) as sore,
                sum(case when rp.status_lanjut = 'Ranap' or dbd.kd_kamar is not null then 1 else 0 end) as ranap,
                sum(case when rp.status_lanjut = 'Ralan' and dbd.kd_kamar is null then 1 else 0 end) as ralan,
                sum(case when rp.status_lanjut = 'Ralan' and dbd.kd_kamar is null and (rp.kd_poli != 'IGDK' or rp.kd_poli is null) then 1 else 0 end) as poli,
                sum(case when rp.status_lanjut = 'Ralan' and dbd.kd_kamar is null and rp.kd_poli = 'IGDK' then 1 else 0 end) as igd
            ")
            ->first();

        $adimeSummary = $conn->table('catatan_adime_gizi')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw("count(*) as total_adime, count(distinct no_rawat) as total_pasien_adime")
            ->first();

        $topDiets = $conn->table('detail_beri_diet as dbd')
            ->join('diet as d', 'dbd.kd_diet', '=', 'd.kd_diet')
            ->whereBetween('dbd.tanggal', [$startDate, $endDate])
            ->selectRaw("d.nama_diet, count(*) as total_porsi")
            ->groupBy('d.nama_diet')
            ->orderByDesc('total_porsi')
            ->take(6)
            ->get();

        $topDietBangsal = $conn->table('detail_beri_diet as dbd')
            ->leftJoin('kamar as k', 'dbd.kd_kamar', '=', 'k.kd_kamar')
            ->leftJoin('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
            ->whereBetween('dbd.tanggal', [$startDate, $endDate])
            ->selectRaw("coalesce(b.nm_bangsal, dbd.kd_kamar) as nm_bangsal, count(*) as total_porsi")
            ->groupBy('nm_bangsal')
            ->orderByDesc('total_porsi')
            ->take(6)
            ->get();

        // 5. Tren Harian (Daily Trend for all 4 services)
        $dailyLab = $conn->table('periksa_lab')->whereBetween('tgl_periksa', [$startDate, $endDate])->selectRaw('tgl_periksa as tgl, count(*) as c')->groupBy('tgl')->get()->keyBy('tgl');
        $dailyRad = $conn->table('periksa_radiologi')->whereBetween('tgl_periksa', [$startDate, $endDate])->selectRaw('tgl_periksa as tgl, count(*) as c')->groupBy('tgl')->get()->keyBy('tgl');
        $dailyFar = $conn->table('resep_obat')->whereBetween('tgl_perawatan', [$startDate, $endDate])->selectRaw('tgl_perawatan as tgl, count(*) as c')->groupBy('tgl')->get()->keyBy('tgl');
        $dailyPul = $conn->table('resep_pulang')->whereBetween('tanggal', [$startDate, $endDate])->selectRaw('tanggal as tgl, count(distinct concat(no_rawat, " ", tanggal, " ", jam)) as c')->groupBy('tgl')->get()->keyBy('tgl');
        $dailyGizi = $conn->table('detail_beri_diet')->whereBetween('tanggal', [$startDate, $endDate])->selectRaw('tanggal as tgl, count(*) as c')->groupBy('tgl')->get()->keyBy('tgl');

        $periodRange = CarbonPeriod::create($startDate, $endDate);
        $trendLabels = [];
        $trendTotal = [];
        $trendFar = [];
        $trendLab = [];
        $trendRad = [];
        $trendGizi = [];

        foreach ($periodRange as $date) {
            $dStr = $date->format('Y-m-d');
            $dLabel = $date->format('d/m');

            $cLab = (int) ($dailyLab->get($dStr)->c ?? 0);
            $cRad = (int) ($dailyRad->get($dStr)->c ?? 0);
            $cFar = ((int) ($dailyFar->get($dStr)->c ?? 0)) + ((int) ($dailyPul->get($dStr)->c ?? 0));
            $cGizi = (int) ($dailyGizi->get($dStr)->c ?? 0);
            $cTot = $cLab + $cRad + $cFar + $cGizi;

            $trendLabels[] = $dLabel;
            $trendTotal[] = $cTot;
            $trendFar[] = $cFar;
            $trendLab[] = $cLab;
            $trendRad[] = $cRad;
            $trendGizi[] = $cGizi;
        }

        // Totals & Rasio
        $totLab = (int) ($labSummary->total_pemeriksaan ?? 0);
        $totRad = (int) ($radSummary->total_pemeriksaan ?? 0);
        $totFar = $farTotalResep;
        $totGizi = (int) ($dietSummary->total_porsi ?? 0);
        $totAll = $totLab + $totRad + $totFar + $totGizi;

        $totPasien = ((int) ($labSummary->total_pasien ?? 0)) +
            ((int) ($radSummary->total_pasien ?? 0)) +
            ((int) ($farmasiSummary->total_pasien ?? 0)) +
            ((int) ($dietSummary->total_pasien ?? 0));

        $totPoli = ((int) ($labSummary->poli ?? 0)) + ((int) ($radSummary->poli ?? 0)) + $farPoli + ((int) ($dietSummary->poli ?? 0));
        $totIgd = ((int) ($labSummary->igd ?? 0)) + ((int) ($radSummary->igd ?? 0)) + $farIgd + ((int) ($dietSummary->igd ?? 0));
        $totRalan = $totPoli + $totIgd;
        $totRanap = ((int) ($labSummary->ranap ?? 0)) + ((int) ($radSummary->ranap ?? 0)) + $farRanap + ((int) ($dietSummary->ranap ?? 0));

        $pctFar = $totAll > 0 ? round(($totFar / $totAll) * 100, 1) : 0;
        $pctLab = $totAll > 0 ? round(($totLab / $totAll) * 100, 1) : 0;
        $pctRad = $totAll > 0 ? round(($totRad / $totAll) * 100, 1) : 0;
        $pctGizi = $totAll > 0 ? round(($totGizi / $totAll) * 100, 1) : 0;

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
            ],
            'summary' => [
                'total_pelayanan' => $totAll,
                'total_pasien' => $totPasien,
                'total_farmasi' => $totFar,
                'total_lab' => $totLab,
                'total_rad' => $totRad,
                'total_gizi' => $totGizi,
                'total_poli' => $totPoli,
                'total_igd' => $totIgd,
                'total_ralan' => $totRalan,
                'total_ranap' => $totRanap,
                'pct_farmasi' => $pctFar,
                'pct_lab' => $pctLab,
                'pct_rad' => $pctRad,
                'pct_gizi' => $pctGizi,
            ],
            'farmasi' => [
                'total' => $totFar,
                'pasien' => (int) ($farmasiSummary->total_pasien ?? 0),
                'poli' => $farPoli,
                'igd' => $farIgd,
                'ralan' => $farRalan,
                'ranap_harian' => $farRanapHarian,
                'resep_pulang' => $farResepPulang,
                'ranap' => $farRanap,
                'biasa' => (int) ($farmasiSummary->biasa ?? 0),
                'kronis' => (int) ($farmasiSummary->kronis ?? 0),
                'cito' => (int) ($farmasiSummary->cito ?? 0),
                'prb' => (int) ($farmasiSummary->prb ?? 0),
                'diserahkan' => $farDiserahkan,
                'belum_diserahkan' => (int) ($farmasiSummary->belum_diserahkan ?? 0),
                'waktu_tunggu' => (float) ($farmasiSummary->avg_waktu_tunggu ?? 0),
            ],
            'laboratorium' => [
                'total' => $totLab,
                'pasien' => (int) ($labSummary->total_pasien ?? 0),
                'poli' => (int) ($labSummary->poli ?? 0),
                'igd' => (int) ($labSummary->igd ?? 0),
                'ralan' => (int) ($labSummary->ralan ?? 0),
                'ranap' => (int) ($labSummary->ranap ?? 0),
                'pk' => (int) ($labSummary->pk ?? 0),
                'pa' => (int) ($labSummary->pa ?? 0),
                'mb' => (int) ($labSummary->mb ?? 0),
                'top_tests' => $topLabTests,
            ],
            'radiologi' => [
                'total' => $totRad,
                'pasien' => (int) ($radSummary->total_pasien ?? 0),
                'poli' => (int) ($radSummary->poli ?? 0),
                'igd' => (int) ($radSummary->igd ?? 0),
                'ralan' => (int) ($radSummary->ralan ?? 0),
                'ranap' => (int) ($radSummary->ranap ?? 0),
                'cr' => (int) ($radSummary->cr ?? 0),
                'ct' => (int) ($radSummary->ct ?? 0),
                'us' => (int) ($radSummary->us ?? 0),
                'mr' => (int) ($radSummary->mr ?? 0),
                'px' => (int) ($radSummary->px ?? 0),
                'mg' => (int) ($radSummary->mg ?? 0),
                'top_tests' => $topRadTests,
            ],
            'gizi' => [
                'total' => $totGizi,
                'pasien' => (int) ($dietSummary->total_pasien ?? 0),
                'pagi' => (int) ($dietSummary->pagi ?? 0),
                'siang' => (int) ($dietSummary->siang ?? 0),
                'sore' => (int) ($dietSummary->sore ?? 0),
                'ranap' => (int) ($dietSummary->ranap ?? 0),
                'ralan' => (int) ($dietSummary->ralan ?? 0),
                'poli' => (int) ($dietSummary->poli ?? 0),
                'igd' => (int) ($dietSummary->igd ?? 0),
                'asuhan_adime' => (int) ($adimeSummary->total_adime ?? 0),
                'pasien_adime' => (int) ($adimeSummary->total_pasien_adime ?? 0),
                'top_diets' => $topDiets,
                'top_bangsal' => $topDietBangsal,
            ],
            'charts' => [
                'trend' => [
                    'labels' => $trendLabels,
                    'datasets' => [
                        [
                            'label' => 'Total Penunjang',
                            'data' => $trendTotal,
                            'borderColor' => '#059669',
                            'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                            'fill' => true,
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Farmasi',
                            'data' => $trendFar,
                            'borderColor' => '#10b981',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Laboratorium',
                            'data' => $trendLab,
                            'borderColor' => '#0284c7',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Radiologi',
                            'data' => $trendRad,
                            'borderColor' => '#8b5cf6',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                        [
                            'label' => 'Gizi (Porsi)',
                            'data' => $trendGizi,
                            'borderColor' => '#f59e0b',
                            'backgroundColor' => 'transparent',
                            'tension' => 0.35,
                        ],
                    ],
                ],
                'proportion' => [
                    'labels' => ['Farmasi', 'Laboratorium', 'Radiologi', 'Gizi'],
                    'datasets' => [[
                        'data' => [$totFar, $totLab, $totRad, $totGizi],
                        'backgroundColor' => ['#10b981', '#0284c7', '#8b5cf6', '#f59e0b'],
                    ]],
                ],
                'care_setting' => [
                    'labels' => ['Poli (Rawat Jalan)', 'IGD (Gawat Darurat)', 'Rawat Inap (Ranap)'],
                    'datasets' => [[
                        'data' => [$totPoli, $totIgd, $totRanap],
                        'backgroundColor' => ['#0284c7', '#ef4444', '#f59e0b'],
                    ]],
                ],
            ],
        ];
    }
}
