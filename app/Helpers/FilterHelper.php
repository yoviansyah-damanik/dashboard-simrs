<?php

namespace App\Helpers;

use App\Models\Patient;
use App\Models\RegisteredPatient;
use App\Repository\RoomRepository;
use App\Repository\WardRepository;
use App\Repository\DoctorRepository;
use App\Repository\TniGroupRepository;
use App\Repository\PolyclinicRepository;
use App\Repository\PersonResponsibilityRepository;

class FilterHelper
{
    public static function getRooms()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(RoomRepository::getAll(limit: 0, withRelations: false))
                ->map(function ($type) {
                    return [
                        'title' => $type['kode_kamar'],
                        'value' => $type['kode_kamar'],
                    ];
                })->toArray()
        ];
    }

    public static function getTniGroups()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(TniGroupRepository::getAll(limit: 0, withRelations: false))
                ->map(function ($type) {
                    return [
                        'title' => $type['nama_golongan'],
                        'value' => $type['kode_golongan'],
                    ];
                })->toArray()
        ];
    }

    public static function getWards()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(WardRepository::getAll(limit: 0, withRelations: false))
                ->map(function ($type) {
                    return [
                        'title' => $type['nama_bangsal'],
                        'value' => $type['kode_bangsal'],
                    ];
                })->toArray()
        ];
    }

    public static function getStatus()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(RegisteredPatient::KELOMPOK_STATUS_PELAYANAN)
                ->map(function ($status) {
                    return [
                        'title' => $status,
                        'value' => $status,
                    ];
                })->toArray()
        ];
    }

    public static function getAdvanceStatus()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(RegisteredPatient::KELOMPOK_STATUS_LANJUT)
                ->map(function ($status) {
                    return [
                        'title' => $status,
                        'value' => $status,
                    ];
                })->toArray()
        ];
    }

    public static function getTypes()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(Patient::KELOMPOK_PASIEN)
                ->map(function ($type, $key) {
                    return [
                        'title' => $type,
                        'value' => $key,
                    ];
                })->toArray()
        ];
    }

    public static function getPolyclinics()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(PolyclinicRepository::getAll(limit: 0, withRelations: false))
                ->map(function ($type) {
                    return [
                        'title' => $type['nama_poliklinik'],
                        'value' => $type['kode_poliklinik'],
                    ];
                })->toArray()
        ];
    }

    public static function getGenders()
    {
        return [
            [
                'value' => 'semua',
                'title' => 'Semua'
            ],
            ...collect(Patient::KELOMPOK_JENIS_KELAMIN)
                ->map(function ($gender, $key) {
                    return [
                        'title' => $gender,
                        'value' => $key,
                    ];
                })->toArray()
        ];
    }

    public static function getPayTypes()
    {
        return [
            [
                'title' => 'Semua',
                'value' => 'semua'
            ],
            ...collect(PersonResponsibilityRepository::getAll(limit: 0))
                ->map(function ($type) {
                    return [
                        'title' => $type['penanggungjawab'],
                        'value' => $type['kode_penanggungjawab'],
                    ];
                })->toArray()
        ];
    }

    public static function getAgeCategories()
    {
        return [
            [
                'value' => 'semua',
                'title' => 'Semua'
            ],
            ...collect(Patient::KELOMPOK_UMUR)
                ->map(function ($age, $key) {
                    return [
                        'title' => $age,
                        'value' => $key,
                    ];
                })->toArray()
        ];
    }

    public static function getDoctors(?string $spesialisCode = null)
    {
        return [
            [
                'value' => 'semua',
                'title' => 'Semua'
            ],
            ...collect(DoctorRepository::getAll(limit: 0, spesialisCode: $spesialisCode))
                ->map(function ($type) {
                    return [
                        'title' => $type['nama_dokter'],
                        'value' => $type['kode_dokter'],
                    ];
                })->toArray()
        ];
    }
}
