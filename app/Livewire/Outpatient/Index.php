<?php

namespace App\Livewire\Outpatient;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Repository\OutpatientsRepository;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public array $ageCategories;
    public array $genders;
    public array $statusGroup;
    public array $polyclinics;
    public array $types;
    public array $payTypes;
    public array $limits;
    public array $doctors;
    public array $tniGroups;

    #[Url]
    public string $ageCategory;

    #[Url]
    public string $gender;

    #[Url]
    public string $startDate;

    #[Url]
    public string $endDate;

    #[Url]
    public string $status;

    #[Url]
    public string $polyclinic;

    #[Url]
    public string $type;

    #[Url]
    public string $payType;

    #[Url]
    public string $education;

    #[Url]
    public string $limit;

    #[Url]
    public string $doctor;

    #[Url]
    public string $tniGroup;

    public function mount()
    {
        $this->ageCategories = FilterHelper::getAgeCategories();

        $this->ageCategory = $this->ageCategories[0]['value'];

        $this->genders = FilterHelper::getGenders();

        $this->gender = $this->genders[0]['value'];

        if (!isset($this->startDate))
            $this->startDate = Carbon::now()->format('Y-m-d');

        if (!isset($this->endDate))
            $this->endDate = Carbon::now()->format('Y-m-d');

        $this->statusGroup = FilterHelper::getStatuses();

        $this->status = $this->statusGroup[0]['value'];

        $this->payTypes = FilterHelper::getPayTypes();

        $this->payType = $this->payTypes[0]['value'];

        $this->types = FilterHelper::getPayTypes();

        $this->type = $this->types[0]['value'];

        $this->polyclinics = FilterHelper::getPolyclinics();

        $this->polyclinic = $this->polyclinics[0]['value'];

        $this->doctors = FilterHelper::getDoctors();
        $this->doctor = $this->doctors[0]['value'];

        $this->tniGroups = FilterHelper::getTniGroups();
        $this->tniGroup = $this->tniGroups[0]['value'];

        $this->limits =  FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $patients = OutpatientsRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            status: $this->status,
            type: $this->type,
            payType: $this->payType,
            polyclinic: $this->polyclinic,
            limit: $this->limit,
            doctor: $this->doctor,
            tniGroup: $this->tniGroup
        );
        return view('pages.outpatient.index', compact('patients'));
    }

    public function exportCsv()
    {
        set_time_limit(0);
        $patients = OutpatientsRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            status: $this->status,
            type: $this->type,
            payType: $this->payType,
            polyclinic: $this->polyclinic,
            limit: 0,
            doctor: $this->doctor,
            tniGroup: $this->tniGroup
        );

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data-rawat-jalan-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($patients) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Waktu Daftar', 'No Rawat', 'No RM', 'Nama Pasien', 'Jenis Pasien', 'Poliklinik', 'Dokter', 'Jenis Bayar', 'Status']);

            foreach ($patients as $index => $patient) {
                fputcsv($file, [
                    $index + 1,
                    $patient['pendaftaran']['waktu_pendaftaran'],
                    $patient['pendaftaran']['no_rawat'],
                    $patient['pendaftaran']['no_rekam_medis'],
                    $patient['pasien']['data']['nama'],
                    $patient['pasien']['data']['jenis_pasien'],
                    $patient['poliklinik']['nama_poliklinik'],
                    $patient['dokter']['nama_dokter'],
                    $patient['pendaftaran']['jenis_bayar'],
                    $patient['pendaftaran']['status_pelayanan'],
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['Data diperoleh melalui ' . config('app.name') . ' milik ' . config('app.hospital_name') . ' pada ' . now()->format('d/m/Y H:i:s')]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        set_time_limit(0);
        $patients = OutpatientsRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            status: $this->status,
            type: $this->type,
            payType: $this->payType,
            polyclinic: $this->polyclinic,
            limit: 0,
            doctor: $this->doctor,
            tniGroup: $this->tniGroup
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.outpatient-pdf', [
            'patients' => $patients,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'data-rawat-jalan-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
