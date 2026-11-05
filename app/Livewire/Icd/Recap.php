<?php

namespace App\Livewire\Icd;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Helpers\ReportHelper;
use App\Repository\DiseaseReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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
    public $dinasFilter = 'all'; // 'all', 'DINAS', 'TNI', 'POLRI', 'NON_DINAS'
    public $search = '';
    public $perPage = 25;

    // Tab Aktif & Mode Sensor Privasi
    public $activeTab = 'morbiditas'; // 'morbiditas', 'kunjungan', 'pengunjung', 'mom'
    public $momView = 'disease'; // 'disease', 'polyclinic', 'doctor', 'overview'
    public $momDisplayMode = 'both'; // 'both', 'count', 'growth'
    public $matrixLimit = 20; // 10, 20, 50, 100
    public $isMasked = true; // Mode sensor aktif (default true demi privasi)
    public $showExportModal = false; // Modal dialog cetak PDF / Excel
    public $exportType = 'morbiditas'; // 'morbiditas', 'kunjungan', 'pengunjung', 'mom'
    public $exportFormat = 'pdf'; // 'pdf' atau 'excel'
    public $exportMasked = true; // Mode sensor untuk cetak PDF / Excel

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
        'dinasFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'morbiditas'],
        'momView' => ['except' => 'disease'],
        'momDisplayMode' => ['except' => 'both'],
        'matrixLimit' => ['except' => 20],
        'isMasked' => ['except' => true],
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
        if (in_array($propertyName, ['year', 'month', 'periodType', 'startDate', 'endDate', 'serviceStatus', 'priority', 'caseType', 'gender', 'specialCase', 'dinasFilter', 'search', 'perPage'])) {
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
        $this->dinasFilter = 'all';
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
            'dinas_filter' => $this->dinasFilter,
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
        return DiseaseReportRepository::getSummary($this->filters());
    }

    #[Computed]
    public function topDiseases(): array
    {
        return DiseaseReportRepository::getTopDiseases($this->filters(), 10);
    }

    #[Computed]
    public function isYearly(): bool
    {
        return $this->periodType === 'month' && empty($this->month);
    }

    #[Computed]
    public function monthlyBreakdown(): array
    {
        if (!$this->isYearly && $this->activeTab !== 'mom') {
            return [];
        }

        return DiseaseReportRepository::getMonthlyBreakdown($this->filters());
    }

    #[Computed]
    public function diseaseMonthlyMatrix(): array
    {
        return DiseaseReportRepository::getDiseaseMonthlyMatrix($this->filters(), (int) $this->matrixLimit);
    }

    #[Computed]
    public function polyclinicMonthlyMatrix(): array
    {
        return DiseaseReportRepository::getPolyclinicMonthlyMatrix($this->filters());
    }

    #[Computed]
    public function doctorMonthlyMatrix(): array
    {
        return DiseaseReportRepository::getDoctorMonthlyMatrix($this->filters(), (int) $this->matrixLimit);
    }

    #[Computed]
    public function momSummaryKpi(): array
    {
        return DiseaseReportRepository::getMomSummaryKpi($this->filters());
    }

    public function setMomView(string $view)
    {
        $this->momView = in_array($view, ['disease', 'polyclinic', 'doctor', 'overview']) ? $view : 'disease';
    }

    public function setMomDisplayMode(string $mode)
    {
        $this->momDisplayMode = in_array($mode, ['both', 'count', 'growth']) ? $mode : 'both';
    }

    public function selectMonth(int $month)
    {
        $this->month = $month;
        $this->resetPage();
        $this->dispatch('disease-chart-updated', top: $this->topDiseases);
    }

    public function openDetail(string $code, string $name)
    {
        $this->selectedDiseaseCode = $code;
        $this->selectedDiseaseName = $name;
        $this->drilldownPatients = DiseaseReportRepository::getPatientDrilldown($code, $this->filters(), 50);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedDiseaseCode = '';
        $this->selectedDiseaseName = '';
        $this->drilldownPatients = [];
    }

    public function toggleMask()
    {
        $this->isMasked = !$this->isMasked;
    }

    public function setActiveTab(string $tab)
    {
        if (in_array($tab, ['morbiditas', 'kunjungan', 'pengunjung', 'mom'])) {
            $this->activeTab = $tab;
            $this->resetPage('visits_page');
            $this->resetPage('visitors_page');
            $this->resetPage();
        }
    }

    public function openExportModal(?string $type = null, string $format = 'pdf')
    {
        $this->exportType = $type ?: $this->activeTab;
        $this->exportFormat = in_array($format, ['pdf', 'excel']) ? $format : 'pdf';
        $this->exportMasked = $this->isMasked;
        $this->showExportModal = true;
    }

    public function closeExportModal()
    {
        $this->showExportModal = false;
    }

    /**
     * Helper untuk menyiapkan metadata filter laporan PDF
     */
    private function prepareFilterInfo(): array
    {
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

        $dinasMap = [
            'all' => 'Semua Pasien (Dinas & Umum)',
            'DINAS' => 'Semua Personel Dinas (TNI & POLRI)',
            'TNI' => 'Personel TNI',
            'POLRI' => 'Personel POLRI',
            'NON_DINAS' => 'Pasien Non-Dinas (Sipil / Umum)',
        ];

        return [
            'period_label' => $periodLabel,
            'service_label' => $serviceMap[$this->serviceStatus] ?? 'Semua',
            'priority_label' => $priorityMap[$this->priority] ?? 'Semua',
            'case_label' => $caseMap[$this->caseType] ?? 'Semua',
            'gender_label' => $genderMap[$this->gender] ?? 'Semua',
            'special_case_label' => $specialCaseLabel,
            'dinas_label' => $dinasMap[$this->dinasFilter] ?? 'Semua Pasien',
        ];
    }

    /**
     * Ekspor Laporan PDF Data Berbasis Server (DomPDF) dengan Opsi Mode Sensor
     */
    public function exportPdf(?string $type = null, ?bool $masked = null)
    {
        $profile = ReportHelper::getHospitalProfile();
        $filterInfo = $this->prepareFilterInfo();
        $targetType = $type ?: $this->exportType;
        $isMasked = $masked !== null ? $masked : $this->exportMasked;

        $this->showExportModal = false;

        if ($targetType === 'kunjungan') {
            $visits = DiseaseReportRepository::getAllVisitsForExport($this->filters(), 500);

            $pdf = Pdf::loadView('reports.disease-visits-pdf', [
                'profile' => $profile,
                'filterInfo' => $filterInfo,
                'visits' => $visits,
                'isMasked' => $isMasked,
            ]);

            $pdf->setPaper('a4', 'landscape');
            $fileName = 'data-kunjungan-pasien-' . ($isMasked ? 'sensor-' : 'lengkap-') . str_replace(' ', '-', strtolower($filterInfo['period_label'])) . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, $fileName);
        }

        if ($targetType === 'pengunjung') {
            $visitors = DiseaseReportRepository::getAllVisitorsForExport($this->filters(), 500);

            $pdf = Pdf::loadView('reports.disease-visitors-pdf', [
                'profile' => $profile,
                'filterInfo' => $filterInfo,
                'visitors' => $visitors,
                'isMasked' => $isMasked,
            ]);

            $pdf->setPaper('a4', 'landscape');
            $fileName = 'data-pengunjung-pasien-' . ($isMasked ? 'sensor-' : 'lengkap-') . str_replace(' ', '-', strtolower($filterInfo['period_label'])) . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, $fileName);
        }

        if ($targetType === 'mom') {
            $monthlyBreakdown = DiseaseReportRepository::getMonthlyBreakdown($this->filters());
            $diseaseMatrix = DiseaseReportRepository::getDiseaseMonthlyMatrix($this->filters(), 100);
            $momKpi = DiseaseReportRepository::getMomSummaryKpi($this->filters());

            $pdf = Pdf::loadView('reports.disease-mom-pdf', [
                'profile' => $profile,
                'filterInfo' => $filterInfo,
                'year' => $this->year,
                'monthlyBreakdown' => $monthlyBreakdown,
                'diseaseMatrix' => $diseaseMatrix,
                'momKpi' => $momKpi,
            ]);

            $pdf->setPaper('a4', 'landscape');
            $fileName = 'laporan-mom-growth-penyakit-' . $this->year . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, $fileName);
        }

        // Default: Rekapitulasi Morbiditas
        $summary = $this->summary;
        $exportData = DiseaseReportRepository::getAllDiseasesForExport($this->filters(), 100);
        $isYearly = $this->isYearly;
        $monthlyBreakdown = $this->monthlyBreakdown;

        $pdf = Pdf::loadView('reports.disease-recap-pdf', [
            'profile' => $profile,
            'summary' => $summary,
            'diseases' => $exportData['items'],
            'total_all' => $exportData['total_all'],
            'filterInfo' => $filterInfo,
            'isYearly' => $isYearly,
            'monthlyBreakdown' => $monthlyBreakdown,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $fileName = 'rekap-data-penyakit-' . str_replace(' ', '-', strtolower($filterInfo['period_label'])) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    /**
     * Ekspor Laporan PDF Rincian Pasien dari Modal Drilldown
     */
    public function exportDrilldownPdf(?bool $masked = null)
    {
        if (empty($this->selectedDiseaseCode)) {
            return;
        }

        $profile = ReportHelper::getHospitalProfile();
        $filterInfo = $this->prepareFilterInfo();
        $isMasked = $masked !== null ? $masked : $this->isMasked;
        $patients = DiseaseReportRepository::getPatientDrilldown($this->selectedDiseaseCode, $this->filters(), 100);

        $pdf = Pdf::loadView('reports.disease-drilldown-pdf', [
            'profile' => $profile,
            'filterInfo' => $filterInfo,
            'diseaseCode' => $this->selectedDiseaseCode,
            'diseaseName' => $this->selectedDiseaseName,
            'patients' => $patients,
            'isMasked' => $isMasked,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'rincian-pasien-' . $this->selectedDiseaseCode . '-' . ($isMasked ? 'sensor' : 'lengkap') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    /**
     * Ekspor Laporan ke format Spreadsheet Excel (.xlsx) dengan Opsi Mode Sensor
     */
    public function exportExcel(?string $type = null, ?bool $masked = null)
    {
        set_time_limit(0);

        $filterInfo = $this->prepareFilterInfo();
        $targetType = $type ?: $this->exportType;
        $isMasked = $masked !== null ? $masked : $this->exportMasked;
        $hospitalName = config('app.hospital_name', 'RUMAH SAKIT');

        $this->showExportModal = false;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(config('app.name', 'Dashboard SIMRS'))
            ->setTitle('Laporan Data Penyakit & Morbiditas');

        if ($targetType === 'kunjungan') {
            $visits = DiseaseReportRepository::getAllVisitsForExport($this->filters(), 1000);
            $this->buildVisitsExcelSheet($spreadsheet, $visits, $filterInfo, $isMasked, $hospitalName);
            $fileName = 'data-kunjungan-pasien-' . ($isMasked ? 'sensor-' : 'lengkap-') . str_replace(' ', '-', strtolower($filterInfo['period_label'])) . '.xlsx';
        } elseif ($targetType === 'pengunjung') {
            $visitors = DiseaseReportRepository::getAllVisitorsForExport($this->filters(), 1000);
            $this->buildVisitorsExcelSheet($spreadsheet, $visitors, $filterInfo, $isMasked, $hospitalName);
            $fileName = 'data-pengunjung-pasien-' . ($isMasked ? 'sensor-' : 'lengkap-') . str_replace(' ', '-', strtolower($filterInfo['period_label'])) . '.xlsx';
        } elseif ($targetType === 'mom') {
            $this->buildMomExcelSheet($spreadsheet, $filterInfo, $hospitalName);
            $fileName = 'laporan-mom-growth-penyakit-' . $this->year . '.xlsx';
        } else {
            $exportData = DiseaseReportRepository::getAllDiseasesForExport($this->filters(), 200);
            $this->buildMorbiditasExcelSheet($spreadsheet, $exportData, $filterInfo, $hospitalName);
            $fileName = 'rekap-data-penyakit-' . str_replace(' ', '-', strtolower($filterInfo['period_label'])) . '.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Ekspor Rincian Pasien dari Modal Drilldown ke format Excel (.xlsx)
     */
    public function exportDrilldownExcel(?bool $masked = null)
    {
        if (empty($this->selectedDiseaseCode)) {
            return;
        }

        set_time_limit(0);

        $filterInfo = $this->prepareFilterInfo();
        $isMasked = $masked !== null ? $masked : $this->isMasked;
        $patients = DiseaseReportRepository::getPatientDrilldown($this->selectedDiseaseCode, $this->filters(), 500);
        $hospitalName = config('app.hospital_name', 'RUMAH SAKIT');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(config('app.name', 'Dashboard SIMRS'))
            ->setTitle('Rincian Pasien Diagnosa ' . $this->selectedDiseaseCode);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rincian Pasien');
        $sheet->setShowGridLines(true);

        // Header Dokumen
        $sheet->setCellValue('A1', $hospitalName);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'RINCIAN DATA KUNJUNGAN PASIEN KASUS DIAGNOSA ' . $this->selectedDiseaseCode . ' - ' . strtoupper($this->selectedDiseaseName));
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setBold(true)->getColor()->setRGB('059669');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Periode: ' . $filterInfo['period_label'] . ' | Layanan: ' . $filterInfo['service_label']);
        $sheet->setCellValue('A5', 'Mode Sensor Privasi: ' . ($isMasked ? 'AKTIF (No. RM 3 Digit Belakang & Nama Tengah Disamarkan)' : 'NONAKTIF (Identitas Pasien Lengkap)') . ' | Waktu Unduh: ' . now()->format('d/m/Y H:i:s'));

        $headers = ['No', 'No. Rawat', 'No. RM', 'Nama Pasien', 'L/P', 'Umur', 'Tgl Kunjungan', 'Layanan', 'Prioritas', 'Kasus', 'Dokter DPJP', 'Penjamin'];
        $sheet->fromArray($headers, null, 'A7');
        $sheet->getRowDimension(7)->setRowHeight(24);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '064E3B']]]
        ];
        $sheet->getStyle('A7:L7')->applyFromArray($headerStyle);

        $row = 8;
        foreach ($patients as $idx => $p) {
            $rm = $isMasked ? $p['no_rkm_medis_masked'] : $p['no_rkm_medis'];
            $nama = $isMasked ? $p['nama_masked'] : $p['nama'];

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, $p['no_rawat'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $rm, DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('E' . $row, $p['jk']);
            $sheet->setCellValue('F' . $row, $p['umur'] !== '-' ? $p['umur'] . ' th' : '-');
            $sheet->setCellValue('G' . $row, $p['tanggal']);
            $sheet->setCellValue('H' . $row, $p['layanan']);
            $sheet->setCellValue('I' . $row, $p['prioritas']);
            $sheet->setCellValue('J' . $row, $p['kasus']);
            $sheet->setCellValue('K' . $row, $p['dokter']);
            $sheet->setCellValue('L' . $row, $p['penjamin']);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($idx % 2 === 1) {
                $sheet->getStyle('A' . $row . ':L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        if ($row > 8) {
            $sheet->getStyle('A8:L' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'rincian-pasien-' . $this->selectedDiseaseCode . '-' . ($isMasked ? 'sensor' : 'lengkap') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Helper menyusun sheet Excel untuk Data Kunjungan Pasien
     */
    private function buildVisitsExcelSheet(Spreadsheet $spreadsheet, array $visits, array $filterInfo, bool $isMasked, string $hospitalName): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kunjungan');
        $sheet->setShowGridLines(true);

        // Header Dokumen
        $sheet->setCellValue('A1', $hospitalName);
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LAPORAN DATA KUNJUNGAN PASIEN BERDASARKAN DIAGNOSA PENYAKIT');
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setSize(10.5)->setBold(true)->getColor()->setRGB('059669');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Periode: ' . $filterInfo['period_label'] . ' | Layanan: ' . $filterInfo['service_label'] . ' | Kategori: ' . ($filterInfo['special_case_label'] ?? 'Semua'));
        $sheet->setCellValue('A5', 'Mode Sensor Privasi: ' . ($isMasked ? 'AKTIF (3 Digit RM & Nama Tengah Disamarkan)' : 'NONAKTIF (Identitas Pasien Lengkap)') . ' | Waktu Unduh: ' . now()->format('d/m/Y H:i:s'));

        $headers = [
            'No', 'No. Rawat', 'No. RM', 'Nama Pasien', 'L/P', 'Umur', 'Tgl Kunjungan',
            'Kode ICD', 'Nama Diagnosa / Penyakit', 'Layanan', 'Prioritas', 'Kasus',
            'Status Dinas', 'Dokter DPJP', 'Penjamin'
        ];
        $sheet->fromArray($headers, null, 'A7');
        $sheet->getRowDimension(7)->setRowHeight(24);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '064E3B']]]
        ];
        $sheet->getStyle('A7:O7')->applyFromArray($headerStyle);

        $row = 8;
        foreach ($visits as $idx => $v) {
            $rm = $isMasked ? $v['no_rkm_medis_masked'] : $v['no_rkm_medis'];
            $nama = $isMasked ? $v['nama_masked'] : $v['nama'];

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, $v['no_rawat'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $rm, DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('E' . $row, $v['jk']);
            $sheet->setCellValue('F' . $row, $v['umur'] !== '-' ? $v['umur'] . ' th' : '-');
            $sheet->setCellValue('G' . $row, $v['tanggal']);
            $sheet->setCellValueExplicit('H' . $row, $v['kd_penyakit'], DataType::TYPE_STRING);
            $sheet->setCellValue('I' . $row, $v['nm_penyakit']);
            $sheet->setCellValue('J' . $row, $v['layanan']);
            $sheet->setCellValue('K' . $row, $v['prioritas']);
            $sheet->setCellValue('L' . $row, $v['kasus']);
            $sheet->setCellValue('M' . $row, $v['status_dinas'] ?? 'UMUM');
            $sheet->setCellValue('N' . $row, $v['dokter']);
            $sheet->setCellValue('O' . $row, $v['penjamin']);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($idx % 2 === 1) {
                $sheet->getStyle('A' . $row . ':O' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        if ($row > 8) {
            $sheet->getStyle('A8:O' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
        }

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Helper menyusun sheet Excel untuk Data Pengunjung Pasien (Individu Unik)
     */
    private function buildVisitorsExcelSheet(Spreadsheet $spreadsheet, array $visitors, array $filterInfo, bool $isMasked, string $hospitalName): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pengunjung');
        $sheet->setShowGridLines(true);

        // Header Dokumen
        $sheet->setCellValue('A1', $hospitalName);
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LAPORAN DATA PENGUNJUNG PASIEN (INDIVIDU UNIK) BERDASARKAN DIAGNOSA PENYAKIT');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setSize(10.5)->setBold(true)->getColor()->setRGB('0284C7');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Periode: ' . $filterInfo['period_label'] . ' | Layanan: ' . $filterInfo['service_label'] . ' | Kategori: ' . ($filterInfo['special_case_label'] ?? 'Semua'));
        $sheet->setCellValue('A5', 'Mode Sensor Privasi: ' . ($isMasked ? 'AKTIF (3 Digit RM & Nama Tengah Disamarkan)' : 'NONAKTIF (Identitas Pasien Lengkap)') . ' | Waktu Unduh: ' . now()->format('d/m/Y H:i:s'));

        $headers = [
            'No', 'No. RM', 'Nama Pasien', 'L/P', 'Umur', 'Total Kunjungan',
            'Kunjungan Pertama', 'Kunjungan Terakhir', 'Status Dinas', 'Riwayat Kode Diagnosa'
        ];
        $sheet->fromArray($headers, null, 'A7');
        $sheet->getRowDimension(7)->setRowHeight(24);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0369A1']]]
        ];
        $sheet->getStyle('A7:J7')->applyFromArray($headerStyle);

        $row = 8;
        foreach ($visitors as $idx => $v) {
            $rm = $isMasked ? $v['no_rkm_medis_masked'] : $v['no_rkm_medis'];
            $nama = $isMasked ? $v['nama_masked'] : $v['nama'];

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, $rm, DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('C' . $row, $rm);
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('E' . $row, $v['umur'] !== '-' ? $v['umur'] . ' th' : '-');
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('C' . $row, $rm);
            $sheet->setCellValue('B' . $row, $rm);
            $sheet->setCellValue('C' . $row, $nama);
            $sheet->setCellValue('D' . $row, $v['jk']);
            $sheet->setCellValue('E' . $row, $v['umur'] !== '-' ? $v['umur'] . ' th' : '-');
            $sheet->setCellValue('F' . $row, $v['total_kunjungan']);
            $sheet->setCellValue('G' . $row, $v['kunjungan_pertama']);
            $sheet->setCellValue('H' . $row, $v['kunjungan_terakhir']);
            $sheet->setCellValue('I' . $row, $v['status_dinas']);
            $sheet->setCellValue('J' . $row, $v['riwayat_diagnosa']);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($idx % 2 === 1) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        if ($row > 8) {
            $sheet->getStyle('A8:J' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Helper menyusun sheet Excel untuk Rekapitulasi Morbiditas Penyakit
     */
    private function buildMorbiditasExcelSheet(Spreadsheet $spreadsheet, array $exportData, array $filterInfo, string $hospitalName): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Morbiditas Penyakit');
        $sheet->setShowGridLines(true);

        // Header Dokumen
        $sheet->setCellValue('A1', $hospitalName);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI DATA MORBIDITAS PENYAKIT (ICD-10)');
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->getFont()->setSize(10.5)->setBold(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Periode: ' . $filterInfo['period_label'] . ' | Layanan: ' . $filterInfo['service_label']);
        $sheet->setCellValue('A5', 'Kategori Kasus: ' . ($filterInfo['special_case_label'] ?? 'Semua') . ' | Waktu Unduh: ' . now()->format('d/m/Y H:i:s'));

        $headers = [
            'No', 'Kode ICD', 'Nama Diagnosa / Penyakit', 'Baru (L)', 'Baru (P)',
            'Lama (L)', 'Lama (P)', 'Poli', 'IGD', 'Ranap', 'Total Kasus', 'Persentase (%)'
        ];
        $sheet->fromArray($headers, null, 'A7');
        $sheet->getRowDimension(7)->setRowHeight(24);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]]
        ];
        $sheet->getStyle('A7:L7')->applyFromArray($headerStyle);

        $row = 8;
        $items = $exportData['items'] ?? [];
        foreach ($items as $idx => $d) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, $d['code'], DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $d['name']);
            $sheet->setCellValue('D' . $row, $d['baru_pria']);
            $sheet->setCellValue('E' . $row, $d['baru_wanita']);
            $sheet->setCellValue('F' . $row, $d['lama_pria']);
            $sheet->setCellValue('G' . $row, $d['lama_wanita']);
            $sheet->setCellValue('H' . $row, $d['total_poli']);
            $sheet->setCellValue('I' . $row, $d['total_igd']);
            $sheet->setCellValue('J' . $row, $d['total_ranap']);
            $sheet->setCellValue('K' . $row, $d['total_kasus']);
            $sheet->setCellValue('L' . $row, $d['percent'] . '%');

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            if ($idx % 2 === 1) {
                $sheet->getStyle('A' . $row . ':L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        if ($row > 8) {
            // Baris Total
            $sheet->setCellValue('A' . $row, 'TOTAL KESELURUHAN (' . count($items) . ' PENYAKIT)');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->setCellValue('D' . $row, collect($items)->sum('baru_pria'));
            $sheet->setCellValue('E' . $row, collect($items)->sum('baru_wanita'));
            $sheet->setCellValue('F' . $row, collect($items)->sum('lama_pria'));
            $sheet->setCellValue('G' . $row, collect($items)->sum('lama_wanita'));
            $sheet->setCellValue('H' . $row, collect($items)->sum('total_poli'));
            $sheet->setCellValue('I' . $row, collect($items)->sum('total_igd'));
            $sheet->setCellValue('J' . $row, collect($items)->sum('total_ranap'));
            $sheet->setCellValue('K' . $row, collect($items)->sum('total_kasus'));
            $sheet->setCellValue('L' . $row, '100%');

            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
            ]);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle('A8:L' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Membangun multi-sheet laporan Matriks 12 Bulan & MoM Growth ke Excel
     */
    private function buildMomExcelSheet(Spreadsheet $spreadsheet, array $filterInfo, string $hospitalName): void
    {
        $year = $this->year;
        $monthlyBreakdown = DiseaseReportRepository::getMonthlyBreakdown($this->filters());
        $diseaseMatrix = DiseaseReportRepository::getDiseaseMonthlyMatrix($this->filters(), 100);
        $polyMatrix = DiseaseReportRepository::getPolyclinicMonthlyMatrix($this->filters());
        $docMatrix = DiseaseReportRepository::getDoctorMonthlyMatrix($this->filters(), 50);

        // ==================== SHEET 1: Matriks Penyakit ICD-10 ====================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Penyakit ICD-10');
        $sheet->setShowGridLines(true);

        $sheet->setCellValue('A1', $hospitalName);
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', "MATRIKS 12 BULAN & PERTUMBUHAN MoM KASUS PENYAKIT (ICD-10) TAHUN {$year}");
        $sheet->mergeCells('A2:Q2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setBold(true)->getColor()->setRGB('059669');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', "Filter Layanan: {$filterInfo['service_label']} | Kasus Khusus: {$filterInfo['special_case_label']}");
        $sheet->setCellValue('A5', 'Waktu Unduh: ' . now()->format('d/m/Y H:i:s') . ' WIB');

        $headers = ['No', 'Kode ICD', 'Nama Diagnosa / Penyakit', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des', 'Total Kasus', 'MoM % Growth'];
        $sheet->fromArray($headers, null, 'A7');
        $sheet->getRowDimension(7)->setRowHeight(24);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '064E3B']]]
        ];
        $sheet->getStyle('A7:Q7')->applyFromArray($headerStyle);

        $row = 8;
        foreach ($diseaseMatrix['items'] as $idx => $d) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, $d['kd_penyakit'], DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $d['nm_penyakit']);

            $colLetter = 'D';
            for ($m = 1; $m <= 12; $m++) {
                $sheet->setCellValue($colLetter . $row, $d['months'][$m] ?? 0);
                $colLetter++;
            }

            $sheet->setCellValue('P' . $row, $d['total_kasus']);
            $sheet->setCellValue('Q' . $row, ($d['growth_pct'] > 0 ? '+' : '') . $d['growth_pct'] . '%');

            $row++;
        }

        $lastRow = max($row - 1, 8);
        $sheet->getStyle('A8:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B8:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D8:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('Q8:Q' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7:Q' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ==================== SHEET 2: Tren Bulanan RS ====================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Tren Bulanan RS');
        $sheet2->setShowGridLines(true);

        $sheet2->setCellValue('A1', "REKAPITULASI TREN MoM BULANAN RUMAH SAKIT TAHUN {$year}");
        $sheet2->mergeCells('A1:M1');
        $sheet2->getStyle('A1')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('059669');

        $headers2 = ['No', 'Bulan', 'Kasus Baru', 'Kasus Lama', 'Poli', 'IGD', 'Total Ralan', 'Ranap', 'Penyakit Unik', 'Pasien Unik', 'Total Kasus', 'MoM Growth (%)', 'Share (%)'];
        $sheet2->fromArray($headers2, null, 'A3');
        $sheet2->getStyle('A3:M3')->applyFromArray($headerStyle);

        $row2 = 4;
        foreach ($monthlyBreakdown['months'] as $m => $item) {
            $sheet2->setCellValue('A' . $row2, $m);
            $sheet2->setCellValue('B' . $row2, $item['nama_bulan']);
            $sheet2->setCellValue('C' . $row2, $item['kasus_baru']);
            $sheet2->setCellValue('D' . $row2, $item['kasus_lama']);
            $sheet2->setCellValue('E' . $row2, $item['poli']);
            $sheet2->setCellValue('F' . $row2, $item['igd']);
            $sheet2->setCellValue('G' . $row2, $item['ralan']);
            $sheet2->setCellValue('H' . $row2, $item['ranap']);
            $sheet2->setCellValue('I' . $row2, $item['total_penyakit_unik']);
            $sheet2->setCellValue('J' . $row2, $item['total_pasien_unik']);
            $sheet2->setCellValue('K' . $row2, $item['total_kasus']);
            $sheet2->setCellValue('L' . $row2, $item['growth_total'] !== null ? ($item['growth_total'] > 0 ? '+' : '') . $item['growth_total'] . '%' : '-');
            $sheet2->setCellValue('M' . $row2, $item['persentase'] . '%');
            $row2++;
        }

        // Total Setahun
        $sheet2->setCellValue('A' . $row2, 'TOTAL SETAHUN');
        $sheet2->mergeCells('A' . $row2 . ':B' . $row2);
        $sheet2->setCellValue('C' . $row2, $monthlyBreakdown['totals']['kasus_baru']);
        $sheet2->setCellValue('D' . $row2, $monthlyBreakdown['totals']['kasus_lama']);
        $sheet2->setCellValue('E' . $row2, $monthlyBreakdown['totals']['poli']);
        $sheet2->setCellValue('F' . $row2, $monthlyBreakdown['totals']['igd']);
        $sheet2->setCellValue('G' . $row2, $monthlyBreakdown['totals']['ralan']);
        $sheet2->setCellValue('H' . $row2, $monthlyBreakdown['totals']['ranap']);
        $sheet2->setCellValue('I' . $row2, $monthlyBreakdown['totals']['total_penyakit_unik']);
        $sheet2->setCellValue('J' . $row2, $monthlyBreakdown['totals']['total_pasien_unik']);
        $sheet2->setCellValue('K' . $row2, $monthlyBreakdown['totals']['total_kasus']);
        $sheet2->setCellValue('L' . $row2, '-');
        $sheet2->setCellValue('M' . $row2, '100%');

        $sheet2->getStyle('A' . $row2 . ':M' . $row2)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
        ]);

        $sheet2->getStyle('A3:M' . $row2)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet2->getStyle('C4:K' . $row2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet2->getStyle('L4:M' . $row2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'M') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // ==================== SHEET 3: Poliklinik & IGD ====================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Poliklinik & IGD');
        $sheet3->setShowGridLines(true);

        $sheet3->setCellValue('A1', "MATRIKS 12 BULAN KASUS PENYAKIT PER POLIKLINIK & IGD TAHUN {$year}");
        $sheet3->mergeCells('A1:R1');
        $sheet3->getStyle('A1')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('059669');

        $headers3 = ['No', 'Kode Unit', 'Nama Poliklinik / Ruang', 'Layanan', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des', 'Total Kasus', 'MoM % Growth'];
        $sheet3->fromArray($headers3, null, 'A3');
        $sheet3->getStyle('A3:R3')->applyFromArray($headerStyle);

        $row3 = 4;
        foreach ($polyMatrix['items'] as $idx => $pl) {
            $sheet3->setCellValue('A' . $row3, $idx + 1);
            $sheet3->setCellValueExplicit('B' . $row3, $pl['kd_poli'], DataType::TYPE_STRING);
            $sheet3->setCellValue('C' . $row3, $pl['nm_poli']);
            $sheet3->setCellValue('D' . $row3, $pl['jenis_layanan']);

            $colLetter = 'E';
            for ($m = 1; $m <= 12; $m++) {
                $sheet3->setCellValue($colLetter . $row3, $pl['months'][$m] ?? 0);
                $colLetter++;
            }
            $sheet3->setCellValue('Q' . $row3, $pl['total_kasus']);
            $sheet3->setCellValue('R' . $row3, ($pl['growth_pct'] > 0 ? '+' : '') . $pl['growth_pct'] . '%');
            $row3++;
        }

        $lastRow3 = max($row3 - 1, 4);
        $sheet3->getStyle('A3:R' . $lastRow3)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet3->getStyle('E4:Q' . $lastRow3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet3->getStyle('R4:R' . $lastRow3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'R') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        // ==================== SHEET 4: Dokter DPJP ====================
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Dokter DPJP');
        $sheet4->setShowGridLines(true);

        $sheet4->setCellValue('A1', "MATRIKS 12 BULAN KASUS PENYAKIT PER DOKTER DPJP TAHUN {$year}");
        $sheet4->mergeCells('A1:S1');
        $sheet4->getStyle('A1')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('059669');

        $headers4 = ['No', 'Nama Dokter DPJP', 'Penyakit Unik', 'Pasien Unik', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des', 'Total Kasus', 'MoM % Growth'];
        $sheet4->fromArray($headers4, null, 'A3');
        $sheet4->getStyle('A3:R3')->applyFromArray($headerStyle);

        $row4 = 4;
        foreach ($docMatrix['items'] as $idx => $dr) {
            $sheet4->setCellValue('A' . $row4, $idx + 1);
            $sheet4->setCellValue('B' . $row4, $dr['nm_dokter']);
            $sheet4->setCellValue('C' . $row4, $dr['total_penyakit_unik']);
            $sheet4->setCellValue('D' . $row4, $dr['total_pasien_unik']);

            $colLetter = 'E';
            for ($m = 1; $m <= 12; $m++) {
                $sheet4->setCellValue($colLetter . $row4, $dr['months'][$m] ?? 0);
                $colLetter++;
            }
            $sheet4->setCellValue('Q' . $row4, $dr['total_kasus']);
            $sheet4->setCellValue('R' . $row4, ($dr['growth_pct'] > 0 ? '+' : '') . $dr['growth_pct'] . '%');
            $row4++;
        }

        $lastRow4 = max($row4 - 1, 4);
        $sheet4->getStyle('A3:R' . $lastRow4)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet4->getStyle('C4:Q' . $lastRow4)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet4->getStyle('R4:R' . $lastRow4)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'R') as $col) {
            $sheet4->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
    }

    public function render()
    {
        $diseases = null;
        $visits = null;
        $visitors = null;

        if ($this->activeTab === 'morbiditas') {
            $diseases = DiseaseReportRepository::getDiseasesPaginated($this->filters(), $this->perPage);
        } elseif ($this->activeTab === 'kunjungan') {
            $visits = DiseaseReportRepository::getVisitsPaginated($this->filters(), $this->perPage);
        } elseif ($this->activeTab === 'pengunjung') {
            $visitors = DiseaseReportRepository::getVisitorsPaginated($this->filters(), $this->perPage);
        }

        return view('pages.icd.recap', array_merge(get_object_vars($this), [
            'diseases' => $diseases,
            'visits' => $visits,
            'visitors' => $visitors,
        ]));
    }
}
