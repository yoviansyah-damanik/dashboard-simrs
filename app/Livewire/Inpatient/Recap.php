<?php

namespace App\Livewire\Inpatient;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Recap extends Component
{
    public $startDate;
    public $endDate;
    public $period = 'this_month';

    public $selectedMonth;
    public $selectedYear;

    public $mainTab = 'current_patients';
    public $searchPatient = '';
    public $mainView = 'list';
    public $snapshotView = 'list';

    public function mount()
    {
        $this->selectedMonth = date('n');
        $this->selectedYear = date('Y');
        $this->setPeriod('this_month');
    }

    public function switchTab($tab)
    {
        $this->mainTab = $tab;
        $this->refreshCharts();
    }

    public function refreshCharts()
    {
        if ($this->mainTab === 'recap' && $this->mainView === 'chart') {
            $this->dispatch('refresh-all-charts', charts: $this->patientDemographics['charts']);
            $this->dispatch('refresh-main-charts', charts: $this->overallStats['charts']);
        }
        
        if ($this->mainTab === 'snapshot' && $this->snapshotView === 'chart') {
            $this->dispatch('refresh-snapshot-charts', charts: $this->snapshotCharts);
        }
    }

    public function updatedPeriod($value)
    {
        $this->setPeriod($value);
    }

    public function updatedMainView($value)
    {
        if ($value === 'chart') {
            $this->refreshCharts();
        }
    }

    public function updatedStartDate()
    {
        $this->refreshCharts();
    }

    public function updatedEndDate()
    {
        $this->refreshCharts();
    }

    public function updatedSnapshotView($value)
    {
        if ($value === 'chart') {
            $this->refreshCharts();
        }
    }

    public function updatedSelectedMonth()
    {
        if ($this->period === 'monthly') {
            $this->setPeriod('monthly');
        }
    }

    public function updatedSelectedYear()
    {
        if ($this->period === 'monthly' || $this->period === 'yearly') {
            $this->setPeriod($this->period);
        }
    }

    public function setPeriod($value)
    {
        switch ($value) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = Carbon::today()->subDays(6)->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'last_30_days':
                $this->startDate = Carbon::today()->subDays(29)->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'monthly':
                $this->startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'yearly':
                $this->startDate = Carbon::createFromDate($this->selectedYear, 1, 1)->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::createFromDate($this->selectedYear, 1, 1)->endOfYear()->format('Y-m-d');
                break;
        }

        $this->refreshCharts();
    }


    public function getRealtimeBedStatsProperty()
    {
        return DB::connection('simrs')
            ->table('kamar')
            ->where('kd_bangsal', '!=', 'TRANS')
            ->select(
                DB::raw('count(*) as kapasitas'),
                DB::raw('sum(case when status = "ISI" then 1 else 0 end) as terisi'),
                DB::raw('sum(case when status = "KOSONG" and statusdata = "1" then 1 else 0 end) as kosong'),
                DB::raw('sum(case when statusdata = "0" then 1 else 0 end) as tidak_tersedia')
            )
            ->first();
    }

    public function getSnapshotStatsProperty()
    {
        $realtime = $this->realtimeBedStats;

        $admissions = DB::connection('simrs')
            ->table('kamar_inap')
            ->whereBetween('tgl_masuk', [$this->startDate, $this->endDate])
            ->where('stts_pulang', '!=', 'Pindah Kamar')
            ->count();

        $discharges = DB::connection('simrs')
            ->table('kamar_inap')
            ->whereBetween('tgl_keluar', [$this->startDate, $this->endDate])
            ->where('stts_pulang', '!=', 'Pindah Kamar')
            ->count();

        $tniPatients = DB::connection('simrs')
            ->table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien_tni', 'reg_periksa.no_rkm_medis', '=', 'pasien_tni.no_rkm_medis')
            ->whereBetween('kamar_inap.tgl_masuk', [$this->startDate, $this->endDate])
            ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar')
            ->count();

        return [
            'total_bed' => $realtime->kapasitas,
            'occupied' => $realtime->terisi,
            'available' => $realtime->kosong,
            'admissions' => $admissions,
            'discharges' => $discharges,
            'tni_patients' => $tniPatients
        ];
    }


    public function getRealtimeClassStatsProperty()
    {
        return DB::connection('simrs')
            ->table('kamar')
            ->where('kd_bangsal', '!=', 'TRANS')
            ->select(
                'kelas',
                DB::raw('count(*) as kapasitas'),
                DB::raw('sum(case when kamar.status = "ISI" then 1 else 0 end) as terisi')
            )
            ->groupBy('kelas')
            ->get();
    }

    public function getPatientDemographicsProperty()
    {
        $baseQuery = DB::connection('simrs')
            ->table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('tgl_masuk', [$this->startDate, $this->endDate])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('kamar')
                    ->whereColumn('kamar_inap.kd_kamar', 'kamar.kd_kamar')
                    ->where('kd_bangsal', '!=', 'TRANS');
            });

        $total = (clone $baseQuery)->count();

        $gender = (clone $baseQuery)
            ->select('pasien.jk', DB::raw('count(*) as total'))
            ->groupBy('pasien.jk')
            ->get();

        $insurance = (clone $baseQuery)
            ->select('penjab.png_jawab', DB::raw('count(*) as total'))
            ->groupBy('penjab.png_jawab')
            ->orderBy('total', 'desc')
            ->get();

        $age = (clone $baseQuery)
            ->select(DB::raw("
                CASE 
                    WHEN reg_periksa.umurdaftar < 1 AND reg_periksa.sttsumur = 'Hr' THEN 'Bayi (<1 th)'
                    WHEN reg_periksa.umurdaftar < 1 AND reg_periksa.sttsumur = 'Bl' THEN 'Bayi (<1 th)'
                    WHEN reg_periksa.umurdaftar <= 5 AND reg_periksa.sttsumur = 'Th' THEN 'Balita (1-5 th)'
                    WHEN reg_periksa.umurdaftar <= 12 AND reg_periksa.sttsumur = 'Th' THEN 'Anak (6-12 th)'
                    WHEN reg_periksa.umurdaftar <= 18 AND reg_periksa.sttsumur = 'Th' THEN 'Remaja (13-18 th)'
                    WHEN reg_periksa.umurdaftar <= 60 AND reg_periksa.sttsumur = 'Th' THEN 'Dewasa (19-60 th)'
                    ELSE 'Lansia (>60 th)'
                END as kelompok_umur
            "), 
            DB::raw('count(*) as total'),
            DB::raw('sum(case when pasien.jk = "L" then 1 else 0 end) as laki'),
            DB::raw('sum(case when pasien.jk = "P" then 1 else 0 end) as perempuan')
            )
            ->groupBy('kelompok_umur')
            ->orderBy('total', 'desc')
            ->get();

        $discharge = (clone $baseQuery)
            ->select('kamar_inap.stts_pulang', DB::raw('count(*) as total'))
            ->where('kamar_inap.stts_pulang', '!=', '-')
            ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar')
            ->groupBy('kamar_inap.stts_pulang')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'total' => $total,
            'gender' => $gender,
            'insurance' => $insurance,
            'age' => $age,
            'discharge' => $discharge,
            'charts' => [
                'gender' => [
                    'labels' => $gender->map(fn($g) => $g->jk == 'L' ? 'Laki-laki' : 'Perempuan')->toArray(),
                    'datasets' => [[
                        'data' => $gender->pluck('total')->toArray(),
                        'backgroundColor' => ['#2563eb', '#db2777'],
                        'borderWidth' => 0
                    ]]
                ],
                'age' => [
                    'labels' => $age->pluck('kelompok_umur')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Pasien',
                        'data' => $age->pluck('total')->toArray(),
                        'backgroundColor' => '#4f46e5',
                        'borderRadius' => 6
                    ]]
                ],
                'insurance' => [
                    'labels' => $insurance->take(10)->pluck('png_jawab')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Pasien',
                        'data' => $insurance->take(10)->pluck('total')->toArray(),
                        'backgroundColor' => '#10b981',
                        'borderRadius' => 4
                    ]]
                ],
                'discharge' => [
                    'labels' => $discharge->pluck('stts_pulang')->toArray(),
                    'datasets' => [[
                        'data' => $discharge->pluck('total')->toArray(),
                        'backgroundColor' => ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#6366f1', '#a855f7'],
                        'borderWidth' => 0
                    ]]
                ]
            ]
        ];
    }

    public function getRecapDataProperty()
    {
        // 1. Get all available wards and classes from 'kamar'
        $allWards = DB::connection('simrs')
            ->table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar.kd_bangsal', '!=', 'TRANS')
            ->select('bangsal.nm_bangsal', 'kamar.kelas', 'kamar.kd_bangsal')
            ->groupBy('bangsal.nm_bangsal', 'kamar.kelas', 'kamar.kd_bangsal');

        // 2. Subquery for real-time bed status (current occupancy)
        $bedStatus = DB::connection('simrs')
            ->table('kamar')
            ->where('kd_bangsal', '!=', 'TRANS')
            ->select(
                'kd_bangsal',
                'kelas',
                DB::raw('count(*) as kapasitas'),
                DB::raw('sum(case when kamar.status = "ISI" then 1 else 0 end) as terisi'),
                DB::raw('sum(case when kamar.status = "KOSONG" and kamar.statusdata = "1" then 1 else 0 end) as tersedia')
            )
            ->groupBy('kd_bangsal', 'kelas');

        // 3. Subquery for historical patient metrics in selected period
        // Includes patients who were in the room at any point during the period
        $patientStats = DB::connection('simrs')
            ->table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->where('kamar_inap.tgl_masuk', '<=', $this->endDate)
            ->where(function ($q) {
                $q->where('kamar_inap.tgl_keluar', '>=', $this->startDate)
                    ->orWhere('kamar_inap.stts_pulang', '-');
            })
            ->where('kamar.kd_bangsal', '!=', 'TRANS')
            ->select(
                'kamar.kd_bangsal',
                'kamar.kelas',
                DB::raw('count(*) as total_pasien'),
                DB::raw('sum(case when pasien.jk = "L" then 1 else 0 end) as total_laki'),
                DB::raw('sum(case when pasien.jk = "P" then 1 else 0 end) as total_perempuan'),
                DB::raw('sum(case when stts_pulang NOT IN ("-", "Pindah Kamar") then 1 else 0 end) as jumlah_pulang'),
                DB::raw('sum(case when stts_pulang = "-" then 1 else 0 end) as jumlah_dirawat'),
                DB::raw('sum(case when stts_pulang = "Rujuk" then 1 else 0 end) as jumlah_dirujuk'),
                DB::raw('sum(case when stts_pulang IN ("APS", "Atas Permintaan Sendiri", "Pulang Paksa") then 1 else 0 end) as jumlah_aps'),
                DB::raw('sum(case when stts_pulang = "Meninggal" then 1 else 0 end) as jumlah_meninggal'),
                DB::raw('sum(case when lama = 0 then 1 else lama end) as total_hp'),
                DB::raw('avg(case when stts_pulang NOT IN ("-", "Pindah Kamar") then lama else null end) as rata_lama_hari')
            )
            ->groupBy('kamar.kd_bangsal', 'kamar.kelas');

        // 4. Combine everything starting from ALL wards
        return DB::connection('simrs')
            ->query()
            ->fromSub($allWards, 'w')
            ->leftJoinSub($bedStatus, 'bed', function ($join) {
                $join->on('w.kd_bangsal', '=', 'bed.kd_bangsal')
                    ->on('w.kelas', '=', 'bed.kelas');
            })
            ->leftJoinSub($patientStats, 'p', function ($join) {
                $join->on('w.kd_bangsal', '=', 'p.kd_bangsal')
                    ->on('w.kelas', '=', 'p.kelas');
            })
            ->select(
                'w.nm_bangsal',
                'w.kelas',
                DB::raw('IFNULL(bed.kapasitas, 0) as kapasitas'),
                DB::raw('IFNULL(bed.terisi, 0) as terisi'),
                DB::raw('IFNULL(bed.tersedia, 0) as tersedia'),
                DB::raw('IFNULL(p.total_pasien, 0) as total_pasien'),
                DB::raw('IFNULL(p.total_laki, 0) as total_laki'),
                DB::raw('IFNULL(p.total_perempuan, 0) as total_perempuan'),
                DB::raw('IFNULL(p.jumlah_pulang, 0) as jumlah_pulang'),
                DB::raw('IFNULL(p.jumlah_dirawat, 0) as jumlah_dirawat'),
                DB::raw('IFNULL(p.jumlah_dirujuk, 0) as jumlah_dirujuk'),
                DB::raw('IFNULL(p.jumlah_aps, 0) as jumlah_aps'),
                DB::raw('IFNULL(p.jumlah_meninggal, 0) as jumlah_meninggal'),
                DB::raw('IFNULL(p.total_hp, 0) as total_hp'),
                DB::raw('IFNULL(p.rata_lama_hari, 0) as rata_lama_hari')
            )
            ->orderBy('w.nm_bangsal')
            ->orderBy('w.kelas')
            ->get();
    }

    public function getCurrentPatientsProperty()
    {
        return DB::connection('simrs')
            ->table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'kamar_inap.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'bangsal.nm_bangsal',
                'kamar.kelas',
                'penjab.png_jawab',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                DB::raw('DATEDIFF(NOW(), kamar_inap.tgl_masuk) + 1 as lama_inap')
            )
            ->where('kamar_inap.stts_pulang', '-')
            ->where('kamar.kd_bangsal', '!=', 'TRANS')
            ->when($this->searchPatient, function ($query) {
                $query->where(function ($q) {
                    $q->where('pasien.nm_pasien', 'like', '%' . $this->searchPatient . '%')
                        ->orWhere('pasien.no_rkm_medis', 'like', '%' . $this->searchPatient . '%')
                        ->orWhere('kamar_inap.no_rawat', 'like', '%' . $this->searchPatient . '%');
                });
            })
            ->orderBy('kamar_inap.tgl_masuk', 'desc')
            ->get();
    }

    public function getOverallStatsProperty()
    {
        $diffDays = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
        $totalHp = $this->recapData->sum('total_hp');
        $totalKapasitas = $this->recapData->sum('kapasitas');
        $totalPulang = $this->recapData->sum('jumlah_pulang');

        // Trend Data (In/Out)
        $trendAdmissions = DB::connection('simrs')
            ->table('kamar_inap')
            ->select('tgl_masuk as date', DB::raw('count(*) as total'))
            ->whereBetween('tgl_masuk', [$this->startDate, $this->endDate])
            ->where('stts_pulang', '!=', 'Pindah Kamar')
            ->groupBy('tgl_masuk')
            ->orderBy('tgl_masuk')
            ->get();

        $trendDischarges = DB::connection('simrs')
            ->table('kamar_inap')
            ->select('tgl_keluar as date', DB::raw('count(*) as total'))
            ->whereBetween('tgl_keluar', [$this->startDate, $this->endDate])
            ->where('stts_pulang', '!=', 'Pindah Kamar')
            ->where('stts_pulang', '!=', '-')
            ->groupBy('tgl_keluar')
            ->orderBy('tgl_keluar')
            ->get();

        return [
            'alos' => $totalPulang > 0 ? $totalHp / $totalPulang : 0,
            'bor' => ($totalKapasitas > 0 && $diffDays > 0) ? ($totalHp / ($totalKapasitas * $diffDays)) * 100 : 0,
            'bto' => $totalKapasitas > 0 ? $totalPulang / $totalKapasitas : 0,
            'gdr' => $this->recapData->sum('total_pasien') > 0 ? ($this->recapData->sum('jumlah_meninggal') / $this->recapData->sum('total_pasien')) * 1000 : 0,
            'charts' => [
                'wards_patients' => [
                    'labels' => $this->recapData->pluck('nm_bangsal')->toArray(),
                    'datasets' => [[
                        'label' => 'Total Pasien',
                        'data' => $this->recapData->pluck('total_pasien')->toArray(),
                        'backgroundColor' => '#4f46e5',
                        'borderRadius' => 4
                    ]]
                ],
                'wards_bor' => [
                    'labels' => $this->recapData->pluck('nm_bangsal')->toArray(),
                    'datasets' => [[
                        'label' => 'BOR (%)',
                        'data' => $this->recapData->map(function ($item) {
                            $diffDays = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
                            return ($item->kapasitas > 0 && $diffDays > 0) ? round(($item->total_hp / ($item->kapasitas * $diffDays)) * 100, 1) : 0;
                        })->toArray(),
                        'backgroundColor' => '#10b981',
                        'borderRadius' => 4
                    ]]
                ],
                'wards_alos' => [
                    'labels' => $this->recapData->pluck('nm_bangsal')->toArray(),
                    'datasets' => [[
                        'label' => 'ALOS (Hari)',
                        'data' => $this->recapData->pluck('rata_lama_hari')->toArray(),
                        'backgroundColor' => '#8b5cf6',
                        'borderRadius' => 4
                    ]]
                ],
                'wards_bto' => [
                    'labels' => $this->recapData->pluck('nm_bangsal')->toArray(),
                    'datasets' => [[
                        'label' => 'BTO (Kali)',
                        'data' => $this->recapData->map(fn($item) => $item->kapasitas > 0 ? round($item->jumlah_pulang / $item->kapasitas, 2) : 0)->toArray(),
                        'backgroundColor' => '#f59e0b',
                        'borderRadius' => 4
                    ]]
                ],
                'wards_gdr' => [
                    'labels' => $this->recapData->pluck('nm_bangsal')->toArray(),
                    'datasets' => [[
                        'label' => 'GDR (Permil)',
                        'data' => $this->recapData->map(fn($item) => $item->total_pasien > 0 ? round(($item->jumlah_meninggal / $item->total_pasien) * 1000, 1) : 0)->toArray(),
                        'backgroundColor' => '#ef4444',
                        'borderRadius' => 4
                    ]]
                ],
                'trend' => [
                    'labels' => $trendAdmissions->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Masuk',
                            'data' => $trendAdmissions->pluck('total')->toArray(),
                            'borderColor' => '#4f46e5',
                            'tension' => 0.4,
                            'fill' => false
                        ],
                        [
                            'label' => 'Keluar',
                            'data' => $trendDischarges->pluck('total')->toArray(),
                            'borderColor' => '#10b981',
                            'tension' => 0.4,
                            'fill' => false
                        ]
                    ]
                ]
            ]
        ];
    }


    public function getSnapshotChartsProperty()
    {
        $classStats = $this->realtimeClassStats;
        $allWards = $this->recapData;

        return [
            'class_occupancy' => [
                'labels' => $classStats->pluck('kelas')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Bed Terisi',
                        'data' => $classStats->pluck('terisi')->toArray(),
                        'backgroundColor' => '#4f46e5',
                        'borderRadius' => 4
                    ],
                    [
                        'label' => 'Total Kapasitas',
                        'data' => $classStats->pluck('kapasitas')->toArray(),
                        'backgroundColor' => '#e2e8f0',
                        'borderRadius' => 4
                    ]
                ]
            ],
            'ward_occupancy' => [
                'labels' => $allWards->pluck('nm_bangsal')->toArray(),
                'datasets' => [[
                    'label' => 'Persentase Terisi (%)',
                    'data' => $allWards->map(fn($w) => $w->kapasitas > 0 ? round(($w->terisi / $w->kapasitas) * 100, 1) : 0)->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderRadius' => 4
                ]]
            ]
        ];
    }

    public function render()
    {
        return view('pages.inpatient.recap', [
            'recapData' => $this->recapData,
            'currentPatients' => $this->currentPatients,
            'realtimeClassStats' => $this->realtimeClassStats,
            'realtimeBed' => $this->realtimeBedStats,
            'snapshotCharts' => $this->snapshotCharts,
            'demographics' => $this->patientDemographics,
            'snapshotStats' => $this->snapshotStats,
            'overall' => $this->overallStats,
            'months' => [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ],
            'years' => range(date('Y'), date('Y') - 5)
        ])->title('Rekap Rawat Inap');
    }
}
