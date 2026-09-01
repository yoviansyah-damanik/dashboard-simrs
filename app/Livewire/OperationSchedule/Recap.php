<?php

namespace App\Livewire\OperationSchedule;

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

    public $searchPackage = '';
    public $mainView = 'list';

    public function mount()
    {
        $this->selectedMonth = date('n');
        $this->selectedYear = date('Y');
        $this->setPeriod('this_month');
    }

    public function refreshCharts()
    {
        if ($this->mainView === 'chart') {
            $this->dispatch('refresh-recap-charts', charts: $this->chartData);
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

    public function updatedSearchPackage()
    {
        $this->refreshCharts();
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

    public function getRecapDataProperty()
    {
        return DB::connection('simrs')
            ->table('booking_operasi')
            ->join('paket_operasi', 'booking_operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->whereBetween('booking_operasi.tanggal', [$this->startDate, $this->endDate])
            ->when($this->searchPackage, function ($query) {
                $query->where('paket_operasi.nm_perawatan', 'like', '%' . $this->searchPackage . '%');
            })
            ->select(
                'paket_operasi.kode_paket',
                'paket_operasi.nm_perawatan as nama_paket',
                'paket_operasi.kategori',
                DB::raw('count(*) as jumlah_operasi'),
                DB::raw('sum(case when booking_operasi.status = "Selesai" then 1 else 0 end) as jumlah_selesai'),
                DB::raw('sum(case when booking_operasi.status = "Proses Operasi" then 1 else 0 end) as jumlah_proses'),
                DB::raw('sum(case when booking_operasi.status = "Menunggu" then 1 else 0 end) as jumlah_menunggu')
            )
            ->groupBy('paket_operasi.kode_paket', 'paket_operasi.nm_perawatan', 'paket_operasi.kategori')
            ->orderByDesc('jumlah_operasi')
            ->get();
    }

    public function getCategoryDataProperty()
    {
        return DB::connection('simrs')
            ->table('booking_operasi')
            ->join('paket_operasi', 'booking_operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->whereBetween('booking_operasi.tanggal', [$this->startDate, $this->endDate])
            ->select('paket_operasi.kategori', DB::raw('count(*) as jumlah'))
            ->groupBy('paket_operasi.kategori')
            ->orderByDesc('jumlah')
            ->get();
    }

    public function getChartDataProperty()
    {
        $topPackages = $this->recapData->take(10);

        return [
            'packages' => [
                'labels' => $topPackages->pluck('nama_paket')->toArray(),
                'datasets' => [[
                    'label' => 'Jumlah Operasi',
                    'data' => $topPackages->pluck('jumlah_operasi')->toArray(),
                    'backgroundColor' => '#4f46e5',
                    'borderRadius' => 4
                ]]
            ],
            'category' => [
                'labels' => $this->categoryData->pluck('kategori')->toArray(),
                'datasets' => [[
                    'data' => $this->categoryData->pluck('jumlah')->toArray(),
                    'backgroundColor' => ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#0ea5e9', '#8b5cf6', '#db2777'],
                    'borderWidth' => 0
                ]]
            ]
        ];
    }

    public function render()
    {
        return view('pages.operation-schedule.recap', [
            'recapData' => $this->recapData,
            'chartData' => $this->chartData,
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
        ])->title('Rekap Operasi');
    }
}
