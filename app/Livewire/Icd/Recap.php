<?php

namespace App\Livewire\Icd;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Helpers\ReportHelper;
use App\Repository\DiseaseReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;

class Recap extends Component
{
    use WithPagination;

    public $year;
    public $month;
    public $periodType = 'month'; // 'month' atau 'range'
    public $startDate;
    public $endDate;
    public $serviceStatus = 'all'; // 'all', 'Ralan', 'Ranap', 'IGD'
    public $priority = 'all'; // 'all', '1' (primer), '2' (sekunder)
    public $caseType = 'all'; // 'all', 'Baru', 'Lama'
    public $gender = 'all'; // 'all', 'L', 'P'
    public $specialCase = 'all'; // 'all', 'isk', 'ispa', 'tb', 'diare', 'dbd', dll
    public $search = '';
    public $perPage = 25;

    // Modal Drilldown Pasien
    public $showDetailModal = false;
    public $selectedDiseaseCode = '';
    public $selectedDiseaseName = '';
    public $drilldownPatients = [];

    protected $queryString = [
        'year' => ['except' => ''],
        'month' => ['except' => ''],
        'periodType' => ['except' => 'month'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'serviceStatus' => ['except' => 'all'],
        'priority' => ['except' => 'all'],
        'caseType' => ['except' => 'all'],
        'gender' => ['except' => 'all'],
        'specialCase' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->year = (int) date('Y');
        $this->month = (int) date('n');
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['year', 'month', 'periodType', 'startDate', 'endDate', 'serviceStatus', 'priority', 'caseType', 'gender', 'specialCase', 'search', 'perPage'])) {
            $this->resetPage();
            $this->dispatch('disease-chart-updated', top: $this->topDiseases);
        }
    }

    public function setPeriodType(string $type)
    {
        if ($type === $this->periodType) {
            return;
        }

        $this->periodType = $type;

        if ($type === 'range') {
            // Sinkronkan rentang tanggal dengan tahun dan bulan aktif
            if ($this->month) {
                $date = Carbon::create((int) $this->year, (int) $this->month, 1);
                $this->startDate = $date->startOfMonth()->toDateString();
                $this->endDate = $date->endOfMonth()->toDateString();
            } else {
                $date = Carbon::create((int) $this->year, 1, 1);
                $this->startDate = $date->startOfYear()->toDateString();
                $this->endDate = $date->endOfYear()->toDateString();
            }
        } else {
            // Sinkronkan tahun dan bulan dari tanggal mulai aktif
            if ($this->startDate) {
                $parsed = Carbon::parse($this->startDate);
                $this->year = (int) $parsed->year;
                $this->month = (int) $parsed->month;
            }
        }

        $this->resetPage();
        $this->dispatch('disease-chart-updated', top: $this->topDiseases);
    }

    public function rendering()
    {
        $this->dispatch('disease-chart-updated', top: $this->topDiseases);
    }

    public function resetFilters()
    {
        $this->year = (int) date('Y');
        $this->month = (int) date('n');
        $this->periodType = 'month';
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->serviceStatus = 'all';
        $this->priority = 'all';
        $this->caseType = 'all';
        $this->gender = 'all';
        $this->specialCase = 'all';
        $this->search = '';
        $this->resetPage();
    }

    #[Computed]
    public function availableYears(): array
    {
        return DiseaseReportRepository::getAvailableYears();
    }

    #[Computed]
    public function specialCases(): array
    {
        return DiseaseReportRepository::getSpecialCases();
    }

    #[Computed]
    public function filters(): array
    {
        $filters = [
            'service_status' => $this->serviceStatus,
            'priority' => $this->priority,
            'case_type' => $this->caseType,
            'gender' => $this->gender,
            'special_case' => $this->specialCase,
            'search' => $this->search,
        ];

        if ($this->periodType === 'range' && $this->startDate && $this->endDate) {
            $filters['start_date'] = $this->startDate;
            $filters['end_date'] = $this->endDate;
        } else {
            $filters['year'] = (int) $this->year;
            $filters['month'] = $this->month ? (int) $this->month : null;
        }

        return $filters;
    }

    #[Computed]
    public function summary(): array
    {
        return DiseaseReportRepository::getSummary($this->filters);
    }

    #[Computed]
    public function topDiseases(): array
    {
        return DiseaseReportRepository::getTopDiseases($this->filters, 10);
    }

    public function openDetail(string $code, string $name)
    {
        $this->selectedDiseaseCode = $code;
        $this->selectedDiseaseName = $name;
        $this->drilldownPatients = DiseaseReportRepository::getPatientDrilldown($code, $this->filters, 50);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedDiseaseCode = '';
        $this->selectedDiseaseName = '';
        $this->drilldownPatients = [];
    }

    /**
     * Ekspor Laporan PDF Data Berbasis Server (DomPDF)
     */
    public function exportPdf()
    {
        $profile = ReportHelper::getHospitalProfile();
        $summary = $this->summary;
        $exportData = DiseaseReportRepository::getAllDiseasesForExport($this->filters, 100);

        // Keterangan filter untuk header PDF
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($this->periodType === 'range') {
            $periodLabel = Carbon::parse($this->startDate)->translatedFormat('d M Y') . ' s.d. ' . Carbon::parse($this->endDate)->translatedFormat('d M Y');
        } else {
            $periodLabel = ($this->month ? ($monthNames[$this->month] ?? '') . ' ' : '') . $this->year;
        }

        $serviceMap = [
            'all' => 'Semua Layanan (Poli, IGD, Ranap)',
            'Poli' => 'Poliklinik (Poli)',
            'IGD' => 'Gawat Darurat (IGD)',
            'Ralan' => 'Rawat Jalan (Poli + IGD)',
            'Ranap' => 'Rawat Inap (Ranap)',
        ];

        $priorityMap = [
            'all' => 'Semua (Primer & Sekunder)',
            '1' => 'Diagnosa Primer Saja',
            '2' => 'Diagnosa Sekunder Saja',
        ];

        $caseMap = [
            'all' => 'Semua (Kasus Baru & Lama)',
            'Baru' => 'Kasus Baru',
            'Lama' => 'Kasus Lama',
        ];

        $genderMap = [
            'all' => 'Semua (Laki-laki & Perempuan)',
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];

        $specialCaseMap = DiseaseReportRepository::getSpecialCases();
        $specialCaseLabel = ($this->specialCase !== 'all' && isset($specialCaseMap[$this->specialCase]))
            ? $specialCaseMap[$this->specialCase]['label']
            : 'Semua Kasus (Umum)';

        $filterInfo = [
            'period_label' => $periodLabel,
            'service_label' => $serviceMap[$this->serviceStatus] ?? 'Semua',
            'priority_label' => $priorityMap[$this->priority] ?? 'Semua',
            'case_label' => $caseMap[$this->caseType] ?? 'Semua',
            'gender_label' => $genderMap[$this->gender] ?? 'Semua',
            'special_case_label' => $specialCaseLabel,
        ];

        $pdf = Pdf::loadView('reports.disease-recap-pdf', [
            'profile' => $profile,
            'summary' => $summary,
            'diseases' => $exportData['items'],
            'total_all' => $exportData['total_all'],
            'filterInfo' => $filterInfo,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = 'rekap-data-penyakit-' . str_replace(' ', '-', strtolower($periodLabel)) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    public function render()
    {
        $diseases = DiseaseReportRepository::getDiseasesPaginated($this->filters, $this->perPage);

        return view('pages.icd.recap', [
            'diseases' => $diseases,
        ]);
    }
}
