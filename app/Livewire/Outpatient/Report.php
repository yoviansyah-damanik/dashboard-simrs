<?php

namespace App\Livewire\Outpatient;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Repository\OutpatientReportRepository;
use Illuminate\Support\Facades\DB;
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
    public $poly = 'semua';

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

    public $activeTab = 'rekap_poli'; // rekap_poli, rekap_bayar, rekap_gender

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

    public function updatedPoly()
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

    public function updatedLimit()
    {
        $this->resetPage();
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleCharts(): void
    {
        $this->showCharts = !$this->showCharts;
    }

    public function setPeriod(string $period)
    {
        $this->period = $period;
        $this->syncDates();
        $this->resetPage();
    }

    /**
     * Menyinkronkan rentang tanggal berdasarkan filter periode.
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
        $this->poly = 'semua';
        $this->payType = 'semua';
        $this->doctor = 'semua';
        $this->gender = 'semua';
        $this->sttsDaftar = 'semua';
        $this->search = '';
        $this->limit = 25;
        $this->syncDates();
        $this->resetPage();
    }

    #[Computed]
    public function months(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    #[Computed]
    public function years(): array
    {
        return range(date('Y') - 5, date('Y'));
    }

    #[Computed]
    public function polyclinics(): array
    {
        return DB::connection('simrs')
            ->table('poliklinik')
            ->where('status', '1')
            ->where('kd_poli', '!=', 'IGDK')
            ->select('kd_poli', 'nm_poli')
            ->orderBy('nm_poli')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function payTypes(): array
    {
        return DB::connection('simrs')
            ->table('penjab')
            ->where('status', '1')
            ->select('kd_pj', 'png_jawab')
            ->orderBy('png_jawab')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function doctors(): array
    {
        return DB::connection('simrs')
            ->table('dokter')
            ->where('status', '1')
            ->select('kd_dokter', 'nm_dokter')
            ->orderBy('nm_dokter')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function limits(): array
    {
        return [25, 50, 100];
    }

    #[Computed]
    public function summary(): array
    {
        return OutpatientReportRepository::getSummary(
            startDate: $this->startDate,
            endDate: $this->endDate,
            poly: $this->poly,
            payType: $this->payType,
            doctor: $this->doctor,
            gender: $this->gender,
            sttsDaftar: $this->sttsDaftar,
            search: $this->search
        );
    }

    #[Computed]
    public function polyBreakdown(): array
    {
        return OutpatientReportRepository::getPolyBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            payType: $this->payType,
            gender: $this->gender
        );
    }

    #[Computed]
    public function payTypeBreakdown(): array
    {
        return OutpatientReportRepository::getPayTypeBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            poly: $this->poly,
            gender: $this->gender
        );
    }

    #[Computed]
    public function ageGroupBreakdown(): array
    {
        return OutpatientReportRepository::getAgeGroupBreakdown(
            startDate: $this->startDate,
            endDate: $this->endDate,
            poly: $this->poly,
            payType: $this->payType
        );
    }

    #[Computed]
    public function chartPayload(): array
    {
        // 1. Tren Kunjungan Harian
        $trend = OutpatientReportRepository::getTrend(
            startDate: $this->startDate,
            endDate: $this->endDate,
            poly: $this->poly,
            payType: $this->payType,
            doctor: $this->doctor,
            gender: $this->gender,
            sttsDaftar: $this->sttsDaftar
        );

        // 2. Top 8 Poliklinik
        $polyList = array_slice($this->polyBreakdown, 0, 8);
        $polyLabels = array_map(function ($p) {
            $name = $p['nm_poli'];
            return strlen($name) > 22 ? substr($name, 0, 20) . '...' : $name;
        }, $polyList);
        $polyTotals = array_column($polyList, 'total');
        $polyPria = array_column($polyList, 'pria');
        $polyWanita = array_column($polyList, 'wanita');

        // 3. Distribusi Jenis Bayar / Penjamin (Top 5 + Lainnya)
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

        // 4. Piramida Kelompok Umur & Gender SIRS
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
            'poly' => [
                'labels' => $polyLabels,
                'total' => $polyTotals,
                'pria' => $polyPria,
                'wanita' => $polyWanita,
            ],
            'payer' => [
                'labels' => $payerLabels,
                'totals' => $payerTotals,
            ],
            'age' => [
                'labels' => $ageLabels,
                'pria' => $agePria,
                'wanita' => $ageWanita,
            ],
        ];
    }

    #[Computed]
    public function patients()
    {
        return OutpatientReportRepository::getPatients(
            startDate: $this->startDate,
            endDate: $this->endDate,
            poly: $this->poly,
            payType: $this->payType,
            doctor: $this->doctor,
            gender: $this->gender,
            sttsDaftar: $this->sttsDaftar,
            search: $this->search,
            limit: $this->limit
        );
    }

    /**
     * Nama file ekspor dokumen.
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

        return "Laporan Pasien Rawat Jalan {$bulanLaporan} {$tahunLaporan}_{$timestamp}.{$extension}";
    }

    /**
     * Ekspor data ke format CSV.
     */
    public function exportCsv()
    {
        set_time_limit(0);

        $polyBreakdown = OutpatientReportRepository::getPolyBreakdown($this->startDate, $this->endDate, $this->payType, $this->gender);
        $payTypeBreakdown = OutpatientReportRepository::getPayTypeBreakdown($this->startDate, $this->endDate, $this->poly, $this->gender);
        $ageGroupBreakdown = OutpatientReportRepository::getAgeGroupBreakdown($this->startDate, $this->endDate, $this->poly, $this->payType);

        $filename = $this->getExportFilename('csv');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($polyBreakdown, $payTypeBreakdown, $ageGroupBreakdown) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Section 1: Rekapitulasi Poliklinik
            fputcsv($file, ['REKAPITULASI PASIEN RAWAT JALAN PER POLIKLINIK / UNIT']);
            fputcsv($file, ['Periode', Carbon::parse($this->startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d/m/Y')]);
            fputcsv($file, []);
            fputcsv($file, ['No', 'Kode Poli', 'Nama Poliklinik', 'Total Pasien', 'Proporsi (%)', 'Laki-laki', 'Perempuan', 'Pasien Baru', 'Pasien Lama', 'BPJS', 'Umum', 'Dinas', 'Sudah Periksa', 'Belum Periksa']);

            foreach ($polyBreakdown as $index => $p) {
                fputcsv($file, [
                    $index + 1,
                    $p['kd_poli'],
                    $p['nm_poli'],
                    $p['total'],
                    $p['percent'] . '%',
                    $p['pria'],
                    $p['wanita'],
                    $p['baru'],
                    $p['lama'],
                    $p['bpjs'],
                    $p['umum'],
                    $p['dinas'],
                    $p['sudah'],
                    $p['belum'],
                ]);
            }

            // Section 2: Rekapitulasi Jenis Bayar
            fputcsv($file, []);
            fputcsv($file, ['REKAPITULASI PASIEN RAWAT JALAN PER JENIS BAYAR / PENJAMIN']);
            fputcsv($file, ['No', 'Kode PJ', 'Nama Penjamin', 'Total Pasien', 'Proporsi (%)', 'Laki-laki', 'Perempuan', 'Pasien Baru', 'Pasien Lama']);

            foreach ($payTypeBreakdown as $index => $pay) {
                fputcsv($file, [
                    $index + 1,
                    $pay['kd_pj'],
                    $pay['png_jawab'],
                    $pay['total'],
                    $pay['percent'] . '%',
                    $pay['pria'],
                    $pay['wanita'],
                    $pay['baru'],
                    $pay['lama'],
                ]);
            }

            // Section 3: Rekapitulasi Kelompok Umur
            fputcsv($file, []);
            fputcsv($file, ['REKAPITULASI DEMOGRAFI & KELOMPOK UMUR (SIRS)']);
            fputcsv($file, ['No', 'Kode', 'Kelompok Umur', 'Total Pasien', 'Distribusi (%)', 'Laki-laki', 'Perempuan']);

            foreach ($ageGroupBreakdown as $index => $age) {
                fputcsv($file, [
                    $index + 1,
                    $age['kode'],
                    $age['nama'],
                    $age['total'],
                    $age['percent'] . '%',
                    $age['pria'],
                    $age['wanita'],
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

        $polyBreakdown = OutpatientReportRepository::getPolyBreakdown($this->startDate, $this->endDate, $this->payType, $this->gender);
        $payTypeBreakdown = OutpatientReportRepository::getPayTypeBreakdown($this->startDate, $this->endDate, $this->poly, $this->gender);
        $ageGroupBreakdown = OutpatientReportRepository::getAgeGroupBreakdown($this->startDate, $this->endDate, $this->poly, $this->payType);

        $pdf = Pdf::loadView('reports.outpatient-report-pdf', [
            'polyBreakdown' => $polyBreakdown,
            'payTypeBreakdown' => $payTypeBreakdown,
            'ageGroupBreakdown' => $ageGroupBreakdown,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'summary' => $this->summary(),
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

        $polyBreakdown = OutpatientReportRepository::getPolyBreakdown($this->startDate, $this->endDate, $this->payType, $this->gender);
        $payTypeBreakdown = OutpatientReportRepository::getPayTypeBreakdown($this->startDate, $this->endDate, $this->poly, $this->gender);
        $ageGroupBreakdown = OutpatientReportRepository::getAgeGroupBreakdown($this->startDate, $this->endDate, $this->poly, $this->payType);
        $summary = $this->summary();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(config('app.name', 'Dashboard SIMRS'))
            ->setTitle('Laporan Rekapitulasi Pasien Rawat Jalan');

        // Sheet 1: Rekap per Poliklinik / Unit
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Rekap Poliklinik');
        $sheet1->setShowGridLines(true);

        $sheet1->setCellValue('A1', config('app.hospital_name', 'RUMAH SAKIT'));
        $sheet1->mergeCells('A1:L1');
        $sheet1->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet1->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet1->setCellValue('A2', 'LAPORAN REKAPITULASI PASIEN RAWAT JALAN PER POLIKLINIK');
        $sheet1->mergeCells('A2:L2');
        $sheet1->getStyle('A2')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('475569');
        $sheet1->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet1->setCellValue('A4', 'Periode: ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d/m/Y'));
        $sheet1->mergeCells('A4:F4');
        $sheet1->getStyle('A4')->getFont()->setBold(true)->setSize(9.5);

        $sheet1->setCellValue('G4', 'Dicetak: ' . now()->format('d/m/Y H:i:s'));
        $sheet1->mergeCells('G4:L4');
        $sheet1->getStyle('G4')->getFont()->setSize(9.5);
        $sheet1->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet1->setCellValue('A5', 'Total Pasien: ' . number_format($summary['total_pasien'], 0, ',', '.') . ' (L: ' . number_format($summary['total_pria'], 0, ',', '.') . ', P: ' . number_format($summary['total_wanita'], 0, ',', '.') . ')');
        $sheet1->mergeCells('A5:L5');
        $sheet1->getStyle('A5')->getFont()->setSize(9);

        $headersPoli = ['No', 'Kode', 'Nama Poliklinik', 'Total Pasien', 'Proporsi (%)', 'Laki-laki', 'Perempuan', 'Baru', 'Lama', 'BPJS', 'Umum', 'Dinas'];
        $sheet1->fromArray($headersPoli, null, 'A7');
        $sheet1->getStyle('A7:L7')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet1->getStyle('A7:L7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00923F');

        $rowIdx = 8;
        foreach ($polyBreakdown as $idx => $p) {
            $sheet1->fromArray([
                $idx + 1,
                $p['kd_poli'],
                $p['nm_poli'],
                $p['total'],
                $p['percent'] . '%',
                $p['pria'],
                $p['wanita'],
                $p['baru'],
                $p['lama'],
                $p['bpjs'],
                $p['umum'],
                $p['dinas']
            ], null, "A{$rowIdx}");
            $rowIdx++;
        }

        // Sheet 2: Rekap Jenis Bayar / Penjamin
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rekap Jenis Bayar');
        $sheet2->setShowGridLines(true);

        $sheet2->setCellValue('A1', 'REKAPITULASI PASIEN RAWAT JALAN PER JENIS BAYAR');
        $sheet2->mergeCells('A1:I1');
        $sheet2->getStyle('A1')->getFont()->setSize(12)->setBold(true);

        $headersBayar = ['No', 'Kode PJ', 'Nama Penjamin', 'Total Pasien', 'Proporsi (%)', 'Laki-laki', 'Perempuan', 'Pasien Baru', 'Pasien Lama'];
        $sheet2->fromArray($headersBayar, null, 'A3');
        $sheet2->getStyle('A3:I3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet2->getStyle('A3:I3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0284C7');

        $payRow = 4;
        foreach ($payTypeBreakdown as $idx => $pay) {
            $sheet2->fromArray([
                $idx + 1,
                $pay['kd_pj'],
                $pay['png_jawab'],
                $pay['total'],
                $pay['percent'] . '%',
                $pay['pria'],
                $pay['wanita'],
                $pay['baru'],
                $pay['lama']
            ], null, "A{$payRow}");
            $payRow++;
        }

        // Sheet 3: Demografi Kelompok Umur
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Demografi Umur');
        $sheet3->setShowGridLines(true);

        $sheet3->setCellValue('A1', 'REKAPITULASI DEMOGRAFI & KELOMPOK UMUR (SIRS)');
        $sheet3->mergeCells('A1:G1');
        $sheet3->getStyle('A1')->getFont()->setSize(12)->setBold(true);

        $headersUmur = ['No', 'Kode', 'Kelompok Umur', 'Total Pasien', 'Distribusi (%)', 'Laki-laki', 'Perempuan'];
        $sheet3->fromArray($headersUmur, null, 'A3');
        $sheet3->getStyle('A3:G3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet3->getStyle('A3:G3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6366F1');

        $ageRow = 4;
        foreach ($ageGroupBreakdown as $idx => $age) {
            $sheet3->fromArray([
                $idx + 1,
                $age['kode'],
                $age['nama'],
                $age['total'],
                $age['percent'] . '%',
                $age['pria'],
                $age['wanita']
            ], null, "A{$ageRow}");
            $ageRow++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = $this->getExportFilename('xlsx');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        return view('pages.outpatient.report', [
            'profil' => \App\Helpers\SirsHelper::getProfilRS(),
        ])->title('Laporan Pasien Rawat Jalan');
    }
}
