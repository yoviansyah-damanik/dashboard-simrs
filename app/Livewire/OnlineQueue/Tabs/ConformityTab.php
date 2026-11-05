<?php

namespace App\Livewire\OnlineQueue\Tabs;

use App\Repository\OnlineQueueComplianceRepository;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ConformityTab extends Component
{
    public int $year;
    public ?int $month = null;
    public ?string $kdPoli = null;
    public string $filterStatus = '';
    public int $page = 1;
    public int $perPage = 20;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/40 p-5 space-y-4 animate-pulse">
            <div class="flex justify-between items-center">
                <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/3"></div>
                <div class="h-8 bg-slate-200 dark:bg-slate-800 rounded-xl w-1/4"></div>
            </div>
            <div class="grid grid-cols-4 gap-3">
                <div class="h-16 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
                <div class="h-16 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
                <div class="h-16 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
                <div class="h-16 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
            </div>
            <div class="h-96 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
        </div>
        HTML;
    }

    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
        $this->page = 1;
    }

    public function nextPage(): void
    {
        $this->page++;
    }

    public function prevPage(): void
    {
        $this->page = max(1, $this->page - 1);
    }

    public function render()
    {
        $summary = OnlineQueueComplianceRepository::getSummary(
            year: $this->year,
            month: $this->month,
            kdPoli: $this->kdPoli
        );

        $offset = ($this->page - 1) * $this->perPage;
        $conformityData = OnlineQueueComplianceRepository::getJknConformityDetails(
            year: $this->year,
            month: $this->month,
            kdPoli: $this->kdPoli,
            filterStatus: $this->filterStatus !== '' ? $this->filterStatus : null,
            limit: $this->perPage,
            offset: $offset
        );

        $totalConformity = $conformityData['total'];
        $lastPage = max(1, (int) ceil($totalConformity / $this->perPage));

        return view('livewire.online-queue.tabs.conformity-tab', [
            'summary' => $summary,
            'conformityRows' => $conformityData['rows'],
            'totalConformity' => $totalConformity,
            'lastPage' => $lastPage,
        ]);
    }
}

