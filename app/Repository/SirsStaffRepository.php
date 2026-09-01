<?php

namespace App\Repository;

use App\Helpers\SirsHelper;
use Illuminate\Support\Facades\DB;

interface SirsStaffInterface
{
    public static function getRL2(): array;
}

class SirsStaffRepository implements SirsStaffInterface
{
    const KONEKSI = 'simrs';

    /** RL 2 - Ketenagaan (rekap pegawai aktif per jenis tenaga x status kepegawaian) */
    public static function getRL2(): array
    {
        $labels = SirsHelper::getRL2CategoryLabels();

        $data = [];
        foreach ($labels as $id => $nama) {
            $data[$id] = ['nama' => $nama, 'pns' => 0, 'non_pns' => 0, 'total' => 0];
        }

        $pegawai = DB::connection(self::KONEKSI)
            ->table('pegawai')
            ->where('stts_aktif', 'AKTIF')
            ->select('jbtn', 'stts_kerja', DB::raw('count(*) as jumlah'))
            ->groupBy('jbtn', 'stts_kerja')
            ->get();

        foreach ($pegawai as $row) {
            $kategori = SirsHelper::mapJabatanToRL2Category((string) $row->jbtn);
            $status = SirsHelper::categorizeEmploymentStatus((string) $row->stts_kerja);

            $data[$kategori][$status] += $row->jumlah;
            $data[$kategori]['total'] += $row->jumlah;
        }

        $data[99] = ['nama' => 'TOTAL', 'pns' => 0, 'non_pns' => 0, 'total' => 0];
        foreach ($labels as $id => $nama) {
            $data[99]['pns'] += $data[$id]['pns'];
            $data[99]['non_pns'] += $data[$id]['non_pns'];
            $data[99]['total'] += $data[$id]['total'];
        }

        return $data;
    }
}
