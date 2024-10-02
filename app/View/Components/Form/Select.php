<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $baseClass;
    /**
     * Create a new component instance.
     * @param $items should have title and value
     */
    public function __construct(
        public array $items,
        public ?string $label = null,
        public ?string $labelClass = null,
        public ?string $errorClass = null,
        public string $size = 'md',
        public string $color = 'primary',
        public bool $required = false,
        public ?string $info = null,
        public string $base = '',
        public bool $block = false,
        public ?string $error = null,
        public bool $loading = false,
    ) {
        $this->baseClass = join(' ', [
            'relative appearance-none border outline-none dark:border-gray-700 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none dark:bg-slate-800 dark:text-white',
            $block ? 'block w-full' : 'min-w-16',
            $error ? 'invalid:border-red-500 invalid:text-red-600 focus:invalid:border-red-500 focus:invalid:ring-red-500 border-red-500' : '',
            $this->colorVariant($color),
            $this->sizeVariant($size),
            $base
        ]);

        $this->labelClass = join(' ', [
            'block mb-2 text-base font-semibold text-slate-700 dark:text-slate-100',
            $required ? 'after:content-[\'*\'] after:ml-0.5 after:text-red-500' : '',
            $labelClass
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
        return view('components.form.select');
    }

    public function sizeVariant($size)
    {
        $sizeVariants = [
            'md' => 'rounded-lg text-base py-2 pl-5 pr-9'
        ];
        return $sizeVariants[$size];
    }

    public function colorVariant($color)
    {
        $colorVariants = [
            'primary' => 'bg-white focus:border-ocean-blue-700 dark:focus:border-ocean-blue-500',
            'secondary' => 'bg-white focus:border-primary-700 dark:focus:border-primary-1000',
            'red' => 'bg-white focus:border-red-700 dark:focus:border-red-500',
        ];

        return $colorVariants[$color];
    }
}
