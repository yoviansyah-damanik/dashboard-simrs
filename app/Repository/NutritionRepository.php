<?php

namespace App\Repository;

use App\Helpers\DateHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

interface NutritionInterface {}

class NutritionRepository implements NutritionInterface
{
    const LIMIT_DEFAULT = 25;

    /**
     * Ambil data asuhan gizi
     */
    public static function getAll(
        string $startDate,
        string $endDate,
        int $limit = self::LIMIT_DEFAULT,
        ?string $search = null,
        ?string $gender = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $hasAdime = \Illuminate\Support\Facades\Schema::connection('simrs')->hasTable('catatan_adime_gizi');

        if ($hasAdime) {
            $query = DB::connection('simrs')
                ->table('catatan_adime_gizi as cag')
                ->join('reg_periksa as rp', 'cag.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('petugas as pt', 'cag.nip', '=', 'pt.nip')
                ->select([
                    'cag.no_rawat',
                    'cag.tanggal',
                    'cag.diagnosis',
                    'cag.intervensi as intervensi_gizi',
                    'cag.asesmen as pola_makan',
                    'cag.nip',
                    'rp.no_rkm_medis',
                    'rp.status_lanjut',
                    'p.nm_pasien as nama_pasien',
                    'p.jk as jenis_kelamin',
                    'pt.nama as nama_petugas',
                ])
                ->whereBetween(DB::raw('DATE(cag.tanggal)'), [$startDate, $endDate]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.nm_pasien', 'like', "%$search%")
                        ->orWhere('rp.no_rkm_medis', 'like', "%$search%")
                        ->orWhere('cag.no_rawat', 'like', "%$search%");
                });
            }

            if ($gender && $gender !== 'semua') {
                $query->where('p.jk', $gender);
            }

            $query->orderByDesc('cag.tanggal');
        } else {
            $query = DB::connection('simrs')
                ->table('asuhan_gizi as ag')
                ->join('reg_periksa as rp', 'ag.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('petugas as pt', 'ag.nip', '=', 'pt.nip')
                ->select([
                    'ag.no_rawat',
                    'ag.tanggal',
                    'ag.diagnosis',
                    'ag.intervensi_gizi',
                    'ag.pola_makan',
                    'ag.nip',
                    'rp.no_rkm_medis',
                    'rp.status_lanjut',
                    'p.nm_pasien as nama_pasien',
                    'p.jk as jenis_kelamin',
                    'pt.nama as nama_petugas',
                ])
                ->whereBetween('ag.tanggal', [$startDate, $endDate]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.nm_pasien', 'like', "%$search%")
                        ->orWhere('rp.no_rkm_medis', 'like', "%$search%")
                        ->orWhere('ag.no_rawat', 'like', "%$search%");
                });
            }

            if ($gender && $gender !== 'semua') {
                $query->where('p.jk', $gender);
            }

            $query->orderByDesc('ag.tanggal');
        }

        $result = $limit > 0 ? $query->paginate($limit) : $query->get();

        $collection = $result instanceof LengthAwarePaginator ? $result->getCollection() : $result;

        $collection->transform(fn($row) => self::mapping($row));

        return $result;
    }

    private static function mapping(object $row): array
    {
        return [
            'gizi' => [
                'no_rawat' => $row->no_rawat,
                'tanggal' => DateHelper::dateFormat($row->tanggal, isTranslated: true, translatedFormat: 'd F Y'),
                'diagnosis' => $row->diagnosis ?? '-',
                'intervensi_gizi' => $row->intervensi_gizi ?? '-',
                'pola_makan' => $row->pola_makan ?? '-',
                'status_lanjut' => $row->status_lanjut,
            ],
            'pasien' => [
                'no_rekam_medis' => $row->no_rkm_medis,
                'nama' => $row->nama_pasien,
                'jenis_kelamin' => $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            ],
            'petugas' => [
                'nama' => $row->nama_petugas ?? '-',
            ],
        ];
    }

    /**
     * Ambil data permintaan / pemberian diet pasien terpaginasi dengan berbagai filter.
     */
    public static function getDietOrders(
        array $filters = [],
        int $limit = self::LIMIT_DEFAULT
    ): LengthAwarePaginator {
        $startDate = $filters['startDate'] ?? null;
        $endDate = $filters['endDate'] ?? null;
        $search = $filters['search'] ?? null;
        $waktu = $filters['waktu'] ?? 'semua';
        $kdDiet = $filters['kd_diet'] ?? 'semua';
        $kdBangsal = $filters['kd_bangsal'] ?? 'semua';

        $query = DB::connection('simrs')
            ->table('detail_beri_diet as dbd')
            ->leftJoin('diet as d', 'dbd.kd_diet', '=', 'd.kd_diet')
            ->leftJoin('reg_periksa as rp', 'dbd.no_rawat', '=', 'rp.no_rawat')
            ->leftJoin('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('kamar as k', 'dbd.kd_kamar', '=', 'k.kd_kamar')
            ->leftJoin('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
            ->leftJoin('sisa_diet_pasien as sdp', function ($join) {
                $join->on('dbd.no_rawat', '=', 'sdp.no_rawat')
                    ->on('dbd.kd_kamar', '=', 'sdp.kd_kamar')
                    ->on('dbd.tanggal', '=', 'sdp.tanggal')
                    ->on('dbd.waktu', '=', 'sdp.waktu');
            })
            ->select([
                'dbd.no_rawat',
                'dbd.kd_kamar',
                'dbd.tanggal',
                'dbd.waktu',
                'dbd.kd_diet',
                'd.nama_diet',
                'rp.no_rkm_medis',
                'rp.status_lanjut',
                'rp.umurdaftar',
                'rp.sttsumur',
                'p.nm_pasien',
                'p.jk',
                'b.kd_bangsal',
                'b.nm_bangsal',
                'k.kelas',
                'sdp.karbohidrat as sisa_karbo',
                'sdp.hewani as sisa_hewani',
                'sdp.nabati as sisa_nabati',
                'sdp.sayur as sisa_sayur',
                'sdp.buah as sisa_buah',
            ]);

        if ($startDate && $endDate) {
            $query->whereBetween('dbd.tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('dbd.tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('dbd.tanggal', '<=', $endDate);
        }

        if ($waktu && $waktu !== 'semua') {
            $query->where('dbd.waktu', 'like', "{$waktu}%");
        }

        if ($kdDiet && $kdDiet !== 'semua') {
            $query->where('dbd.kd_diet', $kdDiet);
        }

        if ($kdBangsal && $kdBangsal !== 'semua') {
            $query->where('b.kd_bangsal', $kdBangsal);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.nm_pasien', 'like', "%{$search}%")
                    ->orWhere('rp.no_rkm_medis', 'like', "%{$search}%")
                    ->orWhere('dbd.no_rawat', 'like', "%{$search}%")
                    ->orWhere('d.nama_diet', 'like', "%{$search}%")
                    ->orWhere('b.nm_bangsal', 'like', "%{$search}%")
                    ->orWhere('dbd.kd_kamar', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('dbd.tanggal')
            ->orderByRaw("CASE WHEN dbd.waktu LIKE 'Pagi%' THEN 1 WHEN dbd.waktu LIKE 'Siang%' THEN 2 WHEN dbd.waktu LIKE 'Sore%' THEN 3 ELSE 4 END ASC")
            ->orderByDesc('dbd.no_rawat');

        return $query->paginate($limit);
    }

    /**
     * Ringkasan cepat statistik permintaan diet (Total, Pasien Unik, Waktu, Jenis).
     */
    public static function getDietSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = DB::connection('simrs')->table('detail_beri_diet as dbd');

        if ($startDate && $endDate) {
            $query->whereBetween('dbd.tanggal', [$startDate, $endDate]);
        }

        $row = $query->selectRaw("
            count(*) as total_porsi,
            count(distinct dbd.no_rawat) as total_pasien,
            sum(case when dbd.waktu like 'Pagi%' then 1 else 0 end) as pagi,
            sum(case when dbd.waktu like 'Siang%' then 1 else 0 end) as siang,
            sum(case when dbd.waktu like 'Sore%' or dbd.waktu like 'Malam%' then 1 else 0 end) as sore,
            count(distinct dbd.kd_diet) as total_jenis_diet
        ")->first();

        return [
            'total_porsi' => (int) ($row->total_porsi ?? 0),
            'total_pasien' => (int) ($row->total_pasien ?? 0),
            'pagi' => (int) ($row->pagi ?? 0),
            'siang' => (int) ($row->siang ?? 0),
            'sore' => (int) ($row->sore ?? 0),
            'total_jenis_diet' => (int) ($row->total_jenis_diet ?? 0),
        ];
    }

    /**
     * Mengambil data rekap komprehensif permintaan diet (KPI, Tren, Distribusi Waktu, Top Diet, Sebaran Bangsal).
     */
    public static function getDietRecap(?string $startDate = null, ?string $endDate = null): array
    {
        $baseQuery = DB::connection('simrs')->table('detail_beri_diet as dbd')
            ->leftJoin('diet as d', 'dbd.kd_diet', '=', 'd.kd_diet')
            ->leftJoin('kamar as k', 'dbd.kd_kamar', '=', 'k.kd_kamar')
            ->leftJoin('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal');

        if ($startDate && $endDate) {
            $baseQuery->whereBetween('dbd.tanggal', [$startDate, $endDate]);
        }

        // 1. KPI Summary
        $summaryRow = (clone $baseQuery)->selectRaw("
            count(*) as total_porsi,
            count(distinct dbd.no_rawat) as total_pasien,
            sum(case when dbd.waktu like 'Pagi%' then 1 else 0 end) as pagi,
            sum(case when dbd.waktu like 'Siang%' then 1 else 0 end) as siang,
            sum(case when dbd.waktu like 'Sore%' or dbd.waktu like 'Malam%' then 1 else 0 end) as sore,
            count(distinct dbd.kd_diet) as total_jenis_diet
        ")->first();

        $totalPorsi = (int) ($summaryRow->total_porsi ?? 0);
        $totalPasien = (int) ($summaryRow->total_pasien ?? 0);
        $pagi = (int) ($summaryRow->pagi ?? 0);
        $siang = (int) ($summaryRow->siang ?? 0);
        $sore = (int) ($summaryRow->sore ?? 0);
        $totalJenisDiet = (int) ($summaryRow->total_jenis_diet ?? 0);

        // 2. Trend Time Series
        $trendData = self::calculateDietTrend($startDate, $endDate);

        // 3. 10 Jenis Diet Terbanyak
        $topDietRows = (clone $baseQuery)
            ->select('dbd.kd_diet', DB::raw("coalesce(d.nama_diet, dbd.kd_diet) as nama_diet"), DB::raw('count(*) as total'))
            ->groupBy('dbd.kd_diet', 'nama_diet')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // 4. Sebaran Bangsal Terbanyak
        $bangsalRows = (clone $baseQuery)
            ->select('b.kd_bangsal', DB::raw("coalesce(b.nm_bangsal, dbd.kd_kamar) as nama_bangsal"), DB::raw('count(*) as total'))
            ->groupBy('b.kd_bangsal', 'nama_bangsal')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // 5. Tabel Rincian per Jenis Diet
        $perDietTable = (clone $baseQuery)
            ->select([
                'dbd.kd_diet',
                DB::raw("coalesce(d.nama_diet, dbd.kd_diet) as nama_diet"),
                DB::raw("sum(case when dbd.waktu like 'Pagi%' then 1 else 0 end) as pagi"),
                DB::raw("sum(case when dbd.waktu like 'Siang%' then 1 else 0 end) as siang"),
                DB::raw("sum(case when dbd.waktu like 'Sore%' or dbd.waktu like 'Malam%' then 1 else 0 end) as sore"),
                DB::raw("count(*) as total"),
            ])
            ->groupBy('dbd.kd_diet', 'nama_diet')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalPorsi) {
                $item->persen = $totalPorsi > 0 ? round(($item->total / $totalPorsi) * 100, 1) : 0;
                return $item;
            });

        // 6. Tabel Rincian per Bangsal
        $perBangsalTable = (clone $baseQuery)
            ->select([
                'b.kd_bangsal',
                DB::raw("coalesce(b.nm_bangsal, dbd.kd_kamar) as nama_bangsal"),
                DB::raw("sum(case when dbd.waktu like 'Pagi%' then 1 else 0 end) as pagi"),
                DB::raw("sum(case when dbd.waktu like 'Siang%' then 1 else 0 end) as siang"),
                DB::raw("sum(case when dbd.waktu like 'Sore%' or dbd.waktu like 'Malam%' then 1 else 0 end) as sore"),
                DB::raw("count(*) as total"),
            ])
            ->groupBy('b.kd_bangsal', 'nama_bangsal')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalPorsi) {
                $item->persen = $totalPorsi > 0 ? round(($item->total / $totalPorsi) * 100, 1) : 0;
                return $item;
            });

        return [
            'summary' => [
                'total_porsi' => $totalPorsi,
                'total_pasien' => $totalPasien,
                'pagi' => $pagi,
                'siang' => $siang,
                'sore' => $sore,
                'total_jenis_diet' => $totalJenisDiet,
            ],
            'charts' => [
                'trend' => [
                    'labels' => $trendData->pluck('label')->toArray(),
                    'datasets' => [[
                        'label' => 'Total Porsi Diet',
                        'data' => $trendData->pluck('total')->toArray(),
                        'borderColor' => '#f59e0b',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ]],
                ],
                'waktu' => [
                    'labels' => ['Pagi', 'Siang', 'Sore / Malam'],
                    'datasets' => [[
                        'data' => [$pagi, $siang, $sore],
                        'backgroundColor' => ['#f59e0b', '#f97316', '#6366f1'],
                    ]],
                ],
                'top_diet' => [
                    'labels' => $topDietRows->pluck('nama_diet')->toArray(),
                    'datasets' => [[
                        'label' => 'Porsi',
                        'data' => $topDietRows->pluck('total')->toArray(),
                        'backgroundColor' => '#10b981',
                        'borderRadius' => 6,
                    ]],
                ],
                'bangsal' => [
                    'labels' => $bangsalRows->pluck('nama_bangsal')->toArray(),
                    'datasets' => [[
                        'label' => 'Porsi',
                        'data' => $bangsalRows->pluck('total')->toArray(),
                        'backgroundColor' => '#06b6d4',
                        'borderRadius' => 6,
                    ]],
                ],
            ],
            'tables' => [
                'per_diet' => $perDietTable,
                'per_bangsal' => $perBangsalTable,
            ],
        ];
    }

    /**
     * Hitung tren permintaan diet berdasarkan rentang tanggal.
     */
    private static function calculateDietTrend(?string $startDate, ?string $endDate)
    {
        $query = DB::connection('simrs')->table('detail_beri_diet');
        $hasRange = $startDate && $endDate;
        $dateColumn = 'tanggal';

        if ($hasRange) {
            $query->whereBetween($dateColumn, [$startDate, $endDate]);
            $spanInDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));

            if ($spanInDays > 60) {
                return $query
                    ->selectRaw("DATE_FORMAT($dateColumn, '%Y-%m') as time_key, DATE_FORMAT($dateColumn, '%b %Y') as label, count(*) as total")
                    ->groupBy('time_key', 'label')
                    ->orderBy('time_key')
                    ->get();
            }

            return $query
                ->selectRaw("$dateColumn as time_key, DATE_FORMAT($dateColumn, '%d/%m') as label, count(*) as total")
                ->groupBy('time_key', 'label')
                ->orderBy('time_key')
                ->get();
        }

        return $query
            ->selectRaw("YEAR($dateColumn) as time_key, cast(YEAR($dateColumn) as char) as label, count(*) as total")
            ->groupBy('time_key', 'label')
            ->orderBy('time_key')
            ->get();
    }

    /**
     * Ambil seluruh master jenis diet.
     */
    public static function getMasterDiet()
    {
        return DB::connection('simrs')->table('diet')
            ->select('kd_diet', 'nama_diet')
            ->orderBy('nama_diet')
            ->get();
    }

    /**
     * Ambil seluruh master bangsal rawat inap.
     */
    public static function getMasterBangsal()
    {
        return DB::connection('simrs')->table('bangsal')
            ->where('status', '1')
            ->select('kd_bangsal', 'nm_bangsal')
            ->orderBy('nm_bangsal')
            ->get();
    }
}
