<?php

namespace App\Repository;

use App\Models\RegisteredPatient;
use Illuminate\Support\Facades\DB;

interface PatientReportInterface {}

class PatientReportRepository implements PatientReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Mapping golongan_tni (id) per angkatan, dari tabel golongan_tni di database SIMRS.
     * Setiap angkatan memiliki 4 rincian: Militer, ASN, Keluarga, Purnawirawan.
     */
    const TNI_GROUPS = [
        'ad' => ['nama' => 'Angkatan Darat',  'mil' => 3, 'asn' => 8,  'kel' => 5, 'purn' => 4],
        'al' => ['nama' => 'Angkatan Laut',   'mil' => 2, 'asn' => 10, 'kel' => 6, 'purn' => 11],
        'au' => ['nama' => 'Angkatan Udara',  'mil' => 1, 'asn' => 9,  'kel' => 7, 'purn' => 12],
    ];

    // Label rincian TNI, urut sesuai tampilan tabel (a, b, c, d)
    const TNI_RINCIAN_LABEL = [
        'mil'  => 'Militer',
        'asn'  => 'ASN',
        'kel'  => 'Keluarga',
        'purn' => 'Purnawirawan',
    ];

    /**
     * Mapping golongan_polri (id), dari tabel golongan_polri di database SIMRS.
     */
    const POLRI_GROUPS = [
        'anggota'      => 1,
        'asn'          => 2,
        'keluarga'     => 3,
        'purnawirawan' => 4,
    ];

    const POLRI_RINCIAN_LABEL = [
        'anggota'      => 'Anggota',
        'asn'          => 'ASN',
        'keluarga'     => 'Keluarga',
        'purnawirawan' => 'Purnawirawan',
    ];

    /**
     * Menyusun 5 ekspresi metrik (Pengunjung, Rawat Jalan, Rawat Inap, Rujukan, Meninggal)
     * untuk satu kondisi kelompok pasien.
     */
    private static function metrikGolongan(string $prefix, string $kondisi, string $rp, string $stts, string $statusLanjut): array
    {
        return [
            "COUNT(DISTINCT CASE WHEN {$kondisi} THEN {$rp}.no_rkm_medis END) AS {$prefix}_p",
            "COUNT(CASE WHEN {$kondisi} AND {$statusLanjut} = 'Ralan' THEN 1 END) AS {$prefix}_rj",
            "COUNT(CASE WHEN {$kondisi} AND {$statusLanjut} = 'Ranap' THEN 1 END) AS {$prefix}_ri",
            "COUNT(CASE WHEN {$kondisi} AND {$stts} = 'Dirujuk' THEN 1 END) AS {$prefix}_ruj",
            "COUNT(CASE WHEN {$kondisi} AND {$stts} = 'Meninggal' THEN 1 END) AS {$prefix}_men",
        ];
    }

    /**
     * Mengambil 5 metrik yang sudah dihitung dari hasil query berdasarkan prefix alias.
     */
    private static function ambilMetrik(array $d, string $prefix): array
    {
        return [
            'pengunjung'  => (int) $d["{$prefix}_p"],
            'rawat_jalan' => (int) $d["{$prefix}_rj"],
            'rawat_inap'  => (int) $d["{$prefix}_ri"],
            'rujukan'     => (int) $d["{$prefix}_ruj"],
            'meninggal'   => (int) $d["{$prefix}_men"],
        ];
    }

    /**
     * Menjumlahkan beberapa hasil ambilMetrik() (boleh disisipi key 'label') menjadi satu total.
     */
    private static function jumlahkanMetrik(array $daftarMetrik): array
    {
        $total = ['pengunjung' => 0, 'rawat_jalan' => 0, 'rawat_inap' => 0, 'rujukan' => 0, 'meninggal' => 0];
        foreach ($daftarMetrik as $metrik) {
            foreach ($total as $key => $_) {
                $total[$key] += $metrik[$key];
            }
        }
        return $total;
    }

    /**
     * Merekap data kunjungan pasien per kelompok: TNI (per angkatan: Darat, Laut, Udara,
     * masing-masing dirinci Militer/ASN/Keluarga/Purnawirawan), POLRI (Anggota/ASN/Keluarga/
     * Purnawirawan), dan Pasien Umum. Metrik yang dihitung: Pengunjung (distinct pasien),
     * Kunjungan (Rawat Jalan & Rawat Inap), Rujukan (stts = 'Dirujuk'), dan Meninggal
     * (stts = 'Meninggal').
     *
     * @param string|null $startDate Tanggal mulai (Y-m-d)
     * @param string|null $endDate   Tanggal akhir (Y-m-d)
     * @return array
     */
    public static function getSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $rp = RegisteredPatient::getTableName();
        $tgl = RegisteredPatient::TGL_REGISTRASI;
        $stts = "{$rp}." . RegisteredPatient::STATUS_PELAYANAN;
        $statusLanjut = "{$rp}." . RegisteredPatient::STATUS_LANJUT;

        // Susun kondisi SQL per kelompok: TNI per rincian angkatan, POLRI per golongan, dan Umum.
        $kondisi = [];
        foreach (self::TNI_GROUPS as $angkatanKey => $angkatan) {
            foreach (self::TNI_RINCIAN_LABEL as $rincianKey => $label) {
                $id = $angkatan[$rincianKey];
                $kondisi["tni_{$angkatanKey}_{$rincianKey}"] = "pt.golongan_tni = {$id}";
            }
        }
        foreach (self::POLRI_GROUPS as $rincianKey => $id) {
            $kondisi["polri_{$rincianKey}"] = "pt.no_rkm_medis IS NULL AND pp.golongan_polri = {$id}";
        }
        $kondisi['umum'] = "pt.no_rkm_medis IS NULL AND pp.no_rkm_medis IS NULL";

        $selects = [
            "COUNT(DISTINCT {$rp}.no_rkm_medis) AS total_p",
            "COUNT({$rp}.no_rawat) AS total_k",
            "COUNT(CASE WHEN {$statusLanjut} = 'Ralan' THEN 1 END) AS total_rj",
            "COUNT(CASE WHEN {$statusLanjut} = 'Ranap' THEN 1 END) AS total_ri",
            "COUNT(CASE WHEN {$stts} = 'Dirujuk' THEN 1 END) AS total_ruj",
            "COUNT(CASE WHEN {$stts} = 'Meninggal' THEN 1 END) AS total_men",
        ];
        foreach ($kondisi as $prefix => $syarat) {
            array_push($selects, ...self::metrikGolongan($prefix, $syarat, $rp, $stts, $statusLanjut));
        }

        $row = DB::connection(self::KONEKSI)
            ->table($rp)
            ->leftJoin('pasien_tni as pt', "pt.no_rkm_medis", '=', "{$rp}.no_rkm_medis")
            ->leftJoin('pasien_polri as pp', "pp.no_rkm_medis", '=', "{$rp}.no_rkm_medis")
            ->where($stts, '!=', 'Batal')
            ->when($startDate, fn($q) => $q->where("{$rp}.{$tgl}", '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where("{$rp}.{$tgl}", '<=', $endDate))
            ->selectRaw(implode(', ', $selects))
            ->first();

        $d = (array) $row;

        // --- Susun rincian & total per angkatan TNI ---
        $tni = [];
        foreach (self::TNI_GROUPS as $angkatanKey => $angkatan) {
            $rincian = [];
            foreach (self::TNI_RINCIAN_LABEL as $rincianKey => $label) {
                $rincian[$rincianKey] = [
                    'label' => $label,
                    ...self::ambilMetrik($d, "tni_{$angkatanKey}_{$rincianKey}"),
                ];
            }
            $tni[$angkatanKey] = [
                'nama'    => $angkatan['nama'],
                'rincian' => $rincian,
                'total'   => self::jumlahkanMetrik($rincian),
            ];
        }

        // --- Susun rincian & total POLRI ---
        $polriRincian = [];
        foreach (self::POLRI_GROUPS as $rincianKey => $id) {
            $polriRincian[$rincianKey] = [
                'label' => self::POLRI_RINCIAN_LABEL[$rincianKey],
                ...self::ambilMetrik($d, "polri_{$rincianKey}"),
            ];
        }

        return [
            'tni' => [
                'angkatan' => $tni,
                'total'    => self::jumlahkanMetrik(array_column($tni, 'total')),
            ],
            'polri' => [
                'rincian' => $polriRincian,
                'total'   => self::jumlahkanMetrik($polriRincian),
            ],
            'umum' => self::ambilMetrik($d, 'umum'),

            'total_pengunjung' => (int) $d['total_p'],
            'total_kunjungan'  => (int) $d['total_k'],
            'rawat_jalan'      => (int) $d['total_rj'],
            'rawat_inap'       => (int) $d['total_ri'],
            'rujukan'          => (int) $d['total_ruj'],
            'meninggal'        => (int) $d['total_men'],
        ];
    }
}
