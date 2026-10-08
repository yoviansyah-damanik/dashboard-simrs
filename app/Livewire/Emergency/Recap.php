<?php

namespace App\Livewire\Emergency;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\SirsHelper;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Recap extends Component
{
    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    #[Url]
    public $period = 'today';

    public $selectedMonth;
    public $selectedYear;

    public $mainTab = 'recap'; // recap, demographics
    public $mainView = 'list'; // chart, list

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

    public function switchTab($tab)
    {
        $this->mainTab = $tab;
        if ($tab === 'recap') $this->refreshCharts();
    }

    private function syncDates()
    {
        switch ($this->period) {
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
            $this->dispatch('refresh-main-charts', charts: $this->overallStats['charts']);
            $this->dispatch('refresh-demo-charts', charts: $this->patientDemographics['charts']);
        }
    }

    #[Computed]
    public function months()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }

    #[Computed]
    public function years()
    {
        return range(date('Y') - 5, date('Y'));
    }

    #[Computed]
    public function recapData()
    {
        return DB::connection('simrs')
            ->table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereBetween('tgl_registrasi', [$this->startDate, $this->endDate])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->where('reg_periksa.kd_poli', '=', 'IGDK')
            ->select(
                'dokter.nm_dokter',
                DB::raw('count(*) as total_reg'),
                DB::raw('sum(case when stts_daftar = "Baru" then 1 else 0 end) as pasien_baru'),
                DB::raw('sum(case when stts_daftar = "Lama" then 1 else 0 end) as pasien_lama'),
                DB::raw('sum(case when status_lanjut = "Ranap" then 1 else 0 end) as masuk_ranap'),
                DB::raw('sum(case when status_lanjut = "Ralan" then 1 else 0 end) as rawat_jalan'),
                DB::raw('sum(case when stts = "Dirujuk" then 1 else 0 end) as dirujuk'),
                DB::raw('sum(case when stts = "Meninggal" then 1 else 0 end) as meninggal'),
                DB::raw('sum(case when stts = "Sudah" then 1 else 0 end) as sudah_periksa'),
                DB::raw('sum(case when stts = "Belum" then 1 else 0 end) as belum_periksa')
            )
            ->groupBy('reg_periksa.kd_dokter', 'dokter.nm_dokter')
            ->orderByDesc('total_reg')
            ->get();
    }

    #[Computed]
    public function overallStats()
    {
        $total = $this->recapData->sum('total_reg');
        $new = $this->recapData->sum('pasien_baru');
        $ranap = $this->recapData->sum('masuk_ranap');
        $ralan = $this->recapData->sum('rawat_jalan');

        // Demographics: Kelompok Umur SIRS
        $ageGroups = DB::connection('simrs')
            ->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->whereBetween('tgl_registrasi', [$this->startDate, $this->endDate])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->where('reg_periksa.kd_poli', '=', 'IGDK')
            ->select(
                DB::raw(SirsHelper::ageGroupCategoryCaseSql('pasien.tgl_lahir') . ' as kelompok_kode'),
                DB::raw('count(*) as total'),
                DB::raw('sum(case when jk = "L" then 1 else 0 end) as laki'),
                DB::raw('sum(case when jk = "P" then 1 else 0 end) as perempuan')
            )
            ->groupBy('kelompok_kode')
            ->get()
            ->map(function ($item) {
                $item->kelompok = SirsHelper::ageGroupCategoryLabels()[$item->kelompok_kode] ?? $item->kelompok_kode;
                return $item;
            });

        // Cara Bayar
        $insuranceStats = DB::connection('simrs')
            ->table('reg_periksa')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('tgl_registrasi', [$this->startDate, $this->endDate])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->where('reg_periksa.kd_poli', '=', 'IGDK')
            ->select(
                DB::raw('sum(case when penjab.png_jawab like "%BPJS%" then 1 else 0 end) as bpjs'),
                DB::raw('sum(case when penjab.png_jawab not like "%BPJS%" and penjab.png_jawab != "UMUM" then 1 else 0 end) as asuransi_lain'),
                DB::raw('sum(case when penjab.png_jawab = "UMUM" then 1 else 0 end) as umum')
            )
            ->first();

        // Gender
        $genderStats = DB::connection('simrs')
            ->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->whereBetween('tgl_registrasi', [$this->startDate, $this->endDate])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->where('reg_periksa.kd_poli', '=', 'IGDK')
            ->select(
                DB::raw('sum(case when pasien.jk = "L" then 1 else 0 end) as laki'),
                DB::raw('sum(case when pasien.jk = "P" then 1 else 0 end) as perempuan')
            )
            ->first();

        // Dokter IGD
        $doctorStats = $this->recapData->take(10);

        // Tren harian
        $trend = DB::connection('simrs')
            ->table('reg_periksa')
            ->select('tgl_registrasi as date', DB::raw('count(*) as total'))
            ->whereBetween('tgl_registrasi', [$this->startDate, $this->endDate])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->where('reg_periksa.kd_poli', '=', 'IGDK')
            ->groupBy('tgl_registrasi')
            ->orderBy('tgl_registrasi')
            ->get();

        return [
            'total' => $total,
            'ranap' => $ranap,
            'ralan' => $ralan,
            'rasio_ranap' => $total > 0 ? round(($ranap / $total) * 100, 1) : 0,
            'gender' => [
                'laki' => $genderStats->laki ?? 0,
                'perempuan' => $genderStats->perempuan ?? 0,
            ],
            'age_groups' => $ageGroups,
            'insurance' => [
                'bpjs' => $insuranceStats->bpjs ?? 0,
                'umum' => $insuranceStats->umum ?? 0,
                'lain' => $insuranceStats->asuransi_lain ?? 0,
            ],
            'new_ratio' => $total > 0 ? ($new / $total) * 100 : 0,
            'charts' => [
                'doctor_distribution' => [
                    'labels' => $doctorStats->pluck('nm_dokter')->toArray(),
                    'datasets' => [[
                        'label' => 'Total Pasien',
                        'data' => $doctorStats->pluck('total_reg')->toArray(),
                        'backgroundColor' => '#e11d48',
                        'borderRadius' => 6
                    ]]
                ],
                'status_lanjut' => [
                    'labels' => ['Masuk Ranap', 'Rawat Jalan / Pulang'],
                    'datasets' => [[
                        'data' => [$ranap, $ralan],
                        'backgroundColor' => ['#8b5cf6', '#0284c7']
                    ]]
                ],
                'trend' => [
                    'labels' => $trend->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))->toArray(),
                    'datasets' => [[
                        'label' => 'Kunjungan IGD',
                        'data' => $trend->pluck('total')->toArray(),
                        'borderColor' => '#e11d48',
                        'backgroundColor' => 'rgba(225, 29, 72, 0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]]
                ]
            ]
        ];
    }

    #[Computed]
    public function patientDemographics()
    {
        $baseQuery = DB::connection('simrs')
            ->table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('tgl_registrasi', [$this->startDate, $this->endDate])
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->where('reg_periksa.kd_poli', '=', 'IGDK');

        $gender = (clone $baseQuery)
            ->select('pasien.jk', DB::raw('count(*) as total'))
            ->groupBy('pasien.jk')
            ->get();

        $insurance = (clone $baseQuery)
            ->select('penjab.png_jawab', DB::raw('count(*) as total'))
            ->groupBy('penjab.png_jawab')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return [
            'charts' => [
                'gender' => [
                    'labels' => $gender->map(fn($g) => $g->jk == 'L' ? 'Laki-laki' : 'Perempuan')->toArray(),
                    'datasets' => [[
                        'data' => $gender->pluck('total')->toArray(),
                        'backgroundColor' => ['#0284c7', '#ec4899']
                    ]]
                ],
                'insurance' => [
                    'labels' => $insurance->pluck('png_jawab')->toArray(),
                    'datasets' => [[
                        'label' => 'Pasien',
                        'data' => $insurance->pluck('total')->toArray(),
                        'backgroundColor' => '#10b981',
                        'borderRadius' => 4
                    ]]
                ]
            ]
        ];
    }

    public function render()
    {
        return view('pages.emergency.recap', [
            'recapData' => $this->recapData,
            'overallStats' => $this->overallStats,
            'patientDemographics' => $this->patientDemographics,
        ]);
    }
}
