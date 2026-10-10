<?php

namespace App\Livewire\Inpatient;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use Livewire\Attributes\Computed;
use App\Repository\InpatientReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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
    public $payType = 'BPJ';

    #[Url]
    public $statusPulang = 'semua';

    #[Url]
    public $ward = 'semua';

    #[Url]
    public $search = '';

    #[Url]
    public $limit = 25;

    #[Url]
    public $dinasFilter = 'semua';

    public $activeTab = 'daftar_pasien';

    public bool $showCharts = true;

    public $selectedMonth;
    public $selectedYear;

    public function toggleCharts(): void
    {
        $this->showCharts = !$this->showCharts;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

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

    public function updatedPayType()
    {
        $this->resetPage();
    }

    public function updatedStatusPulang()
    {
        $this->resetPage();
    }

    public function updatedWard()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedLimit()
    {
        $this->resetPage();
    }

    public function updatedDinasFilter()
    {
        $this->resetPage();
    }

    /**
     * Menyinkronkan tanggal awal dan akhir berdasarkan jenis periode yang dipilih.
     */
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
    }

    /**
     * Mengatur ulang seluruh filter kembali ke default.
     */
    public function resetFilters()
    {
        $this->period = 'monthly';
        $this->selectedMonth = (int) date('n');
        $this->selectedYear = (int) date('Y');
        $this->payType = 'BPJ';
        $this->dinasFilter = 'semua';
        $this->statusPulang = 'semua';
        $this->ward = 'semua';
        $this->search = '';
        $this->limit = 25;
        $this->syncDates();
        $this->resetPage();
    }

    #[Computed]
    public function months(): array
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
    public function years(): array
    {
        return range(date('Y') - 5, date('Y'));
    }

    #[Computed]
    public function payTypes(): array
    {
        return [
            ['title' => 'Semua Penjab', 'value' => 'semua'],
            ...collect(FilterHelper::getPayTypes())
                ->reject(fn($item) => $item['value'] === 'semua')
                ->toArray()
        ];
    }

    #[Computed]
    public function patientTypes(): array
    {
        return [
            ['title' => 'Semua Jenis Pasien', 'value' => 'semua'],
            ['title' => 'Pasien Dinas (TNI & POLRI)', 'value' => 'DINAS'],
            ['title' => '• Dinas TNI', 'value' => 'TNI'],
            ['title' => '• Dinas POLRI', 'value' => 'POLRI'],
            ['title' => '• Pasien Sipil / Non-Dinas', 'value' => 'NON_DINAS'],
        ];
    }

    #[Computed]
    public function wards(): array
    {
        return FilterHelper::getWards();
    }

    #[Computed]
    public function limits(): array
    {
        return [25, 50, 100];
    }

    #[Computed]
    public function statusPulangOptions(): array
    {
        return [
            ['value' => 'semua', 'title' => 'Semua Status Pulang'],
            ['value' => 'sudah_pulang', 'title' => 'Sudah Pulang / Keluar'],
            ['value' => 'masih_dirawat', 'title' => 'Masih Dirawat'],
        ];
    }

    #[Computed]
    public function summary(): array
    {
        return InpatientReportRepository::getSummary(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search,
            dinasFilter: $this->dinasFilter
        );
    }

    #[Computed]
    public function wardBreakdown(): array
    {
        return InpatientReportRepository::getWardBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            search: $this->search
        );
    }

    #[Computed]
    public function payTypeBreakdown(): array
    {
        return InpatientReportRepository::getPayTypeBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search
        );
    }

    #[Computed]
    public function patientTypeBreakdown(): array
    {
        return InpatientReportRepository::getPatientTypeBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search
        );
    }

    #[Computed]
    public function ageGroupBreakdown(): array
    {
        return InpatientReportRepository::getAgeGroupBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search
        );
    }

    #[Computed]
    public function dinasBreakdown(): array
    {
        return InpatientReportRepository::getDinasBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search
        );
    }

    #[Computed]
    public function diagnosisBreakdown(): array
    {
        return InpatientReportRepository::getDiagnosisBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ward: $this->ward,
            doctor: $this->doctor ?? null,
            payType: $this->payType,
            gender: $this->gender ?? null,
            limit: 50,
            dinasFilter: $this->dinasFilter
        );
    }

    public function rendered()
    {
        if ($this->showCharts) {
            $this->dispatch('inpatient-charts-updated', payload: $this->chartPayload);
        }
    }

    #[Computed]
    public function chartPayload(): array
    {
        // 1. Tren Pasien Masuk Harian
        $trend = InpatientReportRepository::getTrend(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search
        );

        // 2. Top 8 Bangsal / Ruangan
        $wardList = array_slice($this->wardBreakdown, 0, 8);
        $wardLabels = array_map(function ($w) {
            $name = $w['nm_bangsal'];
            return strlen($name) > 22 ? substr($name, 0, 20) . '...' : $name;
        }, $wardList);
        $wardTotals = array_column($wardList, 'total');
        $wardMasih = array_column($wardList, 'masih_dirawat');
        $wardPulang = array_column($wardList, 'sudah_pulang');

        // 3. Distribusi Penjab (Top 5 + Lainnya)
        $topPayers = array_slice($this->payTypeBreakdown, 0, 5);
        $otherPayerCount = array_sum(array_column(array_slice($this->payTypeBreakdown, 5), 'total'));
        $payerLabels = array_map(function ($p) {
            $name = $p['png_jawab'];
            return strlen($name) > 20 ? substr($name, 0, 18) . '...' : $name;
        }, $topPayers);
        $payerTotals = array_column($topPayers, 'total');
        if ($otherPayerCount > 0) {
            $payerLabels[] = 'Lainnya';
            $payerTotals[] = $otherPayerCount;
        }

        // 4. Distribusi Jenis Pasien (Dinas TNI, Dinas POLRI, Pasien Sipil / Non-Dinas)
        $patientTypeItems = $this->patientTypeBreakdown['items'] ?? [];
        $patientTypeLabels = array_column($patientTypeItems, 'label');
        $patientTypeTotals = array_column($patientTypeItems, 'total');
        $patientTypeColors = array_column($patientTypeItems, 'color');

        // 5. Sebaran Kelompok Umur & Gender SIRS
        $ageList = $this->ageGroupBreakdown;
        $ageLabels = array_column($ageList, 'nama');
        $agePria = array_column($ageList, 'pria');
        $ageWanita = array_column($ageList, 'wanita');

        return [
            'trend' => [
                'labels' => $trend['labels'],
                'total' => $trend['total'],
                'pria' => $trend['pria'],
                'wanita' => $trend['wanita'],
            ],
            'ward' => [
                'labels' => $wardLabels,
                'total' => $wardTotals,
                'masih' => $wardMasih,
                'pulang' => $wardPulang,
            ],
            'payer' => [
                'labels' => $payerLabels,
                'totals' => $payerTotals,
            ],
            'patientType' => [
                'labels' => $patientTypeLabels,
                'totals' => $patientTypeTotals,
                'colors' => $patientTypeColors,
            ],
            'age' => [
                'labels' => $ageLabels,
                'pria' => $agePria,
                'wanita' => $ageWanita,
            ],
        ];
    }

    /**
     * Menghasilkan nama file dokumen ekspor sesuai format:
     * Laporan Pasien Rawat Inap [bulan laporan] [tahun laporan]_[timestamp].[extension]
     */
    private function getExportFilename(string $extension): string
    {
        $startCarbon = Carbon::parse($this->startDate);
        $endCarbon = Carbon::parse($this->endDate);

        $startMonth = $this->months()[(int) $startCarbon->format('n')] ?? $startCarbon->translatedFormat('F');
        $endMonth = $this->months()[(int) $endCarbon->format('n')] ?? $endCarbon->translatedFormat('F');

        if ($startCarbon->format('Y-m') === $endCarbon->format('Y-m')) {
            $bulanLaporan = $startMonth;
            $tahunLaporan = $startCarbon->format('Y');
        } elseif ($startCarbon->format('Y') === $endCarbon->format('Y')) {
            $bulanLaporan = "{$startMonth} - {$endMonth}";
            $tahunLaporan = $startCarbon->format('Y');
        } else {
            $bulanLaporan = "{$startMonth} {$startCarbon->format('Y')} - {$endMonth}";
            $tahunLaporan = $endCarbon->format('Y');
        }

        $timestamp = now()->format('Ymd_His');

        return "Laporan Pasien Rawat Inap {$bulanLaporan} {$tahunLaporan}_{$timestamp}.{$extension}";
    }

    /**
     * Ekspor data ke format CSV.
     */
    public function exportCsv()
    {
        set_time_limit(0);

        $patients = InpatientReportRepository::getPatients(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search,
            limit: 0,
            dinasFilter: $this->dinasFilter
        );

        $filename = $this->getExportFilename('csv');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($patients) {
            $file = fopen('php://output', 'w');
            // Menambahkan UTF-8 BOM untuk kompatibilitas Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'No. Rawat',
                'No. RM',
                'Nama Pasien',
                'Bangsal',
                'Tgl Masuk',
                'Tgl Keluar',
                'Penjab',
                'Jenis Pasien',
                'DPJP Ranap'
            ]);

            foreach ($patients as $index => $patient) {
                $tglKeluar = ($patient->tgl_keluar == '0000-00-00' || empty($patient->tgl_keluar))
                    ? 'Masih Dirawat'
                    : Carbon::parse($patient->tgl_keluar)->format('d/m/Y');

                fputcsv($file, [
                    $index + 1,
                    $patient->no_rawat,
                    $patient->no_rkm_medis,
                    $patient->nm_pasien,
                    $patient->nm_bangsal,
                    Carbon::parse($patient->tgl_masuk)->format('d/m/Y'),
                    $tglKeluar,
                    $patient->png_jawab ?? '-',
                    $patient->status_dinas ?? '-',
                    $patient->dpjp_ranap ?? '-'
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, [
                'Data diperoleh melalui ' . config('app.name') . ' milik ' . config('app.hospital_name') . ' pada ' . now()->format('d/m/Y H:i:s')
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ekspor data ke format PDF.
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $patients = InpatientReportRepository::getPatients(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search,
            limit: 0,
            dinasFilter: $this->dinasFilter
        );

        $selectedPayTypeTitle = 'Semua Penjab';
        if ($this->payType !== 'semua') {
            $matched = collect($this->payTypes())->firstWhere('value', $this->payType);
            if ($matched) {
                $selectedPayTypeTitle = $matched['title'];
            }
        }

        $selectedPatientTypeTitle = 'Semua Jenis Pasien';
        if ($this->dinasFilter !== 'semua') {
            $matchedPt = collect($this->patientTypes())->firstWhere('value', $this->dinasFilter);
            if ($matchedPt) {
                $selectedPatientTypeTitle = $matchedPt['title'];
            }
        }

        $pdf = Pdf::loadView('reports.inpatient-report-pdf', [
            'patients' => $patients,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'payTypeTitle' => $selectedPayTypeTitle,
            'patientTypeTitle' => $selectedPatientTypeTitle,
            'summary' => $this->summary(),
            'patientTypeBreakdown' => $this->patientTypeBreakdown,
            'dinasBreakdown' => $this->dinasBreakdown,
            'diagnosisBreakdown' => $this->diagnosisBreakdown,
        ])->setPaper('a4', 'landscape');

        $filename = $this->getExportFilename('pdf');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }

    /**
     * Ekspor data ke format Excel (.xlsx).
     */
    public function exportExcel()
    {
        set_time_limit(0);

        $patients = InpatientReportRepository::getPatients(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search,
            limit: 0,
            dinasFilter: $this->dinasFilter
        );

        $selectedPayTypeTitle = 'Semua Penjab';
        if ($this->payType !== 'semua') {
            $matched = collect($this->payTypes())->firstWhere('value', $this->payType);
            if ($matched) {
                $selectedPayTypeTitle = $matched['title'];
            }
        }

        $summary = $this->summary();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(config('app.name', 'Dashboard SIMRS'))
            ->setTitle('Laporan Pasien Rawat Inap');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Ranap');
        $sheet->setShowGridLines(true);

        // Judul Laporan
        $sheet->setCellValue('A1', config('app.hospital_name', 'RUMAH SAKIT'));
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LAPORAN PASIEN RAWAT INAP');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('475569');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Informasi Ringkasan / Metadata
        $sheet->setCellValue('A4', 'Periode Tanggal Masuk: ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d/m/Y'));
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(9.5);

        $sheet->setCellValue('G4', 'Penjab: ' . $selectedPayTypeTitle . ' | ' . $selectedPatientTypeTitle);
        $sheet->mergeCells('G4:J4');
        $sheet->getStyle('G4')->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue('A5', 'Total Pasien: ' . number_format($summary['total_pasien'] ?? count($patients), 0, ',', '.') . ' orang (Sudah Pulang: ' . number_format($summary['sudah_pulang'] ?? 0, 0, ',', '.') . ', Masih Dirawat: ' . number_format($summary['masih_dirawat'] ?? 0, 0, ',', '.') . ') | TNI: ' . number_format($summary['total_tni'] ?? 0, 0, ',', '.') . ' | POLRI: ' . number_format($summary['total_polri'] ?? 0, 0, ',', '.'));
        $sheet->mergeCells('A5:F5');
        $sheet->getStyle('A5')->getFont()->setSize(9);

        $sheet->setCellValue('G5', 'Dicetak pada: ' . now()->format('d/m/Y H:i:s'));
        $sheet->mergeCells('G5:J5');
        $sheet->getStyle('G5')->getFont()->setSize(9);
        $sheet->getStyle('G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Header Tabel
        $headers = ['No', 'No. Rawat', 'No. RM', 'Nama Pasien', 'Bangsal', 'Tgl Masuk', 'Tgl Keluar', 'Penjab', 'Jenis Pasien', 'DPJP Ranap'];
        $sheet->fromArray($headers, null, 'A7');
        $sheet->getRowDimension(7)->setRowHeight(25);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0F172A']]
            ]
        ];
        $sheet->getStyle('A7:J7')->applyFromArray($headerStyle);

        // Data Baris
        $row = 8;
        foreach ($patients as $index => $patient) {
            $isMasihDirawat = ($patient->tgl_keluar == '0000-00-00' || empty($patient->tgl_keluar));

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValueExplicit('B' . $row, $patient->no_rawat, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $patient->no_rkm_medis, DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $patient->nm_pasien);
            $sheet->setCellValue('E' . $row, $patient->nm_bangsal);
            $sheet->setCellValue('F' . $row, Carbon::parse($patient->tgl_masuk)->format('d/m/Y'));
            $sheet->setCellValue('G' . $row, $isMasihDirawat ? 'Masih Dirawat' : Carbon::parse($patient->tgl_keluar)->format('d/m/Y'));
            $sheet->setCellValue('H' . $row, $patient->png_jawab ?? '-');
            $sheet->setCellValue('I' . $row, $patient->status_dinas ?? '-');
            $sheet->setCellValue('J' . $row, $patient->dpjp_ranap ?? '-');

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Indikator visual pasien masih dirawat
            if ($isMasihDirawat) {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'B45309']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']]
                ]);
            }

            // Zebra striping untuk baris genap
            if ($index % 2 === 1) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                if (!$isMasihDirawat) {
                    $sheet->getStyle('G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                }
                $sheet->getStyle('H' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        // Garis batas sel data
        if ($row > 8) {
            $sheet->getStyle('A8:J' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
        }

        // Penyesuaian lebar kolom (kolom No diberi lebar tetap agar ringkas dan proporsional)
        $sheet->getColumnDimension('A')->setAutoSize(false);
        $sheet->getColumnDimension('A')->setWidth(7);

        foreach (range('B', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Rekapitulasi Pasien Dinas (TNI / POLRI)
        $dinasBreakdown = $this->dinasBreakdown;
        $sheetDinas = $spreadsheet->createSheet();
        $sheetDinas->setTitle('Rekap Pasien Dinas');
        $sheetDinas->setShowGridLines(true);

        $sheetDinas->setCellValue('A1', 'REKAPITULASI PASIEN DINAS RAWAT INAP (TNI / POLRI)');
        $sheetDinas->mergeCells('A1:H1');
        $sheetDinas->getStyle('A1')->getFont()->setSize(12)->setBold(true);

        $sheetDinas->setCellValue('A2', 'Periode: ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d/m/Y') . ' | Total Dinas: ' . number_format($dinasBreakdown['summary']['total'] ?? 0, 0, ',', '.') . ' (TNI: ' . number_format($dinasBreakdown['summary']['tni'] ?? 0, 0, ',', '.') . ', POLRI: ' . number_format($dinasBreakdown['summary']['polri'] ?? 0, 0, ',', '.') . ')');
        $sheetDinas->mergeCells('A2:H2');
        $sheetDinas->getStyle('A2')->getFont()->setSize(9.5)->setItalic(true);

        $headersDinas = ['No', 'Kode Bangsal', 'Nama Bangsal / Ruangan', 'Total Dinas', 'Proporsi (%)', 'TNI', 'POLRI', 'Masih Dirawat', 'Sudah Pulang'];
        $sheetDinas->fromArray($headersDinas, null, 'A4');
        $sheetDinas->getStyle('A4:I4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheetDinas->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('7C3AED');

        $dinasRow = 5;
        foreach ($dinasBreakdown['wards'] ?? [] as $idx => $dw) {
            $sheetDinas->fromArray([
                $idx + 1,
                $dw['kd_bangsal'],
                $dw['nm_bangsal'],
                $dw['total'],
                $dw['percent'] . '%',
                $dw['tni'],
                $dw['polri'],
                $dw['masih_dirawat'],
                $dw['sudah_pulang']
            ], null, "A{$dinasRow}");
            $dinasRow++;
        }

        // Kategori Personel
        $catRowStart = $dinasRow + 2;
        $sheetDinas->setCellValue("A{$catRowStart}", 'KATEGORI PERSONEL PASIEN DINAS');
        $sheetDinas->mergeCells("A{$catRowStart}:G{$catRowStart}");
        $sheetDinas->getStyle("A{$catRowStart}")->getFont()->setSize(11)->setBold(true);

        $headersCat = ['No', 'Kategori Personel', 'Total Pasien', 'Proporsi (%)', 'TNI', 'POLRI', 'Masih Dirawat', 'Sudah Pulang'];
        $headerCatRow = $catRowStart + 1;
        $sheetDinas->fromArray($headersCat, null, "A{$headerCatRow}");
        $sheetDinas->getStyle("A{$headerCatRow}:H{$headerCatRow}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheetDinas->getStyle("A{$headerCatRow}:H{$headerCatRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0284C7');

        $catDataRow = $headerCatRow + 1;
        foreach ($dinasBreakdown['categories'] ?? [] as $cIdx => $cat) {
            $sheetDinas->fromArray([
                $cIdx + 1,
                $cat['kategori'],
                $cat['total'],
                $cat['percent'] . '%',
                $cat['tni'],
                $cat['polri'],
                $cat['masih_dirawat'],
                $cat['sudah_pulang']
            ], null, "A{$catDataRow}");
            $catDataRow++;
        }

        foreach (range('A', 'I') as $col) {
            $sheetDinas->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = $this->getExportFilename('xlsx');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function render()
    {
        $patients = InpatientReportRepository::getPatients(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            statusPulang: $this->statusPulang,
            ward: $this->ward,
            search: $this->search,
            limit: (int) $this->limit,
            dinasFilter: $this->dinasFilter
        );

        return view('pages.inpatient.report', [
            'dinasFilter' => $this->dinasFilter,
            'patientTypes' => $this->patientTypes(),
            'patients' => $patients,
            'summary' => $this->summary(),
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
            'payType' => $this->payType,
            'statusPulang' => $this->statusPulang,
            'ward' => $this->ward,
            'search' => $this->search,
            'limit' => $this->limit,
            'months' => $this->months(),
            'years' => $this->years(),
            'payTypes' => $this->payTypes(),
            'statusPulangOptions' => $this->statusPulangOptions(),
            'wards' => $this->wards(),
            'limits' => $this->limits(),
        ]);
    }
}
