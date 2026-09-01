<?php

namespace App\Repository;

use App\Helpers\SirsHelper;
use Illuminate\Support\Facades\DB;

interface SirsIndicatorInterface
{
    public static function getRL31(int $tahun, int $bulan): array;
    public static function getRL32(int $tahun, int $bulan): array;
}

class SirsIndicatorRepository implements SirsIndicatorInterface
{
    const KONEKSI = 'simrs';

    /** RL 3.1 - Indikator Pelayanan Rawat Inap */
    public static function getRL31(int $tahun, int $bulan): array
    {
        $range = SirsHelper::getDateRange($tahun, $bulan);
        $startDate = $range['start'];
        $endDate = $range['end'];
        $jumlahHari = $range['jumlah_hari'];

        $kategori = [];
        foreach (SirsHelper::getKategoriRL31Labels() as $id => $nama) {
            $kategori[$id] = [
                'nama' => $nama,
                'tt' => 0,
                'hari_perawatan' => 0,
                'pasien_keluar_hidup' => 0,
                'pasien_keluar_mati' => 0,
                'pasien_keluar_mati_kurang48' => 0,
                'total_lama_dirawat' => 0,
                'pasien_keluar_total' => 0,
            ];
        }

        foreach (SirsHelper::getBedsPerWard(excludeTr: true) as $row) {
            $katId = SirsHelper::mapWardToRL31Category($row->nm_bangsal);
            $kategori[$katId]['tt'] += $row->jumlah_tt;
        }

        $hariPerawatan = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, SUM(ki.lama) as total_hari_perawatan
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE (ki.tgl_masuk BETWEEN ? AND ?
                   OR ki.tgl_keluar BETWEEN ? AND ?
                   OR (ki.tgl_masuk <= ? AND (ki.tgl_keluar IS NULL OR ki.tgl_keluar >= ?)))
            AND b.status = '1'
            GROUP BY b.nm_bangsal
        ", [$startDate, $endDate, $startDate, $endDate, $endDate, $startDate]);

        foreach ($hariPerawatan as $row) {
            $katId = SirsHelper::mapWardToRL31Category($row->nm_bangsal);
            $kategori[$katId]['hari_perawatan'] += $row->total_hari_perawatan;
        }

        $pasienKeluar = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, ki.stts_pulang, p.jk,
                CASE
                    WHEN TIMESTAMPDIFF(HOUR, CONCAT(ki.tgl_masuk, ' ', ki.jam_masuk), CONCAT(ki.tgl_keluar, ' ', ki.jam_keluar)) < 48 THEN 'kurang48'
                    ELSE 'lebih48'
                END as waktu,
                COUNT(DISTINCT ki.no_rawat) as jumlah,
                SUM(ki.lama) as total_lama
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            INNER JOIN reg_periksa rp ON ki.no_rawat = rp.no_rawat
            INNER JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            WHERE ki.tgl_keluar BETWEEN ? AND ?
            AND b.status = '1'
            GROUP BY b.nm_bangsal, ki.stts_pulang, p.jk, waktu
        ", [$startDate, $endDate]);

        foreach ($pasienKeluar as $row) {
            $katId = SirsHelper::mapWardToRL31Category($row->nm_bangsal);

            if ($row->stts_pulang === 'Meninggal') {
                $kategori[$katId]['pasien_keluar_mati'] += $row->jumlah;
                if ($row->waktu === 'kurang48') {
                    $kategori[$katId]['pasien_keluar_mati_kurang48'] += $row->jumlah;
                }
            } else {
                $kategori[$katId]['pasien_keluar_hidup'] += $row->jumlah;
            }

            $kategori[$katId]['pasien_keluar_total'] += $row->jumlah;
            $kategori[$katId]['total_lama_dirawat'] += $row->total_lama;
        }

        $result = [];
        foreach ($kategori as $id => $data) {
            $row = ['nama' => $data['nama'], 'bor' => 0, 'alos' => 0, 'bto' => 0, 'toi' => 0, 'ndr' => 0, 'gdr' => 0];

            if ($data['tt'] > 0 && $data['pasien_keluar_total'] > 0) {
                $row['bor'] = round(($data['hari_perawatan'] / ($data['tt'] * $jumlahHari)) * 100, 2);
                $row['alos'] = round($data['total_lama_dirawat'] / $data['pasien_keluar_total'], 2);
                $row['bto'] = round($data['pasien_keluar_total'] / $data['tt'], 2);
                $row['toi'] = round((($data['tt'] * $jumlahHari) - $data['hari_perawatan']) / $data['pasien_keluar_total'], 2);
                $row['ndr'] = round(($data['pasien_keluar_mati_kurang48'] / $data['pasien_keluar_total']), 2);
                $row['gdr'] = round(($data['pasien_keluar_mati'] / $data['pasien_keluar_total']), 2);
            }

            $result[$id] = $row;
        }

        $total = ['bor' => 0, 'alos' => 0, 'bto' => 0, 'toi' => 0, 'ndr' => 0, 'gdr' => 0];
        $count = ['bor' => 0, 'alos' => 0, 'bto' => 0, 'toi' => 0, 'ndr' => 0, 'gdr' => 0];

        foreach ($result as $row) {
            foreach (['bor', 'alos', 'bto', 'toi', 'ndr', 'gdr'] as $key) {
                if ($row[$key] > 0) {
                    $total[$key] += $row[$key];
                    $count[$key]++;
                }
            }
        }

        $rataRata = ['nama' => 'Rata-rata'];
        foreach ($total as $key => $val) {
            $rataRata[$key] = $count[$key] > 0 ? round($val / $count[$key], 2) : 0;
        }
        $result[99] = $rataRata;

        return $result;
    }

    /** RL 3.2 - Rekapitulasi Kegiatan Pelayanan Rawat Inap */
    public static function getRL32(int $tahun, int $bulan): array
    {
        $range = SirsHelper::getDateRange($tahun, $bulan);
        $startDate = $range['start'];
        $endDate = $range['end'];
        $labels = SirsHelper::getServiceTypeLabels();

        $data = [];
        $fields = [
            'pasien_awal',
            'pasien_masuk',
            'pasien_pindahan',
            'pasien_dipindahkan',
            'pasien_keluar_hidup',
            'pasien_laki_mati_kurang48',
            'pasien_laki_mati_lebih48',
            'pasien_perempuan_mati_kurang48',
            'pasien_perempuan_mati_lebih48',
            'jumlah_lama_dirawat',
            'pasien_akhir',
            'jumlah_hari_perawatan',
            'hari_perawatan_vvip',
            'hari_perawatan_vip',
            'hari_perawatan_i',
            'hari_perawatan_ii',
            'hari_perawatan_iii',
            'hari_perawatan_khusus',
            'tempat_tidur',
        ];

        for ($i = 1; $i <= 36; $i++) {
            $data[$i] = ['nama' => $labels[$i] ?? "Jenis $i"];
            foreach ($fields as $f) {
                $data[$i][$f] = 0;
            }
        }

        $lastMonthEnd = date('Y-m-t', strtotime("$startDate -1 month"));
        $pasienAwal = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, COUNT(DISTINCT ki.no_rawat) as jumlah
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_masuk <= ? AND (ki.tgl_keluar IS NULL OR ki.tgl_keluar > ?)
            AND b.status = '1'
            GROUP BY b.nm_bangsal
        ", [$lastMonthEnd, $lastMonthEnd]);

        foreach ($pasienAwal as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis >= 1 && $jenis <= 36)
                $data[$jenis]['pasien_awal'] += $row->jumlah;
        }

        $pasienMasuk = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, COUNT(DISTINCT ki.no_rawat) as jumlah
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_masuk BETWEEN ? AND ? AND b.status = '1'
            GROUP BY b.nm_bangsal
        ", [$startDate, $endDate]);

        foreach ($pasienMasuk as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis >= 1 && $jenis <= 36)
                $data[$jenis]['pasien_masuk'] += $row->jumlah;
        }

        $pasienKeluarHidup = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, COUNT(DISTINCT ki.no_rawat) as jumlah
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_keluar BETWEEN ? AND ?
            AND ki.stts_pulang IN ('Sehat','Rujuk','Sembuh','Membaik','Pulang Paksa','Atas Persetujuan Dokter','Atas Permintaan Sendiri')
            AND b.status = '1'
            GROUP BY b.nm_bangsal
        ", [$startDate, $endDate]);

        foreach ($pasienKeluarHidup as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis >= 1 && $jenis <= 36)
                $data[$jenis]['pasien_keluar_hidup'] += $row->jumlah;
        }

        $pasienMati = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, p.jk,
                CASE
                    WHEN TIMESTAMPDIFF(HOUR, CONCAT(ki.tgl_masuk, ' ', ki.jam_masuk), CONCAT(ki.tgl_keluar, ' ', ki.jam_keluar)) < 48 THEN 'kurang48'
                    ELSE 'lebih48'
                END as waktu,
                COUNT(*) as jumlah
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            INNER JOIN reg_periksa rp ON ki.no_rawat = rp.no_rawat
            INNER JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            WHERE ki.tgl_keluar BETWEEN ? AND ?
            AND ki.stts_pulang = 'Meninggal' AND b.status = '1'
            GROUP BY b.nm_bangsal, p.jk, waktu
        ", [$startDate, $endDate]);

        foreach ($pasienMati as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis < 1 || $jenis > 36)
                continue;

            $prefix = $row->jk === 'L' ? 'pasien_laki' : 'pasien_perempuan';
            $suffix = $row->waktu === 'kurang48' ? '_mati_kurang48' : '_mati_lebih48';
            $data[$jenis][$prefix . $suffix] += $row->jumlah;
        }

        $lamaDirawat = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, SUM(ki.lama) as total_lama
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_keluar BETWEEN ? AND ? AND b.status = '1'
            GROUP BY b.nm_bangsal
        ", [$startDate, $endDate]);

        foreach ($lamaDirawat as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis >= 1 && $jenis <= 36)
                $data[$jenis]['jumlah_lama_dirawat'] += $row->total_lama;
        }

        $hariPerKelas = DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, k.kelas, SUM(ki.lama) as total_hari
            FROM kamar_inap ki
            INNER JOIN kamar k ON ki.kd_kamar = k.kd_kamar
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE ki.tgl_masuk BETWEEN ? AND ? AND b.status = '1'
            GROUP BY b.nm_bangsal, k.kelas
        ", [$startDate, $endDate]);

        foreach ($hariPerKelas as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis < 1 || $jenis > 36)
                continue;

            $kelasMap = [
                'Kelas VVIP' => 'hari_perawatan_vvip',
                'Kelas VIP' => 'hari_perawatan_vip',
                'Kelas Utama' => 'hari_perawatan_i',
                'Kelas 1' => 'hari_perawatan_i',
                'Kelas 2' => 'hari_perawatan_ii',
                'Kelas 3' => 'hari_perawatan_iii',
            ];

            $field = $kelasMap[$row->kelas] ?? 'hari_perawatan_khusus';
            $data[$jenis][$field] += $row->total_hari;
            $data[$jenis]['jumlah_hari_perawatan'] += $row->total_hari;
        }

        foreach (SirsHelper::getBedsPerWard(excludeTr: true) as $row) {
            $jenis = SirsHelper::mapWardToServiceType($row->nm_bangsal);
            if ($jenis >= 1 && $jenis <= 35)
                $data[$jenis]['tempat_tidur'] += $row->jumlah_tt;
        }

        for ($i = 1; $i <= 35; $i++) {
            $d = $data[$i];
            $mati = $d['pasien_laki_mati_kurang48'] + $d['pasien_laki_mati_lebih48']
                + $d['pasien_perempuan_mati_kurang48'] + $d['pasien_perempuan_mati_lebih48'];
            $data[$i]['pasien_akhir'] = max(
                0,
                $d['pasien_awal'] + $d['pasien_masuk'] + $d['pasien_pindahan']
                - ($d['pasien_keluar_hidup'] + $mati + $d['pasien_dipindahkan'])
            );
        }

        $data[99] = ['nama' => 'TOTAL'];
        foreach ($fields as $f) {
            $data[99][$f] = 0;
        }
        for ($i = 1; $i <= 35; $i++) {
            foreach ($fields as $f) {
                $data[99][$f] += $data[$i][$f];
            }
        }

        return $data;
    }
}
