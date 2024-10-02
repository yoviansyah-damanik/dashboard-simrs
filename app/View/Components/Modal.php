<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $modalTitle = null,
        public ?string $backdropClass = null,
        public ?string $modalClass = null,
        public ?string $name = null,
        public string $size = 'md',
    ) {
        $this->backdropClass = join(' ', [
            'fixed inset-0 z-9999 flex justify-center items-start'
        ]);
        $this->modalClass = join(' ', [
            'bg-white dark:bg-slate-800 min-h-40 min-w-[250px] w-full rounded-xl mt-9 mx-3 sm:m-9 shadow-sm overflow-hidden',
            $this->sizeVariant($size)
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }

    public function sizeVariant($size)
    {
        $sizeVariants = [
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'lg' => 'max-w-lg',
            'xl' => 'max-w-xl',
            '2xl' => 'max-w-2xl',
            '3xl' => 'max-w-3xl',
            '4xl' => 'max-w-4xl',
            'screen' => 'max-w-screen-2xl',
        ];
        return $sizeVariants[$size];
    }
}
