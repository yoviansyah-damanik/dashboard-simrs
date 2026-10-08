<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use App\Helpers\SirsHelper;
use App\Services\HospitalIndicatorService;
use App\Repository\InmReportRepository;
use App\Repository\SpmReportRepository;
use App\Repository\IkpReportRepository;
use App\Repository\PpiReportRepository;

class Home extends Component
{
    #[Computed]
    public function outpatientToday()
    {
        return DB::connection('simrs')
            ->table('reg_periksa')
            ->whereDate('tgl_registrasi', Carbon::today())
            ->where('status_lanjut', 'Ralan')
            ->where('kd_poli', '!=', 'IGDK')
            ->count();
    }

    #[Computed]
    public function inpatientActive()
    {
        return DB::connection('simrs')
            ->table('kamar_inap')
            ->where('stts_pulang', '-')
            ->count();
    }

    #[Computed]
    public function emergencyToday()
    {
        return DB::connection('simrs')
            ->table('reg_periksa')
            ->where('kd_poli', 'IGDK')
            ->whereDate('tgl_registrasi', Carbon::today())
            ->count();
    }

    #[Computed]
    public function operationToday()
    {
        $ops = DB::connection('simrs')
            ->table('booking_operasi')
            ->whereDate('tanggal', Carbon::today())
            ->selectRaw("
                count(*) as total,
                sum(case when status = 'Selesai' then 1 else 0 end) as selesai,
                sum(case when status = 'Proses Operasi' then 1 else 0 end) as proses,
                sum(case when status = 'Menunggu' then 1 else 0 end) as menunggu
            ")
            ->first();

        return [
            'total' => (int) ($ops->total ?? 0),
            'finished' => (int) ($ops->selesai ?? 0),
            'in_progress' => (int) ($ops->proses ?? 0),
            'waiting' => (int) ($ops->menunggu ?? 0),
        ];
    }

    #[Computed]
    public function roomStats()
    {
        $stats = DB::connection('simrs')
            ->table('kamar')
            ->where('statusdata', '1')
            ->where('kd_bangsal', '!=', 'TRANS')
            ->selectRaw("SUM(CASE WHEN status = 'KOSONG' THEN 1 ELSE 0 END) as tersedia")
            ->selectRaw("SUM(CASE WHEN status = 'ISI' THEN 1 ELSE 0 END) as terisi")
            ->first();

        $available = (int) ($stats->tersedia ?? 0);
        $filled = (int) ($stats->terisi ?? 0);
        $total = $available + $filled;
        $occupancyRate = $total > 0 ? round(($filled / $total) * 100, 1) : 0;

        return [
            'available' => $available,
            'filled' => $filled,
            'total' => $total,
            'occupancy_rate' => $occupancyRate,
        ];
    }

    #[Computed]
    public function polyclinicCount()
    {
        return DB::connection('simrs')
            ->table('poliklinik')
            ->where('status', '1')
            ->whereNotIn('kd_poli', ['-', 'TES', 'TEST'])
            ->count();
    }

    #[Computed]
    public function bedOccupancyByClass()
    {
        $rows = DB::connection('simrs')
            ->table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('bangsal.status', '1')
            ->where('kamar.statusdata', '1')
            ->where('bangsal.kd_bangsal', '!=', 'TRANS')
            ->selectRaw("
                kamar.kelas,
                sum(case when kamar.status = 'KOSONG' then 1 else 0 end) as kosong,
                sum(case when kamar.status = 'ISI' then 1 else 0 end) as isi,
                count(*) as total
            ")
            ->groupBy('kamar.kelas')
            ->get();

        return $rows->map(function ($r) {
            $rate = $r->total > 0 ? round(($r->isi / $r->total) * 100, 1) : 0;
            return [
                'kelas' => $r->kelas,
                'kosong' => (int) $r->kosong,
                'isi' => (int) $r->isi,
                'total' => (int) $r->total,
                'rate' => $rate,
            ];
        })->sortByDesc('total')->values()->toArray();
    }

    #[Computed]
    public function payerDistribution()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $rows = DB::connection('simrs')
            ->table('reg_periksa')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereMonth('reg_periksa.tgl_registrasi', $month)
            ->whereYear('reg_periksa.tgl_registrasi', $year)
            ->selectRaw("
                CASE 
                    WHEN penjab.png_jawab LIKE '%BPJS%' THEN 'BPJS Kesehatan'
                    WHEN penjab.png_jawab LIKE '%UMUM%' THEN 'Umum / Mandiri'
                    WHEN penjab.png_jawab LIKE '%TNI%' OR penjab.png_jawab LIKE '%POLRI%' THEN 'TNI / POLRI'
                    ELSE 'Asuransi / Lainnya'
                END as kategori,
                count(*) as total
            ")
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $totalVisits = $rows->sum('total');

        $colorMap = [
            'BPJS Kesehatan' => ['bg' => '#10B981', 'text' => 'text-emerald-600 dark:text-emerald-400', 'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'],
            'Umum / Mandiri' => ['bg' => '#3B82F6', 'text' => 'text-blue-600 dark:text-blue-400', 'badge' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400'],
            'TNI / POLRI' => ['bg' => '#F59E0B', 'text' => 'text-amber-600 dark:text-amber-400', 'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400'],
            'Asuransi / Lainnya' => ['bg' => '#8B5CF6', 'text' => 'text-purple-600 dark:text-purple-400', 'badge' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400'],
        ];

        $labels = [];
        $data = [];
        $bgColors = [];
        $items = [];

        foreach ($rows as $r) {
            $pct = $totalVisits > 0 ? round(($r->total / $totalVisits) * 100, 1) : 0;
            $color = $colorMap[$r->kategori]['bg'] ?? '#94A3B8';
            $labels[] = $r->kategori;
            $data[] = (int) $r->total;
            $bgColors[] = $color;
            $items[] = [
                'label' => $r->kategori,
                'total' => (int) $r->total,
                'percent' => $pct,
                'color' => $color,
                'textColor' => $colorMap[$r->kategori]['text'] ?? 'text-gray-600',
                'badge' => $colorMap[$r->kategori]['badge'] ?? 'bg-gray-100 text-gray-700',
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                    'borderWidth' => 0,
                ],
            ],
            'items' => $items,
            'total' => $totalVisits,
        ];
    }

    #[Computed]
    public function topPolyclinics()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $rows = DB::connection('simrs')
            ->table('reg_periksa')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereMonth('reg_periksa.tgl_registrasi', $month)
            ->whereYear('reg_periksa.tgl_registrasi', $year)
            ->whereNotIn('poliklinik.kd_poli', ['-', 'TES', 'TEST', 'IGDK'])
            ->selectRaw('poliklinik.nm_poli, count(*) as total')
            ->groupBy('poliklinik.kd_poli', 'poliklinik.nm_poli')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $maxTotal = $rows->max('total') ?: 1;

        return $rows->map(function ($r, $idx) use ($maxTotal) {
            return [
                'rank' => $idx + 1,
                'name' => $r->nm_poli,
                'total' => (int) $r->total,
                'percent' => round(($r->total / $maxTotal) * 100),
            ];
        })->toArray();
    }

    #[Computed]
    public function inpatientTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $masuk = DB::connection('simrs')
            ->table('kamar_inap')
            ->selectRaw('tgl_masuk as tanggal, count(*) as total')
            ->where('tgl_masuk', '>=', Carbon::today()->subDays(14))
            ->groupBy('tgl_masuk')
            ->pluck('total', 'tanggal');

        $keluar = DB::connection('simrs')
            ->table('kamar_inap')
            ->selectRaw('tgl_keluar as tanggal, count(*) as total')
            ->where('tgl_keluar', '>=', Carbon::today()->subDays(14))
            ->where('tgl_keluar', '!=', '0000-00-00')
            ->groupBy('tgl_keluar')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Pasien Masuk',
                    'data' => $days->map(fn($d) => (int)$masuk->get($d, 0))->values()->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ],
                [
                    'label' => 'Pasien Keluar',
                    'data' => $days->map(fn($d) => (int)$keluar->get($d, 0))->values()->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function outpatientTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $visits = DB::connection('simrs')
            ->table('reg_periksa')
            ->selectRaw('tgl_registrasi as tanggal, count(*) as total')
            ->where('tgl_registrasi', '>=', Carbon::today()->subDays(14))
            ->where('status_lanjut', 'Ralan')
            ->where('kd_poli', '!=', 'IGDK')
            ->groupBy('tgl_registrasi')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Kunjungan Rawat Jalan',
                    'data' => $days->map(fn($d) => (int)$visits->get($d, 0))->values()->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function emergencyTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $visits = DB::connection('simrs')
            ->table('reg_periksa')
            ->selectRaw('tgl_registrasi as tanggal, count(*) as total')
            ->where('kd_poli', 'IGDK')
            ->where('tgl_registrasi', '>=', Carbon::today()->subDays(14))
            ->groupBy('tgl_registrasi')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Kunjungan IGD',
                    'data' => $days->map(fn($d) => (int)$visits->get($d, 0))->values()->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function pharmacyTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $prescriptions = DB::connection('simrs')
            ->table('resep_obat')
            ->selectRaw('tgl_perawatan as tanggal, count(*) as total')
            ->where('tgl_perawatan', '>=', Carbon::today()->subDays(14))
            ->groupBy('tgl_perawatan')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Resep Obat Dilayani',
                    'data' => $days->map(fn($d) => (int)$prescriptions->get($d, 0))->values()->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function laboratoryTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $exams = DB::connection('simrs')
            ->table('periksa_lab')
            ->selectRaw('tgl_periksa as tanggal, count(*) as total')
            ->where('tgl_periksa', '>=', Carbon::today()->subDays(14))
            ->groupBy('tgl_periksa')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Pemeriksaan Laboratorium',
                    'data' => $days->map(fn($d) => (int)$exams->get($d, 0))->values()->toArray(),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function yearlyVisitsTrend()
    {
        $year = Carbon::now()->year;
        $shortMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $shortMonths[] = substr(SirsHelper::getMonthName($m), 0, 3);
        }

        $ralan = DB::connection('simrs')
            ->table('reg_periksa')
            ->selectRaw('MONTH(tgl_registrasi) as bulan, count(*) as total')
            ->whereYear('tgl_registrasi', $year)
            ->where('status_lanjut', 'Ralan')
            ->where('kd_poli', '!=', 'IGDK')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $ranap = DB::connection('simrs')
            ->table('kamar_inap')
            ->selectRaw('MONTH(tgl_masuk) as bulan, count(*) as total')
            ->whereYear('tgl_masuk', $year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $igd = DB::connection('simrs')
            ->table('reg_periksa')
            ->selectRaw('MONTH(tgl_registrasi) as bulan, count(*) as total')
            ->whereYear('tgl_registrasi', $year)
            ->where('kd_poli', 'IGDK')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $ralanData = [];
        $ranapData = [];
        $igdData = [];

        for ($m = 1; $m <= 12; $m++) {
            $ralanData[] = (int) ($ralan[$m] ?? 0);
            $ranapData[] = (int) ($ranap[$m] ?? 0);
            $igdData[] = (int) ($igd[$m] ?? 0);
        }

        return [
            'labels' => $shortMonths,
            'year' => $year,
            'totals' => [
                'ralan' => array_sum($ralanData),
                'ranap' => array_sum($ranapData),
                'igd' => array_sum($igdData),
            ],
            'datasets' => [
                [
                    'label' => 'Rawat Jalan',
                    'data' => $ralanData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Rawat Inap',
                    'data' => $ranapData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Gawat Darurat',
                    'data' => $igdData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ]
        ];
    }

    #[Computed]
    public function clinicalIndicators()
    {
        $range = SirsHelper::getDateRange(now()->year, now()->month);
        $ind = HospitalIndicatorService::computeForPeriod($range['start'], $range['end'], $range['jumlah_hari']);
        $totalBed = SirsHelper::getActiveBedCount();

        return [
            'bor' => [
                'value' => round((float) ($ind['bor'] ?? 0), 1),
                'unit' => '%',
                'isIdeal' => HospitalIndicatorService::isWithinRange('bor', (float) $ind['bor'], true, 'depkes'),
                'target' => '60 - 85%',
                'label' => 'Bed Occupancy Rate',
            ],
            'alos' => [
                'value' => round((float) ($ind['alos'] ?? 0), 1),
                'unit' => ' Hari',
                'isIdeal' => HospitalIndicatorService::isWithinRange('alos', (float) $ind['alos'], true, 'depkes'),
                'target' => '6 - 9 Hari',
                'label' => 'Avg Length of Stay',
            ],
            'toi' => [
                'value' => round((float) ($ind['toi'] ?? 0), 1),
                'unit' => ' Hari',
                'isIdeal' => HospitalIndicatorService::isWithinRange('toi', (float) $ind['toi'], true, 'depkes'),
                'target' => '1 - 3 Hari',
                'label' => 'Turn Over Interval',
            ],
            'bto' => [
                'value' => round((float) ($ind['bto'] ?? 0), 1),
                'unit' => ' Kali',
                'isIdeal' => HospitalIndicatorService::isWithinRange('bto', (float) $ind['bto'], true, 'depkes'),
                'target' => '2 - 4 Kali/Bln',
                'label' => 'Bed Turn Over',
            ],
            'ndr' => [
                'value' => round((float) ($ind['ndr'] ?? 0), 1),
                'unit' => '‰',
                'isIdeal' => HospitalIndicatorService::isWithinRange('ndr', (float) $ind['ndr'], true, 'depkes'),
                'target' => '< 25‰',
                'label' => 'Net Death Rate',
            ],
            'gdr' => [
                'value' => round((float) ($ind['gdr'] ?? 0), 1),
                'unit' => '‰',
                'isIdeal' => HospitalIndicatorService::isWithinRange('gdr', (float) $ind['gdr'], true, 'depkes'),
                'target' => '< 45‰',
                'label' => 'Gross Death Rate',
            ],
            'beds' => $totalBed,
        ];
    }

    #[Computed]
    public function ancillaryStats()
    {
        $today = Carbon::today()->format('Y-m-d');
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Laboratorium
        $labToday = DB::connection('simrs')->table('periksa_lab')->whereDate('tgl_periksa', $today)->count();
        $labMonth = DB::connection('simrs')->table('periksa_lab')->whereMonth('tgl_periksa', $month)->whereYear('tgl_periksa', $year)->count();

        // Radiologi
        $radToday = DB::connection('simrs')->table('periksa_radiologi')->whereDate('tgl_periksa', $today)->count();
        $radMonth = DB::connection('simrs')->table('periksa_radiologi')->whereMonth('tgl_periksa', $month)->whereYear('tgl_periksa', $year)->count();

        // Farmasi (Resep)
        $pharmacyToday = DB::connection('simrs')->table('resep_obat')->whereDate('tgl_perawatan', $today)->count();
        $pharmacyMonth = DB::connection('simrs')->table('resep_obat')->whereMonth('tgl_perawatan', $month)->whereYear('tgl_perawatan', $year)->count();

        // SDM Dokter
        $doctorTotal = DB::connection('simrs')->table('dokter')->where('status', '1')->count();
        $specialistTotal = DB::connection('simrs')->table('dokter')->where('status', '1')->where('kd_sps', '!=', '-')->count();

        return [
            'laboratory' => ['today' => $labToday, 'month' => $labMonth],
            'radiology' => ['today' => $radToday, 'month' => $radMonth],
            'pharmacy' => ['today' => $pharmacyToday, 'month' => $pharmacyMonth],
            'medical_staff' => ['total' => $doctorTotal, 'specialists' => $specialistTotal],
        ];
    }

    #[Computed]
    public function topDiagnoses()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $rows = DB::connection('simrs')
            ->table('diagnosa_pasien as dp')
            ->join('penyakit as p', 'dp.kd_penyakit', '=', 'p.kd_penyakit')
            ->join('reg_periksa as rp', 'dp.no_rawat', '=', 'rp.no_rawat')
            ->whereMonth('rp.tgl_registrasi', $month)
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw('dp.kd_penyakit, p.nm_penyakit, count(*) as total')
            ->groupBy('dp.kd_penyakit', 'p.nm_penyakit')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $maxTotal = $rows->max('total') ?: 1;

        return $rows->map(function ($r, $idx) use ($maxTotal) {
            return [
                'rank' => $idx + 1,
                'code' => $r->kd_penyakit,
                'name' => $r->nm_penyakit,
                'total' => (int) $r->total,
                'percent' => round(($r->total / $maxTotal) * 100),
            ];
        })->toArray();
    }

    #[Computed]
    public function demographicGenderTrend()
    {
        $year = Carbon::now()->year;

        // Query tren bulanan berdasarkan jenis kelamin (Pria / Wanita)
        $genderMonthly = DB::connection('simrs')
            ->table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw("
                MONTH(rp.tgl_registrasi) as bulan,
                SUM(CASE WHEN p.jk = 'L' THEN 1 ELSE 0 END) as pria,
                SUM(CASE WHEN p.jk = 'P' THEN 1 ELSE 0 END) as wanita,
                COUNT(*) as total
            ")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $labels = [];
        $maleData = [];
        $femaleData = [];
        $totalPria = 0;
        $totalWanita = 0;

        foreach ($monthNames as $m => $name) {
            $labels[] = $name;
            $pria = isset($genderMonthly[$m]) ? (int) $genderMonthly[$m]->pria : 0;
            $wanita = isset($genderMonthly[$m]) ? (int) $genderMonthly[$m]->wanita : 0;

            $maleData[] = $pria;
            $femaleData[] = $wanita;

            $totalPria += $pria;
            $totalWanita += $wanita;
        }

        $totalAll = $totalPria + $totalWanita;
        $ratioPria = $totalAll > 0 ? round(($totalPria / $totalAll) * 100, 1) : 0;
        $ratioWanita = $totalAll > 0 ? round(($totalWanita / $totalAll) * 100, 1) : 0;

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Laki-laki',
                    'data' => $maleData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.08)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 2.5,
                ],
                [
                    'label' => 'Perempuan',
                    'data' => $femaleData,
                    'borderColor' => '#EC4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.08)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 2.5,
                ],
            ],
            'totalPria' => $totalPria,
            'totalWanita' => $totalWanita,
            'totalAll' => $totalAll,
            'ratioPria' => $ratioPria,
            'ratioWanita' => $ratioWanita,
        ];
    }

    #[Computed]
    public function demographicAgeDistribution()
    {
        $year = Carbon::now()->year;

        // Ambil definisi kelompok umur dari tabel simrs.kelompok_umur via SirsHelper
        $categories = SirsHelper::getAgeGroupCategories();
        $caseSql = SirsHelper::ageGroupCategoryCaseSql('p.tgl_lahir', 'rp.tgl_registrasi');

        // Query distribusi pasien berdasarkan tabel kelompok_umur
        $rows = DB::connection('simrs')
            ->table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw("
                {$caseSql} as kode_kelompok,
                SUM(CASE WHEN p.jk = 'L' THEN 1 ELSE 0 END) as pria,
                SUM(CASE WHEN p.jk = 'P' THEN 1 ELSE 0 END) as wanita,
                COUNT(*) as total
            ")
            ->groupBy('kode_kelompok')
            ->get()
            ->keyBy('kode_kelompok');

        $colorMeta = [
            'NEO' => [
                'icon' => 'icon-[solar--sleeping-circle-bold-duotone]',
                'barColor' => 'bg-cyan-500',
                'badge' => 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400',
                'rangeDesc' => '0–28 Hari',
            ],
            'BAY' => [
                'icon' => 'icon-[solar--hearts-bold-duotone]',
                'barColor' => 'bg-teal-500',
                'badge' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400',
                'rangeDesc' => '29–364 Hari',
            ],
            'BAL' => [
                'icon' => 'icon-[solar--sticker-smile-circle-bold-duotone]',
                'barColor' => 'bg-amber-500',
                'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'rangeDesc' => '1–5 Tahun',
            ],
            'ANK' => [
                'icon' => 'icon-[solar--user-circle-bold-duotone]',
                'barColor' => 'bg-blue-500',
                'badge' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                'rangeDesc' => '6–11 Tahun',
            ],
            'RMJ' => [
                'icon' => 'icon-[solar--running-round-bold-duotone]',
                'barColor' => 'bg-emerald-500',
                'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                'rangeDesc' => '12–18 Tahun',
            ],
            'DWS' => [
                'icon' => 'icon-[solar--users-group-rounded-bold-duotone]',
                'barColor' => 'bg-purple-500',
                'badge' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400',
                'rangeDesc' => '19–45 Tahun',
            ],
            'PRL' => [
                'icon' => 'icon-[solar--user-hand-up-bold-duotone]',
                'barColor' => 'bg-indigo-500',
                'badge' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
                'rangeDesc' => '46–59 Tahun',
            ],
            'LNS' => [
                'icon' => 'icon-[solar--shield-user-bold-duotone]',
                'barColor' => 'bg-rose-500',
                'badge' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
                'rangeDesc' => '≥60 Tahun',
            ],
        ];

        $totalAll = $rows->sum('total') ?: 1;
        $maxTotal = $rows->max('total') ?: 1;

        $items = [];
        foreach ($categories as $kode => $info) {
            $row = $rows->get($kode);
            $total = $row ? (int) $row->total : 0;
            $pria = $row ? (int) $row->pria : 0;
            $wanita = $row ? (int) $row->wanita : 0;
            $pctTotal = round(($total / $totalAll) * 100, 1);
            $barWidth = round(($total / $maxTotal) * 100);

            $meta = $colorMeta[$kode] ?? [
                'icon' => 'solar--user-bold-duotone',
                'barColor' => 'bg-gray-500',
                'badge' => 'bg-gray-500/10 text-gray-600 dark:text-gray-400',
                'rangeDesc' => '',
            ];

            $items[] = [
                'kode' => $kode,
                'group' => $info['nama'],
                'rangeDesc' => $meta['rangeDesc'],
                'total' => $total,
                'pria' => $pria,
                'wanita' => $wanita,
                'percent' => $pctTotal,
                'barWidth' => $barWidth,
                'icon' => $meta['icon'],
                'barColor' => $meta['barColor'],
                'badge' => $meta['badge'],
            ];
        }

        return [
            'items' => $items,
            'total' => $totalAll,
        ];
    }

    #[Computed]
    public function demographicCategorySummary()
    {
        $year = Carbon::now()->year;

        // Query perbandingan kategori pasien: TNI, POLRI, Umum
        $row = DB::connection('simrs')
            ->table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('pasien_tni as pt', 'p.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'p.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw("
                SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as tni,
                SUM(CASE WHEN pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as polri,
                SUM(CASE WHEN pt.no_rkm_medis IS NULL AND pp.no_rkm_medis IS NULL THEN 1 ELSE 0 END) as umum,
                COUNT(*) as total
            ")
            ->first();

        $total = $row ? (int) $row->total : 0;
        $tni = $row ? (int) $row->tni : 0;
        $polri = $row ? (int) $row->polri : 0;
        $umum = $row ? (int) $row->umum : 0;

        return [
            'tni' => $tni,
            'polri' => $polri,
            'umum' => $umum,
            'total' => $total,
            'pctTni' => $total > 0 ? round(($tni / $total) * 100, 1) : 0,
            'pctPolri' => $total > 0 ? round(($polri / $total) * 100, 1) : 0,
            'pctUmum' => $total > 0 ? round(($umum / $total) * 100, 1) : 0,
        ];
    }

    #[Computed]
    public function dinasPatientsTrend()
    {
        $year = Carbon::now()->year;

        // Query tren bulanan pelayanan pasien dinas (TNI dan POLRI)
        $monthly = DB::connection('simrs')
            ->table('reg_periksa as rp')
            ->leftJoin('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'rp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->where(function ($q) {
                $q->whereNotNull('pt.no_rkm_medis')
                  ->orWhereNotNull('pp.no_rkm_medis');
            })
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw("
                MONTH(rp.tgl_registrasi) as bulan,
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as ralan,
                SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap,
                SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as igd,
                SUM(CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as tni,
                SUM(CASE WHEN pp.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END) as polri,
                COUNT(*) as total
            ")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $labels = [];
        $ralanData = [];
        $ranapData = [];
        $igdData = [];
        $totals = [];
        $totalRalan = 0;
        $totalRanap = 0;
        $totalIgd = 0;
        $totalTni = 0;
        $totalPolri = 0;
        $totalDinas = 0;

        foreach ($monthNames as $m => $name) {
            $labels[] = $name;
            $ralan = isset($monthly[$m]) ? (int) $monthly[$m]->ralan : 0;
            $ranap = isset($monthly[$m]) ? (int) $monthly[$m]->ranap : 0;
            $igd = isset($monthly[$m]) ? (int) $monthly[$m]->igd : 0;
            $tot = isset($monthly[$m]) ? (int) $monthly[$m]->total : 0;
            $tni = isset($monthly[$m]) ? (int) $monthly[$m]->tni : 0;
            $polri = isset($monthly[$m]) ? (int) $monthly[$m]->polri : 0;

            $ralanData[] = $ralan;
            $ranapData[] = $ranap;
            $igdData[] = $igd;
            $totals[] = $tot;

            $totalRalan += $ralan;
            $totalRanap += $ranap;
            $totalIgd += $igd;
            $totalTni += $tni;
            $totalPolri += $polri;
            $totalDinas += $tot;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rawat Jalan',
                    'data' => $ralanData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.08)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 2.5,
                ],
                [
                    'label' => 'Rawat Inap',
                    'data' => $ranapData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.08)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 2.5,
                ],
                [
                    'label' => 'Gawat Darurat (IGD)',
                    'data' => $igdData,
                    'borderColor' => '#F43F5E',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.08)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 2.5,
                ],
            ],
            'totals' => $totals,
            'totalRalan' => $totalRalan,
            'totalRanap' => $totalRanap,
            'totalIgd' => $totalIgd,
            'totalTni' => $totalTni,
            'totalPolri' => $totalPolri,
            'totalDinas' => $totalDinas,
        ];
    }

    #[Computed]
    public function dinasCategoryBreakdown()
    {
        $year = Carbon::now()->year;

        // Query rincian golongan pasien dinas
        $categories = DB::connection('simrs')
            ->table('reg_periksa as rp')
            ->leftJoin('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('pasien_polri as pp', 'rp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->leftJoin('golongan_tni as gt', 'pt.golongan_tni', '=', 'gt.id')
            ->leftJoin('golongan_polri as gp', 'pp.golongan_polri', '=', 'gp.id')
            ->where(function ($q) {
                $q->whereNotNull('pt.no_rkm_medis')
                  ->orWhereNotNull('pp.no_rkm_medis');
            })
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw("
                CASE 
                    WHEN gt.id IN (1, 2, 3) OR gp.id = 1 THEN 'Militer / Anggota Aktif'
                    WHEN gt.id IN (8, 9, 10) OR gp.id = 2 THEN 'ASN / PNS'
                    WHEN gt.id IN (5, 6, 7) OR gp.id = 3 THEN 'Keluarga Personel'
                    WHEN gt.id IN (4, 11, 12) OR gp.id = 4 THEN 'Purnawirawan'
                    ELSE 'Lainnya'
                END as kategori,
                COUNT(*) as total,
                SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap,
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as ralan,
                SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as igd
            ")
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $totalAll = $categories->sum('total') ?: 1;
        $maxTotal = $categories->max('total') ?: 1;

        $metaConfig = [
            'Militer / Anggota Aktif' => [
                'icon' => 'icon-[solar--shield-bold-duotone]',
                'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                'barColor' => 'bg-emerald-500',
            ],
            'ASN / PNS' => [
                'icon' => 'icon-[solar--user-id-bold-duotone]',
                'badge' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                'barColor' => 'bg-blue-500',
            ],
            'Keluarga Personel' => [
                'icon' => 'icon-[solar--users-group-rounded-bold-duotone]',
                'badge' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400',
                'barColor' => 'bg-purple-500',
            ],
            'Purnawirawan' => [
                'icon' => 'icon-[solar--medal-ribbon-star-bold-duotone]',
                'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'barColor' => 'bg-amber-500',
            ],
        ];

        $items = [];
        foreach ($categories as $c) {
            $conf = $metaConfig[$c->kategori] ?? [
                'icon' => 'icon-[solar--shield-bold-duotone]',
                'badge' => 'bg-gray-500/10 text-gray-600 dark:text-gray-400',
                'barColor' => 'bg-gray-500',
            ];

            $items[] = [
                'kategori' => $c->kategori,
                'total' => (int) $c->total,
                'ralan' => (int) $c->ralan,
                'ranap' => (int) $c->ranap,
                'igd' => (int) $c->igd,
                'percent' => round(($c->total / $totalAll) * 100, 1),
                'barWidth' => round(($c->total / $maxTotal) * 100),
                'icon' => $conf['icon'],
                'badge' => $conf['badge'],
                'barColor' => $conf['barColor'],
            ];
        }

        // Query Top 4 Satuan Pasien TNI
        $topSatuan = DB::connection('simrs')
            ->table('reg_periksa as rp')
            ->join('pasien_tni as pt', 'rp.no_rkm_medis', '=', 'pt.no_rkm_medis')
            ->leftJoin('satuan_tni as st', 'pt.satuan_tni', '=', 'st.id')
            ->whereYear('rp.tgl_registrasi', $year)
            ->selectRaw("COALESCE(st.nama_satuan, 'Lainnya') as nama_satuan, count(*) as total")
            ->groupBy('nama_satuan')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        return [
            'items' => $items,
            'topSatuan' => $topSatuan,
            'total' => $totalAll,
        ];
    }

    #[Computed]
    public function mutuSummary()
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        return Cache::remember("home_mutu_executive_summary_{$year}_{$month}", 600, function () use ($year, $month) {
            $inm = InmReportRepository::getSummary($year, $month);
            $spm = SpmReportRepository::getSummary($year, $month);
            $ikp = IkpReportRepository::getSummary($year, $month);
            $ppi = PpiReportRepository::getSummary($year, $month);

            // Pilih 6 indikator INM representatif untuk kartu ringkasan eksekutif
            $priorityKeys = [
                'waktu_tunggu_rajal' => [
                    'short_label' => 'Waktu Tunggu Ralan',
                    'icon' => 'icon-[solar--clock-circle-bold-duotone]',
                    'color' => 'text-blue-500',
                ],
                'fornas' => [
                    'short_label' => 'Kepatuhan Fornas',
                    'icon' => 'icon-[solar--pill-bold-duotone]',
                    'color' => 'text-amber-500',
                ],
                'sc_emergensi' => [
                    'short_label' => 'Tanggap SC Darurat',
                    'icon' => 'icon-[solar--danger-circle-bold-duotone]',
                    'color' => 'text-rose-500',
                ],
                'identifikasi' => [
                    'short_label' => 'Identifikasi Pasien',
                    'icon' => 'icon-[solar--user-check-bold-duotone]',
                    'color' => 'text-emerald-500',
                ],
                'pencegahan_jatuh' => [
                    'short_label' => 'Pencegahan Pasien Jatuh',
                    'icon' => 'icon-[solar--shield-check-bold-duotone]',
                    'color' => 'text-cyan-500',
                ],
                'visite_dokter' => [
                    'short_label' => 'Waktu Visite DPJP',
                    'icon' => 'icon-[solar--stethoscope-bold-duotone]',
                    'color' => 'text-indigo-500',
                ],
            ];

            $keyInm = [];
            foreach ($priorityKeys as $key => $meta) {
                if (isset($inm['indicators'][$key])) {
                    $ind = $inm['indicators'][$key];
                    $ind['short_label'] = $meta['short_label'];
                    $ind['icon'] = $meta['icon'];
                    $ind['color'] = $meta['color'];
                    $keyInm[] = $ind;
                }
            }

            return [
                'year' => $year,
                'month' => $month,
                'inm' => [
                    'total' => $inm['total_indicators'],
                    'achieved' => $inm['achieved_count'],
                    'unachieved' => $inm['unachieved_count'],
                    'average_score' => $inm['average_score'],
                    'compliance_percent' => $inm['total_indicators'] > 0 
                        ? round(($inm['achieved_count'] / $inm['total_indicators']) * 100, 1) 
                        : 0.0,
                    'key_indicators' => $keyInm,
                ],
                'spm' => [
                    'total' => $spm['total_indikator'],
                    'achieved' => $spm['total_tercapai'],
                    'percent' => $spm['persen_tercapai'],
                    'sections_summary' => collect($spm['sections'])->map(function ($sec, $key) {
                        $tot = count($sec['indicators']);
                        $ach = collect($sec['indicators'])->where('is_achieved', true)->count();
                        return [
                            'key' => $key,
                            'unit' => $sec['unit'],
                            'total' => $tot,
                            'achieved' => $ach,
                            'rate' => $tot > 0 ? round(($ach / $tot) * 100) : 0,
                        ];
                    })->values()->toArray(),
                ],
                'ikp' => [
                    'total' => $ikp['counts']['total'],
                    'sentinel' => $ikp['counts']['sentinel'],
                    'ktd' => $ikp['counts']['ktd'],
                ],
                'ppi' => [
                    'total_infeksi' => $ppi['total_infeksi'],
                    'bundle_rata' => $ppi['kepatuhan_bundle_rata'],
                ],
            ];
        });
    }

    public function refreshMutuCache()
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        Cache::forget("home_mutu_executive_summary_{$year}_{$month}");
    }

    public function render()
    {
        return view('pages.home');
    }
}
