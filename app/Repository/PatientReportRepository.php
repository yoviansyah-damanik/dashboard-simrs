<?php

namespace App\Repository;

use App\Models\RegisteredPatient;
use Carbon\Carbon;
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
     * Menyusun ekspresi metrik (Pengunjung, Pengunjung Ralan, Pengunjung IGD, Rawat Jalan, IGD, Rawat Inap, Rujukan, Meninggal)
     * untuk satu kondisi kelompok pasien.
     */
    private static function metrikGolongan(string $prefix, string $kondisi, string $rp, string $stts, string $statusLanjut, string $kdPoli): array
    {
        return [
            "COUNT(DISTINCT CASE WHEN {$kondisi} THEN {$rp}.no_rkm_medis END) AS {$prefix}_p",
            "COUNT(DISTINCT CASE WHEN {$kondisi} AND {$statusLanjut} = 'Ralan' AND {$kdPoli} != 'IGDK' THEN {$rp}.no_rkm_medis END) AS {$prefix}_p_rj",
            "COUNT(DISTINCT CASE WHEN {$kondisi} AND {$kdPoli} = 'IGDK' THEN {$rp}.no_rkm_medis END) AS {$prefix}_p_igd",
            "COUNT(CASE WHEN {$kondisi} AND {$statusLanjut} = 'Ralan' AND {$kdPoli} != 'IGDK' THEN 1 END) AS {$prefix}_rj",
            "COUNT(CASE WHEN {$kondisi} AND {$kdPoli} = 'IGDK' THEN 1 END) AS {$prefix}_igd",
            "COUNT(CASE WHEN {$kondisi} AND {$statusLanjut} = 'Ranap' THEN 1 END) AS {$prefix}_ri",
            "COUNT(CASE WHEN {$kondisi} AND {$stts} = 'Dirujuk' THEN 1 END) AS {$prefix}_ruj",
            "COUNT(CASE WHEN {$kondisi} AND {$stts} = 'Meninggal' THEN 1 END) AS {$prefix}_men",
        ];
    }

    /**
     * Mengambil metrik yang sudah dihitung dari hasil query berdasarkan prefix alias.
     */
    private static function ambilMetrik(array $d, string $prefix): array
    {
        $pRj  = (int) ($d["{$prefix}_p_rj"] ?? 0);
        $pIgd = (int) ($d["{$prefix}_p_igd"] ?? 0);
        $rj   = (int) ($d["{$prefix}_rj"] ?? 0);
        $igd  = (int) ($d["{$prefix}_igd"] ?? 0);

        return [
            'pengunjung'             => (int) ($d["{$prefix}_p"] ?? 0),
            'pengunjung_ralan'       => $pRj,
            'pengunjung_igd'         => $pIgd,
            'pengunjung_total_ralan' => $pRj + $pIgd,
            'rawat_jalan'            => $rj,
            'igd'                    => $igd,
            'total_rawat_jalan'      => $rj + $igd,
            'rawat_inap'             => (int) ($d["{$prefix}_ri"] ?? 0),
            'rujukan'                => (int) ($d["{$prefix}_ruj"] ?? 0),
            'meninggal'              => (int) ($d["{$prefix}_men"] ?? 0),
        ];
    }

    /**
     * Menjumlahkan beberapa hasil ambilMetrik() menjadi satu total.
     */
    private static function jumlahkanMetrik(array $daftarMetrik): array
    {
        $total = [
            'pengunjung'             => 0,
            'pengunjung_ralan'       => 0,
            'pengunjung_igd'         => 0,
            'pengunjung_total_ralan' => 0,
            'rawat_jalan'            => 0,
            'igd'                    => 0,
            'total_rawat_jalan'      => 0,
            'rawat_inap'             => 0,
            'rujukan'                => 0,
            'meninggal'              => 0,
        ];
        foreach ($daftarMetrik as $metrik) {
            foreach ($total as $key => $_) {
                $total[$key] += ($metrik[$key] ?? 0);
            }
        }
        return $total;
    }

    /**
     * Merekap data kunjungan pasien per kelompok: TNI (per angkatan: Darat, Laut, Udara,
     * masing-masing dirinci Militer/ASN/Keluarga/Purnawirawan), POLRI (Anggota/ASN/Keluarga/
     * Purnawirawan), dan Pasien Umum. Metrik yang dihitung: Pengunjung,
     * Kunjungan (Rawat Jalan, IGD, Rawat Inap), Rujukan, dan Meninggal.
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
        $kdPoli = "{$rp}." . RegisteredPatient::KODE_POLIKLINIK;

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
            "COUNT(DISTINCT CASE WHEN {$statusLanjut} = 'Ralan' AND {$kdPoli} != 'IGDK' THEN {$rp}.no_rkm_medis END) AS total_p_rj",
            "COUNT(DISTINCT CASE WHEN {$kdPoli} = 'IGDK' THEN {$rp}.no_rkm_medis END) AS total_p_igd",
            "COUNT({$rp}.no_rawat) AS total_k",
            "COUNT(CASE WHEN {$statusLanjut} = 'Ralan' AND {$kdPoli} != 'IGDK' THEN 1 END) AS total_rj",
            "COUNT(CASE WHEN {$kdPoli} = 'IGDK' THEN 1 END) AS total_igd",
            "COUNT(CASE WHEN {$statusLanjut} = 'Ranap' THEN 1 END) AS total_ri",
            "COUNT(CASE WHEN {$stts} = 'Dirujuk' THEN 1 END) AS total_ruj",
            "COUNT(CASE WHEN {$stts} = 'Meninggal' THEN 1 END) AS total_men",
        ];
        foreach ($kondisi as $prefix => $syarat) {
            array_push($selects, ...self::metrikGolongan($prefix, $syarat, $rp, $stts, $statusLanjut, $kdPoli));
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

            'total_pengunjung'             => (int) $d['total_p'],
            'total_pengunjung_ralan'       => (int) $d['total_p_rj'],
            'total_pengunjung_igd'         => (int) $d['total_p_igd'],
            'total_pengunjung_total_ralan' => ((int) $d['total_p_rj']) + ((int) $d['total_p_igd']),
            'total_kunjungan'              => (int) $d['total_k'],
            'rawat_jalan'                  => (int) $d['total_rj'],
            'igd'                          => (int) $d['total_igd'],
            'total_rawat_jalan'            => ((int) $d['total_rj']) + ((int) $d['total_igd']),
            'rawat_inap'                   => (int) $d['total_ri'],
            'rujukan'                      => (int) $d['total_ruj'],
            'meninggal'                    => (int) $d['total_men'],
        ];
    }

    /**
     * Mengambil data tren kunjungan dan pengunjung berkala (harian atau bulanan).
     *
     * @param string|null $startDate Tanggal mulai (Y-m-d)
     * @param string|null $endDate   Tanggal akhir (Y-m-d)
     * @return array
     */
    public static function getTrendData(?string $startDate = null, ?string $endDate = null): array
    {
        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-t');
        }

        $startCarbon = Carbon::parse($startDate);
        $endCarbon   = Carbon::parse($endDate);
        $diffDays    = $startCarbon->diffInDays($endCarbon);
        $isDaily     = $diffDays <= 35;
        $format      = $isDaily ? '%Y-%m-%d' : '%Y-%m';

        $rp           = RegisteredPatient::getTableName();
        $tgl          = RegisteredPatient::TGL_REGISTRASI;
        $stts         = RegisteredPatient::STATUS_PELAYANAN;
        $statusLanjut = RegisteredPatient::STATUS_LANJUT;
        $rm           = RegisteredPatient::NO_REKAM_MEDIS;
        $rawat        = RegisteredPatient::NO_RAWAT;
        $kdPoli       = RegisteredPatient::KODE_POLIKLINIK;

        $rows = DB::connection(self::KONEKSI)
            ->table($rp)
            ->where("{$rp}.{$stts}", '!=', 'Batal')
            ->whereBetween("{$rp}.{$tgl}", [$startDate, $endDate])
            ->selectRaw("
                DATE_FORMAT({$rp}.{$tgl}, '{$format}') as periode,
                COUNT(DISTINCT {$rp}.{$rm}) as pengunjung,
                COUNT(DISTINCT CASE WHEN {$rp}.{$statusLanjut} = 'Ralan' AND {$rp}.{$kdPoli} != 'IGDK' THEN {$rp}.{$rm} END) as pengunjung_ralan,
                COUNT(DISTINCT CASE WHEN {$rp}.{$kdPoli} = 'IGDK' THEN {$rp}.{$rm} END) as pengunjung_igd,
                COUNT({$rp}.{$rawat}) as kunjungan,
                COUNT(CASE WHEN {$rp}.{$statusLanjut} = 'Ralan' AND {$rp}.{$kdPoli} != 'IGDK' THEN 1 END) as rawat_jalan,
                COUNT(CASE WHEN {$rp}.{$kdPoli} = 'IGDK' THEN 1 END) as igd,
                COUNT(CASE WHEN {$rp}.{$statusLanjut} = 'Ranap' THEN 1 END) as rawat_inap
            ")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        $labels          = [];
        $fullLabels      = [];
        $pengunjung      = [];
        $pengunjungRalan = [];
        $pengunjungIgd   = [];
        $kunjungan       = [];
        $rawatJalan      = [];
        $igd             = [];
        $rawatInap       = [];

        $totalRawatJalan      = [];
        $pengunjungTotalRalan = [];

        foreach ($rows as $row) {
            if ($isDaily) {
                $c = Carbon::parse($row->periode);
                $labels[]     = $c->format('d M');
                $fullLabels[] = $c->translatedFormat('d F Y');
            } else {
                $c = Carbon::parse($row->periode . '-01');
                $labels[]     = $c->translatedFormat('M Y');
                $fullLabels[] = $c->translatedFormat('F Y');
            }
            $pengunjung[]           = (int) $row->pengunjung;
            $pengunjungRalan[]      = (int) $row->pengunjung_ralan;
            $pengunjungIgd[]        = (int) $row->pengunjung_igd;
            $pengunjungTotalRalan[] = ((int) $row->pengunjung_ralan) + ((int) $row->pengunjung_igd);
            $kunjungan[]            = (int) $row->kunjungan;
            $rawatJalan[]           = (int) $row->rawat_jalan;
            $igd[]                  = (int) $row->igd;
            $totalRawatJalan[]      = ((int) $row->rawat_jalan) + ((int) $row->igd);
            $rawatInap[]            = (int) $row->rawat_inap;
        }

        $totalK = array_sum($kunjungan);
        $totalP = array_sum($pengunjung);
        $avgK   = count($kunjungan) > 0 ? round($totalK / count($kunjungan), 1) : 0;

        $maxKunjungan = 0;
        $maxPeriode   = '-';
        foreach ($kunjungan as $i => $val) {
            if ($val > $maxKunjungan) {
                $maxKunjungan = $val;
                $maxPeriode   = $fullLabels[$i] ?? ($labels[$i] ?? '-');
            }
        }

        $ratio = $totalP > 0 ? round($totalK / $totalP, 2) : 1;

        return [
            'isDaily'              => $isDaily,
            'mode'                 => $isDaily ? 'daily' : 'monthly',
            'labels'               => $labels,
            'fullLabels'           => $fullLabels,
            'pengunjung'           => $pengunjung,
            'pengunjungRalan'      => $pengunjungRalan,
            'pengunjungIgd'        => $pengunjungIgd,
            'pengunjungTotalRalan' => $pengunjungTotalRalan,
            'kunjungan'            => $kunjungan,
            'rawatJalan'           => $rawatJalan,
            'igd'                  => $igd,
            'totalRawatJalan'      => $totalRawatJalan,
            'rawatInap'            => $rawatInap,
            'totalKunjungan'       => $totalK,
            'totalPengunjung'      => $totalP,
            'avgKunjungan'         => $avgK,
            'maxKunjungan'         => $maxKunjungan,
            'maxPeriode'           => $maxPeriode,
            'stats'                => [
                'total_pengunjung'               => $totalP,
                'total_pengunjung_ralan'         => array_sum($pengunjungRalan),
                'total_pengunjung_igd'           => array_sum($pengunjungIgd),
                'total_pengunjung_total_ralan'   => array_sum($pengunjungTotalRalan),
                'total_kunjungan'                => $totalK,
                'total_rawat_jalan'              => array_sum($rawatJalan),
                'total_igd'                      => array_sum($igd),
                'total_rawat_jalan_akumulasi'    => array_sum($totalRawatJalan),
                'total_rawat_inap'               => array_sum($rawatInap),
                'avg_kunjungan_per_day'          => $avgK,
                'peak_kunjungan'                 => $maxKunjungan,
                'peak_date'                      => $maxPeriode,
                'rasio_kunjungan_per_pengunjung' => $ratio,
            ],
        ];
    }

    /**
     * Mengambil rekapitulasi data kunjungan dan pengunjung per bulan untuk tahun tertentu (1-12).
     *
     * @param int|string|null $year
     * @return array
     */
    public static function getMonthlyBreakdown(int|string|null $year = null): array
    {
        $year = (int) ($year ?: date('Y'));

        $rp           = RegisteredPatient::getTableName();
        $tgl          = RegisteredPatient::TGL_REGISTRASI;
        $stts         = "{$rp}." . RegisteredPatient::STATUS_PELAYANAN;
        $statusLanjut = "{$rp}." . RegisteredPatient::STATUS_LANJUT;
        $rm           = "{$rp}." . RegisteredPatient::NO_REKAM_MEDIS;
        $rawat        = "{$rp}." . RegisteredPatient::NO_RAWAT;
        $kdPoli       = "{$rp}." . RegisteredPatient::KODE_POLIKLINIK;

        $rows = DB::connection(self::KONEKSI)
            ->table($rp)
            ->leftJoin('pasien_tni as pt', 'pt.no_rkm_medis', '=', "{$rp}.no_rkm_medis")
            ->leftJoin('pasien_polri as pp', 'pp.no_rkm_medis', '=', "{$rp}.no_rkm_medis")
            ->where($stts, '!=', 'Batal')
            ->whereYear("{$rp}.{$tgl}", $year)
            ->selectRaw("
                MONTH({$rp}.{$tgl}) as bulan,
                COUNT(DISTINCT {$rm}) as pengunjung,
                COUNT(DISTINCT CASE WHEN {$statusLanjut} = 'Ralan' AND {$kdPoli} != 'IGDK' THEN {$rm} END) as pengunjung_ralan,
                COUNT(DISTINCT CASE WHEN {$kdPoli} = 'IGDK' THEN {$rm} END) as pengunjung_igd,
                COUNT({$rawat}) as kunjungan,
                COUNT(CASE WHEN {$statusLanjut} = 'Ralan' AND {$kdPoli} != 'IGDK' THEN 1 END) as rawat_jalan,
                COUNT(CASE WHEN {$kdPoli} = 'IGDK' THEN 1 END) as igd,
                COUNT(CASE WHEN {$statusLanjut} = 'Ranap' THEN 1 END) as rawat_inap,
                COUNT(CASE WHEN pt.no_rkm_medis IS NOT NULL THEN 1 END) as tni,
                COUNT(CASE WHEN pt.no_rkm_medis IS NULL AND pp.no_rkm_medis IS NOT NULL THEN 1 END) as polri,
                COUNT(CASE WHEN pt.no_rkm_medis IS NULL AND pp.no_rkm_medis IS NULL THEN 1 END) as umum,
                COUNT(CASE WHEN {$stts} = 'Dirujuk' THEN 1 END) as rujukan,
                COUNT(CASE WHEN {$stts} = 'Meninggal' THEN 1 END) as meninggal
            ")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $items = [];
        $totalPengunjung      = 0;
        $totalPengunjungRalan = 0;
        $totalPengunjungIgd   = 0;
        $totalKunjungan       = 0;
        $totalRawatJalan      = 0;
        $totalIgd             = 0;
        $totalRawatInap       = 0;
        $totalTni             = 0;
        $totalPolri           = 0;
        $totalUmum            = 0;
        $totalRujukan         = 0;
        $totalMeninggal       = 0;

        foreach ($monthNames as $m => $namaBulan) {
            $row             = $rows->get($m);
            $pengunjung      = (int) ($row->pengunjung ?? 0);
            $pengunjungRalan = (int) ($row->pengunjung_ralan ?? 0);
            $pengunjungIgd   = (int) ($row->pengunjung_igd ?? 0);
            $kunjungan       = (int) ($row->kunjungan ?? 0);
            $rawatJalan      = (int) ($row->rawat_jalan ?? 0);
            $igd             = (int) ($row->igd ?? 0);
            $rawatInap       = (int) ($row->rawat_inap ?? 0);
            $tni             = (int) ($row->tni ?? 0);
            $polri           = (int) ($row->polri ?? 0);
            $umum            = (int) ($row->umum ?? 0);
            $rujukan         = (int) ($row->rujukan ?? 0);
            $meninggal       = (int) ($row->meninggal ?? 0);

            $items[$m] = [
                'bulan'                  => $m,
                'nama_bulan'             => $namaBulan,
                'pengunjung'             => $pengunjung,
                'pengunjung_ralan'       => $pengunjungRalan,
                'pengunjung_igd'         => $pengunjungIgd,
                'pengunjung_total_ralan' => $pengunjungRalan + $pengunjungIgd,
                'kunjungan'              => $kunjungan,
                'rawat_jalan'            => $rawatJalan,
                'igd'                    => $igd,
                'total_rawat_jalan'      => $rawatJalan + $igd,
                'rawat_inap'             => $rawatInap,
                'tni'                    => $tni,
                'polri'                  => $polri,
                'umum'                   => $umum,
                'rujukan'                => $rujukan,
                'meninggal'              => $meninggal,
            ];

            $totalPengunjung      += $pengunjung;
            $totalPengunjungRalan += $pengunjungRalan;
            $totalPengunjungIgd   += $pengunjungIgd;
            $totalKunjungan       += $kunjungan;
            $totalRawatJalan      += $rawatJalan;
            $totalIgd             += $igd;
            $totalRawatInap       += $rawatInap;
            $totalTni             += $tni;
            $totalPolri           += $polri;
            $totalUmum            += $umum;
            $totalRujukan         += $rujukan;
            $totalMeninggal       += $meninggal;
        }

        return [
            'year'   => $year,
            'months' => $items,
            'totals' => [
                'pengunjung'              => $totalPengunjung,
                'pengunjung_ralan'        => $totalPengunjungRalan,
                'pengunjung_igd'          => $totalPengunjungIgd,
                'pengunjung_total_ralan'  => $totalPengunjungRalan + $totalPengunjungIgd,
                'kunjungan'               => $totalKunjungan,
                'rawat_jalan'             => $totalRawatJalan,
                'igd'                     => $totalIgd,
                'total_rawat_jalan'       => $totalRawatJalan + $totalIgd,
                'rawat_inap'              => $totalRawatInap,
                'tni'                     => $totalTni,
                'polri'                   => $totalPolri,
                'umum'                    => $totalUmum,
                'rujukan'                 => $totalRujukan,
                'meninggal'               => $totalMeninggal,
                'avg_kunjungan_per_bulan' => round($totalKunjungan / 12, 1),
            ],
        ];
    }
}
