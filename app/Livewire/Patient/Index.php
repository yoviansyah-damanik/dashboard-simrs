<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Repository\PatientRepository;

class Index extends Component
{
    use WithPagination;

    public array $ageCategories;
    public array $genders;
    public array $types;
    public array $payTypes;
    public array $tniGroups;
    public array $tniUnits;
    public array $limits;

    #[Url]
    public string $search = '';

    #[Url]
    public string $ageCategory;

    #[Url]
    public string $gender;

    #[Url]
    public string $type;

    #[Url]
    public string $payType;

    #[Url]
    public string $tniGroup;

    #[Url]
    public string $tniUnit;

    #[Url]
    public string $limit;

    public function mount()
    {
        $this->ageCategories = FilterHelper::getAgeCategories();
        $this->ageCategory = $this->ageCategories[0]['value'];

        $this->genders = FilterHelper::getGenders();
        $this->gender = $this->genders[0]['value'];

        $this->types = FilterHelper::getTypes();
        $this->type = $this->types[0]['value'];

        $this->payTypes = FilterHelper::getPayTypes();
        $this->payType = $this->payTypes[0]['value'];

        $this->tniGroups = FilterHelper::getTniGroups();
        $this->tniGroup = $this->tniGroups[0]['value'];

        $this->tniUnits = FilterHelper::getTniUnits();
        $this->tniUnit = $this->tniUnits[0]['value'];

        $this->limits =  FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $patients = PatientRepository::getAll(
            limit: $this->limit,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            type: $this->type,
            payType: $this->payType,
            tniGroup: $this->tniGroup,
            tniUnit: $this->tniUnit
        );

        return view('pages.patient.index', compact('patients'));
    }

    public function exportCsv()
    {
        set_time_limit(0);
        $patients = PatientRepository::getAll(
            limit: 0,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            type: $this->type,
            payType: $this->payType,
            tniGroup: $this->tniGroup,
            tniUnit: $this->tniUnit
        );

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data-pasien-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($patients) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'No Rekam Medis', 'NIK', 'Nama Pasien', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Umur', 'Jenis Bayar', 'Jenis Pasien']);

            foreach ($patients as $index => $patient) {
                fputcsv($file, [
                    $index + 1,
                    $patient['data']['no_rekam_medis'],
                    $patient['data']['nik'],
                    $patient['data']['nama'],
                    $patient['data']['jenis_kelamin'],
                    $patient['data']['tempat_lahir'],
                    $patient['data']['tanggal_lahir'],
                    $patient['data']['umur'],
                    $patient['data']['jenis_bayar'],
                    $patient['data']['jenis_pasien'],
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
        $patients = PatientRepository::getAll(
            limit: 0,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            type: $this->type,
            payType: $this->payType,
            tniGroup: $this->tniGroup,
            tniUnit: $this->tniUnit
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.patient-pdf', [
            'patients' => $patients
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'data-pasien-' . now()->format('Y-m-d-His') . '.pdf');
    }

    public function updated($attribute)
    {
        $this->resetPage();
    }
}

