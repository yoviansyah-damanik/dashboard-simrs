<?php

namespace App\Repository;

use App\Models\RegisteredPatient;
use Illuminate\Database\Eloquent\Builder;

interface FinancialReportInterface
{
}

class FinancialReportRepository implements FinancialReportInterface
{
    /**
     * Merekap total pendapatan registrasi (biaya_reg) dari pasien yang
     * status pelayanannya bukan 'Batal', dikelompokkan per jenis kunjungan
     * (Rawat Jalan, Rawat Inap, IGD) serta status pembayaran.
     *
     * @param string|null $startDate Tanggal mulai (Y-m-d)
     * @param string|null $endDate Tanggal akhir (Y-m-d)
     * @return array
     */
    public static function getSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $result = new RegisteredPatient;

        $result = $result->where(RegisteredPatient::STATUS_PELAYANAN, '!=', 'Batal');

        $result = $result->when($startDate, function (Builder $query, string $startDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '>=', $startDate);
        });

        $result = $result->when($endDate, function (Builder $query, string $endDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '<=', $endDate);
        });

        $kodeIgd = RegisteredPatient::KODE_IGD;
        $kodePoliklinik = RegisteredPatient::KODE_POLIKLINIK;
        $statusLanjut = RegisteredPatient::STATUS_LANJUT;
        $statusBayar = RegisteredPatient::STATUS_BAYAR;
        $biayaRegistrasi = RegisteredPatient::BIAYA_REGISTRASI;

        $summary = $result
            ->selectRaw(
                'COUNT(*) AS total_pasien,'
                    . 'IFNULL(SUM(' . $biayaRegistrasi . '),0) AS total_pendapatan,'

                    . 'IFNULL(SUM(CASE WHEN ' . $kodePoliklinik . ' = \'' . $kodeIgd . '\' THEN 1 ELSE 0 END),0) AS igd_jumlah_pasien,'
                    . 'IFNULL(SUM(CASE WHEN ' . $kodePoliklinik . ' = \'' . $kodeIgd . '\' THEN ' . $biayaRegistrasi . ' ELSE 0 END),0) AS igd_total_pendapatan,'

                    . 'IFNULL(SUM(CASE WHEN ' . $kodePoliklinik . ' != \'' . $kodeIgd . '\' AND ' . $statusLanjut . ' = \'' . RegisteredPatient::STATUS_RANAP . '\' THEN 1 ELSE 0 END),0) AS rawat_inap_jumlah_pasien,'
                    . 'IFNULL(SUM(CASE WHEN ' . $kodePoliklinik . ' != \'' . $kodeIgd . '\' AND ' . $statusLanjut . ' = \'' . RegisteredPatient::STATUS_RANAP . '\' THEN ' . $biayaRegistrasi . ' ELSE 0 END),0) AS rawat_inap_total_pendapatan,'

                    . 'IFNULL(SUM(CASE WHEN ' . $kodePoliklinik . ' != \'' . $kodeIgd . '\' AND ' . $statusLanjut . ' = \'' . RegisteredPatient::STATUS_RALAN . '\' THEN 1 ELSE 0 END),0) AS rawat_jalan_jumlah_pasien,'
                    . 'IFNULL(SUM(CASE WHEN ' . $kodePoliklinik . ' != \'' . $kodeIgd . '\' AND ' . $statusLanjut . ' = \'' . RegisteredPatient::STATUS_RALAN . '\' THEN ' . $biayaRegistrasi . ' ELSE 0 END),0) AS rawat_jalan_total_pendapatan,'

                    . 'IFNULL(SUM(CASE WHEN ' . $statusBayar . ' = \'Sudah Bayar\' THEN 1 ELSE 0 END),0) AS sudah_bayar_jumlah_pasien,'
                    . 'IFNULL(SUM(CASE WHEN ' . $statusBayar . ' = \'Belum Bayar\' THEN 1 ELSE 0 END),0) AS belum_bayar_jumlah_pasien'
            )
            ->first()
            ->toArray();

        return [
            'total_pasien' => (int) $summary['total_pasien'],
            'total_pendapatan' => (float) $summary['total_pendapatan'],
            'rawat_jalan' => [
                'jumlah_pasien' => (int) $summary['rawat_jalan_jumlah_pasien'],
                'total_pendapatan' => (float) $summary['rawat_jalan_total_pendapatan'],
            ],
            'rawat_inap' => [
                'jumlah_pasien' => (int) $summary['rawat_inap_jumlah_pasien'],
                'total_pendapatan' => (float) $summary['rawat_inap_total_pendapatan'],
            ],
            'igd' => [
                'jumlah_pasien' => (int) $summary['igd_jumlah_pasien'],
                'total_pendapatan' => (float) $summary['igd_total_pendapatan'],
            ],
            'status_bayar' => [
                'sudah_bayar' => (int) $summary['sudah_bayar_jumlah_pasien'],
                'belum_bayar' => (int) $summary['belum_bayar_jumlah_pasien'],
            ],
        ];
    }
}
