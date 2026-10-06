<?php

namespace App\Livewire\PatientReport;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Repository\PatientReportRepository;

class Index extends Component
{
    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    #[Url]
    public $period = 'monthly';

    #[Url]
    public $selectedMonth;

    #[Url]
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = $this->selectedMonth ? (int) $this->selectedMonth : (int) date('n');
        $this->selectedYear  = $this->selectedYear ? (int) $this->selectedYear : (int) date('Y');
        if (!in_array($this->period, ['monthly', 'yearly'])) {
            $this->period = 'monthly';
        }
        $this->syncDates();
    }

    public function setPeriod(string $period)
    {
        if (in_array($period, ['monthly', 'yearly'])) {
            $this->period = $period;
            $this->syncDates();
        }
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

    private function syncDates()
    {
        if ($this->period === 'yearly') {
            $year = (int) ($this->selectedYear ?: date('Y'));
            $this->startDate = Carbon::create($year, 1, 1)->startOfYear()->format('Y-m-d');
            $this->endDate   = Carbon::create($year, 1, 1)->endOfYear()->format('Y-m-d');
        } else {
            // Default bulanan
            $this->period = 'monthly';
            $year  = (int) ($this->selectedYear ?: date('Y'));
            $month = (int) ($this->selectedMonth ?: date('n'));
            $this->startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $this->endDate   = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }
    }

    #[Computed]
    public function months()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    #[Computed]
    public function years()
    {
        return range(date('Y') - 5, date('Y'));
    }

    #[Computed]
    public function summary()
    {
        return PatientReportRepository::getSummary($this->startDate, $this->endDate);
    }

    #[Computed]
    public function trendData()
    {
        return PatientReportRepository::getTrendData($this->startDate, $this->endDate);
    }

    #[Computed]
    public function chartData()
    {
        $s = $this->summary();
        $trend = $this->trendData();

        // Komposisi Kelompok (TNI, POLRI, Umum)
        $tniKunjungan = (int) (($s['tni']['total']['rawat_jalan'] ?? 0) + ($s['tni']['total']['igd'] ?? 0) + ($s['tni']['total']['rawat_inap'] ?? 0));
        $polriKunjungan = (int) (($s['polri']['total']['rawat_jalan'] ?? 0) + ($s['polri']['total']['igd'] ?? 0) + ($s['polri']['total']['rawat_inap'] ?? 0));
        $umumKunjungan = (int) (($s['umum']['rawat_jalan'] ?? 0) + ($s['umum']['igd'] ?? 0) + ($s['umum']['rawat_inap'] ?? 0));

        $tniPengunjung = (int) ($s['tni']['total']['pengunjung'] ?? 0);
        $polriPengunjung = (int) ($s['polri']['total']['pengunjung'] ?? 0);
        $umumPengunjung = (int) ($s['umum']['pengunjung'] ?? 0);

        // Komposisi TNI per Angkatan
        $ad = $s['tni']['angkatan']['ad'] ?? null;
        $al = $s['tni']['angkatan']['al'] ?? null;
        $au = $s['tni']['angkatan']['au'] ?? null;

        $tniBreakdown = [
            'labels' => ['TNI AD', 'TNI AL', 'TNI AU'],
            'militer' => [
                (int) (($ad['rincian']['mil']['rawat_jalan'] ?? 0) + ($ad['rincian']['mil']['igd'] ?? 0) + ($ad['rincian']['mil']['rawat_inap'] ?? 0)),
                (int) (($al['rincian']['mil']['rawat_jalan'] ?? 0) + ($al['rincian']['mil']['igd'] ?? 0) + ($al['rincian']['mil']['rawat_inap'] ?? 0)),
                (int) (($au['rincian']['mil']['rawat_jalan'] ?? 0) + ($au['rincian']['mil']['igd'] ?? 0) + ($au['rincian']['mil']['rawat_inap'] ?? 0)),
            ],
            'asn' => [
                (int) (($ad['rincian']['asn']['rawat_jalan'] ?? 0) + ($ad['rincian']['asn']['igd'] ?? 0) + ($ad['rincian']['asn']['rawat_inap'] ?? 0)),
                (int) (($al['rincian']['asn']['rawat_jalan'] ?? 0) + ($al['rincian']['asn']['igd'] ?? 0) + ($al['rincian']['asn']['rawat_inap'] ?? 0)),
                (int) (($au['rincian']['asn']['rawat_jalan'] ?? 0) + ($au['rincian']['asn']['igd'] ?? 0) + ($au['rincian']['asn']['rawat_inap'] ?? 0)),
            ],
            'keluarga' => [
                (int) (($ad['rincian']['kel']['rawat_jalan'] ?? 0) + ($ad['rincian']['kel']['igd'] ?? 0) + ($ad['rincian']['kel']['rawat_inap'] ?? 0)),
                (int) (($al['rincian']['kel']['rawat_jalan'] ?? 0) + ($al['rincian']['kel']['igd'] ?? 0) + ($al['rincian']['kel']['rawat_inap'] ?? 0)),
                (int) (($au['rincian']['kel']['rawat_jalan'] ?? 0) + ($au['rincian']['kel']['igd'] ?? 0) + ($au['rincian']['kel']['rawat_inap'] ?? 0)),
            ],
            'purnawirawan' => [
                (int) (($ad['rincian']['purn']['rawat_jalan'] ?? 0) + ($ad['rincian']['purn']['igd'] ?? 0) + ($ad['rincian']['purn']['rawat_inap'] ?? 0)),
                (int) (($al['rincian']['purn']['rawat_jalan'] ?? 0) + ($al['rincian']['purn']['igd'] ?? 0) + ($al['rincian']['purn']['rawat_inap'] ?? 0)),
                (int) (($au['rincian']['purn']['rawat_jalan'] ?? 0) + ($au['rincian']['purn']['igd'] ?? 0) + ($au['rincian']['purn']['rawat_inap'] ?? 0)),
            ],
            'total' => [
                (int) (($ad['total']['rawat_jalan'] ?? 0) + ($ad['total']['igd'] ?? 0) + ($ad['total']['rawat_inap'] ?? 0)),
                (int) (($al['total']['rawat_jalan'] ?? 0) + ($al['total']['igd'] ?? 0) + ($al['total']['rawat_inap'] ?? 0)),
                (int) (($au['total']['rawat_jalan'] ?? 0) + ($au['total']['igd'] ?? 0) + ($au['total']['rawat_inap'] ?? 0)),
            ],
        ];

        return [
            'trend' => $trend,
            'composition' => [
                'kunjungan' => [
                    'tni' => $tniKunjungan,
                    'polri' => $polriKunjungan,
                    'umum' => $umumKunjungan,
                    'total' => $tniKunjungan + $polriKunjungan + $umumKunjungan,
                ],
                'pengunjung' => [
                    'tni' => $tniPengunjung,
                    'polri' => $polriPengunjung,
                    'umum' => $umumPengunjung,
                    'total' => $tniPengunjung + $polriPengunjung + $umumPengunjung,
                ],
            ],
            'tniBreakdown' => $tniBreakdown,
        ];
    }

    #[Computed]
    public function monthlyBreakdown()
    {
        if ($this->period === 'yearly') {
            return PatientReportRepository::getMonthlyBreakdown($this->selectedYear);
        }

        return null;
    }

    public function render()
    {
        return view('pages.patient-report.index', [
            'chartData' => $this->chartData(),
            'summary' => $this->summary(),
            'monthlyBreakdown' => $this->monthlyBreakdown(),
            'months' => $this->months(),
            'years' => $this->years(),
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
        ])->title('Laporan Kunjungan dan Pengunjung');
    }
}
