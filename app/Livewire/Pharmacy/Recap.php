<?php

namespace App\Livewire\Pharmacy;

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
    public $period = 'this_month';

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
            $this->dispatch('refresh-drug-charts', charts: $this->drugUsage['charts']);
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
            ->table('resep_obat as ro')
            ->when(
                $this->startDate && $this->endDate,
                fn($q) => $q->whereBetween('ro.tgl_perawatan', [$this->startDate, $this->endDate])
            );
    }

    private function obatBaseQuery()
    {
        return DB::connection('simrs')
            ->table('detail_pemberian_obat as dpo')
            ->when(
                $this->startDate && $this->endDate,
                fn($q) => $q->whereBetween('dpo.tgl_perawatan', [$this->startDate, $this->endDate])
            );
    }

    #[Computed]
    public function summary()
    {
        $total = (clone $this->baseQuery())->count();

        $statusStats = (clone $this->baseQuery())
            ->selectRaw('sum(case when status = "ralan" then 1 else 0 end) as ralan, sum(case when status = "ranap" then 1 else 0 end) as ranap')
            ->first();

        $resepPulang = (clone $this->baseQuery())
            ->where('ro.status', 'ranap')
            ->whereExists(function ($q) {
                $q->selectRaw(1)
                    ->from('kamar_inap as ki')
                    ->whereColumn('ki.no_rawat', 'ro.no_rawat')
                    ->whereNotIn('ki.stts_pulang', ['-', 'Pindah Kamar']);
            })
            ->count();

        $penyerahanStats = (clone $this->baseQuery())
            ->selectRaw('sum(case when tgl_penyerahan is not null and tgl_penyerahan != "0000-00-00" then 1 else 0 end) as sudah, sum(case when tgl_penyerahan is null or tgl_penyerahan = "0000-00-00" then 1 else 0 end) as belum')
            ->first();

        $jenisResepStats = (clone $this->baseQuery())
            ->select('jenis_resep', DB::raw('count(*) as total'))
            ->groupBy('jenis_resep')
            ->orderByDesc('total')
            ->get();

        $trend = $this->trendStats((clone $this->baseQuery()), 'ro.tgl_perawatan');

        return [
            'total' => $total,
            'status' => [
                'ralan' => $statusStats->ralan ?? 0,
                'ranap' => $statusStats->ranap ?? 0,
            ],
            'resep_pulang' => $resepPulang,
            'penyerahan' => [
                'sudah' => $penyerahanStats->sudah ?? 0,
                'belum' => $penyerahanStats->belum ?? 0,
            ],
            'jenis_resep' => $jenisResepStats,
            'charts' => [
                'trend' => [
                    'labels' => $trend->pluck('label')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Resep',
                        'data' => $trend->pluck('total')->toArray(),
                        'borderColor' => '#4f46e5',
                        'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ]],
                ],
                'status' => [
                    'labels' => ['Ralan', 'Ranap'],
                    'datasets' => [[
                        'data' => [$statusStats->ralan ?? 0, $statusStats->ranap ?? 0],
                        'backgroundColor' => ['#06b6d4', '#8b5cf6'],
                    ]],
                ],
                'jenis_resep' => [
                    'labels' => $jenisResepStats->pluck('jenis_resep')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Resep',
                        'data' => $jenisResepStats->pluck('total')->toArray(),
                        'backgroundColor' => '#f59e0b',
                        'borderRadius' => 6,
                    ]],
                ],
                'penyerahan' => [
                    'labels' => ['Sudah Diserahkan', 'Belum Diserahkan'],
                    'datasets' => [[
                        'data' => [$penyerahanStats->sudah ?? 0, $penyerahanStats->belum ?? 0],
                        'backgroundColor' => ['#10b981', '#ef4444'],
                    ]],
                ],
            ],
        ];
    }

    #[Computed]
    public function drugUsage()
    {
        $totalQty = (clone $this->obatBaseQuery())->sum('jml');
        $totalBiaya = (clone $this->obatBaseQuery())->sum('total');

        $topObat = (clone $this->obatBaseQuery())
            ->join('databarang as db', 'dpo.kode_brng', '=', 'db.kode_brng')
            ->select('db.nama_brng', DB::raw('sum(dpo.jml) as qty'), DB::raw('count(distinct dpo.no_rawat) as pemakaian'))
            ->groupBy('db.nama_brng')
            ->orderByDesc('qty')
            ->take(10)
            ->get();

        $trend = $this->trendStats((clone $this->obatBaseQuery()), 'dpo.tgl_perawatan', 'dpo.jml');

        return [
            'total_qty' => $totalQty ?? 0,
            'total_biaya' => $totalBiaya ?? 0,
            'top_obat' => $topObat,
            'charts' => [
                'top_obat' => [
                    'labels' => $topObat->pluck('nama_brng')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Terpakai',
                        'data' => $topObat->pluck('qty')->toArray(),
                        'backgroundColor' => '#4f46e5',
                        'borderRadius' => 6,
                    ]],
                ],
                'trend' => [
                    'labels' => $trend->pluck('label')->toArray(),
                    'datasets' => [[
                        'label' => 'Item Obat Terpakai',
                        'data' => $trend->pluck('total')->toArray(),
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ]],
                ],
            ],
        ];
    }

    /**
     * Kelompokkan data tren berdasarkan rentang tanggal (harian/bulanan/tahunan).
     */
    private function trendStats($query, string $dateColumn, ?string $sumColumn = null)
    {
        $hasRange = $this->startDate && $this->endDate;
        $spanInDays = $hasRange ? Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) : null;
        $totalExpr = $sumColumn ? "sum($sumColumn) as total" : 'count(*) as total';

        if (!$hasRange) {
            return $query
                ->selectRaw("YEAR($dateColumn) as label, $totalExpr")
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }

        if ($spanInDays > 60) {
            return $query
                ->selectRaw("DATE_FORMAT($dateColumn, '%Y-%m') as label, $totalExpr")
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }

        return $query
            ->selectRaw("$dateColumn as raw_date, $totalExpr")
            ->groupBy('raw_date')
            ->orderBy('raw_date')
            ->get()
            ->map(fn($item) => (object) [
                'label' => date('d/m', strtotime($item->raw_date)),
                'total' => $item->total,
            ]);
    }

    public function render()
    {
        return view('pages.pharmacy.recap');
    }
}
