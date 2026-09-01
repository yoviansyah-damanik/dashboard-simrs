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
    public function __construct()
    {
    }

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
                                ],
                                [
                                    'title' => 'Laporan Kunjungan dan Pengunjung',
                                    'href' => route('registered-patient.report'),
                                    'isActive' => request()->routeIs('registered-patient.report'),
                                    'isShown' => auth()->user()->hasPermissionTo('registered-patient report')
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
                        [
                            'title' => 'Jadwal Operasi',
                            'icon' => 'i-medical-icon-i-pathology',
                            'items' => [
                                [
                                    'title' => 'Data Jadwal',
                                    'href' => route('operation-schedule'),
                                    'isActive' => request()->routeIs('operation-schedule'),
                                    'isShown' => auth()->user()->hasPermissionTo('operation-schedule show')
                                ],
                                [
                                    'title' => 'Rekap Operasi',
                                    'href' => route('operation-schedule.recap'),
                                    'isActive' => request()->routeIs('operation-schedule.recap'),
                                    'isShown' => auth()->user()->hasPermissionTo('operation-schedule recap')
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
                        [
                            'title' => 'Laboratorium',
                            'href' => route('laboratory'),
                            'icon' => 'i-medical-icon-i-pathology',
                            'isActive' => request()->routeIs('laboratory'),
                            'isShown' => auth()->user()->hasPermissionTo('laboratory show')
                        ],
                        [
                            'title' => 'Radiologi',
                            'href' => route('radiology'),
                            'icon' => 'i-medical-icon-i-radiology',
                            'isActive' => request()->routeIs('radiology'),
                            'isShown' => auth()->user()->hasPermissionTo('radiology show')
                        ],
                        [
                            'title' => 'Farmasi',
                            'icon' => 'i-medical-icon-i-pharmacy',
                            'items' => [
                                [
                                    'title' => 'Data Resep',
                                    'href' => route('pharmacy'),
                                    'isActive' => request()->routeIs('pharmacy'),
                                    'isShown' => auth()->user()->hasPermissionTo('pharmacy show')
                                ],
                                [
                                    'title' => 'Rekap Farmasi',
                                    'href' => route('pharmacy.recap'),
                                    'isActive' => request()->routeIs('pharmacy.recap'),
                                    'isShown' => auth()->user()->hasPermissionTo('pharmacy recap')
                                ]
                            ]
                        ],
                        [
                            'title' => 'Gizi',
                            'href' => route('nutrition'),
                            'icon' => 'i-medical-icon-i-nutrition',
                            'isActive' => request()->routeIs('nutrition'),
                            'isShown' => auth()->user()->hasPermissionTo('nutrition show')
                        ],
                        // [
                        //     'title' => 'ICD',
                        //     'icon' => 'i-medical-icon-i-medical-records',
                        //     'items' => [
                        //         [
                        //             'title' => 'ICD X',
                        //             'href' => route('icd.icd10'),
                        //             'isActive' => request()->routeIs('icd.icd10'),
                        //             'isShown' => auth()->user()->hasPermissionTo('icd icd10 show')
                        //         ],
                        //         [
                        //             'title' => 'ICD IX',
                        //             'href' => route('icd.icd9'),
                        //             'isActive' => request()->routeIs('icd.icd9'),
                        //             'isShown' => auth()->user()->hasPermissionTo('icd icd9 show')
                        //         ],
                        //         [
                        //             'title' => 'Rekap',
                        //             'href' => route('icd'),
                        //             'isActive' => request()->routeIs('icd'),
                        //             'isShown' => auth()->user()->hasPermissionTo('icd recap')
                        //         ],
                        //     ]
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
                'items' => [
                    [
                        'title' => 'Laporan Data Pasien',
                        'href' => route('patient-report'),
                        'icon' => 'i-ph-clipboard-text',
                        'isActive' => request()->routeIs('patient-report'),
                        'isShown' => auth()->user()->hasPermissionTo('patient-report show')
                    ],
                ]
            ],
            [
                'title' => 'SDM',
                'items' => [
                    [
                        'title' => 'Tenaga Medis',
                        'icon' => 'i-medical-icon-i-health-education',
                        'href' => route('medical-personnel'),
                        'isActive' => request()->routeIs('medical-personnel'),
                        'isShown' => auth()->user()->hasPermissionTo('medical-personnel show')
                    ],
                    [
                        'title' => 'Tenaga Non Medis',
                        'icon' => 'i-medical-icon-i-oncology',
                        'href' => route('medical-non-personnel'),
                        'isActive' => request()->routeIs('medical-non-personnel'),
                        'isShown' => auth()->user()->hasPermissionTo('medical-non-personnel show')
                    ]
                ]
            ],
            [
                'title' => 'Laporan Keuangan',
                'items' => [
                    [
                        'title' => 'Pendapatan',
                        'icon' => 'i-ph-wallet',
                        'href' => route('financial-report'),
                        'isActive' => request()->routeIs('financial-report'),
                        'isShown' => auth()->user()->hasPermissionTo('financial-report show')
                    ]
                ]
            ],
            [
                'title' => 'SIRS Online',
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'icon' => 'i-ph-chart-bar',
                        'href' => route('sirs.index'),
                        'isActive' => request()->routeIs('sirs.index'),
                        'isShown' => auth()->user()->hasPermissionTo('sirs.dashboard')
                    ],
                    [
                        'title' => 'RL 1 - Data Dasar',
                        'icon' => 'i-ph-buildings',
                        'items' => [
                            [
                                'title' => 'RL 1.1 - Data Dasar RS',
                                'href' => route('sirs.rl11'),
                                'isActive' => request()->routeIs('sirs.rl11'),
                                'isShown' => auth()->user()->hasPermissionTo('sirs.rl1')
                            ],
                            [
                                'title' => 'RL 1.2 - Indikator Pelayanan',
                                'href' => route('sirs.rl12'),
                                'isActive' => request()->routeIs('sirs.rl12'),
                                'isShown' => auth()->user()->hasPermissionTo('sirs.rl1')
                            ],
                            [
                                'title' => 'RL 1.3 - Fasilitas Tempat Tidur',
                                'href' => route('sirs.rl13'),
                                'isActive' => request()->routeIs('sirs.rl13'),
                                'isShown' => auth()->user()->hasPermissionTo('sirs.rl1')
                            ]
                        ]
                    ],
                    [
                        'title' => 'RL 2 - Ketenagaan',
                        'icon' => 'i-ph-users-three',
                        'href' => route('sirs.rl2'),
                        'isActive' => request()->routeIs('sirs.rl2'),
                        'isShown' => auth()->user()->hasPermissionTo('sirs.rl2')
                    ],
                    [
                        'title' => 'RL 3 Bulanan',
                        'icon' => 'i-ph-calendar-blank',
                        'items' => [
                            ['title' => 'RL 3.1 - Indikator Pelayanan', 'href' => route('sirs.rl31'), 'isActive' => request()->routeIs('sirs.rl31'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.2 - Rawat Inap', 'href' => route('sirs.rl32'), 'isActive' => request()->routeIs('sirs.rl32'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.3 - Rawat Darurat', 'href' => route('sirs.rl33'), 'isActive' => request()->routeIs('sirs.rl33'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.4 - Pengunjung', 'href' => route('sirs.rl34'), 'isActive' => request()->routeIs('sirs.rl34'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.5 - Kunjungan Rajal', 'href' => route('sirs.rl35'), 'isActive' => request()->routeIs('sirs.rl35'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.6 - Kebidanan', 'href' => route('sirs.rl36'), 'isActive' => request()->routeIs('sirs.rl36'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.7 - Neonatal/Bayi/Balita', 'href' => route('sirs.rl37'), 'isActive' => request()->routeIs('sirs.rl37'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.8 - Laboratorium', 'href' => route('sirs.rl38'), 'isActive' => request()->routeIs('sirs.rl38'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.9 - Radiologi', 'href' => route('sirs.rl39'), 'isActive' => request()->routeIs('sirs.rl39'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.10 - Rujukan', 'href' => route('sirs.rl310'), 'isActive' => request()->routeIs('sirs.rl310'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.12 - Pembedahan', 'href' => route('sirs.rl312'), 'isActive' => request()->routeIs('sirs.rl312'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                            ['title' => 'RL 3.14 - Pelayanan Khusus', 'href' => route('sirs.rl314'), 'isActive' => request()->routeIs('sirs.rl314'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_bulanan')],
                        ]
                    ],
                    [
                        'title' => 'RL 3 Tahunan',
                        'icon' => 'i-ph-calendar',
                        'items' => [
                            ['title' => 'RL 3.11 - Gigi & Mulut', 'href' => route('sirs.rl311'), 'isActive' => request()->routeIs('sirs.rl311'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                            ['title' => 'RL 3.13 - Rehab Medik', 'href' => route('sirs.rl313'), 'isActive' => request()->routeIs('sirs.rl313'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                            ['title' => 'RL 3.15 - Kesehatan Jiwa', 'href' => route('sirs.rl315'), 'isActive' => request()->routeIs('sirs.rl315'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                            ['title' => 'RL 3.16 - Keluarga Berencana', 'href' => route('sirs.rl316'), 'isActive' => request()->routeIs('sirs.rl316'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                            ['title' => 'RL 3.17 - Farmasi Pengadaan', 'href' => route('sirs.rl317'), 'isActive' => request()->routeIs('sirs.rl317'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                            ['title' => 'RL 3.18 - Farmasi Resep', 'href' => route('sirs.rl318'), 'isActive' => request()->routeIs('sirs.rl318'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                            ['title' => 'RL 3.19 - Cara Bayar', 'href' => route('sirs.rl319'), 'isActive' => request()->routeIs('sirs.rl319'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl3_tahunan')],
                        ]
                    ],
                    [
                        'title' => 'RL 4-5 Penyakit',
                        'icon' => 'i-ph-virus',
                        'items' => [
                            ['title' => 'RL 4.1 - Morbiditas Ranap', 'href' => route('sirs.rl41'), 'isActive' => request()->routeIs('sirs.rl41'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl4')],
                            ['title' => 'RL 4.2 - 10 Besar Ranap', 'href' => route('sirs.rl42'), 'isActive' => request()->routeIs('sirs.rl42'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl4')],
                            ['title' => 'RL 4.3 - 10 Besar Kematian', 'href' => route('sirs.rl43'), 'isActive' => request()->routeIs('sirs.rl43'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl4')],
                            ['title' => 'RL 5.1 - Morbiditas Rajal', 'href' => route('sirs.rl51'), 'isActive' => request()->routeIs('sirs.rl51'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl5')],
                            ['title' => 'RL 5.2 - 10 Besar Rajal', 'href' => route('sirs.rl52'), 'isActive' => request()->routeIs('sirs.rl52'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl5')],
                            ['title' => 'RL 5.3 - 10 Besar Kunjungan', 'href' => route('sirs.rl53'), 'isActive' => request()->routeIs('sirs.rl53'), 'isShown' => auth()->user()->hasPermissionTo('sirs.rl5')],
                        ]
                    ],
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
                            'title' => 'Hak Akses',
                            'href' => route('role-and-permissions'),
                            'icon' => 'i-ph-shield-check',
                            'isActive' => request()->routeIs('role-and-permissions'),
                            'isShown' => auth()->user()->hasPermissionTo('role_and_permissions')
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
