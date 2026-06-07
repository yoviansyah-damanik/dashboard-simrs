<?php

namespace App\Repository;

use App\Models\RegisteredPatient;
use Illuminate\Support\Facades\DB;

interface FinancialReportInterface
{
}

class FinancialReportRepository implements FinancialReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Tabel tindakan rawat jalan & rawat inap (tarif dokter, dokter+perawat, perawat).
     * Sumber kolom biaya_rawat untuk komponen pendapatan "tindakan".
     */
    const TABEL_TINDAKAN = ['rawat_jl_dr', 'rawat_jl_drpr', 'rawat_jl_pr', 'rawat_inap_dr', 'rawat_inap_drpr', 'rawat_inap_pr'];

    /**
     * Subquery agregat total biaya tindakan per no_rawat,
     * dijumlahkan dari kolom biaya_rawat pada seluruh tabel tindakan dokter/perawat.
     */
    private static function tindakanSubquery()
    {
        $union = null;
        foreach (self::TABEL_TINDAKAN as $table) {
            $query = DB::connection(self::KONEKSI)->table($table)->select('no_rawat', 'biaya_rawat');
            $union = $union === null ? $query : $union->unionAll($query);
        }

        return DB::connection(self::KONEKSI)->query()
            ->fromSub($union, 'tindakan_union')
            ->select('no_rawat', DB::raw('SUM(biaya_rawat) AS total'))
            ->groupBy('no_rawat');
    }

    /**
     * Subquery agregat total biaya per no_rawat dari satu tabel layanan (lab/radiologi/obat).
     */
    private static function biayaLayananSubquery(string $table, string $kolomBiaya)
    {
        return DB::connection(self::KONEKSI)->table($table)
            ->select('no_rawat', DB::raw('SUM(' . $kolomBiaya . ') AS total'))
            ->groupBy('no_rawat');
    }

    /**
     * Merekap pendapatan dari pasien yang status pelayanannya bukan 'Batal',
     * dikelompokkan per jenis kunjungan (Rawat Jalan, Rawat Inap, IGD)
     * serta status pembayaran.
     *
     * Komponen pendapatan yang dijumlahkan:
     * - biaya_reg (biaya registrasi pada reg_periksa)
     * - biaya tindakan (rawat_jl_dr/drpr/pr untuk Ralan, rawat_inap_dr/drpr/pr untuk Ranap)
     * - biaya laboratorium (periksa_lab.biaya)
     * - biaya radiologi (periksa_radiologi.biaya)
     * - biaya obat (detail_pemberian_obat.total)
     *
     * @param string|null $startDate Tanggal mulai (Y-m-d)
     * @param string|null $endDate Tanggal akhir (Y-m-d)
     * @return array
     */
    public static function getSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $rp = RegisteredPatient::getTableName();
        $kodeIgd = RegisteredPatient::KODE_IGD;
        $kodePoliklinik = RegisteredPatient::KODE_POLIKLINIK;
        $statusPelayanan = RegisteredPatient::STATUS_PELAYANAN;
        $statusLanjut = RegisteredPatient::STATUS_LANJUT;
        $statusBayar = RegisteredPatient::STATUS_BAYAR;
        $tanggalRegistrasi = RegisteredPatient::TGL_REGISTRASI;
        $biayaRegistrasi = RegisteredPatient::BIAYA_REGISTRASI;

        $pendapatan = "{$rp}.{$biayaRegistrasi}"
            . " + IFNULL(tindakan.total, 0)"
            . " + IFNULL(lab.total, 0)"
            . " + IFNULL(radiologi.total, 0)"
            . " + IFNULL(obat.total, 0)";

        $bukanIgd = "{$rp}.{$kodePoliklinik} != '{$kodeIgd}'";
        $isRalan = "{$statusLanjut} = '" . RegisteredPatient::STATUS_RALAN . "'";
        $isRanap = "{$statusLanjut} = '" . RegisteredPatient::STATUS_RANAP . "'";
        $isIgd = "{$kodePoliklinik} = '{$kodeIgd}'";

        $query = DB::connection(self::KONEKSI)->table($rp)
            ->leftJoinSub(self::tindakanSubquery(), 'tindakan', 'tindakan.no_rawat', '=', "{$rp}.no_rawat")
            ->leftJoinSub(self::biayaLayananSubquery('periksa_lab', 'biaya'), 'lab', 'lab.no_rawat', '=', "{$rp}.no_rawat")
            ->leftJoinSub(self::biayaLayananSubquery('periksa_radiologi', 'biaya'), 'radiologi', 'radiologi.no_rawat', '=', "{$rp}.no_rawat")
            ->leftJoinSub(self::biayaLayananSubquery('detail_pemberian_obat', 'total'), 'obat', 'obat.no_rawat', '=', "{$rp}.no_rawat")
            ->where("{$rp}.{$statusPelayanan}", '!=', 'Batal')
            ->when($startDate, function ($query, $startDate) use ($rp, $tanggalRegistrasi) {
                $query->where("{$rp}.{$tanggalRegistrasi}", '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) use ($rp, $tanggalRegistrasi) {
                $query->where("{$rp}.{$tanggalRegistrasi}", '<=', $endDate);
            });

        $summary = $query
            ->selectRaw(
                'COUNT(*) AS total_pasien,'
                    . "IFNULL(SUM({$pendapatan}),0) AS total_pendapatan,"

                    . "IFNULL(SUM(CASE WHEN {$isIgd} THEN 1 ELSE 0 END),0) AS igd_jumlah_pasien,"
                    . "IFNULL(SUM(CASE WHEN {$isIgd} THEN ({$pendapatan}) ELSE 0 END),0) AS igd_total_pendapatan,"

                    . "IFNULL(SUM(CASE WHEN {$bukanIgd} AND {$isRanap} THEN 1 ELSE 0 END),0) AS rawat_inap_jumlah_pasien,"
                    . "IFNULL(SUM(CASE WHEN {$bukanIgd} AND {$isRanap} THEN ({$pendapatan}) ELSE 0 END),0) AS rawat_inap_total_pendapatan,"

                    . "IFNULL(SUM(CASE WHEN {$bukanIgd} AND {$isRalan} THEN 1 ELSE 0 END),0) AS rawat_jalan_jumlah_pasien,"
                    . "IFNULL(SUM(CASE WHEN {$bukanIgd} AND {$isRalan} THEN ({$pendapatan}) ELSE 0 END),0) AS rawat_jalan_total_pendapatan,"

                    . "IFNULL(SUM(CASE WHEN {$rp}.{$statusBayar} = 'Sudah Bayar' THEN 1 ELSE 0 END),0) AS sudah_bayar_jumlah_pasien,"
                    . "IFNULL(SUM(CASE WHEN {$rp}.{$statusBayar} = 'Belum Bayar' THEN 1 ELSE 0 END),0) AS belum_bayar_jumlah_pasien"
            )
            ->first();

        $summary = (array) $summary;

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
