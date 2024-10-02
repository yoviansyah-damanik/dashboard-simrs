<?php

namespace App\Livewire\Outpatient;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Models\Doctor;
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

        $this->startDate = Carbon::now()->addDays(-90)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->statusGroup = FilterHelper::getStatus();

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

        $this->limits = [25, 50, 100, 200];
        $this->limit = 25;
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
}
