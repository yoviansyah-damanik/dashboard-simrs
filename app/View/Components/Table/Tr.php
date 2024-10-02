<?php

namespace App\View\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tr extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $trClass = null
    ) {
        $this->trClass = join(' ', [
            'even:bg-primary/10 bg-white hover:bg-primary/20',
            $trClass
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table.tr');
    }
}
