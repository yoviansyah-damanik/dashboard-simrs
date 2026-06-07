<?php

namespace App\Livewire\Radiology;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Repository\RadiologyRepository;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $startDate;

    #[Url]
    public string $endDate;

    #[Url]
    public string $status;

    #[Url]
    public string $gender;

    #[Url]
    public string $limit;

    public array $genders;
    public array $statuses;
    public array $limits;

    public function mount(): void
    {
        if (!isset($this->startDate))
            $this->startDate = Carbon::now()->format('Y-m-d');

        if (!isset($this->endDate))
            $this->endDate = Carbon::now()->format('Y-m-d');

        $this->genders = FilterHelper::getGenders();
        $this->gender = $this->genders[0]['value'];

        $this->statuses = RadiologyRepository::getRadiologyStatuses();
        $this->status = $this->statuses[0]['value'];

        $this->limits = FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $records = RadiologyRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            limit: (int) $this->limit,
            search: $this->search,
            status: $this->status,
            gender: $this->gender
        );

        return view('pages.radiology.index', compact('records'));
    }
}
