<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TaskidHelper
{
    const KONEKSI = 'simrs';
    const TABLE = 'referensi_mobilejkn_bpjs_taskid';

    /**
     * Memeriksa keberadaan tabel referensi_mobilejkn_bpjs_taskid
     */
    public static function isAvailable(): bool
    {
        return Schema::connection(self::KONEKSI)->hasTable(self::TABLE);
    }

    /**
     * Menghitung statistik waktu antara 2 Task ID BPJS
     *
     * Definisi Task ID:
     * task4 - task3 : Waktu Tunggu Poli
     * task5 - task4 : Waktu Pelayanan Poli
     * task6 - task5 : Waktu Tunggu Farmasi
     * task7 - task6 : Waktu Pelayanan Farmasi
     * task2 - task1 : Waktu Tunggu Admisi
     * task3 - task2 : Waktu Pelayanan Admisi
     */
    public static function getIntervalStats(string $startTaskId, string $endTaskId, int $year, ?int $month = null, int $maxMinutes = 60): array
    {
        if (!self::isAvailable()) {
            return [
                'total' => 0,
                'tepat' => 0,
                'rate' => 0.0,
                'avg_menit' => 0.0,
            ];
        }

        $conn = DB::connection(self::KONEKSI);

        $query = $conn->table(self::TABLE . ' as t_start')
            ->join(self::TABLE . ' as t_end', function ($j) use ($endTaskId) {
                $j->on('t_start.no_rawat', '=', 't_end.no_rawat')
                  ->where('t_end.taskid', '=', $endTaskId);
            })
            ->where('t_start.taskid', $startTaskId)
            ->whereYear('t_start.waktu', $year);

        if ($month !== null && $month >= 1 && $month <= 12) {
            $query->whereMonth('t_start.waktu', $month);
        }

        $raw = $query->selectRaw("
            COUNT(*) as total_transaksi,
            SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, t_start.waktu, t_end.waktu) BETWEEN 0 AND {$maxMinutes} THEN 1 ELSE 0 END) as total_tepat,
            AVG(CASE WHEN TIMESTAMPDIFF(MINUTE, t_start.waktu, t_end.waktu) BETWEEN 0 AND 360 THEN TIMESTAMPDIFF(MINUTE, t_start.waktu, t_end.waktu) END) as rata_menit
        ")->first();

        $total = (int) ($raw->total_transaksi ?? 0);
        $tepat = (int) ($raw->total_tepat ?? 0);
        $rate = $total > 0 ? round(($tepat / $total) * 100, 2) : 0.0;
        $avg = round((float) ($raw->rata_menit ?? 0.0), 1);

        return [
            'total' => $total,
            'tepat' => $tepat,
            'rate' => $rate,
            'avg_menit' => $avg,
        ];
    }

    /**
     * Waktu Tunggu Poli (task4 - task3)
     */
    public static function getWaktuTungguPoli(int $year, ?int $month = null): array
    {
        return self::getIntervalStats('3', '4', $year, $month, 60);
    }

    /**
     * Waktu Pelayanan Poli (task5 - task4)
     */
    public static function getWaktuPelayananPoli(int $year, ?int $month = null): array
    {
        return self::getIntervalStats('4', '5', $year, $month, 60);
    }

    /**
     * Waktu Tunggu Farmasi (task6 - task5)
     */
    public static function getWaktuTungguFarmasi(int $year, ?int $month = null, int $maxMinutes = 30): array
    {
        return self::getIntervalStats('5', '6', $year, $month, $maxMinutes);
    }

    /**
     * Waktu Pelayanan Farmasi (task7 - task6)
     */
    public static function getWaktuPelayananFarmasi(int $year, ?int $month = null): array
    {
        return self::getIntervalStats('6', '7', $year, $month, 60);
    }

    /**
     * Waktu Tunggu Admisi (task2 - task1)
     */
    public static function getWaktuTungguAdmisi(int $year, ?int $month = null): array
    {
        return self::getIntervalStats('1', '2', $year, $month, 30);
    }

    /**
     * Waktu Pelayanan Admisi (task3 - task2)
     */
    public static function getWaktuPelayananAdmisi(int $year, ?int $month = null): array
    {
        return self::getIntervalStats('2', '3', $year, $month, 30);
    }

    /**
     * Mengambil daftar log detail per pasien untuk drilldown modal
     */
    public static function getDetailLogs(string $startTaskId, string $endTaskId, int $year, ?int $month = null, int $maxMinutes = 60, int $limit = 50): array
    {
        if (!self::isAvailable()) {
            return [];
        }

        $conn = DB::connection(self::KONEKSI);

        $query = $conn->table(self::TABLE . ' as t_start')
            ->join(self::TABLE . ' as t_end', function ($j) use ($endTaskId) {
                $j->on('t_start.no_rawat', '=', 't_end.no_rawat')
                  ->where('t_end.taskid', '=', $endTaskId);
            })
            ->join('reg_periksa as r', 't_start.no_rawat', '=', 'r.no_rawat')
            ->join('pasien as ps', 'r.no_rkm_medis', '=', 'ps.no_rkm_medis')
            ->leftJoin('poliklinik as pl', 'r.kd_poli', '=', 'pl.kd_poli')
            ->select(
                't_start.waktu as t_start_waktu',
                't_end.waktu as t_end_waktu',
                't_start.no_rawat',
                'ps.nm_pasien',
                'pl.nm_poli',
                DB::raw("TIMESTAMPDIFF(MINUTE, t_start.waktu, t_end.waktu) as durasi_menit")
            )
            ->where('t_start.taskid', $startTaskId)
            ->whereYear('t_start.waktu', $year);

        if ($month) {
            $query->whereMonth('t_start.waktu', $month);
        }

        $records = $query->orderByDesc('t_start.waktu')
            ->limit($limit)
            ->get();

        return $records->map(function ($row) use ($maxMinutes) {
            $menit = (int) ($row->durasi_menit ?? 0);
            $patuh = ($menit >= 0 && $menit <= $maxMinutes);
            return [
                'tanggal' => substr($row->t_start_waktu, 0, 10),
                'no_rawat' => $row->no_rawat,
                'subjek' => $row->nm_pasien,
                'unit' => $row->nm_poli ?? 'Poliklinik',
                'waktu_mulai' => substr($row->t_start_waktu, 11),
                'waktu_selesai' => substr($row->t_end_waktu, 11),
                'nilai' => $menit . ' menit',
                'status' => $patuh ? "Tepat (≤ {$maxMinutes}m)" : "Tertunda (> {$maxMinutes}m)",
                'is_patuh' => $patuh,
            ];
        })->toArray();
    }
}
