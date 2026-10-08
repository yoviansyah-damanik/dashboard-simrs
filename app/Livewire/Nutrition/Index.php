<?php

namespace App\Livewire\Nutrition;

use App\Helpers\FilterHelper;
use App\Repository\NutritionRepository;
use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    #[Url]
    public string $period = 'today';

    #[Url]
    public string $waktu = 'semua'; // 'semua', 'Pagi', 'Siang', 'Sore'

    #[Url]
    public string $kdDiet = 'semua';

    #[Url]
    public string $kdBangsal = 'semua';

    #[Url]
    public string $activeTab = 'permintaan'; // 'permintaan' | 'asuhan'

    #[Url]
    public string $gender = 'semua';

    public int $perPage = 25;

    public array $genders = [];
    public array $limits = [];

    public function mount(): void
    {
        $this->syncDates();

        $this->genders = FilterHelper::getGenders();
        $this->limits = FilterHelper::getPerPageList();
    }

    public function updatedPeriod(): void
    {
        $this->syncDates();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedWaktu(): void
    {
        $this->resetPage();
    }

    public function updatedKdDiet(): void
    {
        $this->resetPage();
    }

    public function updatedKdBangsal(): void
    {
        $this->resetPage();
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->period = 'custom';
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->period = 'custom';
        $this->resetPage();
    }

    public function syncDates(): void
    {
        switch ($this->period) {
            case 'today':
                $this->startDate = date('Y-m-d');
                $this->endDate = date('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = date('Y-m-d', strtotime('-1 day'));
                $this->endDate = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'last_7_days':
                $this->startDate = date('Y-m-d', strtotime('-7 days'));
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
            case 'all':
                $this->startDate = null;
                $this->endDate = null;
                break;
            case 'custom':
                if (!$this->startDate) $this->startDate = date('Y-m-d');
                if (!$this->endDate) $this->endDate = date('Y-m-d');
                break;
        }
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->waktu = 'semua';
        $this->kdDiet = 'semua';
        $this->kdBangsal = 'semua';
        $this->period = 'today';
        $this->syncDates();
    }

    public function render()
    {
        $dietList = NutritionRepository::getMasterDiet();
        $bangsalList = NutritionRepository::getMasterBangsal();

        if ($this->activeTab === 'asuhan') {
            $records = NutritionRepository::getAll(
                startDate: $this->startDate ?? date('Y-01-01'),
                endDate: $this->endDate ?? date('Y-m-d'),
                limit: $this->perPage,
                search: $this->search,
                gender: $this->gender
            );

            return view('pages.nutrition.index', [
                'records' => $records,
                'dietOrders' => null,
                'summary' => null,
                'dietList' => $dietList,
                'bangsalList' => $bangsalList,
            ])->title('Pelayanan Gizi & Asuhan Nutrisi');
        }

        $filters = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->search,
            'waktu' => $this->waktu,
            'kd_diet' => $this->kdDiet,
            'kd_bangsal' => $this->kdBangsal,
        ];

        $dietOrders = NutritionRepository::getDietOrders($filters, $this->perPage);
        $summary = NutritionRepository::getDietSummary($this->startDate, $this->endDate);

        return view('pages.nutrition.index', [
            'dietOrders' => $dietOrders,
            'records' => null,
            'summary' => $summary,
            'dietList' => $dietList,
            'bangsalList' => $bangsalList,
        ])->title('Permintaan Diet Pasien');
    }
}
