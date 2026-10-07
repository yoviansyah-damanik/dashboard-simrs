<?php

namespace App\Repository;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

interface AncillaryReportInterface {}

class AncillaryReportRepository implements AncillaryReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Mengambil matriks indikator tahunan layanan penunjang (Laboratorium dan Radiologi).
     */
    public static function getYearlyMatrix(int|string|null $year = null): array
    {
        $year = (int) ($year ?: date('Y'));

        // Query agregat per bulan untuk Laboratorium
        $labRows = DB::connection(self::KONEKSI)->table('periksa_lab as pl')
            ->whereYear('pl.tgl_periksa', $year)
            ->selectRaw("
                MONTH(pl.tgl_periksa) as bulan,
                count(*) as total_pemeriksaan,
                count(distinct pl.no_rawat) as total_pasien,
                sum(case when pl.status = 'Ralan' then 1 else 0 end) as ralan,
                sum(case when pl.status = 'Ranap' then 1 else 0 end) as ranap,
                sum(case when pl.kategori = 'PK' then 1 else 0 end) as pk,
                sum(case when pl.kategori = 'PA' then 1 else 0 end) as pa,
                sum(case when pl.kategori = 'MB' then 1 else 0 end) as mb
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Query agregat per bulan untuk Radiologi
        $radRows = DB::connection(self::KONEKSI)->table('periksa_radiologi as pr')
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->leftJoin('mapping_radiologi_modality as mrm', 'pr.kd_jenis_prw', '=', 'mrm.kd_jenis_prw')
            ->whereYear('pr.tgl_periksa', $year)
            ->selectRaw("
                MONTH(pr.tgl_periksa) as bulan,
                count(*) as total_pemeriksaan,
                count(distinct pr.no_rawat) as total_pasien,
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
        $totalPemeriksaanList = [];

        for ($m = 1; $m <= 12; $m++) {
            $lab = $labRows->get($m);
            $rad = $radRows->get($m);

            $labPem = $lab ? (int) $lab->total_pemeriksaan : 0;
            $labPas = $lab ? (int) $lab->total_pasien : 0;
            $labRalan = $lab ? (int) $lab->ralan : 0;
            $labRanap = $lab ? (int) $lab->ranap : 0;
            $labPk = $lab ? (int) $lab->pk : 0;
            $labPa = $lab ? (int) $lab->pa : 0;
            $labMb = $lab ? (int) $lab->mb : 0;

            $radPem = $rad ? (int) $rad->total_pemeriksaan : 0;
            $radPas = $rad ? (int) $rad->total_pasien : 0;
            $radRalan = $rad ? (int) $rad->ralan : 0;
            $radRanap = $rad ? (int) $rad->ranap : 0;
            $radCr = $rad ? (int) $rad->cr : 0;
            $radCt = $rad ? (int) $rad->ct : 0;
            $radUs = $rad ? (int) $rad->us : 0;
            $radMr = $rad ? (int) $rad->mr : 0;
            $radPx = $rad ? (int) $rad->px : 0;
            $radMg = $rad ? (int) $rad->mg : 0;

            $totPem = $labPem + $radPem;
            $totPas = $labPas + $radPas;
            $totRalan = $labRalan + $radRalan;
            $totRanap = $labRanap + $radRanap;

            $ratioLab = $totPem > 0 ? round(($labPem / $totPem) * 100, 1) : 0;
            $ratioRad = $totPem > 0 ? round(($radPem / $totPem) * 100, 1) : 0;

            $totalPemeriksaanList[$m] = $totPem;

            $months[$m] = [
                'bulan' => $m,
                'nama_bulan' => $monthNames[$m],
                'nama_pendek' => $shortMonthNames[$m],
                'laboratorium' => [
                    'pemeriksaan' => $labPem,
                    'pasien' => $labPas,
                    'ralan' => $labRalan,
                    'ranap' => $labRanap,
                    'pk' => $labPk,
                    'pa' => $labPa,
                    'mb' => $labMb,
                ],
                'radiologi' => [
                    'pemeriksaan' => $radPem,
                    'pasien' => $radPas,
                    'ralan' => $radRalan,
                    'ranap' => $radRanap,
                    'cr' => $radCr,
                    'ct' => $radCt,
                    'us' => $radUs,
                    'mr' => $radMr,
                    'px' => $radPx,
                    'mg' => $radMg,
                ],
                'gabungan' => [
                    'pemeriksaan' => $totPem,
                    'pasien' => $totPas,
                    'ralan' => $totRalan,
                    'ranap' => $totRanap,
                    'rasio_lab_persen' => $ratioLab,
                    'rasio_rad_persen' => $ratioRad,
                ],
            ];
        }

        // Akumulasi total setahun
        $totals = [
            'laboratorium' => [
                'pemeriksaan' => array_sum(array_column(array_column($months, 'laboratorium'), 'pemeriksaan')),
                'pasien' => array_sum(array_column(array_column($months, 'laboratorium'), 'pasien')),
                'ralan' => array_sum(array_column(array_column($months, 'laboratorium'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'laboratorium'), 'ranap')),
                'pk' => array_sum(array_column(array_column($months, 'laboratorium'), 'pk')),
                'pa' => array_sum(array_column(array_column($months, 'laboratorium'), 'pa')),
                'mb' => array_sum(array_column(array_column($months, 'laboratorium'), 'mb')),
            ],
            'radiologi' => [
                'pemeriksaan' => array_sum(array_column(array_column($months, 'radiologi'), 'pemeriksaan')),
                'pasien' => array_sum(array_column(array_column($months, 'radiologi'), 'pasien')),
                'ralan' => array_sum(array_column(array_column($months, 'radiologi'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'radiologi'), 'ranap')),
                'cr' => array_sum(array_column(array_column($months, 'radiologi'), 'cr')),
                'ct' => array_sum(array_column(array_column($months, 'radiologi'), 'ct')),
                'us' => array_sum(array_column(array_column($months, 'radiologi'), 'us')),
                'mr' => array_sum(array_column(array_column($months, 'radiologi'), 'mr')),
                'px' => array_sum(array_column(array_column($months, 'radiologi'), 'px')),
                'mg' => array_sum(array_column(array_column($months, 'radiologi'), 'mg')),
            ],
            'gabungan' => [
                'pemeriksaan' => array_sum(array_column(array_column($months, 'gabungan'), 'pemeriksaan')),
                'pasien' => array_sum(array_column(array_column($months, 'gabungan'), 'pasien')),
                'ralan' => array_sum(array_column(array_column($months, 'gabungan'), 'ralan')),
                'ranap' => array_sum(array_column(array_column($months, 'gabungan'), 'ranap')),
            ],
        ];

        // Hitung rata-rata per bulan
        $averages = [
            'laboratorium' => [
                'pemeriksaan' => round($totals['laboratorium']['pemeriksaan'] / 12, 1),
                'pasien' => round($totals['laboratorium']['pasien'] / 12, 1),
                'ralan' => round($totals['laboratorium']['ralan'] / 12, 1),
                'ranap' => round($totals['laboratorium']['ranap'] / 12, 1),
            ],
            'radiologi' => [
                'pemeriksaan' => round($totals['radiologi']['pemeriksaan'] / 12, 1),
                'pasien' => round($totals['radiologi']['pasien'] / 12, 1),
                'ralan' => round($totals['radiologi']['ralan'] / 12, 1),
                'ranap' => round($totals['radiologi']['ranap'] / 12, 1),
            ],
            'gabungan' => [
                'pemeriksaan' => round($totals['gabungan']['pemeriksaan'] / 12, 1),
                'pasien' => round($totals['gabungan']['pasien'] / 12, 1),
                'ralan' => round($totals['gabungan']['ralan'] / 12, 1),
                'ranap' => round($totals['gabungan']['ranap'] / 12, 1),
            ],
        ];

        // Cari bulan puncak (Peak Month)
        $peakMonthNum = 1;
        $maxVal = -1;
        foreach ($totalPemeriksaanList as $mNum => $val) {
            if ($val > $maxVal) {
                $maxVal = $val;
                $peakMonthNum = $mNum;
            }
        }

        // Rasio kontribusi tahunan
        $totAncillary = $totals['gabungan']['pemeriksaan'];
        $contribLab = $totAncillary > 0 ? round(($totals['laboratorium']['pemeriksaan'] / $totAncillary) * 100, 1) : 0;
        $contribRad = $totAncillary > 0 ? round(($totals['radiologi']['pemeriksaan'] / $totAncillary) * 100, 1) : 0;

        return [
            'year' => $year,
            'months' => $months,
            'totals' => $totals,
            'averages' => $averages,
            'summary' => [
                'total_pemeriksaan' => $totals['gabungan']['pemeriksaan'],
                'total_lab' => $totals['laboratorium']['pemeriksaan'],
                'total_rad' => $totals['radiologi']['pemeriksaan'],
                'total_pasien' => $totals['gabungan']['pasien'],
                'total_ralan' => $totals['gabungan']['ralan'],
                'total_ranap' => $totals['gabungan']['ranap'],
                'avg_per_month' => $averages['gabungan']['pemeriksaan'],
                'peak_month_name' => $monthNames[$peakMonthNum] ?? '-',
                'peak_month_value' => $maxVal > 0 ? $maxVal : 0,
                'contrib_lab_percent' => $contribLab,
                'contrib_rad_percent' => $contribRad,
            ],
            'charts' => self::buildChartPayload($months, $totals),
        ];
    }

    /**
     * Membangun payload dataset untuk visualisasi grafik tren bulanan & distribusi.
     */
    protected static function buildChartPayload(array $months, array $totals): array
    {
        $labels = array_map(fn($m) => $m['nama_pendek'], $months);

        $dataLab = array_map(fn($m) => $m['laboratorium']['pemeriksaan'], $months);
        $dataRad = array_map(fn($m) => $m['radiologi']['pemeriksaan'], $months);
        $dataTot = array_map(fn($m) => $m['gabungan']['pemeriksaan'], $months);
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
                        'label' => 'Laboratorium',
                        'data' => array_values($dataLab),
                        'borderColor' => '#0284c7',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [4, 4],
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Radiologi',
                        'data' => array_values($dataRad),
                        'borderColor' => '#8b5cf6',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [2, 2],
                        'tension' => 0.35,
                    ],
                ],
            ],
            'service_ratio' => [
                'labels' => ['Laboratorium', 'Radiologi'],
                'datasets' => [[
                    'data' => [
                        (int) $totals['laboratorium']['pemeriksaan'],
                        (int) $totals['radiologi']['pemeriksaan'],
                    ],
                    'backgroundColor' => ['#0284c7', '#8b5cf6'],
                ]],
            ],
            'care_setting' => [
                'labels' => ['Rawat Jalan (Ralan)', 'Rawat Inap (Ranap)'],
                'datasets' => [[
                    'data' => [
                        (int) $totals['gabungan']['ralan'],
                        (int) $totals['gabungan']['ranap'],
                    ],
                    'backgroundColor' => ['#10b981', '#f59e0b'],
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
        ];
    }
}
