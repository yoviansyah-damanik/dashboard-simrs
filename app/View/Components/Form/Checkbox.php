<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Checkbox extends Component
{
    public string $baseClass;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label,
        public array $target = [],
        public bool $required = false,
        public bool $loading = false,
        public string $error = '',
        public string $position = 'left',
        public string $base = '',
        public string $labelClass = '',
        public string $errorClass = '',
        public string $wrapClass = '',
        public bool $inline = false,
    ) {
        $this->baseClass = join(
            ' ',
            [
                'size-4',
                'aspect-square accent-ocean-blue-500 focus:accent-ocean-blue-900 transition duration-150 disabled:bg-slate-50 disabled:text-slate-100 disabled:border-slate-200 disabled:shadow-none',
                $base
            ]
        );

        $this->labelClass = join(' ', [
            'font-normal text-gray-700 dark:text-gray-100',
            $required ? ' after:content-[\'*\'] after:ml-0.5 after:text-red-500' : '',
            $error ? ' text-red-600' : '',
            $labelClass
        ]);

        $this->wrapClass = join(' ', [
            'items-center gap-2',
            $inline ? 'inline-flex mr-3 last:mr-0' : 'flex',
        ]);

        $this->errorClass = join(' ', [
            'mt-1 text-sm text-red-500 ms-4',
            $errorClass
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.checkbox');
    }
}
