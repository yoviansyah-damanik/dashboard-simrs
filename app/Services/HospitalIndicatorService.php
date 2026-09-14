<?php

namespace App\Services;

/**
 * Sumber tunggal perhitungan indikator pelayanan rawat inap (BOR, ALOS, BTO, TOI, NDR, GDR)
 * sesuai formula standar Depkes RI, dipakai ulang oleh dashboard, halaman Rekap Rawat Inap,
 * dan laporan SIRS (RL 3.1 & RL 1.2) agar tidak ada lagi rumus yang berbeda-beda antar modul.
 */
class HospitalIndicatorService
{
    /** Rentang nilai ideal standar Depkes, dipakai untuk status/pewarnaan di UI (bukan untuk clamp). */
    const RANGES = [
        'bor' => ['min' => 60, 'max' => 85],
        'alos' => ['min' => 6, 'max' => 9],
        'toi' => ['min' => 1, 'max' => 3],
        'bto' => ['min' => 40, 'max' => 50],
        'gdr' => ['min' => 0, 'max' => 45],
        'ndr' => ['min' => 0, 'max' => 25],
    ];

    /** Batas fisik mutlak: nilai hasil hitung di-clamp ke sini untuk menutup anomali data. */
    const HARD_BOUNDS = [
        'bor' => ['min' => 0, 'max' => 100],
        'alos' => ['min' => 0, 'max' => null],
        'toi' => ['min' => 0, 'max' => null],
        'bto' => ['min' => 0, 'max' => null],
        'gdr' => ['min' => 0, 'max' => 1000],
        'ndr' => ['min' => 0, 'max' => 1000],
    ];

    public static function calculate(
        int $hariPerawatan,
        int $totalTempatTidur,
        int $jumlahHari,
        int $pasienKeluarHidup,
        int $pasienKeluarMati,
        int $totalLamaDirawatKeluar,
        int $pasienKeluarMatiKurang48 = 0,
    ): array {
        $pasienKeluarTotal = $pasienKeluarHidup + $pasienKeluarMati;

        $bor = ($totalTempatTidur > 0 && $jumlahHari > 0)
            ? ($hariPerawatan / ($totalTempatTidur * $jumlahHari)) * 100
            : 0;

        $alos = $pasienKeluarTotal > 0 ? $totalLamaDirawatKeluar / $pasienKeluarTotal : 0;

        $bto = $totalTempatTidur > 0 ? $pasienKeluarTotal / $totalTempatTidur : 0;

        $toi = ($pasienKeluarTotal > 0 && $totalTempatTidur > 0 && $jumlahHari > 0)
            ? (($totalTempatTidur * $jumlahHari) - $hariPerawatan) / $pasienKeluarTotal
            : 0;

        $ndr = $pasienKeluarTotal > 0 ? ($pasienKeluarMatiKurang48 / $pasienKeluarTotal) * 1000 : 0;

        $gdr = $pasienKeluarTotal > 0 ? ($pasienKeluarMati / $pasienKeluarTotal) * 1000 : 0;

        return [
            'bor' => round(self::clamp('bor', $bor), 2),
            'alos' => round(self::clamp('alos', $alos), 2),
            'bto' => round(self::clamp('bto', $bto), 2),
            'toi' => round(self::clamp('toi', $toi), 2),
            'ndr' => round(self::clamp('ndr', $ndr), 2),
            'gdr' => round(self::clamp('gdr', $gdr), 2),
        ];
    }

    private static function clamp(string $indicator, float $value): float
    {
        $bounds = self::HARD_BOUNDS[$indicator];

        if ($value < $bounds['min']) {
            return $bounds['min'];
        }

        if ($bounds['max'] !== null && $value > $bounds['max']) {
            return $bounds['max'];
        }

        return $value;
    }

    /** Apakah nilai indikator berada di rentang ideal standar Depkes. */
    public static function isWithinRange(string $indicator, float $value): bool
    {
        $range = self::RANGES[$indicator];

        return $value >= $range['min'] && $value <= $range['max'];
    }
}
