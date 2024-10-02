<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct() {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $menus = [
            [
                'title' => 'Data Master',
                'items' => [
                    [
                        'title' => 'Beranda',
                        'href' => route('home'),
                        'icon' => 'i-ph-fire-light',
                        'isActive' => request()->routeIs('home')
                    ],
                    [
                        'title' => 'Pasien',
                        'icon' => 'i-ph-users-four',
                        'items' => [
                            [
                                'title' => 'Data Pasien',
                                'href' => route('patient'),
                                'isActive' => request()->routeIs('patient'),
                                'isShow' => auth()->user()->hasPermissionTo('show patients')
                            ],
                            [
                                'title' => 'Rekap Pasien',
                                'href' => route('patient.recap'),
                                'isActive' => request()->routeIs('patient.recap'),
                                'isShow' => auth()->user()->hasPermissionTo('recap patients')
                            ]
                        ]
                    ],
                    [
                        'title' => 'Kamar',
                        'icon' => 'i-ph-door-open',
                        'href' => route('room'),
                        'isActive' => request()->routeIs('room')
                    ],
                    [
                        'title' => 'Poliklinik',
                        'icon' => 'i-ph-stethoscope',
                        'href' => route('polyclinic'),
                        'isActive' => request()->routeIs('polyclinic')
                    ],
                ]
            ],
            [
                'title' => 'SDM',
                'items' => [
                    [
                        'title' => 'Dokter',
                        'icon' => 'i-medical-icon-i-health-education',
                        'href' => route('account'),
                        'isActive' => request()->routeIs('account')
                    ],
                    [
                        'title' => 'Perawat/Bidan',
                        'icon' => 'i-medical-icon-i-care-staff-area',
                        'href' => route('account'),
                        'isActive' => request()->routeIs('account')
                    ],
                    [
                        'title' => 'Lainnya',
                        'icon' => 'i-medical-icon-i-health-education',
                        'href' => route('account'),
                        'isActive' => request()->routeIs('account')
                    ],
                ]
            ],
            [
                'title' => 'Layanan Medis',
                'items' =>
                [
                    [
                        'title' => 'Pendaftaran',
                        'icon' => 'i-ph-address-book',
                        'items' => [
                            [
                                'title' => 'Data Pasien',
                                'href' => route('registered-patient'),
                                'isActive' => request()->routeIs('registered-patient')
                            ],
                            [
                                'title' => 'Rekap Pendaftaran',
                                'href' => route('registered-patient.recap'),
                                'isActive' => request()->routeIs('registered-patient.recap')
                            ]
                        ]
                    ],
                    [
                        'title' => 'Rawat Inap',
                        'icon' => 'i-medical-icon-i-care-staff-area',
                        'items' => [
                            [
                                'title' => 'Data Pasien',
                                'href' => route('inpatient'),
                                'isActive' => request()->routeIs('inpatient')
                            ],
                            [
                                'title' => 'Rekap Rawat Inap',
                                'href' => route('inpatient.recap'),
                                'isActive' => request()->routeIs('inpatient.recap')
                            ]
                        ]
                    ],
                    [
                        'title' => 'Rawat Jalan',
                        'icon' => 'i-medical-icon-i-family-practice',
                        'items' => [
                            [
                                'title' => 'Data Pasien',
                                'href' => route('outpatient'),
                                'isActive' => request()->routeIs('outpatient')
                            ],
                            [
                                'title' => 'Rekap Rawat Jalan',
                                'href' => route('outpatient.recap'),
                                'isActive' => request()->routeIs('outpatient.recap')
                            ]
                        ]
                    ],
                    [
                        'title' => 'IGD',
                        'icon' => 'i-medical-icon-i-first-aid',
                        'items' => [
                            [
                                'title' => 'Data Pasien',
                                'href' => route('emergency'),
                                'isActive' => request()->routeIs('emergency')
                            ],
                            [
                                'title' => 'Rekap IGD',
                                'href' => route('emergency.recap'),
                                'isActive' => request()->routeIs('emergency.recap')
                            ]
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Layanan Penunjang Medis',
                'items' =>
                [
                    [
                        'title' => 'Laboratorium',
                        'href' => route('account'),
                        'icon' => 'i-medical-icon-i-pathology',
                        'isActive' => request()->routeIs('account')
                    ],
                    [
                        'title' => 'Radiologi',
                        'href' => route('account'),
                        'icon' => 'i-medical-icon-i-radiology',
                        'isActive' => request()->routeIs('account')
                    ],
                    [
                        'title' => 'Farmasi',
                        'href' => route('configuration'),
                        'icon' => 'i-medical-icon-i-pharmacy',
                        'isActive' => request()->routeIs('configuration')
                    ],
                    [
                        'title' => 'ICD',
                        'href' => route('configuration'),
                        'icon' => 'i-medical-icon-i-medical-records',
                        'isActive' => request()->routeIs('configuration')
                    ],
                    [
                        'title' => 'Gizi',
                        'href' => route('nutrition'),
                        'icon' => 'i-medical-icon-i-nutrition',
                        'isActive' => request()->routeIs('nutrition')
                    ],
                ]
            ],
            [
                'title' => 'Layanan Medis Lainnya',
                'items' =>
                [
                    [
                        'title' => 'Kelahiran',
                        'href' => route('account'),
                        'icon' => 'i-medical-icon-i-nursery',
                        'isActive' => request()->routeIs('account')
                    ],
                    [
                        'title' => 'Kematian',
                        'href' => route('configuration'),
                        'icon' => 'i-medical-icon-i-gift-shop',
                        'isActive' => request()->routeIs('configuration')
                    ],
                ]
            ],
            [
                'title' => 'Layanan Khusus',
                'items' => [
                    [
                        'title' => 'Dalam Pengembangan',
                        'href' => '#',
                        'icon' => '',
                        'isActive' => false
                    ],
                ]
            ],
            [
                'title' => 'Lainnya',
                'items' =>
                [
                    [
                        'title' => 'Akun',
                        'href' => route('account'),
                        'icon' => 'i-ph-user',
                        'isActive' => request()->routeIs('account')
                    ],
                    [
                        'title' => 'Akses API',
                        'href' => route('api'),
                        'icon' => 'i-ph-code',
                        'isActive' => request()->routeIs('api')
                    ],
                    [
                        'title' => 'Pengaturan',
                        'href' => route('configuration'),
                        'icon' => 'i-ph-screwdriver',
                        'isActive' => request()->routeIs('configuration')
                    ],
                ]
            ]
        ];

        return view('components.sidebar', compact('menus'));
    }
}
