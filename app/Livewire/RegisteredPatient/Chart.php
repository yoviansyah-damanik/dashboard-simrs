<?php

namespace App\Livewire\RegisteredPatient;

use App\Helpers\RandomColorHelper;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Chart extends Component
{
    #[Reactive]
    public array $data;

    public string $chartId;
    public string $label;
    public string $chartType;

    public array $datasets;
    public array $labels;

    public function mount(string $chartId, string $label, string $chartType = 'bar')
    {
        $this->chartId = $chartId;
        $this->label = $label;
        $this->chartType = $chartType;
    }

    public function setData($data)
    {
        $colors = RandomColorHelper::many(count($data), [
            'format' => 'rgbCss'
        ]);
        $this->datasets =
            [
                [
                    'label' => $this->label,
                    'backgroundColor' => collect($colors)->map(fn ($border) => substr_replace($border, ',0.4', -1, 0))
                        ->toArray(),
                    'borderColor' => $colors,
                    'borderWidth' => 1,
                    'data' => collect($data)->pluck('value')->toArray(),
                ]
            ];
        $this->labels = collect($data)->pluck('title')->toArray();
    }

    public function render()
    {
        $this->setData($this->data);

        $this->dispatch('refreshChartData-' . $this->chartId, [
            'labels' => $this->labels,
            'datasets' => $this->datasets,
        ]);

        return view('pages.registered-patient.chart');
    }
}
