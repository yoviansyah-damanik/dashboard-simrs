<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $menus = [
            [
                'title' => 'Beranda',
                'href' => route('home'),
                'icon' => 'i-ph-fire-light',
            ],
            [
                'title' => 'Akun',
                'href' => route('account'),
                'icon' => 'i-ph-user',
            ],
            [
                'title' => 'Riwayat Login',
                'href' => route('account'),
                'icon' => 'i-ph-user',
            ]
        ];

        return view('components.header', compact('menus'));
    }
}
