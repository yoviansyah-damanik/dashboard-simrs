<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function getAllVersions(): array
    {
        $path = base_path('version.json');
        if (!file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    public static function getVersion()
    {
        $versions = self::getAllVersions();
        if (empty($versions)) {
            return [
                'version' => 'v1.0.0',
                'changeLog' => [],
            ];
        }

        $lastVersion = $versions[0] ?? [
            'version' => '1.0.0',
            'changeLog' => [],
        ];

        return [
            'version' => 'v' . ltrim($lastVersion['version'] ?? '1.0.0', 'v'),
            'changeLog' => $lastVersion['changeLog'] ?? [],
        ];
    }

    public static function numberFormat(float $numb, int $decimals = 0, string $decimal_separator = ',', string $thousand_separator = '.', bool $withCurrency = false, string $currency = 'Rp', string $currencyPosition = 'left'): string
    {
        $format = number_format($numb, $decimals, $decimal_separator, $thousand_separator);

        return $withCurrency ? ($currencyPosition == 'left' ? $currency . ' ' . $format : $format . ' ' . $currency) : $format;
    }
}
