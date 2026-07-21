<?php

namespace App\Repository;

use App\Models\RegisteredPatient;
use Illuminate\Support\Facades\DB;

interface PatientReportInterface {}

class PatientReportRepository implements PatientReportInterface
{
    const KONEKSI = 'simrs';

    // Mapping golongan_tni (id) dari tabel golongan_tni di database SIMRS
    const GOLONGAN_AD_MIL = [3];        // TNI AD
    const GOLONGAN_AD_PNS = [8];        // PNS TNI AD
    const GOLONGAN_AD_KEL = [5];        // Keluarga TNI AD
    const GOLONGAN_AL_MIL = [1, 2];     // TNI AU (1), TNI AL (2)
    const GOLONGAN_AL_PNS = [9, 10];    // PNS TNI AU (9), PNS TNI AL (10)
    const GOLONGAN_AL_KEL = [6, 7];     // Keluarga TNI AL (6), Keluarga TNI AU (7)
    const GOLONGAN_PURN   = [4];        // Purnawirawan

    /**
     * Menyusun ekspresi CASE WHEN untuk COUNT pengunjung (DISTINCT no_rkm_medis) per golongan.
     */
    private static function caseDistinct(string $alias, array $golongan, string $rp): string
    {
        $ids = implode(',', $golongan);
        return "COUNT(DISTINCT CASE WHEN pt.golongan_tni IN ({$ids}) THEN {$rp}.no_rkm_medis END) AS {$alias}";
    }

    /**
     * Menyusun ekspresi CASE WHEN untuk COUNT kunjungan (semua baris) per golongan.
     */
    private static function caseCount(string $alias, array $golongan): string
    {
        $ids = implode(',', $golongan);
        return "COUNT(CASE WHEN pt.golongan_tni IN ({$ids}) THEN 1 END) AS {$alias}";
    }

    /**
     * COUNT rujukan per golongan: stts = 'Dirujuk' AND golongan IN (...).
     */
    private static function caseRujukan(string $alias, array $golongan, string $stts): string
    {
        $ids = implode(',', $golongan);
        return "COUNT(CASE WHEN pt.golongan_tni IN ({$ids}) AND {$stts} = 'Dirujuk' THEN 1 END) AS {$alias}";
    }

    /**
     * COUNT rawat inap per golongan: status_lanjut = 'Ranap' AND golongan IN (...).
     */
    private static function caseRanap(string $alias, array $golongan, string $statusLanjut): string
    {
        $ids = implode(',', $golongan);
        return "COUNT(CASE WHEN pt.golongan_tni IN ({$ids}) AND {$statusLanjut} = 'Ranap' THEN 1 END) AS {$alias}";
    }

    /**
     * Merekap data kunjungan pasien per kelompok (Angkatan Darat, Angkatan Lain, Purnawirawan, Umum)
     * dengan dua metrik: Pengunjung (distinct pasien) dan Kunjungan (total baris).
     * Juga menghitung rujukan (stts = 'Dirujuk') dan rawat inap (status_lanjut = 'Ranap').
     *
     * @param string|null $startDate Tanggal mulai (Y-m-d)
     * @param string|null $endDate   Tanggal akhir (Y-m-d)
     * @return array
     */
    public static function getSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $rp = RegisteredPatient::getTableName();
        $tgl = RegisteredPatient::TGL_REGISTRASI;
        $stts = RegisteredPatient::STATUS_PELAYANAN;
        $statusLanjut = RegisteredPatient::STATUS_LANJUT;

        $row = DB::connection(self::KONEKSI)
            ->table($rp)
            ->leftJoin('pasien_tni as pt', "pt.no_rkm_medis", '=', "{$rp}.no_rkm_medis")
            ->where("{$rp}.{$stts}", '!=', 'Batal')
            ->when($startDate, fn($q) => $q->where("{$rp}.{$tgl}", '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where("{$rp}.{$tgl}", '<=', $endDate))
            ->selectRaw(implode(', ', [
                // --- PENGUNJUNG (distinct no_rkm_medis) ---
                self::caseDistinct('p_ad_mil', self::GOLONGAN_AD_MIL, $rp),
                self::caseDistinct('p_ad_pns', self::GOLONGAN_AD_PNS, $rp),
                self::caseDistinct('p_ad_kel', self::GOLONGAN_AD_KEL, $rp),
                self::caseDistinct('p_al_mil', self::GOLONGAN_AL_MIL, $rp),
                self::caseDistinct('p_al_pns', self::GOLONGAN_AL_PNS, $rp),
                self::caseDistinct('p_al_kel', self::GOLONGAN_AL_KEL, $rp),
                self::caseDistinct('p_purn',   self::GOLONGAN_PURN,   $rp),
                "COUNT(DISTINCT CASE WHEN pt.no_rkm_medis IS NULL THEN {$rp}.no_rkm_medis END) AS p_umum",
                "COUNT(DISTINCT {$rp}.no_rkm_medis) AS p_total",

                // --- KUNJUNGAN (semua baris) ---
                self::caseCount('k_ad_mil', self::GOLONGAN_AD_MIL),
                self::caseCount('k_ad_pns', self::GOLONGAN_AD_PNS),
                self::caseCount('k_ad_kel', self::GOLONGAN_AD_KEL),
                self::caseCount('k_al_mil', self::GOLONGAN_AL_MIL),
                self::caseCount('k_al_pns', self::GOLONGAN_AL_PNS),
                self::caseCount('k_al_kel', self::GOLONGAN_AL_KEL),
                self::caseCount('k_purn',   self::GOLONGAN_PURN),
                "COUNT(CASE WHEN pt.no_rkm_medis IS NULL THEN 1 END) AS k_umum",
                "COUNT({$rp}.no_rawat) AS k_total",

                // --- RUJUKAN per kelompok ---
                self::caseRujukan('r_ad',   array_merge(self::GOLONGAN_AD_MIL, self::GOLONGAN_AD_PNS, self::GOLONGAN_AD_KEL), "{$rp}.{$stts}"),
                self::caseRujukan('r_al',   array_merge(self::GOLONGAN_AL_MIL, self::GOLONGAN_AL_PNS, self::GOLONGAN_AL_KEL), "{$rp}.{$stts}"),
                self::caseRujukan('r_purn', self::GOLONGAN_PURN, "{$rp}.{$stts}"),
                "COUNT(CASE WHEN pt.no_rkm_medis IS NULL AND {$rp}.{$stts} = 'Dirujuk' THEN 1 END) AS r_umum",
                "COUNT(CASE WHEN {$rp}.{$stts} = 'Dirujuk' THEN 1 END) AS rujukan",

                // --- RAWAT INAP per kelompok ---
                self::caseRanap('ri_ad',   array_merge(self::GOLONGAN_AD_MIL, self::GOLONGAN_AD_PNS, self::GOLONGAN_AD_KEL), "{$rp}.{$statusLanjut}"),
                self::caseRanap('ri_al',   array_merge(self::GOLONGAN_AL_MIL, self::GOLONGAN_AL_PNS, self::GOLONGAN_AL_KEL), "{$rp}.{$statusLanjut}"),
                self::caseRanap('ri_purn', self::GOLONGAN_PURN, "{$rp}.{$statusLanjut}"),
                "COUNT(CASE WHEN pt.no_rkm_medis IS NULL AND {$rp}.{$statusLanjut} = 'Ranap' THEN 1 END) AS ri_umum",
                "COUNT(CASE WHEN {$rp}.{$statusLanjut} = 'Ranap' THEN 1 END) AS rawat_inap",
            ]))
            ->first();

        $d = (array) $row;

        return [
            'angkatan_darat' => [
                'pengunjung' => [
                    'mil'   => (int) $d['p_ad_mil'],
                    'pns'   => (int) $d['p_ad_pns'],
                    'kel'   => (int) $d['p_ad_kel'],
                    'total' => (int) $d['p_ad_mil'] + (int) $d['p_ad_pns'] + (int) $d['p_ad_kel'],
                ],
                'kunjungan' => [
                    'mil'   => (int) $d['k_ad_mil'],
                    'pns'   => (int) $d['k_ad_pns'],
                    'kel'   => (int) $d['k_ad_kel'],
                    'total' => (int) $d['k_ad_mil'] + (int) $d['k_ad_pns'] + (int) $d['k_ad_kel'],
                ],
                'rujukan'    => (int) $d['r_ad'],
                'rawat_inap' => (int) $d['ri_ad'],
            ],
            'angkatan_lain' => [
                'pengunjung' => [
                    'mil'   => (int) $d['p_al_mil'],
                    'pns'   => (int) $d['p_al_pns'],
                    'kel'   => (int) $d['p_al_kel'],
                    'total' => (int) $d['p_al_mil'] + (int) $d['p_al_pns'] + (int) $d['p_al_kel'],
                ],
                'kunjungan' => [
                    'mil'   => (int) $d['k_al_mil'],
                    'pns'   => (int) $d['k_al_pns'],
                    'kel'   => (int) $d['k_al_kel'],
                    'total' => (int) $d['k_al_mil'] + (int) $d['k_al_pns'] + (int) $d['k_al_kel'],
                ],
                'rujukan'    => (int) $d['r_al'],
                'rawat_inap' => (int) $d['ri_al'],
            ],
            'purnawirawan' => [
                'pengunjung' => (int) $d['p_purn'],
                'kunjungan'  => (int) $d['k_purn'],
                'rujukan'    => (int) $d['r_purn'],
                'rawat_inap' => (int) $d['ri_purn'],
            ],
            'umum' => [
                'pengunjung' => (int) $d['p_umum'],
                'kunjungan'  => (int) $d['k_umum'],
                'rujukan'    => (int) $d['r_umum'],
                'rawat_inap' => (int) $d['ri_umum'],
            ],
            'total_pengunjung' => (int) $d['p_total'],
            'total_kunjungan'  => (int) $d['k_total'],
            'rujukan'          => (int) $d['rujukan'],
            'rawat_inap'       => (int) $d['rawat_inap'],
        ];
    }
}
