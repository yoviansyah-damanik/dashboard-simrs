<?php

namespace App\Livewire\OperationSchedule;

use App\Helpers\FilterHelper;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Repository\OperationScheduleRepository;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public array $statuses;
    public array $rooms;
    public array $doctors;
    public array $limits;

    public string $startDate;
    public string $endDate;
    public string $status;
    public string $room;
    public string $doctor;
    public string $limit;

    public function mount()
    {
        $this->startDate = Carbon::now()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->statuses = FilterHelper::getOperationStatuses();
        $this->status = $this->statuses[0]['value'];

        $this->rooms = FilterHelper::getOperatingRooms();
        $this->room = $this->rooms[0]['value'];

        $this->doctors = FilterHelper::getDoctors();
        $this->doctor = $this->doctors[0]['value'];

        $this->limits = FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $schedules = OperationScheduleRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            search: $this->search,
            status: $this->status,
            room: $this->room,
            doctor: $this->doctor,
            limit: $this->limit
        );

        return view('pages.operation-schedule.index', compact('schedules'));
    }

    public function updated($attribute)
    {
        $this->resetPage();
    }
}
