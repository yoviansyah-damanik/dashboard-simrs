<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Table extends Component
{
    public ?string $theadClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $columns,
        public bool $headingCentered = false,
        public ?string $tableClass = null,
        public ?string $baseClass = null,
        public ?string $thClass = null,
    ) {
        $this->baseClass = join(' ', [
            'w-full overflow-x-auto relative block',
            $baseClass
        ]);

        $this->tableClass = join(' ', [
            'w-full table-none border-separate pb-12 shadow',
            $tableClass
        ]);
        $this->thClass = join(' ', [
            'font-semibold bg-primary-500 text-white px-3 py-5',
            $headingCentered ? 'text-center' : '',
            $thClass
        ]);
        $this->theadClass = join(' ', []);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table');
    }
}
