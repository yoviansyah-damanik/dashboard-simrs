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

        return self::computeIndicatorsForPeriod($startDate, $endDate, $jumlahHari);
    }

    /**
     * Matriks indikator pelayanan rawat inap (BOR/ALOS/BTO/TOI/NDR/GDR) per bulan untuk satu tahun,
     * seluruh RS. Key 1-12 = bulan, key 'tahun' = angka tahunan penuh (sama dengan getRL12()).
     */
    public static function getYearlyIndicatorMatrix(int $tahun): array
    {
        $months = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $range = SirsHelper::getDateRange($tahun, $bulan);
            $months[$bulan] = self::computeIndicatorsForPeriod($range['start'], $range['end'], $range['jumlah_hari']);
        }

        $months['tahun'] = self::getRL12($tahun);

        return $months;
    }

    /**
     * Hitung BOR/ALOS/BTO/TOI/NDR/GDR seluruh RS untuk satu rentang tanggal, dengan cara: hitung
     * kapasitas TT, hari-perawatan, dan pasien-keluar PER BANGSAL dulu (rules seragam: bangsal
     * aktif, TT aktif/statusdata='1', bangsal TRANS dikecualikan — sama seperti
     * SirsHelper::getBedsPerWard(excludeTr: true) yang jadi acuan kapasitas), baru komponen
     * mentahnya dijumlahkan ke level RS untuk periode ini (bukan dirata-rata dari persentase per
     * bangsal, supaya BOR/ALOS/dst tetap benar secara matematis). Dipakai bersama oleh getRL12()
     * dan getYearlyIndicatorMatrix().
     *
     * Sebelumnya kapasitas TT dihitung per-bangsal dengan rules ketat ini, tapi hari-perawatan &
     * pasien-keluar dihitung longgar (hanya filter bangsal aktif, ikut memasukkan bangsal TRANS
     * dan TT nonaktif) — pembilang dan penyebut tidak konsisten. Method ini menyamakan keduanya.
     */
    private static function computeIndicatorsForPeriod(string $startDate, string $endDate, int $jumlahHari): array
    {
        $bedsPerWard = collect(SirsHelper::getBedsPerWard(excludeTr: true))->keyBy('nm_bangsal');

        $hariPerawatanPerWard = collect(DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, SUM(ki.lama) as total_hari_perawatan
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE (ki.tgl_masuk BETWEEN ? AND ?
                   OR ki.tgl_keluar BETWEEN ? AND ?
                   OR (ki.tgl_masuk <= ? AND (ki.tgl_keluar IS NULL OR ki.tgl_keluar >= ?)))
            AND b.status = '1' AND k.statusdata = '1' AND b.kd_bangsal <> 'TRANS'
            GROUP BY b.nm_bangsal
        ", [$startDate, $endDate, $startDate, $endDate, $endDate, $startDate]))->keyBy('nm_bangsal');

        $pasienKeluarPerWard = collect(DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal,
                COUNT(*) as total,
                SUM(ki.lama) as total_lama,
                SUM(CASE WHEN ki.stts_pulang = 'Meninggal' THEN 1 ELSE 0 END) as mati,
                SUM(CASE WHEN ki.stts_pulang = 'Meninggal'
                    AND TIMESTAMPDIFF(HOUR, CONCAT(ki.tgl_masuk, ' ', ki.jam_masuk), CONCAT(ki.tgl_keluar, ' ', ki.jam_keluar)) < 48
                    THEN 1 ELSE 0 END) as mati_kurang48
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_keluar BETWEEN ? AND ?
            AND b.status = '1' AND k.statusdata = '1' AND b.kd_bangsal <> 'TRANS'
            GROUP BY b.nm_bangsal
        ", [$startDate, $endDate]))->keyBy('nm_bangsal');

        $totalTt = 0;
        $totalHp = 0;
        $totalKeluar = 0;
        $totalLama = 0;
        $mati = 0;
        $matiKurang48 = 0;

        foreach ($bedsPerWard as $nmBangsal => $bed) {
            $totalTt += $bed->jumlah_tt;
            $totalHp += $hariPerawatanPerWard[$nmBangsal]->total_hari_perawatan ?? 0;

            $keluarWard = $pasienKeluarPerWard[$nmBangsal] ?? null;
            $totalKeluar += $keluarWard->total ?? 0;
            $totalLama += $keluarWard->total_lama ?? 0;
            $mati += $keluarWard->mati ?? 0;
            $matiKurang48 += $keluarWard->mati_kurang48 ?? 0;
        }

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
