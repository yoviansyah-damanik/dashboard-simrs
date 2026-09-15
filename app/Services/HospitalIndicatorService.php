<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Sumber tunggal perhitungan indikator pelayanan rawat inap (BOR, ALOS, BTO, TOI, NDR, GDR)
 * sesuai formula standar Depkes RI, dipakai ulang oleh dashboard, halaman Rekap Rawat Inap,
 * dan laporan SIRS (RL 3.1 & RL 1.2) agar tidak ada lagi rumus yang berbeda-beda antar modul.
 *
 * Service ini BERDIRI SENDIRI — tidak bergantung pada modul SIRS (SirsHelper/SirsFacilityRepository/
 * dkk). Sebaliknya, modul SIRS dan halaman Rekap Rawat Inap sama-sama bergantung ke service ini
 * sebagai satu-satunya sumber query data mentah DAN formula, supaya angkanya konsisten persis di
 * seluruh aplikasi untuk periode yang sama.
 */
class HospitalIndicatorService
{
    const KONEKSI = 'simrs';

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

    /**
     * Gabungkan indikator dari beberapa unit (mis. per bangsal, atau per periode) yang masing-masing
     * SUDAH dihitung lewat calculate(), menjadi satu angka gabungan via rata-rata tertimbang — BOR &
     * BTO ditimbang `bedWeight` (jumlah TT unit, keduanya berpenyebut TT), sedangkan ALOS/TOI/NDR/GDR
     * ditimbang `dischargeWeight` (jumlah pasien keluar unit, semuanya berpenyebut pasien keluar).
     *
     * Dipakai supaya nilai ekstrim di satu unit — yang sudah "dijinakkan" oleh clamp di calculate()
     * (mis. BOR satu bangsal kecil yang datanya tumpang tindih tidak bisa lebih dari 100%) — tidak
     * ikut mendistorsi angka gabungan lewat penjumlahan komponen mentah sebelum di-clamp. SATU-
     * SATUNYA cara resmi menggabungkan indikator antar unit di proyek ini (dipakai baik oleh
     * SirsFacilityRepository maupun Livewire\Inpatient\Recap) — jangan menjumlah komponen mentah
     * lalu memanggil calculate() sekali di level gabungan, karena hasilnya bisa berbeda dan tidak
     * konsisten dengan cara ini.
     *
     * @param iterable<array{indicators: array, bedWeight: int|float, dischargeWeight: int|float}> $units
     */
    public static function combineWeighted(iterable $units): array
    {
        $borWeighted = 0;
        $btoWeighted = 0;
        $bedWeightTotal = 0;
        $alosWeighted = 0;
        $toiWeighted = 0;
        $ndrWeighted = 0;
        $gdrWeighted = 0;
        $dischargeWeightTotal = 0;

        foreach ($units as $unit) {
            $ind = $unit['indicators'];
            $bedWeight = $unit['bedWeight'];
            $dischargeWeight = $unit['dischargeWeight'];

            $borWeighted += $ind['bor'] * $bedWeight;
            $btoWeighted += $ind['bto'] * $bedWeight;
            $bedWeightTotal += $bedWeight;

            $alosWeighted += $ind['alos'] * $dischargeWeight;
            $toiWeighted += $ind['toi'] * $dischargeWeight;
            $ndrWeighted += $ind['ndr'] * $dischargeWeight;
            $gdrWeighted += $ind['gdr'] * $dischargeWeight;
            $dischargeWeightTotal += $dischargeWeight;
        }

        return [
            'bor' => $bedWeightTotal > 0 ? round($borWeighted / $bedWeightTotal, 2) : 0,
            'bto' => $bedWeightTotal > 0 ? round($btoWeighted / $bedWeightTotal, 2) : 0,
            'alos' => $dischargeWeightTotal > 0 ? round($alosWeighted / $dischargeWeightTotal, 2) : 0,
            'toi' => $dischargeWeightTotal > 0 ? round($toiWeighted / $dischargeWeightTotal, 2) : 0,
            'ndr' => $dischargeWeightTotal > 0 ? round($ndrWeighted / $dischargeWeightTotal, 2) : 0,
            'gdr' => $dischargeWeightTotal > 0 ? round($gdrWeighted / $dischargeWeightTotal, 2) : 0,
        ];
    }

    /**
     * Data mentah per bangsal (nm_bangsal) untuk satu rentang tanggal: kapasitas TT aktif, hari
     * perawatan, dan pasien keluar (hidup/mati/mati<48jam/total lama dirawat). SATU-SATUNYA query
     * resmi dipakai untuk menghitung BOR/ALOS/BTO/TOI/NDR/GDR di seluruh aplikasi — laporan SIRS
     * (RL 1.2, Matriks Indikator Tahunan) maupun Rekap Rawat Inap — supaya angkanya konsisten
     * persis di semua tempat untuk periode yang sama. Rules: bangsal aktif, TT aktif
     * (statusdata='1'), bangsal TRANS dikecualikan; "keluar" disyaratkan tgl_keluar jatuh di dalam
     * [startDate, endDate]; "meninggal" memakai stts_pulang='Meninggal' langsung (definisi resmi
     * standar Depkes/SIRS).
     */
    public static function getWardRawData(string $startDate, string $endDate): array
    {
        $beds = collect(DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, COUNT(k.kd_kamar) as jumlah_tt
            FROM kamar k
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE b.status = '1' AND k.statusdata = '1' AND b.kd_bangsal <> 'TRANS'
            GROUP BY b.nm_bangsal
        "))->keyBy('nm_bangsal');

        $hariPerawatan = collect(DB::connection(self::KONEKSI)->select("
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

        $pasienKeluar = collect(DB::connection(self::KONEKSI)->select("
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

        $result = [];
        foreach ($beds as $nmBangsal => $bed) {
            $keluar = $pasienKeluar[$nmBangsal] ?? null;

            $result[$nmBangsal] = [
                'nm_bangsal' => $nmBangsal,
                'kapasitas' => $bed->jumlah_tt,
                'hari_perawatan' => $hariPerawatan[$nmBangsal]->total_hari_perawatan ?? 0,
                'pasien_keluar_total' => $keluar->total ?? 0,
                'pasien_keluar_mati' => $keluar->mati ?? 0,
                'pasien_keluar_mati_kurang48' => $keluar->mati_kurang48 ?? 0,
                'total_lama_dirawat' => $keluar->total_lama ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Hitung BOR/ALOS/BTO/TOI/NDR/GDR gabungan seluruh RS untuk satu rentang tanggal, langsung dari
     * data mentah per bangsal (getWardRawData()): tiap bangsal dihitung dulu lewat calculate(), lalu
     * digabung lewat combineWeighted(). SATU-SATUNYA titik masuk resmi untuk angka indikator
     * level-RS di seluruh aplikasi — dipakai baik oleh SirsFacilityRepository (RL 1.2, Matriks
     * Indikator Tahunan) maupun Livewire\Inpatient\Recap (KPI keseluruhan halaman Rekap Rawat
     * Inap), sehingga kedua halaman dijamin menghasilkan angka yang sama persis untuk periode yang
     * sama.
     */
    public static function computeForPeriod(string $startDate, string $endDate, int $jumlahHari): array
    {
        $units = [];
        foreach (self::getWardRawData($startDate, $endDate) as $ward) {
            $units[] = [
                'indicators' => self::calculate(
                    hariPerawatan: $ward['hari_perawatan'],
                    totalTempatTidur: $ward['kapasitas'],
                    jumlahHari: $jumlahHari,
                    pasienKeluarHidup: max(0, $ward['pasien_keluar_total'] - $ward['pasien_keluar_mati']),
                    pasienKeluarMati: $ward['pasien_keluar_mati'],
                    totalLamaDirawatKeluar: $ward['total_lama_dirawat'],
                    pasienKeluarMatiKurang48: $ward['pasien_keluar_mati_kurang48'],
                ),
                'bedWeight' => $ward['kapasitas'],
                'dischargeWeight' => $ward['pasien_keluar_total'],
            ];
        }

        return self::combineWeighted($units);
    }
}
