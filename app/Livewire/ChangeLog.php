<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\GeneralHelper;

class ChangeLog extends Component
{
    public $search = '';
    public $selectedType = 'all'; // 'all', 'major', 'minor'

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedType = 'all';
    }

    public function render()
    {
        $allVersions = GeneralHelper::getAllVersions();

        $filteredVersions = collect($allVersions)->filter(function ($item) {
            // Filter tipe rilis
            if ($this->selectedType !== 'all') {
                $type = $item['type'] ?? 'minor';
                if ($type !== $this->selectedType) {
                    return false;
                }
            }

            // Filter pencarian
            if (!empty($this->search)) {
                $search = strtolower(trim($this->search));
                $versionText = strtolower($item['version'] ?? '');
                $titleText = strtolower($item['title'] ?? '');
                $badgeText = strtolower($item['badge'] ?? '');
                $logs = collect($item['changeLog'] ?? [])->map(fn($l) => strtolower($l))->implode(' ');

                $haystack = "{$versionText} {$titleText} {$badgeText} {$logs}";
                if (!str_contains($haystack, $search)) {
                    return false;
                }
            }

            return true;
        })->values()->all();

        return view('pages.changelog', [
            'versions' => $filteredVersions,
            'totalVersions' => count($allVersions),
            'latestVersion' => $allVersions[0]['version'] ?? '2.0.0',
            'latestDate' => $allVersions[0]['date'] ?? '2026-10-08',
        ]);
    }
}
