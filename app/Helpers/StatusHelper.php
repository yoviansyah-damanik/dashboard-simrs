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
}
