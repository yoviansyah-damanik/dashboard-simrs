<?php

namespace App\Livewire\Icd;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Helpers\ReportHelper;
use App\Repository\ProcedureReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcedureRecap extends Component
{
    use WithPagination;

    public $year;
    public $month;
    public $periodType = 'month'; // 'month' atau 'range'
    public $startDate;
    public $endDate;
    public $serviceStatus = 'all'; // 'all', 'Poli', 'IGD', 'Ralan', 'Ranap'
    public $priority = 'all'; // 'all', '1' (utama), '2' (sekunder)
    public $gender = 'all'; // 'all', 'L', 'P'
    public $category = 'all'; // Kategori bab ICD-9
    public $search = '';
    public $perPage = 25;

    // Modal Drilldown Pasien
    public $showDetailModal = false;
    public $selectedCode = '';
    public $selectedDescription = '';
    public $drilldownPatients = [];

    protected $queryString = [
        'year' => ['except' => ''],
        'month' => ['except' => ''],
        'periodType' => ['except' => 'month'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'serviceStatus' => ['except' => 'all'],
        'priority' => ['except' => 'all'],
        'gender' => ['except' => 'all'],
        'category' => ['except' => 'all'],
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
        if (in_array($propertyName, ['year', 'month', 'periodType', 'startDate', 'endDate', 'serviceStatus', 'priority', 'gender', 'category', 'search', 'perPage'])) {
            $this->resetPage();
            $this->dispatch('procedure-chart-updated', top: $this->topProcedures);
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
        $this->dispatch('procedure-chart-updated', top: $this->topProcedures);
    }

    public function rendering()
    {
        $this->dispatch('procedure-chart-updated', top: $this->topProcedures);
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
        $this->gender = 'all';
        $this->category = 'all';
        $this->search = '';
        $this->resetPage();
        $this->dispatch('procedure-chart-updated', top: $this->topProcedures);
    }

    #[Computed]
    public function availableYears(): array
    {
        return ProcedureReportRepository::getAvailableYears();
    }

    #[Computed]
    public function procedureCategories(): array
    {
        return ProcedureReportRepository::getProcedureCategories();
    }

    #[Computed]
    public function filters(): array
    {
        $filters = [
            'service_status' => $this->serviceStatus,
            'priority' => $this->priority,
            'gender' => $this->gender,
            'category' => $this->category,
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
        return ProcedureReportRepository::getSummary($this->filters);
    }

    #[Computed]
    public function topProcedures(): array
    {
        return ProcedureReportRepository::getTopProcedures($this->filters, 10);
    }

    public function openDetail(string $code, string $description)
    {
        $this->selectedCode = $code;
        $this->selectedDescription = $description;
        $this->drilldownPatients = ProcedureReportRepository::getPatientDrilldown($code, $this->filters, 50);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedCode = '';
        $this->selectedDescription = '';
        $this->drilldownPatients = [];
    }

    /**
     * Ekspor laporan data tindakan ke format PDF server-side menggunakan DomPDF
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $profile = ReportHelper::getHospitalProfile();
        $summary = $this->summary;
        $exportData = ProcedureReportRepository::getAllProceduresForExport($this->filters, 150);

        // Label Periode
        if ($this->periodType === 'range') {
            $periodLabel = Carbon::parse($this->startDate)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($this->endDate)->translatedFormat('d M Y');
        } else {
            if ($this->month) {
                $periodLabel = Carbon::create(2026, (int) $this->month, 1)->translatedFormat('F') . ' ' . $this->year;
            } else {
                $periodLabel = 'Tahun ' . $this->year . ' (Semua Bulan)';
            }
        }

        // Label Status Layanan
        $serviceLabels = [
            'all' => 'Semua Layanan (Poli, IGD & Ranap)',
            'Poli' => 'Rawat Jalan Poliklinik (Poli)',
            'IGD' => 'Instalasi Gawat Darurat (IGD)',
            'Ralan' => 'Rawat Jalan Total (Poli & IGD)',
            'Ranap' => 'Rawat Inap',
        ];
        $serviceLabel = $serviceLabels[$this->serviceStatus] ?? $this->serviceStatus;

        // Label Kategori
        $categoryLabel = 'Semua Bab ICD-9';
        if ($this->category !== 'all' && isset($this->procedureCategories[$this->category])) {
            $categoryLabel = $this->procedureCategories[$this->category]['label'];
        }

        // Label Prioritas
        $priorityLabels = [
            'all' => 'Semua Prioritas',
            '1' => 'Prosedur Utama (Primer)',
            '2' => 'Prosedur Sekunder',
        ];
        $priorityLabel = $priorityLabels[$this->priority] ?? 'Semua';

        // Label Gender
        $genderLabels = [
            'all' => 'Semua Gender (L & P)',
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];
        $genderLabel = $genderLabels[$this->gender] ?? 'Semua';

        $filterInfo = [
            'period' => $periodLabel,
            'service_status' => $serviceLabel,
            'category' => $categoryLabel,
            'priority' => $priorityLabel,
            'gender' => $genderLabel,
        ];

        $pdf = Pdf::loadView('reports.procedure-recap-pdf', [
            'profile' => $profile,
            'summary' => $summary,
            'procedures' => $exportData['items'],
            'total_all' => $exportData['total_all'],
            'filterInfo' => $filterInfo,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = 'rekap-data-tindakan-icd9-' . str_replace(' ', '-', strtolower($periodLabel)) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    public function render()
    {
        $procedures = ProcedureReportRepository::getProceduresPaginated($this->filters, $this->perPage);

        return view('pages.icd.procedure-recap', [
            'procedures' => $procedures,
        ]);
    }
}
