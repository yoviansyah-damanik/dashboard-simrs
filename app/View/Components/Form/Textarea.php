<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public string $baseClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $label = null,
        public ?string $placeholder = null,
        public array $target = [],
        public bool $block = false,
        public bool $required = false,
        public bool $loading = false,
        public ?string $error = null,
        public ?string $base = null,
        public ?string $labelClass = null,
        public ?string $errorClass = null,
        public string $size = 'md',
        public string $color = 'primary',
        public ?string $info = null,
        public ?int $rows = null,
        public int $limit = 0,
        public bool $isEditor = false
    ) {
        $this->baseClass = join(' ', [
            'relative border border-stroke outline-none disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none',
            $block ? 'block w-full' : 'min-w-16',
            $error ? 'invalid:border-red-500 invalid:text-red-600 focus:invalid:border-red-500 focus:invalid:ring-red-500 border-red-500' : '',
            $this->colorVariant($color),
            $this->sizeVariant($size),
            $base
        ]);

        $this->labelClass = join(' ', [
            'block mb-3 text-base font-medium text-slate-700 dark:text-slate-100',
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
        return view('components.form.textarea');
    }

    public function sizeVariant($size)
    {
        $sizeVariants = [
            'md' => 'rounded-xl text-base py-2 px-5'
        ];
        return $sizeVariants[$size];
    }

    public function colorVariant($color)
    {
        $colorVariants = [
            'primary' => 'bg-white focus:border-primary dark:focus:border-primary',
            'secondary' => 'bg-white focus:border-secondary dark:focus:border-secondary',
            'red' => 'bg-white focus:border-red-700 dark:focus:border-red-500',
        ];

        return $colorVariants[$color];
    }
}
