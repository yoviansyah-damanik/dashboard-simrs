<?php

namespace App\Repository;

use App\Helpers\SirsHelper;
use App\Services\HospitalIndicatorService;
use Illuminate\Support\Facades\DB;

interface SirsFacilityInterface
{
    public static function getRL11(): array;
    public static function getRL12(int $tahun): array;
    public static function getRL13(): array;
}

class SirsFacilityRepository implements SirsFacilityInterface
{
    const KONEKSI = 'simrs';

    /**
     * Data yang tidak tersedia di SIMRS (jenis/kelas/kepemilikan RS) untuk RL 1.1.
     * SIMRS hanya menyimpan profil operasional dasar, bukan data registrasi resmi RS ke
     * Kemenkes — nilai berikut disesuaikan dengan kondisi rumah sakit yang menjalankan
     * aplikasi ini dan perlu ditinjau ulang bila digunakan di instansi lain.
     */
    const JENIS_RS = 'RS Umum';
    const KELAS_RS = 'D';
    const KEPEMILIKAN_RS = 'TNI';

    /** RL 1.1 - Data Dasar Rumah Sakit */
    public static function getRL11(): array
    {
        $setting = DB::connection(self::KONEKSI)
            ->table('setting')
            ->select('nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi', 'kontak', 'email', 'kode_ppkkemenkes')
            ->first();

        return [
            'kode_registrasi' => $setting->kode_ppkkemenkes ?? '-',
            'nama_rs' => $setting->nama_instansi ?? '-',
            'alamat' => $setting->alamat_instansi ?? '-',
            'kabupaten_kota' => $setting->kabupaten ?? '-',
            'provinsi' => $setting->propinsi ?? '-',
            'telepon' => $setting->kontak ?? '-',
            'email' => $setting->email ?? '-',
            'jenis_rs' => self::JENIS_RS,
            'kelas_rs' => self::KELAS_RS,
            'kepemilikan' => self::KEPEMILIKAN_RS,
        ];
    }

    /** RL 1.2 - Indikator Pelayanan Rumah Sakit (tahunan, seluruh RS) */
    public static function getRL12(int $tahun): array
    {
        $startDate = "{$tahun}-01-01";
        $endDate = "{$tahun}-12-31";
        $jumlahHari = date('L', strtotime($startDate)) ? 366 : 365;

        return HospitalIndicatorService::computeForPeriod($startDate, $endDate, $jumlahHari);
    }

    /**
     * Matriks indikator pelayanan rawat inap (BOR/ALOS/BTO/TOI/NDR/GDR) per bulan untuk satu tahun,
     * seluruh RS. Key 1-12 = bulan, key 'tahun' = angka tahunan penuh (sama dengan getRL12()).
     * Dihitung lewat HospitalIndicatorService::computeForPeriod() — satu-satunya titik masuk resmi
     * untuk angka indikator level-RS di seluruh aplikasi (dipakai juga oleh Rekap Rawat Inap),
     * supaya bulan yang sama menghasilkan angka yang sama persis di halaman manapun.
     */
    public static function getYearlyIndicatorMatrix(int $tahun): array
    {
        $months = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $range = SirsHelper::getDateRange($tahun, $bulan);
            $months[$bulan] = HospitalIndicatorService::computeForPeriod($range['start'], $range['end'], $range['jumlah_hari']);
        }

        $months['tahun'] = self::getRL12($tahun);

        return $months;
    }

    /** RL 1.3 - Fasilitas Tempat Tidur Rawat Inap (kondisi terkini, per kelas perawatan) */
    public static function getRL13(): array
    {
        $rows = DB::connection(self::KONEKSI)->select("
            SELECT k.kelas, COUNT(*) as jumlah_tt
            FROM kamar k
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE b.status = '1' AND k.statusdata = '1' AND b.kd_bangsal <> 'TRANS'
            GROUP BY k.kelas
        ");

        $kelasMap = [
            'Kelas VVIP' => 'VVIP',
            'Kelas VIP' => 'VIP',
            'Kelas Utama' => 'Kelas I',
            'Kelas 1' => 'Kelas I',
            'Kelas 2' => 'Kelas II',
            'Kelas 3' => 'Kelas III',
            'NON' => 'Non Kelas',
            'ICU' => 'ICU',
            'HCU' => 'HCU',
            'RUANG ISOLASI' => 'Isolasi',
        ];

        $data = [];
        foreach ($kelasMap as $label) {
            $data[$label] = 0;
        }
        $data['Khusus/Lainnya'] = 0;

        foreach ($rows as $row) {
            $label = $kelasMap[$row->kelas] ?? 'Khusus/Lainnya';
            $data[$label] += $row->jumlah_tt;
        }

        return [
            'per_kelas' => $data,
            'total' => array_sum($data),
        ];
    }
}
