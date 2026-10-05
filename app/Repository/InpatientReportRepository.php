<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

interface InpatientReportInterface {}

class InpatientReportRepository implements InpatientReportInterface
{
    const CONNECTION = 'simrs';
    const LIMIT_DEFAULT = 25;

    /**
     * Membangun base query laporan pasien rawat inap sesuai referensi query SIMRS.
     *
     * @param string|null $startDate Tanggal awal masuk (Y-m-d)
     * @param string|null $endDate Tanggal akhir masuk (Y-m-d)
     * @param string|null $payType Kode penanggung jawab / cara bayar (kd_pj)
     * @param string|null $statusPulang Status keluar ('semua', 'sudah_pulang', 'masih_dirawat')
     * @param string|null $ward Kode bangsal (kd_bangsal)
     * @param string|null $search Kata kunci pencarian (No Rawat, No RM, atau Nama Pasien)
     * @return \Illuminate\Database\Query\Builder
     */
    private static function buildQuery(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ) {
        $query = DB::connection(self::CONNECTION)
            ->table('reg_periksa as rp')
            ->join('kamar_inap as ki', 'rp.no_rawat', '=', 'ki.no_rawat')
            ->join('kamar as k', 'k.kd_kamar', '=', 'ki.kd_kamar')
            ->join('bangsal as b', 'b.kd_bangsal', '=', 'k.kd_bangsal')
            ->join('pasien as p', 'p.no_rkm_medis', '=', 'rp.no_rkm_medis')
            ->leftJoin('penjab as pj', 'pj.kd_pj', '=', 'rp.kd_pj')
            ->where('rp.status_lanjut', 'Ranap');

        // Filter Penanggung Jawab / Cara Bayar
        if (!empty($payType) && $payType !== 'semua') {
            $query->where('rp.kd_pj', $payType);
        }

        // Filter Rentang Tanggal Masuk
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('ki.tgl_masuk', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('ki.tgl_masuk', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('ki.tgl_masuk', '<=', $endDate);
        }

        // Filter Status Pulang / Keluar
        if ($statusPulang === 'sudah_pulang') {
            $query->where('ki.tgl_keluar', '<>', '0000-00-00')
                  ->whereNotNull('ki.tgl_keluar');
        } elseif ($statusPulang === 'masih_dirawat') {
            $query->where(function ($q) {
                $q->where('ki.tgl_keluar', '=', '0000-00-00')
                  ->orWhereNull('ki.tgl_keluar');
            });
        }

        // Filter Bangsal
        if (!empty($ward) && $ward !== 'semua') {
            $query->where('b.kd_bangsal', $ward);
        }

        // Filter Pencarian (No Rawat, No RM, atau Nama Pasien)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('rp.no_rawat', 'like', "%{$search}%")
                  ->orWhere('rp.no_rkm_medis', 'like', "%{$search}%")
                  ->orWhere('p.nm_pasien', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Mengambil data laporan pasien rawat inap beserta DPJP dan penanggung jawab.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $payType
     * @param string|null $statusPulang
     * @param string|null $ward
     * @param string|null $search
     * @param int $limit Jika 0, mengembalikan seluruh baris
     * @return LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public static function getPatients(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null,
        int $limit = self::LIMIT_DEFAULT
    ) {
        $query = self::buildQuery($startDate, $endDate, $payType, $statusPulang, $ward, $search)
            ->groupBy('ki.no_rawat')
            ->select([
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'b.nm_bangsal',
                'ki.tgl_masuk',
                'ki.tgl_keluar',
                'pj.png_jawab',
                DB::raw("(
                    SELECT GROUP_CONCAT(DISTINCT d.nm_dokter ORDER BY d.nm_dokter SEPARATOR ', ')
                    FROM dpjp_ranap dpjp
                    JOIN dokter d ON d.kd_dokter = dpjp.kd_dokter
                    WHERE dpjp.no_rawat = rp.no_rawat
                ) AS dpjp_ranap")
            ])
            ->orderByDesc('ki.tgl_masuk')
            ->orderByDesc('rp.no_rawat');

        if ($limit > 0) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * Menghitung ringkasan metrik untuk header laporan.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $payType
     * @param string|null $statusPulang
     * @param string|null $ward
     * @param string|null $search
     * @return array
     */
    public static function getSummary(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $payType = null,
        ?string $statusPulang = 'semua',
        ?string $ward = null,
        ?string $search = null
    ): array {
        // Query keseluruhan tanpa filter status_pulang untuk menghitung komposisi pulang vs dirawat
        $baseQuery = self::buildQuery($startDate, $endDate, $payType, 'semua', $ward, $search);

        $stats = $baseQuery
            ->selectRaw("
                COUNT(DISTINCT ki.no_rawat) AS total_pasien,
                COUNT(DISTINCT CASE WHEN ki.tgl_keluar <> '0000-00-00' AND ki.tgl_keluar IS NOT NULL THEN ki.no_rawat END) AS sudah_pulang,
                COUNT(DISTINCT CASE WHEN ki.tgl_keluar = '0000-00-00' OR ki.tgl_keluar IS NULL THEN ki.no_rawat END) AS masih_dirawat
            ")
            ->first();

        return [
            'total_pasien' => (int) ($stats->total_pasien ?? 0),
            'sudah_pulang' => (int) ($stats->sudah_pulang ?? 0),
            'masih_dirawat' => (int) ($stats->masih_dirawat ?? 0),
        ];
    }
}
