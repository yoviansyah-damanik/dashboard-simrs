<?php

namespace App\Repository;

use App\Services\Bpjs\TerapiAntreanApiClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OnlineQueueComplianceRepository
{
    /**
     * Dapatkan instance client API Terapi
     */
    protected static function client(): TerapiAntreanApiClient
    {
        return app(TerapiAntreanApiClient::class);
    }

    /**
     * Dapatkan daftar tahun yang tersedia
     */
    public static function getAvailableYears(): array
    {
        try {
            $years = DB::connection('terapi')->table('bpjs_antrean_bookings')
                ->selectRaw('DISTINCT SUBSTRING(tanggal, 1, 4) as tahun')
                ->orderByDesc('tahun')
                ->pluck('tahun')
                ->map(fn($y) => (int) $y)
                ->filter(fn($y) => $y > 2000)
                ->toArray();

            return !empty($years) ? $years : [(int) date('Y')];
        } catch (\Throwable $e) {
            return [(int) date('Y')];
        }
    }

    /**
     * Dapatkan daftar poliklinik untuk filter (hanya poliklinik non-IGD)
     */
    public static function getPolyclinics(): array
    {
        try {
            return DB::connection('simrs')->table('maping_poli_bpjs as mp')
                ->join('poliklinik as p', 'p.kd_poli', '=', 'mp.kd_poli_rs')
                ->where('p.status', '1')
                ->where('p.kd_poli', '!=', 'IGDK')
                ->orderBy('p.nm_poli')
                ->pluck('p.nm_poli', 'mp.kd_poli_bpjs')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Agregasi data ringkasan KPI kepatuhan antrean online dari API Terapi
     */
    public static function getSummary(int $year, ?int $month = null, ?string $kdPoli = null, float $threshold = 60.0): array
    {
        $monthStr = $month ? sprintf('%04d-%02d', $year, $month) : sprintf('%04d-%02d', $year, (int) date('m'));
        $data = self::client()->getSummary($monthStr);

        $totalRalan = (int) ($data['totals']['queues'] ?? 0);
        $totalJkn = (int) ($data['channels']['jkn']['count'] ?? 0);
        $totalLoket = (int) ($data['channels']['onsite_counter']['count'] ?? 0);
        $totalApm = (int) ($data['channels']['onsite_apm']['count'] ?? 0);
        $totalLainnya = (int) ($data['channels']['other']['count'] ?? 0);

        // Jika filter poliklinik aktif, ambil agregat khusus poliklinik tersebut
        if ($kdPoli) {
            $polyData = self::client()->getPolyclinic($monthStr);
            $selected = collect($polyData)->firstWhere('polyclinic.bpjs_code', $kdPoli);
            if ($selected) {
                $totalRalan = (int) ($selected['total'] ?? 0);
                $totalJkn = (int) ($selected['channels']['jkn'] ?? 0);
                $totalLoket = (int) ($selected['channels']['onsite_counter'] ?? 0);
                $totalApm = (int) ($selected['channels']['onsite_apm'] ?? 0);
                $totalLainnya = (int) ($selected['channels']['other'] ?? 0);
            }
        }

        $totalOnsite = $totalLoket + $totalApm;
        $totalMandiri = $totalJkn + $totalApm;

        $pctJkn = $totalRalan > 0 ? round(($totalJkn / $totalRalan) * 100, 2) : 0.0;
        $pctLoket = $totalRalan > 0 ? round(($totalLoket / $totalRalan) * 100, 2) : 0.0;
        $pctApm = $totalRalan > 0 ? round(($totalApm / $totalRalan) * 100, 2) : 0.0;
        $pctOnsite = $totalRalan > 0 ? round(($totalOnsite / $totalRalan) * 100, 2) : 0.0;
        $pctMandiri = $totalRalan > 0 ? round(($totalMandiri / $totalRalan) * 100, 2) : 0.0;

        $isPatuh = $pctJkn >= $threshold;
        $gapThreshold = round($pctJkn - $threshold, 2);

        $compliant = (int) ($data['totals']['compliant'] ?? 0);
        $nonCompliant = (int) ($data['totals']['non_compliant'] ?? 0);
        $sepValidityPct = (float) ($data['totals']['sep_validity_percent'] ?? 0.0);
        $utilizationPct = (float) ($data['utilization']['utilization_percent'] ?? 0.0);

        return [
            'total_ralan' => $totalRalan,
            'total_jkn' => $totalJkn,
            'total_loket' => $totalLoket,
            'total_apm' => $totalApm,
            'total_lainnya' => $totalLainnya,
            'total_onsite' => $totalOnsite,
            'total_mandiri' => $totalMandiri,
            'pct_jkn' => $pctJkn,
            'pct_loket' => $pctLoket,
            'pct_apm' => $pctApm,
            'pct_onsite' => $pctOnsite,
            'pct_mandiri' => $pctMandiri,
            'threshold' => $threshold,
            'is_patuh' => $isPatuh,
            'gap_threshold' => $gapThreshold,
            'compliant' => $compliant,
            'non_compliant' => $nonCompliant,
            'sep_validity_pct' => $sepValidityPct,
            'utilization_pct' => $utilizationPct,
            'conformity' => [
                'total' => $totalRalan,
                'sesuai' => $compliant,
                'tidak' => $nonCompliant,
                'dengan_sep' => (int) ($data['totals']['with_sep'] ?? 0),
                'tanpa_sep' => (int) ($data['utilization']['sep_without_queue'] ?? 0),
                'pct_sesuai' => $totalRalan > 0 ? round(($compliant / $totalRalan) * 100, 2) : 0.0,
                'pct_valid_sep' => $sepValidityPct,
            ],
        ];
    }

    /**
     * Dapatkan rekapitulasi 12 bulan (Januari - Desember) lengkap dengan perhitungan Monthly Growth (MoM %)
     */
    public static function getMonthlyReport(int $year, ?string $kdPoli = null, float $threshold = 60.0): array
    {
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $results = [];
        $prevTotal = null;
        $prevJkn = null;
        $prevPctJkn = null;

        foreach ($monthNames as $mNum => $mName) {
            $monthStr = sprintf('%04d-%02d', $year, $mNum);
            $summary = self::client()->getSummary($monthStr);

            $total = (int) ($summary['totals']['queues'] ?? 0);
            $jkn = (int) ($summary['channels']['jkn']['count'] ?? 0);
            $loket = (int) ($summary['channels']['onsite_counter']['count'] ?? 0);
            $apm = (int) ($summary['channels']['onsite_apm']['count'] ?? 0);

            if ($kdPoli) {
                $polyData = self::client()->getPolyclinic($monthStr);
                $selected = collect($polyData)->firstWhere('polyclinic.bpjs_code', $kdPoli);
                if ($selected) {
                    $total = (int) ($selected['total'] ?? 0);
                    $jkn = (int) ($selected['channels']['jkn'] ?? 0);
                    $loket = (int) ($selected['channels']['onsite_counter'] ?? 0);
                    $apm = (int) ($selected['channels']['onsite_apm'] ?? 0);
                } else {
                    $total = 0;
                    $jkn = 0;
                    $loket = 0;
                    $apm = 0;
                }
            }

            $onsite = $loket + $apm;
            $pctJkn = $total > 0 ? round(($jkn / $total) * 100, 2) : 0.0;
            $pctLoket = $total > 0 ? round(($loket / $total) * 100, 2) : 0.0;
            $pctApm = $total > 0 ? round(($apm / $total) * 100, 2) : 0.0;
            $pctOnsite = $total > 0 ? round(($onsite / $total) * 100, 2) : 0.0;

            // Perhitungan Growth MoM (Month-over-Month %)
            $growthTotal = ($prevTotal !== null && $prevTotal > 0)
                ? round((($total - $prevTotal) / $prevTotal) * 100, 2)
                : null;

            $growthJkn = ($prevJkn !== null && $prevJkn > 0)
                ? round((($jkn - $prevJkn) / $prevJkn) * 100, 2)
                : null;

            $deltaPctJkn = ($prevPctJkn !== null)
                ? round($pctJkn - $prevPctJkn, 2)
                : null;

            $isPatuh = $total > 0 ? ($pctJkn >= $threshold) : null;

            $results[] = [
                'month_num' => $mNum,
                'month_name' => $mName,
                'total_ralan' => $total,
                'jkn' => $jkn,
                'pct_jkn' => $pctJkn,
                'loket' => $loket,
                'pct_loket' => $pctLoket,
                'apm' => $apm,
                'pct_apm' => $pctApm,
                'onsite' => $onsite,
                'pct_onsite' => $pctOnsite,
                'growth_total' => $growthTotal,
                'growth_jkn' => $growthJkn,
                'delta_pct_jkn' => $deltaPctJkn,
                'is_patuh' => $isPatuh,
            ];

            if ($total > 0) {
                $prevTotal = $total;
                $prevJkn = $jkn;
                $prevPctJkn = $pctJkn;
            }
        }

        return $results;
    }

    /**
     * Dapatkan rekapitulasi tahunan dan tren MoM 12 bulan per Poliklinik & Dokter DPJP
     */
    public static function getAnnualPolyclinicDoctorReport(int $year, ?string $kdPoli = null, float $threshold = 60.0): array
    {
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $polyDocAgg = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthStr = sprintf('%04d-%02d', $year, $m);
            $rows = self::client()->getPolyDoctor($monthStr);

            foreach ($rows as $r) {
                $pCode = $r['polyclinic']['bpjs_code'] ?? '-';
                if ($kdPoli && $pCode !== $kdPoli) {
                    continue;
                }

                $dCode = $r['doctor']['bpjs_code'] ?? '-';
                $key = "{$pCode}_{$dCode}";

                if (!isset($polyDocAgg[$key])) {
                    $polyDocAgg[$key] = [
                        'key' => $key,
                        'kd_poli' => $pCode,
                        'nm_poli' => $r['polyclinic']['name'] ?? '-',
                        'kd_dokter' => $dCode,
                        'nm_dokter' => $r['doctor']['name'] ?? '-',
                        'total' => 0,
                        'jkn' => 0,
                        'loket' => 0,
                        'apm' => 0,
                        'monthly' => [],
                    ];
                }

                $tot = (int) ($r['total'] ?? 0);
                $jkn = (int) ($r['channels']['jkn'] ?? 0);
                $lok = (int) ($r['channels']['onsite_counter'] ?? 0);
                $apm = (int) ($r['channels']['onsite_apm'] ?? 0);
                $pctJkn = $tot > 0 ? round(($jkn / $tot) * 100, 2) : 0.0;

                $polyDocAgg[$key]['total'] += $tot;
                $polyDocAgg[$key]['jkn'] += $jkn;
                $polyDocAgg[$key]['loket'] += $lok;
                $polyDocAgg[$key]['apm'] += $apm;

                $polyDocAgg[$key]['monthly'][$m] = [
                    'month_num' => $m,
                    'month_name' => $monthNames[$m] ?? (string) $m,
                    'total' => $tot,
                    'jkn' => $jkn,
                    'pct_jkn' => $pctJkn,
                    'loket' => $lok,
                    'apm' => $apm,
                    'onsite' => $lok + $apm,
                    'growth_total' => null,
                    'growth_jkn' => null,
                    'delta_pct_jkn' => null,
                    'is_patuh' => $tot > 0 ? ($pctJkn >= $threshold) : null,
                ];
            }
        }

        // Hitung MoM per bulan untuk masing-masing poliklinik & dokter
        return collect($polyDocAgg)->map(function ($item) use ($threshold, $monthNames) {
            $total = $item['total'];
            $jkn = $item['jkn'];
            $loket = $item['loket'];
            $apm = $item['apm'];
            $onsite = $loket + $apm;

            $pctJkn = $total > 0 ? round(($jkn / $total) * 100, 2) : 0.0;
            $pctLoket = $total > 0 ? round(($loket / $total) * 100, 2) : 0.0;
            $pctApm = $total > 0 ? round(($apm / $total) * 100, 2) : 0.0;
            $pctOnsite = $total > 0 ? round(($onsite / $total) * 100, 2) : 0.0;

            $prevTot = null;
            $prevJkn = null;
            $prevPctJkn = null;
            $monthlyList = [];

            for ($m = 1; $m <= 12; $m++) {
                $mData = $item['monthly'][$m] ?? [
                    'month_num' => $m,
                    'month_name' => $monthNames[$m],
                    'total' => 0,
                    'jkn' => 0,
                    'pct_jkn' => 0.0,
                    'loket' => 0,
                    'apm' => 0,
                    'onsite' => 0,
                    'growth_total' => null,
                    'growth_jkn' => null,
                    'delta_pct_jkn' => null,
                    'is_patuh' => null,
                ];

                $mTot = $mData['total'];
                $mJkn = $mData['jkn'];
                $mPctJkn = $mData['pct_jkn'];
                $mLok = $mData['loket'];
                $mApm = $mData['apm'];
                $mOns = $mData['onsite'];

                $mData['pct_loket'] = $mTot > 0 ? round(($mLok / $mTot) * 100, 2) : 0.0;
                $mData['pct_apm'] = $mTot > 0 ? round(($mApm / $mTot) * 100, 2) : 0.0;
                $mData['pct_onsite'] = $mTot > 0 ? round(($mOns / $mTot) * 100, 2) : 0.0;

                if ($prevTot !== null && $prevTot > 0) {
                    $mData['growth_total'] = round((($mTot - $prevTot) / $prevTot) * 100, 2);
                }
                if ($prevJkn !== null && $prevJkn > 0) {
                    $mData['growth_jkn'] = round((($mJkn - $prevJkn) / $prevJkn) * 100, 2);
                }
                if ($prevPctJkn !== null && $mTot > 0) {
                    $mData['delta_pct_jkn'] = round($mPctJkn - $prevPctJkn, 2);
                }

                $monthlyList[] = $mData;

                if ($mTot > 0) {
                    $prevTot = $mTot;
                    $prevJkn = $mJkn;
                    $prevPctJkn = $mPctJkn;
                }
            }

            $item['onsite'] = $onsite;
            $item['pct_jkn'] = $pctJkn;
            $item['pct_loket'] = $pctLoket;
            $item['pct_apm'] = $pctApm;
            $item['pct_onsite'] = $pctOnsite;
            $item['is_patuh'] = $pctJkn >= $threshold;
            $item['avg_monthly'] = round($total / 12, 1);
            $item['monthly'] = $monthlyList;

            return $item;
        })->sortByDesc('total')->values()->toArray();
    }

    /**
     * Dapatkan tren harian dari API Terapi
     */
    public static function getDailyTrend(int $year, int $month, ?string $kdPoli = null, float $threshold = 60.0): array
    {
        $monthStr = sprintf('%04d-%02d', $year, $month);
        $dailyData = self::client()->getDaily($monthStr);
        $dailyMap = collect($dailyData)->keyBy('date');

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $results = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $row = $dailyMap->get($dateStr);

            $jkn = (int) ($row['channels']['jkn'] ?? 0);
            $loket = (int) ($row['channels']['onsite_counter'] ?? 0);
            $apm = (int) ($row['channels']['onsite_apm'] ?? 0);
            $total = $jkn + $loket + $apm;
            $pctJkn = $total > 0 ? round(($jkn / $total) * 100, 2) : 0.0;

            $results[] = [
                'day' => $d,
                'date' => $dateStr,
                'total' => $total,
                'jkn' => $jkn,
                'loket' => $loket,
                'apm' => $apm,
                'pct_jkn' => $pctJkn,
                'threshold' => $threshold,
            ];
        }

        return $results;
    }

    /**
     * Dapatkan rincian pendaftaran, kepatuhan, serta perhitungan MoM Growth per Poliklinik & Dokter dari API Terapi
     */
    public static function getPolyclinicDoctorBreakdown(int $year, ?int $month = null, ?string $kdPoli = null, float $threshold = 60.0): array
    {
        $monthStr = $month ? sprintf('%04d-%02d', $year, $month) : sprintf('%04d-%02d', $year, (int) date('m'));
        $rows = self::client()->getPolyDoctor($monthStr);

        // Ambil data bulan sebelumnya untuk komparasi Month-over-Month (MoM Growth %)
        $curDate = $month ? Carbon::createFromDate($year, $month, 1) : Carbon::createFromDate($year, (int) date('m'), 1);
        $prevDate = $curDate->copy()->subMonth();
        $prevMonthStr = $prevDate->format('Y-m');
        $prevRows = self::client()->getPolyDoctor($prevMonthStr);

        $prevMap = collect($prevRows)->keyBy(function ($p) {
            return ($p['polyclinic']['bpjs_code'] ?? '') . '_' . ($p['doctor']['bpjs_code'] ?? '');
        });

        $prevPeriodLabel = $prevDate->translatedFormat('F Y');

        return collect($rows)
            ->filter(fn($r) => !$kdPoli || ($r['polyclinic']['bpjs_code'] ?? '') === $kdPoli)
            ->map(function ($r) use ($threshold, $prevMap, $prevPeriodLabel) {
                $total = (int) ($r['total'] ?? 0);
                $jkn = (int) ($r['channels']['jkn'] ?? 0);
                $loket = (int) ($r['channels']['onsite_counter'] ?? 0);
                $apm = (int) ($r['channels']['onsite_apm'] ?? 0);
                $onsite = $loket + $apm;

                $pctJkn = $total > 0 ? round(($jkn / $total) * 100, 2) : 0.0;
                $pctLoket = $total > 0 ? round(($loket / $total) * 100, 2) : 0.0;
                $pctApm = $total > 0 ? round(($apm / $total) * 100, 2) : 0.0;
                $pctOnsite = $total > 0 ? round(($onsite / $total) * 100, 2) : 0.0;

                // Komparasi MoM terhadap bulan sebelumnya
                $key = ($r['polyclinic']['bpjs_code'] ?? '') . '_' . ($r['doctor']['bpjs_code'] ?? '');
                $prev = $prevMap->get($key);

                $prevTotal = $prev ? (int) ($prev['total'] ?? 0) : null;
                $prevJkn = $prev ? (int) ($prev['channels']['jkn'] ?? 0) : null;
                $prevPctJkn = ($prevTotal && $prevTotal > 0) ? round(($prevJkn / $prevTotal) * 100, 2) : null;

                $growthTotal = ($prevTotal !== null && $prevTotal > 0)
                    ? round((($total - $prevTotal) / $prevTotal) * 100, 2)
                    : null;

                $growthJkn = ($prevJkn !== null && $prevJkn > 0)
                    ? round((($jkn - $prevJkn) / $prevJkn) * 100, 2)
                    : null;

                $deltaPctJkn = ($prevPctJkn !== null)
                    ? round($pctJkn - $prevPctJkn, 2)
                    : null;

                return [
                    'kd_poli' => $r['polyclinic']['bpjs_code'] ?? '-',
                    'nm_poli' => $r['polyclinic']['name'] ?? '-',
                    'kd_dokter' => $r['doctor']['bpjs_code'] ?? '-',
                    'nm_dokter' => $r['doctor']['name'] ?? '-',
                    'total' => $total,
                    'jkn' => $jkn,
                    'pct_jkn' => $pctJkn,
                    'loket' => $loket,
                    'pct_loket' => $pctLoket,
                    'apm' => $apm,
                    'pct_apm' => $pctApm,
                    'onsite' => $onsite,
                    'pct_onsite' => $pctOnsite,
                    'compliant' => (int) ($r['compliant'] ?? 0),
                    'non_compliant' => (int) ($r['non_compliant'] ?? 0),
                    'is_patuh' => $pctJkn >= $threshold,
                    'prev_total' => $prevTotal,
                    'prev_jkn' => $prevJkn,
                    'prev_pct_jkn' => $prevPctJkn,
                    'growth_total' => $growthTotal,
                    'growth_jkn' => $growthJkn,
                    'delta_pct_jkn' => $deltaPctJkn,
                    'prev_period_label' => $prevPeriodLabel,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    /**
     * Dapatkan ringkasan kesesuaian data antrean JKN terhadap SEP
     */
    public static function getJknConformitySummary(int $year, ?int $month = null, ?string $kdPoli = null): array
    {
        $summary = self::getSummary($year, $month, $kdPoli);
        return $summary['conformity'];
    }

    /**
     * Dapatkan daftar detail ketidaksesuaian (mismatches) antrean JKN dari API Terapi
     */
    public static function getJknConformityDetails(
        int $year,
        ?int $month = null,
        ?string $kdPoli = null,
        ?string $filterStatus = null,
        int $limit = 50,
        int $offset = 0
    ): array {
        $monthStr = $month ? sprintf('%04d-%02d', $year, $month) : sprintf('%04d-%02d', $year, (int) date('m'));
        $page = (int) floor($offset / $limit) + 1;
        
        $mismatchResponse = self::client()->getMismatches($monthStr, $page, $limit);
        $rawRows = $mismatchResponse['data'] ?? [];
        $meta = $mismatchResponse['meta'] ?? [];

        $filtered = collect($rawRows)
            ->filter(fn($r) => !$kdPoli || ($r['polyclinic_bpjs_code'] ?? '') === $kdPoli)
            ->map(function ($r) {
                $reasons = $r['reasons'] ?? [];
                $alasanStr = !empty($reasons) ? implode(', ', $reasons) : 'Tidak sesuai spesifikasi';

                return [
                    'no_rawat' => $r['booking_code'] ?? '-',
                    'tgl_registrasi' => $r['date'] ?? '-',
                    'nm_poli' => $r['polyclinic_bpjs_code'] ?? '-',
                    'no_rkm_medis' => '-',
                    'nm_pasien' => '-',
                    'nomor_antrean' => '-',
                    'jenis_kunjungan' => $r['visit_purpose'] ?? '-',
                    'no_referensi' => $r['reference_no'] ?: '-',
                    'no_sep' => $r['sep_no'] ?: '-',
                    'no_rujukan' => $r['referral_no'] ?: '-',
                    'noskdp' => $r['skdp_no'] ?: '-',
                    'is_sesuai' => false,
                    'alasan' => $reasons,
                    'alasan_str' => $alasanStr,
                ];
            });

        return [
            'total' => (int) ($meta['total'] ?? count($filtered)),
            'rows' => $filtered->values()->toArray(),
        ];
    }
}
