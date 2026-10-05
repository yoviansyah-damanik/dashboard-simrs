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
        return FilterHelper::getPayTypes();
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
            search: $this->search
        );
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
            limit: 0
        );

        $filename = 'laporan-pasien-rawat-inap-' . now()->format('Y-m-d-His') . '.csv';

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
                'Penjamin',
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
            limit: 0
        );

        $selectedPayTypeTitle = 'Semua Penjamin';
        if ($this->payType !== 'semua') {
            $matched = collect($this->payTypes())->firstWhere('value', $this->payType);
            if ($matched) {
                $selectedPayTypeTitle = $matched['title'];
            }
        }

        $pdf = Pdf::loadView('reports.inpatient-report-pdf', [
            'patients' => $patients,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'payTypeTitle' => $selectedPayTypeTitle,
            'summary' => $this->summary(),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-pasien-rawat-inap-' . now()->format('Y-m-d-His') . '.pdf');
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
            limit: (int) $this->limit
        );

        return view('pages.inpatient.report', [
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
