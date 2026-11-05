<?php

namespace App\Livewire\OnlineQueue\Tabs;

use App\Repository\OnlineQueueComplianceRepository;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OverviewTab extends Component
{
    public int $year;
    public ?int $month = null;
    public ?string $kdPoli = null;
    public float $threshold = 60.0;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="space-y-6 animate-pulse">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/40 p-5 h-96 flex flex-col justify-between">
                    <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/3"></div>
                    <div class="h-64 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/40 p-5 h-96 flex flex-col justify-between">
                    <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/2"></div>
                    <div class="size-48 mx-auto rounded-full bg-slate-100 dark:bg-slate-800/60"></div>
                    <div class="space-y-2">
                        <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded"></div>
                        <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-2/3"></div>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/40 p-5 h-96">
                <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/4 mb-4"></div>
                <div class="h-72 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $summary = OnlineQueueComplianceRepository::getSummary(
            year: $this->year,
            month: $this->month,
            kdPoli: $this->kdPoli,
            threshold: $this->threshold
        );

        $dailyTrend = $this->month
            ? OnlineQueueComplianceRepository::getDailyTrend(
                year: $this->year,
                month: $this->month,
                kdPoli: $this->kdPoli,
                threshold: $this->threshold
            )
            : [];

        $monthlyReport = OnlineQueueComplianceRepository::getMonthlyReport(
            year: $this->year,
            kdPoli: $this->kdPoli,
            threshold: $this->threshold
        );

        $polyclinicRows = OnlineQueueComplianceRepository::getPolyclinicDoctorBreakdown(
            year: $this->year,
            month: $this->month,
            kdPoli: $this->kdPoli,
            threshold: $this->threshold
        );

        return view('livewire.online-queue.tabs.overview-tab', [
            'summary' => $summary,
            'dailyTrend' => $dailyTrend,
            'monthlyReport' => $monthlyReport,
            'polyclinicRows' => $polyclinicRows,
        ]);
    }
}

