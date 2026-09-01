<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

interface SirsPharmacyInterface
{
    public static function getRL317(int $tahun): array;
    public static function getRL318(int $tahun): array;
}

class SirsPharmacyRepository implements SirsPharmacyInterface
{
    const KONEKSI = 'simrs';

    /** RL 3.17 - Farmasi Pengadaan Obat/BHP Medis (Tahunan, top 50) */
    public static function getRL317(int $tahun): array
    {
        $rows = DB::connection(self::KONEKSI)->select("
            SELECT
                db.kode_sat,
                db.nama_brng,
                db.kode_kategori,
                COALESCE(SUM(dsp.jumlah), 0) as jumlah_pengadaan
            FROM databarang db
            INNER JOIN detail_surat_pemesanan_medis dsp ON db.kode_brng = dsp.kode_brng
            INNER JOIN surat_pemesanan_medis sp ON dsp.no_pemesanan = sp.no_pemesanan
            WHERE YEAR(sp.tanggal) = ?
            AND db.status = '1'
            GROUP BY db.kode_brng, db.kode_sat, db.nama_brng, db.kode_kategori
            HAVING jumlah_pengadaan > 0
            ORDER BY jumlah_pengadaan DESC
            LIMIT 50
        ", [$tahun]);

        return collect($rows)->map(fn($r) => (array) $r)->toArray();
    }

    /** RL 3.18 - Farmasi Resep (Tahunan) */
    public static function getRL318(int $tahun): array
    {
        $resep = DB::connection(self::KONEKSI)->select("
            SELECT
                CASE WHEN rp.status_lanjut = 'Ranap' THEN 'ranap' ELSE 'ralan' END as tipe,
                COUNT(DISTINCT ro.no_resep) as jumlah_resep
            FROM resep_obat ro
            INNER JOIN reg_periksa rp ON ro.no_rawat = rp.no_rawat
            WHERE YEAR(ro.tgl_peresepan) = ?
            GROUP BY tipe
        ", [$tahun]);

        $data = ['ralan' => 0, 'ranap' => 0, 'total' => 0];
        foreach ($resep as $row) {
            $data[$row->tipe] = $row->jumlah_resep;
        }
        $data['total'] = $data['ralan'] + $data['ranap'];

        return $data;
    }
}
