<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Recap extends Component
{
    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    #[Url]
    public $period = 'all';

    public $selectedMonth;
    public $selectedYear;

    public $mainView = 'chart'; // chart, figures

    public function mount()
    {
        $this->selectedMonth = date('n');
        $this->selectedYear = date('Y');
        $this->syncDates();
    }

    public function updatedPeriod()
    {
        $this->syncDates();
    }
    public function updatedSelectedMonth()
    {
        $this->syncDates();
    }
    public function updatedSelectedYear()
    {
        $this->syncDates();
    }
    public function updatedStartDate()
    {
        $this->refreshCharts();
    }
    public function updatedEndDate()
    {
        $this->refreshCharts();
    }
    public function updatedMainView($value)
    {
        if ($value === 'chart') $this->refreshCharts();
    }

    private function syncDates()
    {
        switch ($this->period) {
            case 'all':
                $this->startDate = null;
                $this->endDate = null;
                break;
            case 'today':
                $this->startDate = date('Y-m-d');
                $this->endDate = date('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = date('Y-m-d', strtotime('-7 days'));
                $this->endDate = date('Y-m-d');
                break;
            case 'last_30_days':
                $this->startDate = date('Y-m-d', strtotime('-30 days'));
                $this->endDate = date('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = date('Y-m-d', strtotime('monday this week'));
                $this->endDate = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'this_month':
                $this->startDate = date('Y-m-01');
                $this->endDate = date('Y-m-t');
                break;
            case 'this_year':
                $this->startDate = date('Y-01-01');
                $this->endDate = date('Y-12-31');
                break;
            case 'monthly':
                $this->startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');
                break;
            case 'yearly':
                $this->startDate = Carbon::create($this->selectedYear, 1, 1)->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::create($this->selectedYear, 1, 1)->endOfYear()->format('Y-m-d');
                break;
        }
        $this->refreshCharts();
    }

    public function refreshCharts()
    {
        if ($this->mainView === 'chart') {
            $this->dispatch('refresh-main-charts', charts: $this->summary['charts']);
            $this->dispatch('refresh-demo-charts', charts: $this->demographics['charts']);
        }
    }

    #[Computed]
    public function months()
    {
        return [
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
            12 => 'Desember',
        ];
    }

    #[Computed]
    public function years()
    {
        return range(date('Y') - 5, date('Y'));
    }

    private function baseQuery()
    {
        return DB::connection('simrs')
            ->table('pasien')
            ->when(
                $this->startDate && $this->endDate,
                fn($q) => $q->whereBetween('tgl_daftar', [$this->startDate, $this->endDate])
            );
    }

    #[Computed]
    public function summary()
    {
        $total = (clone $this->baseQuery())->count();

        $genderStats = (clone $this->baseQuery())
            ->selectRaw('sum(case when jk = "L" then 1 else 0 end) as laki, sum(case when jk = "P" then 1 else 0 end) as perempuan')
            ->first();

        $typeStats = (clone $this->baseQuery())
            ->leftJoin('pasien_tni', 'pasien.no_rkm_medis', '=', 'pasien_tni.no_rkm_medis')
            ->leftJoin('pasien_polri', 'pasien.no_rkm_medis', '=', 'pasien_polri.no_rkm_medis')
            ->selectRaw(
                'sum(case when pasien_polri.pangkat_polri is not null then 1 else 0 end) as polri,'
                    . 'sum(case when pasien_tni.pangkat_tni is not null then 1 else 0 end) as tni,'
                    . 'sum(case when pasien_tni.pangkat_tni is null and pasien_polri.pangkat_polri is null then 1 else 0 end) as umum'
            )
            ->first();

        $averageAge = (clone $this->baseQuery())
            ->selectRaw('avg(TIMESTAMPDIFF(year, tgl_lahir, now())) as average_age')
            ->value('average_age');

        $ageGroups = (clone $this->baseQuery())
            ->selectRaw(
                'case '
                    . 'when TIMESTAMPDIFF(year, tgl_lahir, now()) < 5 then "' . Patient::KELOMPOK_UMUR['balita'] . '" '
                    . 'when TIMESTAMPDIFF(year, tgl_lahir, now()) between 5 and 11 then "' . Patient::KELOMPOK_UMUR['anak'] . '" '
                    . 'when TIMESTAMPDIFF(year, tgl_lahir, now()) between 12 and 25 then "' . Patient::KELOMPOK_UMUR['remaja'] . '" '
                    . 'when TIMESTAMPDIFF(year, tgl_lahir, now()) between 26 and 45 then "' . Patient::KELOMPOK_UMUR['dewasa'] . '" '
                    . 'when TIMESTAMPDIFF(year, tgl_lahir, now()) between 46 and 65 then "' . Patient::KELOMPOK_UMUR['lansia'] . '" '
                    . 'else "' . Patient::KELOMPOK_UMUR['lainnya'] . '" end as kelompok, '
                    . 'count(*) as total, '
                    . 'sum(case when jk = "L" then 1 else 0 end) as laki, '
                    . 'sum(case when jk = "P" then 1 else 0 end) as perempuan'
            )
            ->groupBy('kelompok')
            ->get()
            ->sortBy(fn($item) => array_search($item->kelompok, Patient::KELOMPOK_UMUR))
            ->values();

        $regionStats = (clone $this->baseQuery())
            ->join('propinsi', 'pasien.kd_prop', '=', 'propinsi.kd_prop')
            ->select('propinsi.nm_prop', DB::raw('count(*) as total'))
            ->groupBy('propinsi.nm_prop')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $trend = $this->trendStats();

        return [
            'total' => $total,
            'gender' => [
                'laki' => $genderStats->laki ?? 0,
                'perempuan' => $genderStats->perempuan ?? 0,
            ],
            'type' => [
                'umum' => $typeStats->umum ?? 0,
                'tni' => $typeStats->tni ?? 0,
                'polri' => $typeStats->polri ?? 0,
            ],
            'average_age' => round($averageAge ?? 0, 1),
            'age_groups' => $ageGroups,
            'charts' => [
                'trend' => [
                    'labels' => $trend->pluck('label')->toArray(),
                    'datasets' => [[
                        'label' => 'Pasien Terdaftar',
                        'data' => $trend->pluck('total')->toArray(),
                        'borderColor' => '#4f46e5',
                        'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ]],
                ],
                'region' => [
                    'labels' => $regionStats->pluck('nm_prop')->toArray(),
                    'datasets' => [[
                        'label' => 'Total Pasien',
                        'data' => $regionStats->pluck('total')->toArray(),
                        'backgroundColor' => '#4f46e5',
                        'borderRadius' => 6,
                    ]],
                ],
            ],
        ];
    }

    private function trendStats()
    {
        $hasRange = $this->startDate && $this->endDate;
        $spanInDays = $hasRange ? Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) : null;

        if (!$hasRange) {
            // Keseluruhan data: kelompokkan per tahun pendaftaran
            return (clone $this->baseQuery())
                ->selectRaw('YEAR(tgl_daftar) as label, count(*) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }

        if ($spanInDays > 60) {
            return (clone $this->baseQuery())
                ->selectRaw('DATE_FORMAT(tgl_daftar, "%Y-%m") as label, count(*) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }

        return (clone $this->baseQuery())
            ->selectRaw('tgl_daftar as raw_date, count(*) as total')
            ->groupBy('raw_date')
            ->orderBy('raw_date')
            ->get()
            ->map(fn($item) => (object) [
                'label' => date('d/m', strtotime($item->raw_date)),
                'total' => $item->total,
            ]);
    }

    #[Computed]
    public function demographics()
    {
        $payType = (clone $this->baseQuery())
            ->join('penjab', 'pasien.kd_pj', '=', 'penjab.kd_pj')
            ->select('penjab.png_jawab', DB::raw('count(*) as total'))
            ->groupBy('penjab.png_jawab')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $education = (clone $this->baseQuery())
            ->select('pnd', DB::raw('count(*) as total'))
            ->groupBy('pnd')
            ->orderByDesc('total')
            ->get();

        $maritalStatus = (clone $this->baseQuery())
            ->select('stts_nikah', DB::raw('count(*) as total'))
            ->groupBy('stts_nikah')
            ->orderByDesc('total')
            ->get();

        $religion = (clone $this->baseQuery())
            ->select('agama', DB::raw('count(*) as total'))
            ->groupBy('agama')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $bloodType = (clone $this->baseQuery())
            ->select('gol_darah', DB::raw('count(*) as total'))
            ->groupBy('gol_darah')
            ->orderByDesc('total')
            ->get();

        $palette = ['#4f46e5', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#8b5cf6', '#ef4444', '#84cc16', '#f97316', '#14b8a6'];

        return [
            'pay_type' => $payType,
            'education' => $education,
            'marital_status' => $maritalStatus,
            'religion' => $religion,
            'blood_type' => $bloodType,
            'charts' => [
                'gender' => [
                    'labels' => ['Laki-laki', 'Perempuan'],
                    'datasets' => [[
                        'data' => [$this->summary['gender']['laki'], $this->summary['gender']['perempuan']],
                        'backgroundColor' => ['#4f46e5', '#ec4899'],
                    ]],
                ],
                'age' => [
                    'labels' => $this->summary['age_groups']->pluck('kelompok')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Laki-laki',
                            'data' => $this->summary['age_groups']->pluck('laki')->toArray(),
                            'backgroundColor' => '#4f46e5',
                            'borderRadius' => 4,
                        ],
                        [
                            'label' => 'Perempuan',
                            'data' => $this->summary['age_groups']->pluck('perempuan')->toArray(),
                            'backgroundColor' => '#ec4899',
                            'borderRadius' => 4,
                        ],
                    ],
                ],
                'pay_type' => [
                    'labels' => $payType->pluck('png_jawab')->toArray(),
                    'datasets' => [[
                        'label' => 'Pasien',
                        'data' => $payType->pluck('total')->toArray(),
                        'backgroundColor' => '#10b981',
                        'borderRadius' => 4,
                    ]],
                ],
                'education' => [
                    'labels' => $education->pluck('pnd')->toArray(),
                    'datasets' => [[
                        'label' => 'Pasien',
                        'data' => $education->pluck('total')->toArray(),
                        'backgroundColor' => '#f59e0b',
                        'borderRadius' => 4,
                    ]],
                ],
                'marital_status' => [
                    'labels' => $maritalStatus->pluck('stts_nikah')->toArray(),
                    'datasets' => [[
                        'data' => $maritalStatus->pluck('total')->toArray(),
                        'backgroundColor' => $palette,
                    ]],
                ],
                'religion' => [
                    'labels' => $religion->pluck('agama')->toArray(),
                    'datasets' => [[
                        'label' => 'Pasien',
                        'data' => $religion->pluck('total')->toArray(),
                        'backgroundColor' => '#06b6d4',
                        'borderRadius' => 4,
                    ]],
                ],
                'blood_type' => [
                    'labels' => $bloodType->pluck('gol_darah')->toArray(),
                    'datasets' => [[
                        'data' => $bloodType->pluck('total')->toArray(),
                        'backgroundColor' => $palette,
                    ]],
                ],
            ],
        ];
    }

    public function render()
    {
        return view('pages.patient.recap');
    }
}
