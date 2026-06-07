<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function dateFormat($date, string $dateFormat = 'd/m/Y', bool $isTranslated = false, string $translatedFormat = 'd F Y')
    {
        if ($isTranslated)
            return Carbon::parse($date)->translatedFormat($translatedFormat);

        return Carbon::parse($date)->format($dateFormat);
    }

    public static function getAge($date, $withMonth = false, $withDay = false)
    {
        $format = '%y Tahun';

        if ($withMonth)
            $format .= ' %m Bulan';

        if ($withDay)
            $format .= ' %m Hari';

        return Carbon::parse($date)->diff(\Carbon\Carbon::now())->format($format);
    }

    public static function getDiffInDays($date, $dateDiff = null)
    {
        $start = Carbon::parse($date);
        $end = $dateDiff ? Carbon::parse($dateDiff) : Carbon::now();

        return ceil($start->diffInMinutes($end) / (24 * 60));
    }
}
