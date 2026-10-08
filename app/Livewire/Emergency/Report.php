<?php

namespace App\Livewire\Emergency;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Repository\EmergencyReportRepository;
use App\Helpers\FilterHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Report extends Component
{
    use WithPagination;

    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    #[Url]
    public $period = 'monthly';

    #[Url]
    public $statusLanjut = 'semua';

    #[Url]
    public $statusPelayanan = 'semua';

    #[Url]
    public $payType = 'semua';

    #[Url]
    public $doctor = 'semua';

    #[Url]
    public $gender = 'semua';

    #[Url]
    public $sttsDaftar = 'semua';

    #[Url]
    public $search = '';

    #[Url]
    public $limit = 25;

    public $activeTab = 'rekap_status'; // rekap_status, rekap_dokter, rekap_bayar, data_pasien

    public bool $showCharts = true;

    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = (int) date('n');
        $this->selectedYear = (int) date('Y');
        $this->syncDates();
    }

    public function updatedPeriod()
    {
        $this->syncDates();
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->syncDates();
        $this->resetPage();
    }

    public function updatedSelectedYear()
    {
        $this->syncDates();
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedStatusLanjut()
    {
        $this->resetPage();
    }

    public function updatedStatusPelayanan()
    {
        $this->resetPage();
    }

    public function updatedPayType()
    {
        $this->resetPage();
    }

    public function updatedDoctor()
    {
        $this->resetPage();
    }

    public function updatedGender()
    {
        $this->resetPage();
    }

    public function updatedSttsDaftar()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleCharts()
    {
        $this->showCharts = !$this->showCharts;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
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
    public function doctors()
    {
        return FilterHelper::getDoctors();
    }

    #[Computed]
    public function summary()
    {
        return EmergencyReportRepository::getSummary(
            $this->startDate,
            $this->endDate,
            $this->statusLanjut,
            $this->statusPelayanan,
            $this->payType,
            $this->doctor,
            $this->gender,
            $this->sttsDaftar,
            $this->search
        );
    }

    #[Computed]
    public function statusBreakdown()
    {
        return EmergencyReportRepository::getStatusBreakdown(
            $this->startDate,
            $this->endDate,
            $this->payType,
            $this->doctor
        );
    }

    #[Computed]
    public function doctorBreakdown()
    {
        return EmergencyReportRepository::getDoctorBreakdown(
            $this->startDate,
            $this->endDate,
            $this->payType
        );
    }

    #[Computed]
    public function payTypeBreakdown()
    {
        return EmergencyReportRepository::getPayTypeBreakdown(
            $this->startDate,
            $this->endDate
        );
    }

    #[Computed]
    public function trendData()
    {
        return EmergencyReportRepository::getTrend(
            $this->startDate,
            $this->endDate,
            $this->statusLanjut,
            $this->payType,
            $this->doctor
        );
    }

    #[Computed]
    public function patients()
    {
        return EmergencyReportRepository::getPatients(
            $this->startDate,
            $this->endDate,
            $this->statusLanjut,
            $this->statusPelayanan,
            $this->payType,
            $this->doctor,
            $this->gender,
            $this->sttsDaftar,
            $this->search,
            $this->limit
        );
    }

    public function exportExcel()
    {
        $allPatients = EmergencyReportRepository::getPatients(
            $this->startDate,
            $this->endDate,
            $this->statusLanjut,
            $this->statusPelayanan,
            $this->payType,
            $this->doctor,
            $this->gender,
            $this->sttsDaftar,
            $this->search,
            0
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pasien IGD');

        // Header Title
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN PASIEN INSTALASI GAWAT DARURAT (IGD)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle Periode
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($this->startDate)->translatedFormat('d F Y') . ' s.d. ' . Carbon::parse($this->endDate)->translatedFormat('d F Y'));
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Header
        $headers = [
            'No', 'No. Rawat', 'No. RM', 'Nama Pasien', 'L/P',
            'Tgl Masuk', 'Jam', 'Dokter Jaga', 'Penanggung Jawab', 'Status Lanjut'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
            $sheet->getStyle($col . '4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // Data Rows
        $rowIdx = 5;
        foreach ($allPatients as $idx => $p) {
            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValueExplicit('B' . $rowIdx, $p->no_rawat, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $rowIdx, $p->no_rkm_medis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowIdx, $p->nm_pasien);
            $sheet->setCellValue('E' . $rowIdx, $p->jk);
            $sheet->setCellValue('F' . $rowIdx, Carbon::parse($p->tgl_registrasi)->format('d/m/Y'));
            $sheet->setCellValue('G' . $rowIdx, $p->jam_reg);
            $sheet->setCellValue('H' . $rowIdx, $p->nm_dokter);
            $sheet->setCellValue('I' . $rowIdx, $p->png_jawab);
            $sheet->setCellValue('J' . $rowIdx, $p->status_lanjut);

            $sheet->getStyle('A' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowIdx++;
        }

        // Auto width
        foreach (range('A', 'J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_IGD_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        return view('pages.emergency.report', [
            'summary' => $this->summary,
            'statusBreakdown' => $this->statusBreakdown,
            'doctorBreakdown' => $this->doctorBreakdown,
            'payTypeBreakdown' => $this->payTypeBreakdown,
            'trendData' => $this->trendData,
            'patients' => $this->patients,
        ]);
    }
}
