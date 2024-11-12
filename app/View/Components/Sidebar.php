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
                        'isActive' => request()->routeIs('home'),
                        'isShown' => true
                    ],
                    [
                        'title' => 'Pasien',
                        'icon' => 'i-ph-users-four',
                        'items' => [
                            [
                                'title' => 'Data Pasien',
                                'href' => route('patient'),
                                'isActive' => request()->routeIs('patient'),
                                'isShown' => auth()->user()->hasPermissionTo('patient show')
                            ],
                            [
                                'title' => 'Rekap Pasien',
                                'href' => route('patient.recap'),
                                'isActive' => request()->routeIs('patient.recap'),
                                'isShown' => auth()->user()->hasPermissionTo('patient recap')
                            ]
                        ]
                    ],
                    [
                        'title' => 'Kamar',
                        'icon' => 'i-ph-door-open',
                        'href' => route('room'),
                        'isActive' => request()->routeIs('room'),
                        'isShown' => auth()->user()->hasPermissionTo('room show')
                    ],
                    [
                        'title' => 'Poliklinik',
                        'icon' => 'i-ph-stethoscope',
                        'href' => route('polyclinic'),
                        'isActive' => request()->routeIs('polyclinic'),
                        'isShown' => auth()->user()->hasPermissionTo('polyclinic show')
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
                                'isActive' => request()->routeIs('registered-patient'),
                                'isShown' => auth()->user()->hasPermissionTo('registered-patient show')
                            ],
                            [
                                'title' => 'Rekap Pendaftaran',
                                'href' => route('registered-patient.recap'),
                                'isActive' => request()->routeIs('registered-patient.recap'),
                                'isShown' => auth()->user()->hasPermissionTo('registered-patient recap')
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
                                'isActive' => request()->routeIs('inpatient'),
                                'isShown' => auth()->user()->hasPermissionTo('inpatient show')
                            ],
                            [
                                'title' => 'Rekap Rawat Inap',
                                'href' => route('inpatient.recap'),
                                'isActive' => request()->routeIs('inpatient.recap'),
                                'isShown' => auth()->user()->hasPermissionTo('inpatient recap')
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
                                'isActive' => request()->routeIs('outpatient'),
                                'isShown' => auth()->user()->hasPermissionTo('outpatient show')
                            ],
                            [
                                'title' => 'Rekap Rawat Jalan',
                                'href' => route('outpatient.recap'),
                                'isActive' => request()->routeIs('outpatient.recap'),
                                'isShown' => auth()->user()->hasPermissionTo('outpatient recap')
                            ]
                        ]
                    ],
                    // [
                    //     'title' => 'IGD',
                    //     'icon' => 'i-medical-icon-i-first-aid',
                    //     'items' => [
                    //         [
                    //             'title' => 'Data Pasien',
                    //             'href' => route('emergency'),
                    //             'isActive' => request()->routeIs('emergency'),
                    //             'isShown' => auth()->user()->hasPermissionTo('emergency show')
                    //         ],
                    //         [
                    //             'title' => 'Rekap IGD',
                    //             'href' => route('emergency.recap'),
                    //             'isActive' => request()->routeIs('emergency.recap'),
                    //             'isShown' => auth()->user()->hasPermissionTo('emergency recap')
                    //         ]
                    //     ]
                    // ],
                ]
            ],
            [
                'title' => 'Layanan Penunjang Medis',
                'items' =>
                [
                    // [
                    //     'title' => 'Laboratorium',
                    //     'href' => route('laboratory'),
                    //     'icon' => 'i-medical-icon-i-pathology',
                    //     'isActive' => request()->routeIs('laboratory'),
                    //     'isShown' => auth()->user()->hasPermissionTo('laboratory show')
                    // ],
                    // [
                    //     'title' => 'Radiologi',
                    //     'href' => route('radiology'),
                    //     'icon' => 'i-medical-icon-i-radiology',
                    //     'isActive' => request()->routeIs('radiology'),
                    //     'isShown' => auth()->user()->hasPermissionTo('radiology show')
                    // ],
                    // [
                    //     'title' => 'Farmasi',
                    //     'href' => route('pharmacy'),
                    //     'icon' => 'i-medical-icon-i-pharmacy',
                    //     'isActive' => request()->routeIs('pharmacy'),
                    //     'isShown' => auth()->user()->hasPermissionTo('pharmacy show')
                    // ],
                    // [
                    //     'title' => 'ICD',
                    //     'icon' => 'i-medical-icon-i-medical-records',
                    //     'items' => [
                    //         [
                    //             'title' => 'ICD X',
                    //             'href' => route('icd.icd10'),
                    //             'icon' => 'i-medical-icon-i-medical-records',
                    //             'isActive' => request()->routeIs('icd.icd10'),
                    //             'isShown' => auth()->user()->hasPermissionTo('icd icd10 show')
                    //         ],
                    //         [
                    //             'title' => 'ICD IX',
                    //             'href' => route('icd.icd9'),
                    //             'icon' => 'i-medical-icon-i-medical-records',
                    //             'isActive' => request()->routeIs('icd.icd9'),
                    //             'isShown' => auth()->user()->hasPermissionTo('icd icd9 show')
                    //         ],
                    //         [
                    //             'title' => 'Rekap',
                    //             'href' => route('icd'),
                    //             'icon' => 'i-medical-icon-i-medical-records',
                    //             'isActive' => request()->routeIs('icd10'),
                    //             'isShown' => auth()->user()->hasPermissionTo('icd recap')
                    //         ],
                    //     ]

                    // ],
                    // [
                    //     'title' => 'Gizi',
                    //     'href' => route('nutrition'),
                    //     'icon' => 'i-medical-icon-i-nutrition',
                    //     'isActive' => request()->routeIs('nutrition'),
                    //     'isShown' => auth()->user()->hasPermissionTo('nutrition show')
                    // ],

                ]
            ],
            [
                'title' => 'Layanan Medis Lainnya',
                'items' =>
                [
                    // [
                    //     'title' => 'Kelahiran',
                    //     'href' => route('birth'),
                    //     'icon' => 'i-medical-icon-i-nursery',
                    //     'isActive' => request()->routeIs('birth'),
                    //     'isShown' => auth()->user()->hasPermissionTo('birth show')
                    // ],
                    // [
                    //     'title' => 'Kematian',
                    //     'href' => route('death'),
                    //     'icon' => 'i-medical-icon-i-gift-shop',
                    //     'isActive' => request()->routeIs('death'),
                    //     'isShown' => auth()->user()->hasPermissionTo('death show')
                    // ],

                ]
            ],
            [
                'title' => 'Layanan Khusus',
                'items' => []
            ],
            [
                'title' => 'SDM',
                'items' => [
                    // [
                    //     'title' => 'Tenaga Medis',
                    //     'icon' => 'i-medical-icon-i-health-education',
                    //     'href' => route('medical-personnel'),
                    //     'isActive' => request()->routeIs('medical-personnel'),
                    //     'isShown' => auth()->user()->hasPermissionTo('medical-personnel show')
                    // ],
                    // [
                    //     'title' => 'Tenaga Non Medis',
                    //     'icon' => 'i-medical-icon-i-oncology',
                    //     'href' => route('medical-non-personnel'),
                    //     'isActive' => request()->routeIs('medical-non-personnel'),
                    //     'isShown' => auth()->user()->hasPermissionTo('medical-non-personnel show')
                    // ]

                ]
            ],
            [
                'title' => 'Lainnya',
                'items' =>
                [
                    [
                        'title' => 'Manajemen Pengguna',
                        'href' => route('users'),
                        'icon' => 'i-ph-users',
                        'isActive' => request()->routeIs('users'),
                        'isShown' => auth()->user()->hasPermissionTo('users')
                    ],
                    [
                        'title' => 'Akun',
                        'href' => route('account'),
                        'icon' => 'i-ph-user',
                        'isActive' => request()->routeIs('account'),
                        'isShown' => true
                    ],
                    [
                        'title' => 'Akses API',
                        'href' => route('api'),
                        'icon' => 'i-ph-code',
                        'isActive' => request()->routeIs('api'),
                        'isShown' => true
                    ],
                    [
                        'title' => 'Pengaturan',
                        'href' => route('configuration'),
                        'icon' => 'i-ph-screwdriver',
                        'isActive' => request()->routeIs('configuration'),
                        'isShown' => auth()->user()->hasPermissionTo('configuration')
                    ],
                ]
            ]
        ];

        $menus = collect($menus)
            ->map(function ($menu) {
                return [
                    ...collect($menu)->except('items'),
                    'items' => collect($menu['items'])
                        ->filter(fn($q) => !empty($q['items']) || (array_key_exists('isShown', $q) && $q['isShown'] === true))
                        ->map(
                            fn($q) => collect($q)
                                ->when(
                                    !empty($q['items']),
                                    fn($r) => [
                                        ...$q,
                                        'items' => collect($r['items'])->where('isShown', true)
                                            ->values()
                                            ->toArray()
                                    ],
                                    fn($r) => $r
                                )
                        )->values()
                        ->toArray()
                ];
            })->values()
            ->toArray();
        // fn($r) => collect($r['items'])->where('isShown', true),
        // fn($r) => collect($r)->where('isShown', true)

        return view('components.sidebar', compact('menus'));
    }
}
