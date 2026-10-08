<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

interface PharmacyReportInterface {}

class PharmacyReportRepository implements PharmacyReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Mengambil matriks indikator tahunan pelayanan farmasi (12 bulan).
     */
    public static function getYearlyMatrix(int|string|null $year = null): array
    {
        $year = (int) ($year ?: date('Y'));

        // Query agregasi per bulan untuk data resep obat
        $resepRows = DB::connection(self::KONEKSI)->table('resep_obat as ro')
            ->whereYear('ro.tgl_perawatan', $year)
            ->selectRaw("
                MONTH(ro.tgl_perawatan) as bulan,
                count(*) as total_resep,
                count(distinct ro.no_rawat) as total_pasien,
                sum(case when ro.status = 'ralan' then 1 else 0 end) as ralan,
                sum(case when ro.status = 'ranap' then 1 else 0 end) as ranap,
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

        // Query agregasi per bulan untuk item obat dari detail_pemberian_obat
        $itemRows = DB::connection(self::KONEKSI)->table('detail_pemberian_obat as dpo')
            ->whereYear('dpo.tgl_perawatan', $year)
            ->selectRaw("
                MONTH(dpo.tgl_perawatan) as bulan,
                count(*) as total_item_obat,
                sum(dpo.jml) as total_qty_obat
            ")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Query agregasi per bulan untuk data resep pulang (pasien rawat inap saat pulang)
        $pulangRows = DB::connection(self::KONEKSI)->table('resep_pulang as rp')
            ->whereYear('rp.tanggal', $year)
            ->selectRaw("
                MONTH(rp.tanggal) as bulan,
                count(distinct concat(rp.no_rawat, ' ', rp.tanggal, ' ', rp.jam)) as total_resep_pulang,
                count(distinct rp.no_rawat) as total_pasien_pulang,
                count(*) as total_item_pulang,
                sum(rp.jml_barang) as total_qty_pulang
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
        $totalResepList = [];

        for ($m = 1; $m <= 12; $m++) {
            $r = $resepRows->get($m);
            $it = $itemRows->get($m);
            $pul = $pulangRows->get($m);

            $ralan = $r ? (int) $r->ralan : 0;
            $ranapHarian = $r ? (int) $r->ranap : 0;
            $resepPulang = $pul ? (int) $pul->total_resep_pulang : 0;
            // Total ranap mengakomodasi ranap harian dan resep saat pasien pulang
            $ranapTotal = $ranapHarian + $resepPulang;
            $totResep = $ralan + $ranapTotal;

            $totPasien = $r ? (int) $r->total_pasien : 0;
            $biasa = $r ? (int) $r->biasa : 0;
            $kronis = $r ? (int) $r->kronis : 0;
            $cito = $r ? (int) $r->cito : 0;
            $prb = $r ? (int) $r->prb : 0;
            $diserahkan = ($r ? (int) $r->diserahkan : 0) + $resepPulang;
            $belumDiserahkan = $r ? (int) $r->belum_diserahkan : 0;
            $waktuTunggu = $r && $r->avg_waktu_tunggu !== null ? (float) $r->avg_waktu_tunggu : 0;
            // Gunakan Waktu Tunggu Farmasi dari referensi_mobilejkn_bpjs_taskid (task6 - task5) jika tersedia
            $farmasiTask = \App\Helpers\TaskidHelper::getWaktuTungguFarmasi($year, $m);
            if ($farmasiTask['total'] > 0) {
                $waktuTunggu = $farmasiTask['avg_menit'];
            }

            $itemObat = ($it ? (int) $it->total_item_obat : 0) + ($pul ? (int) $pul->total_item_pulang : 0);
            $qtyObat = ($it ? (float) $it->total_qty_obat : 0) + ($pul ? (float) $pul->total_qty_pulang : 0);

            $pctDiserahkan = $totResep > 0 ? round(($diserahkan / $totResep) * 100, 1) : 0;
            $pctRalan = $totResep > 0 ? round(($ralan / $totResep) * 100, 1) : 0;
            $pctRanap = $totResep > 0 ? round(($ranapTotal / $totResep) * 100, 1) : 0;
            $avgItemPerResep = $totResep > 0 ? round($itemObat / $totResep, 1) : 0;

            $totalResepList[$m] = $totResep;

            $months[$m] = [
                'bulan' => $m,
                'nama_bulan' => $monthNames[$m],
                'nama_pendek' => $shortMonthNames[$m],
                'total_resep' => $totResep,
                'total_pasien' => $totPasien,
                'ralan' => $ralan,
                'ranap_harian' => $ranapHarian,
                'resep_pulang' => $resepPulang,
                'ranap' => $ranapTotal,
                'rasio_ralan_persen' => $pctRalan,
                'rasio_ranap_persen' => $pctRanap,
                'biasa' => $biasa,
                'kronis' => $kronis,
                'cito' => $cito,
                'prb' => $prb,
                'diserahkan' => $diserahkan,
                'belum_diserahkan' => $belumDiserahkan,
                'persen_penyerahan' => $pctDiserahkan,
                'waktu_tunggu' => $waktuTunggu,
                'item_obat' => $itemObat,
                'qty_obat' => round($qtyObat, 0),
                'avg_item_per_resep' => $avgItemPerResep,
            ];
        }

        // Akumulasi total setahun
        $totals = [
            'total_resep' => array_sum(array_column($months, 'total_resep')),
            'total_pasien' => array_sum(array_column($months, 'total_pasien')),
            'ralan' => array_sum(array_column($months, 'ralan')),
            'ranap_harian' => array_sum(array_column($months, 'ranap_harian')),
            'resep_pulang' => array_sum(array_column($months, 'resep_pulang')),
            'ranap' => array_sum(array_column($months, 'ranap')),
            'biasa' => array_sum(array_column($months, 'biasa')),
            'kronis' => array_sum(array_column($months, 'kronis')),
            'cito' => array_sum(array_column($months, 'cito')),
            'prb' => array_sum(array_column($months, 'prb')),
            'diserahkan' => array_sum(array_column($months, 'diserahkan')),
            'belum_diserahkan' => array_sum(array_column($months, 'belum_diserahkan')),
            'item_obat' => array_sum(array_column($months, 'item_obat')),
            'qty_obat' => array_sum(array_column($months, 'qty_obat')),
        ];

        // Rata-rata per bulan
        $activeWaktuTunggu = array_filter(array_column($months, 'waktu_tunggu'), fn($v) => $v > 0);
        $avgWaktuTungguYearly = count($activeWaktuTunggu) > 0 ? round(array_sum($activeWaktuTunggu) / count($activeWaktuTunggu), 1) : 0;

        $averages = [
            'total_resep' => round($totals['total_resep'] / 12, 1),
            'total_pasien' => round($totals['total_pasien'] / 12, 1),
            'ralan' => round($totals['ralan'] / 12, 1),
            'ranap_harian' => round($totals['ranap_harian'] / 12, 1),
            'resep_pulang' => round($totals['resep_pulang'] / 12, 1),
            'ranap' => round($totals['ranap'] / 12, 1),
            'biasa' => round($totals['biasa'] / 12, 1),
            'kronis' => round($totals['kronis'] / 12, 1),
            'cito' => round($totals['cito'] / 12, 1),
            'prb' => round($totals['prb'] / 12, 1),
            'diserahkan' => round($totals['diserahkan'] / 12, 1),
            'belum_diserahkan' => round($totals['belum_diserahkan'] / 12, 1),
            'item_obat' => round($totals['item_obat'] / 12, 1),
            'qty_obat' => round($totals['qty_obat'] / 12, 0),
            'waktu_tunggu' => $avgWaktuTungguYearly,
        ];

        // Cari bulan puncak (Peak Month)
        $peakMonthNum = 1;
        $maxVal = -1;
        foreach ($totalResepList as $mNum => $val) {
            if ($val > $maxVal) {
                $maxVal = $val;
                $peakMonthNum = $mNum;
            }
        }

        $totAllResep = $totals['total_resep'];
        $pctYearlyDiserahkan = $totAllResep > 0 ? round(($totals['diserahkan'] / $totAllResep) * 100, 1) : 0;
        $pctYearlyRalan = $totAllResep > 0 ? round(($totals['ralan'] / $totAllResep) * 100, 1) : 0;
        $pctYearlyRanap = $totAllResep > 0 ? round(($totals['ranap'] / $totAllResep) * 100, 1) : 0;

        return [
            'year' => $year,
            'months' => $months,
            'totals' => $totals,
            'averages' => $averages,
            'summary' => [
                'total_resep' => $totAllResep,
                'total_pasien' => $totals['total_pasien'],
                'total_ralan' => $totals['ralan'],
                'total_ranap_harian' => $totals['ranap_harian'],
                'total_resep_pulang' => $totals['resep_pulang'],
                'total_ranap' => $totals['ranap'],
                'total_diserahkan' => $totals['diserahkan'],
                'total_belum_diserahkan' => $totals['belum_diserahkan'],
                'persen_diserahkan' => $pctYearlyDiserahkan,
                'persen_ralan' => $pctYearlyRalan,
                'persen_ranap' => $pctYearlyRanap,
                'avg_per_month' => $averages['total_resep'],
                'avg_waktu_tunggu' => $avgWaktuTungguYearly,
                'spm_waktu_tunggu_target' => 30, // Standar SPM Farmasi: <= 30 menit
                'peak_month_name' => $monthNames[$peakMonthNum] ?? '-',
                'peak_month_value' => $maxVal > 0 ? $maxVal : 0,
            ],
            'charts' => self::buildChartPayload($months, $totals),
        ];
    }

    /**
     * Membangun payload dataset untuk visualisasi grafik tren bulanan & distribusi farmasi.
     */
    protected static function buildChartPayload(array $months, array $totals): array
    {
        $labels = array_map(fn($m) => $m['nama_pendek'], $months);

        $dataResep = array_map(fn($m) => $m['total_resep'], $months);
        $dataRalan = array_map(fn($m) => $m['ralan'], $months);
        $dataRanap = array_map(fn($m) => $m['ranap'], $months);
        $dataWaktu = array_map(fn($m) => $m['waktu_tunggu'], $months);

        return [
            'trend_monthly' => [
                'labels' => array_values($labels),
                'datasets' => [
                    [
                        'label' => 'Total Resep',
                        'data' => array_values($dataResep),
                        'borderColor' => '#059669',
                        'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                        'fill' => true,
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Rawat Jalan (Ralan)',
                        'data' => array_values($dataRalan),
                        'borderColor' => '#0284c7',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [4, 4],
                        'tension' => 0.35,
                    ],
                    [
                        'label' => 'Rawat Inap (Ranap)',
                        'data' => array_values($dataRanap),
                        'borderColor' => '#8b5cf6',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [2, 2],
                        'tension' => 0.35,
                    ],
                ],
            ],
            'care_setting' => [
                'labels' => ['Rawat Jalan (Ralan)', 'Rawat Inap (Ranap)'],
                'datasets' => [[
                    'data' => [
                        (int) $totals['ralan'],
                        (int) $totals['ranap'],
                    ],
                    'backgroundColor' => ['#0284c7', '#8b5cf6'],
                ]],
            ],
            'delivery_status' => [
                'labels' => ['Sudah Diserahkan', 'Belum Diserahkan'],
                'datasets' => [[
                    'data' => [
                        (int) $totals['diserahkan'],
                        (int) $totals['belum_diserahkan'],
                    ],
                    'backgroundColor' => ['#10b981', '#f43f5e'],
                ]],
            ],
            'prescription_type' => [
                'labels' => ['Biasa', 'Kronis', 'CITO', 'PRB'],
                'datasets' => [[
                    'label' => 'Jumlah Resep',
                    'data' => [
                        (int) $totals['biasa'],
                        (int) $totals['kronis'],
                        (int) $totals['cito'],
                        (int) $totals['prb'],
                    ],
                    'backgroundColor' => ['#059669', '#3b82f6', '#ef4444', '#f59e0b'],
                    'borderRadius' => 6,
                ]],
            ],
            'waiting_time_trend' => [
                'labels' => array_values($labels),
                'datasets' => [
                    [
                        'label' => 'Waktu Tunggu (Menit)',
                        'data' => array_values($dataWaktu),
                        'borderColor' => '#f59e0b',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'fill' => true,
                        'tension' => 0.35,
                        'pointRadius' => 4,
                    ],
                    [
                        'label' => 'Target SPM (≤ 30 Menit)',
                        'data' => array_fill(0, 12, 30),
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [6, 4],
                        'pointRadius' => 0,
                    ],
                ],
            ],
        ];
    }
}
