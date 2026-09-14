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

        $totalTt = 0;
        foreach (SirsHelper::getBedsPerWard(excludeTr: true) as $row) {
            $totalTt += $row->jumlah_tt;
        }

        return self::computeIndicatorsForPeriod($startDate, $endDate, $jumlahHari, $totalTt);
    }

    /**
     * Matriks indikator pelayanan rawat inap (BOR/ALOS/BTO/TOI/NDR/GDR) per bulan untuk satu tahun,
     * seluruh RS. Key 1-12 = bulan, key 'tahun' = angka tahunan penuh (sama dengan getRL12()).
     */
    public static function getYearlyIndicatorMatrix(int $tahun): array
    {
        $totalTt = 0;
        foreach (SirsHelper::getBedsPerWard(excludeTr: true) as $row) {
            $totalTt += $row->jumlah_tt;
        }

        $months = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $range = SirsHelper::getDateRange($tahun, $bulan);
            $months[$bulan] = self::computeIndicatorsForPeriod($range['start'], $range['end'], $range['jumlah_hari'], $totalTt);
        }

        $months['tahun'] = self::getRL12($tahun);

        return $months;
    }

    /**
     * Hitung BOR/ALOS/BTO/TOI/NDR/GDR seluruh RS untuk satu rentang tanggal (dipakai bersama oleh
     * getRL12() dan getYearlyIndicatorMatrix() agar query hari-perawatan & pasien-keluar tidak
     * diduplikasi).
     */
    private static function computeIndicatorsForPeriod(string $startDate, string $endDate, int $jumlahHari, int $totalTt): array
    {
        $hariPerawatan = DB::connection(self::KONEKSI)->selectOne("
            SELECT SUM(ki.lama) as total
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE (ki.tgl_masuk BETWEEN ? AND ?
                   OR ki.tgl_keluar BETWEEN ? AND ?
                   OR (ki.tgl_masuk <= ? AND (ki.tgl_keluar IS NULL OR ki.tgl_keluar >= ?)))
            AND b.status = '1'
        ", [$startDate, $endDate, $startDate, $endDate, $endDate, $startDate]);

        $pasienKeluar = DB::connection(self::KONEKSI)->selectOne("
            SELECT
                COUNT(*) as total,
                SUM(ki.lama) as total_lama,
                SUM(CASE WHEN ki.stts_pulang = 'Meninggal' THEN 1 ELSE 0 END) as mati,
                SUM(CASE WHEN ki.stts_pulang = 'Meninggal'
                    AND TIMESTAMPDIFF(HOUR, CONCAT(ki.tgl_masuk, ' ', ki.jam_masuk), CONCAT(ki.tgl_keluar, ' ', ki.jam_keluar)) < 48
                    THEN 1 ELSE 0 END) as mati_kurang48
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_keluar BETWEEN ? AND ? AND b.status = '1'
        ", [$startDate, $endDate]);

        $totalHp = $hariPerawatan->total ?? 0;
        $totalKeluar = $pasienKeluar->total ?? 0;
        $totalLama = $pasienKeluar->total_lama ?? 0;
        $mati = $pasienKeluar->mati ?? 0;
        $matiKurang48 = $pasienKeluar->mati_kurang48 ?? 0;

        return HospitalIndicatorService::calculate(
            hariPerawatan: $totalHp,
            totalTempatTidur: $totalTt,
            jumlahHari: $jumlahHari,
            pasienKeluarHidup: max(0, $totalKeluar - $mati),
            pasienKeluarMati: $mati,
            totalLamaDirawatKeluar: $totalLama,
            pasienKeluarMatiKurang48: $matiKurang48,
        );
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
