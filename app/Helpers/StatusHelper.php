<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * @param string $type Tipe data yang ingin ditampilkan warnanya
     * @param string $data Data untuk difilter
     */
    public static function getColor(string $type, string $data)
    {
        try {
            $payload = [
                'status_pelayanan' => [
                    'Sudah' => 'bg-green-100 text-green-700',
                    'Belum' => 'bg-yellow-100 text-yellow-700',
                    'Batal' => 'bg-indigo-100 text-indigo-700',
                    'Dirujuk' => 'bg-cyan-100 text-cyan-700',
                    'Berkas Diterima' => 'bg-pink-100 text-pink-700',
                    'Dirawat' => 'bg-violet-100 text-violet-700',
                    'Meninggal' => 'bg-red-100 text-red-700',
                    'Pulang Paksa' => 'bg-black/10 text-black',
                ]
            ];

            return $payload[$type][$data];
        } catch (\Exception $e) {
            return 'bg-gray-100 text-gray-700';
        }
    }

    /**
     * @param string $type Tipe data yang ingin ditampilkan warnanya
     * @param string $data Data untuk difilter
     */
    public static function getBorderColor(string $type, string $data): string
    {
        try {
            $payload = [
                'status_pelayanan' => [
                    'Sudah' => 'border-l-green-500',
                    'Belum' => 'border-l-yellow-500',
                    'Batal' => 'border-l-indigo-500',
                    'Dirujuk' => 'border-l-cyan-500',
                    'Berkas Diterima' => 'border-l-pink-500',
                    'Dirawat' => 'border-l-violet-500',
                    'Meninggal' => 'border-l-red-500',
                    'Pulang Paksa' => 'border-l-gray-800',
                ]
            ];

            return $payload[$type][$data] ?? 'border-l-gray-300';
        } catch (\Exception $e) {
            return 'border-l-gray-300';
        }
    }

    /**
     * Mengambil daftar keterangan (legenda) warna border untuk status pelayanan.
     * @return array
     */
    public static function getBorderLegends(): array
    {
        return [
            ['label' => 'Sudah', 'color' => 'bg-green-500', 'border' => 'border-l-green-500'],
            ['label' => 'Belum', 'color' => 'bg-yellow-500', 'border' => 'border-l-yellow-500'],
            ['label' => 'Batal', 'color' => 'bg-indigo-500', 'border' => 'border-l-indigo-500'],
            ['label' => 'Dirujuk', 'color' => 'bg-cyan-500', 'border' => 'border-l-cyan-500'],
            ['label' => 'Berkas Diterima', 'color' => 'bg-pink-500', 'border' => 'border-l-pink-500'],
            ['label' => 'Dirawat', 'color' => 'bg-violet-500', 'border' => 'border-l-violet-500'],
            ['label' => 'Meninggal', 'color' => 'bg-red-500', 'border' => 'border-l-red-500'],
            ['label' => 'Pulang Paksa', 'color' => 'bg-gray-800', 'border' => 'border-l-gray-800'],
        ];
    }

    /**
     * Mengambil metadata visual (ikon, warna badge, gradien) untuk setiap kategori kelas kamar.
     *
     * @param string|null $title Nama kelas kamar
     * @return array
     */
    public static function getRoomClassMeta(?string $title): array
    {
        $normalized = strtoupper(trim($title ?? ''));

        if (str_contains($normalized, 'VVIP') || str_contains($normalized, 'VIP')) {
            return [
                'icon' => 'icon-[solar--crown-star-bold-duotone]',
                'iconColor' => 'text-amber-500 bg-amber-500/10 dark:bg-amber-500/15 border border-amber-500/20',
                'glowGradient' => 'from-amber-500 to-amber-600 shadow-amber-500/25',
                'accentColor' => 'text-amber-500',
                'label' => 'VIP / VVIP',
            ];
        }

        if (str_contains($normalized, 'KELAS 1') || str_contains($normalized, 'KELAS I') || str_contains($normalized, 'KL 1') || $normalized === '1') {
            return [
                'icon' => 'icon-[ph--number-circle-one-duotone]',
                'iconColor' => 'text-sky-500 bg-sky-500/10 dark:bg-sky-500/15 border border-sky-500/20',
                'glowGradient' => 'from-sky-500 to-blue-600 shadow-sky-500/25',
                'accentColor' => 'text-sky-500',
                'label' => 'Kelas 1',
            ];
        }

        if (str_contains($normalized, 'KELAS 2') || str_contains($normalized, 'KELAS II') || str_contains($normalized, 'KL 2') || $normalized === '2') {
            return [
                'icon' => 'icon-[ph--number-circle-two-duotone]',
                'iconColor' => 'text-teal-500 bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/20',
                'glowGradient' => 'from-teal-500 to-emerald-600 shadow-teal-500/25',
                'accentColor' => 'text-teal-500',
                'label' => 'Kelas 2',
            ];
        }

        if (str_contains($normalized, 'KELAS 3') || str_contains($normalized, 'KELAS III') || str_contains($normalized, 'KL 3') || $normalized === '3') {
            return [
                'icon' => 'icon-[ph--number-circle-three-duotone]',
                'iconColor' => 'text-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20',
                'glowGradient' => 'from-emerald-500 to-teal-600 shadow-emerald-500/25',
                'accentColor' => 'text-emerald-500',
                'label' => 'Kelas 3',
            ];
        }

        if (str_contains($normalized, 'ICU') || str_contains($normalized, 'ICCU') || str_contains($normalized, 'NICU') || str_contains($normalized, 'PICU')) {
            return [
                'icon' => 'icon-[solar--heart-pulse-bold-duotone]',
                'iconColor' => 'text-rose-500 bg-rose-500/10 dark:bg-rose-500/15 border border-rose-500/20',
                'glowGradient' => 'from-rose-500 to-red-600 shadow-rose-500/25',
                'accentColor' => 'text-rose-500',
                'label' => 'ICU / Intensif',
            ];
        }

        if (str_contains($normalized, 'HCU')) {
            return [
                'icon' => 'icon-[solar--pulse-2-bold-duotone]',
                'iconColor' => 'text-orange-500 bg-orange-500/10 dark:bg-orange-500/15 border border-orange-500/20',
                'glowGradient' => 'from-orange-500 to-amber-600 shadow-orange-500/25',
                'accentColor' => 'text-orange-500',
                'label' => 'HCU',
            ];
        }

        if (str_contains($normalized, 'ISOLASI')) {
            return [
                'icon' => 'icon-[solar--shield-cross-bold-duotone]',
                'iconColor' => 'text-violet-500 bg-violet-500/10 dark:bg-violet-500/15 border border-violet-500/20',
                'glowGradient' => 'from-violet-500 to-purple-600 shadow-violet-500/25',
                'accentColor' => 'text-violet-500',
                'label' => 'Ruang Isolasi',
            ];
        }

        if (str_contains($normalized, 'NON') || str_contains($normalized, 'TRANSIT') || str_contains($normalized, 'IGD')) {
            return [
                'icon' => 'icon-[solar--siren-bold-duotone]',
                'iconColor' => 'text-cyan-500 bg-cyan-500/10 dark:bg-cyan-500/15 border border-cyan-500/20',
                'glowGradient' => 'from-cyan-500 to-blue-600 shadow-cyan-500/25',
                'accentColor' => 'text-cyan-500',
                'label' => 'Transit / Non-Kelas',
            ];
        }

        return [
            'icon' => 'icon-[solar--bed-bold-duotone]',
            'iconColor' => 'text-indigo-500 bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20',
            'glowGradient' => 'from-indigo-500 to-blue-600 shadow-indigo-500/25',
            'accentColor' => 'text-indigo-500',
            'label' => $title ?? 'Kamar',
        ];
    }
}
