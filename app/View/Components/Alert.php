<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $baseClass, $iconClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type,
        public string $base = '',
        public string $icon = '',
        public bool $closeButton = true
    ) {
        $color = 'text-sky-blue-800 bg-sky-blue-50 dark:bg-slate-900 dark:text-sky-blue-400';
        $iconType = 'i-solar-bell-bing-bold-duotone';

        if ($type == 'success') {
            $color = 'text-green-800 bg-green-50 dark:bg-slate-900 dark:text-green-400';
            $iconType = 'i-solar-check-circle-line-duotone';
        } elseif ($type == 'error') {
            $color = 'text-red-800 bg-red-50 dark:bg-slate-900 dark:text-red-400';
            $iconType = 'i-solar-close-circle-line-duotone';
        } elseif ($type == 'info') {
            $color = 'text-cyan-800 bg-cyan-50 dark:bg-slate-900 dark:text-cyan-400';
            $iconType = 'i-solar-alarm-add-bold-duotone';
        } elseif ($type == 'warning') {
            $color = 'text-yellow-800 bg-yellow-50 dark:bg-slate-900 dark:text-yellow-300';
            $iconType = 'i-solar-danger-circle-line-duotone';
        }

        $this->baseClass = join(
            ' ',
            [
                'relative mb-4 text-sm rounded-lg p-6 sm:text-base',
                $base,
                $color
            ]
        );

        $this->iconClass = join(' ', [
            'size-6',
            $iconType,
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
