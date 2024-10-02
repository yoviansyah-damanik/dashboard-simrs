<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tooltip extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public ?string $base = null,
        public ?string $tooltipClass = null,
        public string $position = 'bottom',
    ) {
        $this->base = join(' ', [
            'relative inline group/tooltip',
            $base
        ]);

        $this->tooltipClass = join(' ', [
            'absolute bg-slate-700 shadow-sm dark:shadow-gray whitespace-nowrap text-white py-1 px-3 rounded z-10 text-xs text-center',
            'hidden group-hover/tooltip:block',
            $position ==
                'bottom' ? 'top-full mt-1.5'
                : (
                    $position == 'top' ? 'bottom-full mb-1.5'
                    : ($position == 'left' ? 'right-full mr-1.5'
                        : 'left-full ml-1.5')),
            in_array($position, ['top', 'bottom']) ? 'left-1/2 -translate-x-1/2' : 'top-1/2 -translate-y-1/2',
            $tooltipClass
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tooltip');
    }
}
