<?php

namespace App\Livewire\RegisteredPatient;

use App\Helpers\FilterHelper;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Repository\RegisteredPatientRepository;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public array $ageCategories;
    public array $genders;
    public array $serviceStatuses;
    public array $statuses;
    public array $advanceStatusGroup;
    public array $polyclinics;
    public array $types;
    public array $payTypes;
    public array $limits;
    public array $doctors;
    public array $tniGroups;
    public array $tniUnits;
    public array $polriGroups;
    public array $polriUnits;
    public array $mobileJknStatuses;

    public string $ageCategory;
    public string $gender;
    public string $startDate;
    public string $endDate;
    public string $serviceStatus;
    public string $status;
    public string $advanceStatus;
    public string $polyclinic;
    public string $type;
    public string $payType;
    public string $education;
    public string $limit;
    public string $doctor;
    public string $tniGroup;
    public string $tniUnit;
    public string $polriGroup;
    public string $polriUnit;
    public string $mobileJknStatus;

    public function mount()
    {
        $this->ageCategories = FilterHelper::getAgeCategories();
        $this->ageCategory = $this->ageCategories[0]['value'];

        $this->genders = FilterHelper::getGenders();
        $this->gender = $this->genders[0]['value'];

        $this->startDate = Carbon::now()->addDays(-30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->serviceStatuses = FilterHelper::getServiceStatuses();
        $this->serviceStatus = $this->serviceStatuses[0]['value'];

        $this->advanceStatusGroup = FilterHelper::getAdvanceStatus();
        $this->advanceStatus = $this->advanceStatusGroup[0]['value'];

        $this->payTypes = FilterHelper::getPayTypes();
        $this->payType = $this->payTypes[0]['value'];

        $this->types = FilterHelper::getTypes();
        $this->type = $this->types[0]['value'];

        $this->polyclinics = FilterHelper::getPolyclinics();
        $this->polyclinic = $this->polyclinics[0]['value'];

        $this->doctors = FilterHelper::getDoctors();
        $this->doctor = $this->doctors[0]['value'];

        $this->tniGroups = FilterHelper::getTniGroups();
        $this->tniGroup = $this->tniGroups[0]['value'];

        $this->tniUnits = FilterHelper::getTniUnits();
        $this->tniUnit = $this->tniUnits[0]['value'];

        $this->polriGroups = FilterHelper::getPolriGroups();
        $this->polriGroup = $this->polriGroups[0]['value'];

        $this->polriUnits = FilterHelper::getPolriUnits();
        $this->polriUnit = $this->polriUnits[0]['value'];

        $this->mobileJknStatuses = FilterHelper::getMobileJknStatuses();
        $this->mobileJknStatus = $this->mobileJknStatuses[0]['value'];

        $this->statuses = FilterHelper::getStatuses();
        $this->status = $this->statuses[0]['value'];

        $this->limits =  FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $patients = RegisteredPatientRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            serviceStatus: $this->serviceStatus,
            status: $this->status,
            advanceStatus: $this->advanceStatus,
            type: $this->type,
            payType: $this->payType,
            polyclinic: $this->polyclinic,
            limit: $this->limit,
            doctor: $this->doctor,
            tniGroup: $this->tniGroup,
            tniUnit: $this->tniUnit,
            mobileJkn: $this->mobileJknStatus
        );

        return view('pages.registered-patient.index', compact('patients'));
    }

    public function updated($attribute)
    {
        $this->resetPage();
    }
}
