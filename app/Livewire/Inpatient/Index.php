<?php

namespace App\Livewire\Inpatient;

use App\Helpers\FilterHelper;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Repository\InpatientsRepository;

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
    public array $rooms;
    public array $wards;
    public array $doctors;
    public array $tniGroups;
    public array $inpatientStatuses;

    public string $ageCategory;
    public string $gender;
    public string $startDate;
    public string $endDate;
    public string $status;
    public string $polyclinic;
    public string $type;
    public string $room;
    public string $ward;
    public string $payType;
    public string $education;
    public string $limit;
    public string $doctor;
    public string $tniGroup;
    public string $inpatientStatus;

    public function mount()
    {
        $this->ageCategories = FilterHelper::getAgeCategories();
        $this->ageCategory = $this->ageCategories[0]['value'];

        $this->genders = FilterHelper::getGenders();
        $this->gender = $this->genders[0]['value'];

        $this->startDate = Carbon::now()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->statusGroup = FilterHelper::getStatuses();
        $this->status = $this->statusGroup[0]['value'];

        $this->payTypes = FilterHelper::getPayTypes();
        $this->payType = $this->payTypes[0]['value'];

        $this->types = FilterHelper::getTypes();
        $this->type = $this->types[0]['value'];

        $this->doctors = FilterHelper::getDoctors();
        $this->doctor = $this->doctors[0]['value'];

        $this->rooms = FilterHelper::getRooms();
        $this->room = $this->rooms[0]['value'];

        $this->wards = FilterHelper::getWards();
        $this->ward = $this->wards[0]['value'];

        $this->tniGroups = FilterHelper::getTniGroups();
        $this->tniGroup = $this->tniGroups[0]['value'];

        $this->limits =  FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];

        $this->inpatientStatuses =  FilterHelper::getInpatientStatuses();
        $this->inpatientStatus = 'masih_perawatan';
    }

    public function render()
    {
        $patients = InpatientsRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            type: $this->type,
            payType: $this->payType,
            limit: $this->limit,
            ward: $this->ward,
            room: $this->room,
            doctor: $this->doctor,
            tniGroup: $this->tniGroup,
            inpatientStatus: $this->inpatientStatus
        );

        return view('pages.inpatient.index', compact('patients'));
    }

    public function updated($attribute)
    {
        $this->resetPage();
    }
}
