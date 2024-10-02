<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function getVersion()
    {
        $json = file_get_contents(base_path('version.json'), 'version.json');
        // Check if the file was read successfully
        if ($json === false) {
            die('Error reading the JSON file');
        }

        // Decode the JSON file
        $json_data = json_decode($json, true);

        // Check if the JSON was decoded successfully
        if ($json_data === null) {
            die('Error decoding the JSON file');
        }

        $versions = collect($json_data);
        $lastVersion = $versions->sortByDesc('version')
            ->first();

        return 'Versi ' . $lastVersion['version'];
    }

    public static function getWithInactivePolyclinicStatus(): string
    {
        return env('WITH_INACTIVE_POLYCLINIC', false);
    }

    public static function getWithInactivePersonResponsibilityStatus(): string
    {
        return env('WITH_INACTIVE_PERSON_RESPONSIBILITY', false);
    }

    public static function getWithInactiveDoctorStatus(): string
    {
        return env('WITH_INACTIVE_DOCTOR', false);
    }

    public static function getWithInactiveRoomStatus(): string
    {
        return env('WITH_INACTIVE_ROOM', false);
    }

    public static function getWithInactiveWardStatus(): string
    {
        return env('WITH_INACTIVE_WARD', false);
    }
}
