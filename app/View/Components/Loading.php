<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Loading extends Component
{
    public string $loadingClass;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $icon = 'i-solar-refresh-bold-duotone'
    ) {
        $this->loadingClass = join(' ', [
            'inline-block size-6 animate-spin',
            $icon
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.loading');
    }
}
