<?php

namespace App\Repository;

use App\Models\RegisteredPatient;
use Illuminate\Support\Facades\DB;

interface RegisteredPatientReportInterface {}

class RegisteredPatientReportRepository implements RegisteredPatientReportInterface
{
    const KONEKSI = 'simrs';
    const LIMIT_POLIKLINIK = 10;

    /**
     * Merekap total pengunjung (distinct pasien) dan kunjungan (total baris registrasi)
     * beserta rincian pasien baru/lama, rujukan, dan rawat inap.
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
        $sttsDaftar = RegisteredPatient::STATUS_DAFTAR;
        $statusLanjut = RegisteredPatient::STATUS_LANJUT;

        $query = fn() => DB::connection(self::KONEKSI)
            ->table($rp)
            ->where("{$rp}.{$stts}", '!=', 'Batal')
            ->when($startDate, fn($q) => $q->where("{$rp}.{$tgl}", '>=', $startDate))
            ->when($endDate, fn($q) => $q->where("{$rp}.{$tgl}", '<=', $endDate));

        $overall = $query()
            ->selectRaw(implode(', ', [
                "COUNT(DISTINCT {$rp}.no_rkm_medis) AS total_pengunjung",
                "COUNT({$rp}.no_rawat) AS total_kunjungan",
                "COUNT(DISTINCT CASE WHEN {$rp}.{$sttsDaftar} = 'Baru' THEN {$rp}.no_rkm_medis END) AS pengunjung_baru",
                "COUNT(DISTINCT CASE WHEN {$rp}.{$sttsDaftar} = 'Lama' THEN {$rp}.no_rkm_medis END) AS pengunjung_lama",
                "COUNT(CASE WHEN {$rp}.{$stts} = 'Dirujuk' THEN 1 END) AS rujukan",
                "COUNT(CASE WHEN {$rp}.{$statusLanjut} = 'Ranap' THEN 1 END) AS rawat_inap",
            ]))
            ->first();

        $byPoliklinik = $query()
            ->join('poliklinik', 'poliklinik.kd_poli', '=', "{$rp}.kd_poli")
            ->selectRaw(implode(', ', [
                'poliklinik.nm_poli',
                "COUNT(DISTINCT {$rp}.no_rkm_medis) AS pengunjung",
                "COUNT({$rp}.no_rawat) AS kunjungan",
                "COUNT(DISTINCT CASE WHEN {$rp}.{$sttsDaftar} = 'Baru' THEN {$rp}.no_rkm_medis END) AS baru",
                "COUNT(DISTINCT CASE WHEN {$rp}.{$sttsDaftar} = 'Lama' THEN {$rp}.no_rkm_medis END) AS lama",
            ]))
            ->groupBy('poliklinik.nm_poli')
            ->orderByDesc('kunjungan')
            ->take(self::LIMIT_POLIKLINIK)
            ->get();

        return [
            'total_pengunjung' => (int) $overall->total_pengunjung,
            'total_kunjungan' => (int) $overall->total_kunjungan,
            'pengunjung_baru' => (int) $overall->pengunjung_baru,
            'pengunjung_lama' => (int) $overall->pengunjung_lama,
            'rujukan' => (int) $overall->rujukan,
            'rawat_inap' => (int) $overall->rawat_inap,
            'by_poliklinik' => $byPoliklinik,
        ];
    }
}
