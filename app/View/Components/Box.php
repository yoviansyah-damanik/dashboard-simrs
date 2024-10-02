<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Box extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $value,
        public string $icon = 'i-ph-bounding-box',
        public ?float $percentage = null,
        public bool $isUp = true,
        public bool $isActive = false
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.box');
    }
}
