<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RoomByClassItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public int $total,
        public int $available,
        public int $filled,
        public bool $isActive = false
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.room-by-class-item');
    }
}
