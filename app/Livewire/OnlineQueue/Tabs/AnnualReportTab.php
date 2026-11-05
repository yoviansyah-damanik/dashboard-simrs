<?php

namespace App\Livewire\OnlineQueue\Tabs;

use App\Repository\OnlineQueueComplianceRepository;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AnnualReportTab extends Component
{
    public int $year;
    public ?string $kdPoli = null;
    public float $threshold = 60.0;
    public ?string $selectedPolyDocKey = null;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/40 p-5 space-y-4 animate-pulse">
            <div class="flex justify-between items-center">
                <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/3"></div>
                <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/6"></div>
            </div>
            <div class="h-80 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
            <div class="h-96 bg-slate-100 dark:bg-slate-800/60 rounded-xl"></div>
        </div>
        HTML;
    }

    public function selectPolyDoc(?string $key): void
    {
        $this->selectedPolyDocKey = $key;
    }

    public function render()
    {
        $monthlyReport = OnlineQueueComplianceRepository::getMonthlyReport(
            year: $this->year,
            kdPoli: $this->kdPoli,
            threshold: $this->threshold
        );

        $annualPolyDoc = OnlineQueueComplianceRepository::getAnnualPolyclinicDoctorReport(
            year: $this->year,
            kdPoli: $this->kdPoli,
            threshold: $this->threshold
        );

        // Jika belum ada dokter yang dipilih, otomatis pilih entri pertama yang memiliki data
        if (!$this->selectedPolyDocKey && !empty($annualPolyDoc)) {
            $this->selectedPolyDocKey = $annualPolyDoc[0]['key'];
        }

        $selectedItem = null;
        if ($this->selectedPolyDocKey) {
            $selectedItem = collect($annualPolyDoc)->firstWhere('key', $this->selectedPolyDocKey);
        }

        return view('livewire.online-queue.tabs.annual-report-tab', [
            'year' => $this->year,
            'threshold' => $this->threshold,
            'selectedPolyDocKey' => $this->selectedPolyDocKey,
            'monthlyReport' => $monthlyReport,
            'annualPolyDoc' => $annualPolyDoc,
            'selectedItem' => $selectedItem,
        ]);
    }
}
