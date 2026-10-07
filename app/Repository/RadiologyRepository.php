<?php

namespace App\Repository;

use App\Helpers\DateHelper;
use App\Models\RadiologyExam;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

interface RadiologyInterface {}

class RadiologyRepository implements RadiologyInterface
{
    const LIMIT_DEFAULT = 25;

    /**
     * Ambil data pemeriksaan radiologi
     */
    public static function getAll(
        string $startDate,
        string $endDate,
        int $limit = self::LIMIT_DEFAULT,
        ?string $search = null,
        ?string $status = null,
        ?string $modality = null,
        ?string $gender = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $pmrSub = DB::connection('simrs')->table('permintaan_radiologi')
            ->select('no_rawat', 'tgl_hasil', 'jam_hasil', DB::raw('MAX(dokter_perujuk) as dokter_perujuk'))
            ->groupBy('no_rawat', 'tgl_hasil', 'jam_hasil');

        $query = DB::connection('simrs')
            ->table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'pr.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->leftJoin('mapping_radiologi_modality as mrm', 'pr.kd_jenis_prw', '=', 'mrm.kd_jenis_prw')
            ->leftJoinSub($pmrSub, 'pmr', function ($join) {
                $join->on('pr.no_rawat', '=', 'pmr.no_rawat')
                    ->on('pr.tgl_periksa', '=', 'pmr.tgl_hasil')
                    ->on('pr.jam', '=', 'pmr.jam_hasil');
            })
            ->leftJoin('dokter as d', DB::raw('COALESCE(pmr.dokter_perujuk, pr.dokter_perujuk)'), '=', 'd.kd_dokter')
            ->select([
                'pr.no_rawat',
                'pr.kd_jenis_prw',
                'pr.tgl_periksa',
                'pr.jam',
                'pr.biaya',
                'pr.status',
                'jpr.nm_perawatan as jenis_pemeriksaan',
                DB::raw("COALESCE(
                    mrm.modality_code,
                    CASE
                        WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT'
                        WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US'
                        WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR'
                        WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX'
                        WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG'
                        ELSE 'CR'
                    END
                ) as modality"),
                'rp.no_rkm_medis',
                'p.nm_pasien as nama_pasien',
                'p.jk as jenis_kelamin',
                'd.nm_dokter as nama_dokter_perujuk',
            ])
            ->whereBetween('pr.tgl_periksa', [$startDate, $endDate]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.nm_pasien', 'like', "%$search%")
                    ->orWhere('rp.no_rkm_medis', 'like', "%$search%")
                    ->orWhere('pr.no_rawat', 'like', "%$search%");
            });
        }

        if ($status && $status !== 'semua') {
            $query->where('pr.status', $status);
        }

        if ($modality && $modality !== 'semua') {
            $query->whereRaw("COALESCE(
                mrm.modality_code,
                CASE
                    WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT'
                    WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US'
                    WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR'
                    WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX'
                    WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG'
                    ELSE 'CR'
                END
            ) = ?", [$modality]);
        }

        if ($gender && $gender !== 'semua') {
            $query->where('p.jk', $gender);
        }

        $query->orderByDesc('pr.tgl_periksa')->orderByDesc('pr.jam');

        $result = $limit > 0 ? $query->paginate($limit) : $query->get();

        $collection = $result instanceof LengthAwarePaginator ? $result->getCollection() : $result;

        $collection->transform(fn($row) => self::mapping($row));

        return $result;
    }

    private static function mapping(object $row): array
    {
        return [
            'layanan' => [
                'no_rawat' => $row->no_rawat,
                'kode_jenis_perawatan' => $row->kd_jenis_prw,
                'jenis_pemeriksaan' => $row->jenis_pemeriksaan,
                'modality' => $row->modality ?? '-',
                'tgl_periksa' => DateHelper::dateFormat($row->tgl_periksa, isTranslated: true, translatedFormat: 'd F Y'),
                'jam' => $row->jam,
                'biaya' => $row->biaya,
                'status' => ucfirst(strtolower($row->status)),
            ],
            'pasien' => [
                'no_rekam_medis' => $row->no_rkm_medis,
                'nama' => $row->nama_pasien,
                'jenis_kelamin' => $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            ],
            'dokter' => [
                'nama_dokter' => $row->nama_dokter_perujuk ?? '-',
            ],
        ];
    }

    public static function getRadiologyStatuses(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect(RadiologyExam::KELOMPOK_STATUS)->map(fn($s) => ['title' => $s, 'value' => $s])->toArray(),
        ];
    }

    public static function getRadiologyModalities(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ['title' => 'CR (Rontgen / X-Ray)', 'value' => 'CR'],
            ['title' => 'CT (CT Scan)', 'value' => 'CT'],
            ['title' => 'US (Ultrasonografi)', 'value' => 'US'],
            ['title' => 'MR (MRI)', 'value' => 'MR'],
            ['title' => 'PX (Panoramic)', 'value' => 'PX'],
            ['title' => 'MG (Mammography)', 'value' => 'MG'],
        ];
    }

    public static function formatModalityName(string $code): string
    {
        return match (strtoupper($code)) {
            'CR' => 'CR (Rontgen / X-Ray)',
            'DX' => 'DX (Digital Radiography)',
            'CT' => 'CT (CT Scan)',
            'US' => 'US (Ultrasonografi)',
            'MR' => 'MR (MRI)',
            'PX' => 'PX (Panoramic)',
            'MG' => 'MG (Mammography)',
            default => $code,
        };
    }

    /**
     * Ambil data rekapitulasi dan analisis diagnostik radiologi
     */
    public static function getRecapData(?string $startDate = null, ?string $endDate = null): array
    {
        $base = DB::connection('simrs')->table('periksa_radiologi as pr')
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('pr.tgl_periksa', [$startDate, $endDate]));

        $totalPemeriksaan = (clone $base)->count();

        $uniquePatients = (clone $base)
            ->join('reg_periksa as rp', 'pr.no_rawat', '=', 'rp.no_rawat')
            ->distinct('rp.no_rkm_medis')
            ->count('rp.no_rkm_medis');

        $statusStats = (clone $base)
            ->selectRaw('sum(case when pr.status = "Ranap" then 1 else 0 end) as ranap, sum(case when pr.status = "Ralan" then 1 else 0 end) as ralan')
            ->first();

        // Sebaran Modality Radiologi
        $modalityStats = (clone $base)
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->leftJoin('mapping_radiologi_modality as mrm', 'pr.kd_jenis_prw', '=', 'mrm.kd_jenis_prw')
            ->select(
                DB::raw("COALESCE(
                    mrm.modality_code,
                    CASE
                        WHEN jpr.nm_perawatan LIKE '%CT%' THEN 'CT'
                        WHEN jpr.nm_perawatan LIKE '%USG%' THEN 'US'
                        WHEN jpr.nm_perawatan LIKE '%MR%' THEN 'MR'
                        WHEN jpr.nm_perawatan LIKE '%PANORAMIC%' OR jpr.nm_perawatan LIKE '%DENTAL%' THEN 'PX'
                        WHEN jpr.nm_perawatan LIKE '%MAMMO%' THEN 'MG'
                        ELSE 'CR'
                    END
                ) as modality"),
                DB::raw('count(*) as total')
            )
            ->groupBy('modality')
            ->orderByDesc('total')
            ->get();

        $topPemeriksaan = (clone $base)
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->select('jpr.nm_perawatan', DB::raw('count(*) as total'))
            ->groupBy('jpr.nm_perawatan')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Dokter pengirim diambil dari permintaan_radiologi.dokter_perujuk
        $pmrSub = DB::connection('simrs')->table('permintaan_radiologi')
            ->select('no_rawat', 'tgl_hasil', 'jam_hasil', DB::raw('MAX(dokter_perujuk) as dokter_perujuk'))
            ->groupBy('no_rawat', 'tgl_hasil', 'jam_hasil');

        $topDokter = (clone $base)
            ->leftJoinSub($pmrSub, 'pmr', function ($join) {
                $join->on('pr.no_rawat', '=', 'pmr.no_rawat')
                    ->on('pr.tgl_periksa', '=', 'pmr.tgl_hasil')
                    ->on('pr.jam', '=', 'pmr.jam_hasil');
            })
            ->leftJoin('dokter as d', DB::raw('COALESCE(pmr.dokter_perujuk, pr.dokter_perujuk)'), '=', 'd.kd_dokter')
            ->select(DB::raw('COALESCE(d.nm_dokter, pmr.dokter_perujuk, pr.dokter_perujuk, "-") as nama_dokter'), DB::raw('count(*) as total'))
            ->groupBy('nama_dokter')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $topPenjamin = (clone $base)
            ->join('reg_periksa as rp', 'pr.no_rawat', '=', 'rp.no_rawat')
            ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->select('pj.png_jawab', DB::raw('count(*) as total'))
            ->groupBy('pj.png_jawab')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $trendData = self::getTrendData((clone $base), 'pr.tgl_periksa', $startDate, $endDate);

        return [
            'total_pemeriksaan' => $totalPemeriksaan,
            'unique_patients' => $uniquePatients,
            'status' => [
                'ranap' => (int) ($statusStats->ranap ?? 0),
                'ralan' => (int) ($statusStats->ralan ?? 0),
            ],
            'modality' => $modalityStats,
            'top_pemeriksaan' => $topPemeriksaan,
            'top_dokter' => $topDokter,
            'top_penjamin' => $topPenjamin,
            'charts' => [
                'trend' => [
                    'labels' => $trendData['labels'],
                    'datasets' => [
                        [
                            'label' => 'Total Pemeriksaan',
                            'data' => $trendData['totals'],
                            'borderColor' => '#06b6d4',
                            'backgroundColor' => 'rgba(6, 182, 212, 0.12)',
                            'fill' => true,
                            'tension' => 0.4,
                        ],
                        [
                            'label' => 'Rawat Inap',
                            'data' => $trendData['ranap'],
                            'borderColor' => '#8b5cf6',
                            'backgroundColor' => 'transparent',
                            'borderDash' => [2, 2],
                            'tension' => 0.4,
                        ],
                        [
                            'label' => 'Rawat Jalan',
                            'data' => $trendData['ralan'],
                            'borderColor' => '#0284c7',
                            'backgroundColor' => 'transparent',
                            'borderDash' => [4, 4],
                            'tension' => 0.4,
                        ],
                    ],
                ],
                'modality' => [
                    'labels' => $modalityStats->pluck('modality')->map(fn($m) => self::formatModalityName($m))->toArray(),
                    'datasets' => [[
                        'data' => $modalityStats->pluck('total')->toArray(),
                        'backgroundColor' => ['#06b6d4', '#8b5cf6', '#10b981', '#f59e0b', '#ec4899', '#3b82f6'],
                    ]],
                ],
                'status' => [
                    'labels' => ['Rawat Inap', 'Rawat Jalan'],
                    'datasets' => [[
                        'data' => [(int) ($statusStats->ranap ?? 0), (int) ($statusStats->ralan ?? 0)],
                        'backgroundColor' => ['#8b5cf6', '#0284c7'],
                    ]],
                ],
                'top_pemeriksaan' => [
                    'labels' => $topPemeriksaan->pluck('nm_perawatan')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Pemeriksaan',
                        'data' => $topPemeriksaan->pluck('total')->toArray(),
                        'backgroundColor' => '#06b6d4',
                        'borderRadius' => 6,
                    ]],
                ],
                'top_penjamin' => [
                    'labels' => $topPenjamin->pluck('png_jawab')->toArray(),
                    'datasets' => [[
                        'label' => 'Pemeriksaan',
                        'data' => $topPenjamin->pluck('total')->toArray(),
                        'backgroundColor' => '#f59e0b',
                        'borderRadius' => 6,
                    ]],
                ],
            ],
        ];
    }

    /**
     * Hitung rangkaian waktu kontinu tren pemeriksaan radiologi
     */
    private static function getTrendData($query, string $dateColumn, ?string $startDate, ?string $endDate): array
    {
        $hasRange = $startDate && $endDate;
        $spanInDays = $hasRange ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) : null;

        if (!$hasRange) {
            $data = (clone $query)
                ->selectRaw("YEAR({$dateColumn}) as label, count(*) as total, sum(case when status = 'Ranap' then 1 else 0 end) as ranap, sum(case when status = 'Ralan' then 1 else 0 end) as ralan")
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            return [
                'labels' => $data->pluck('label')->map(fn($v) => (string) $v)->toArray(),
                'totals' => $data->pluck('total')->map(fn($v) => (int) $v)->toArray(),
                'ranap' => $data->pluck('ranap')->map(fn($v) => (int) $v)->toArray(),
                'ralan' => $data->pluck('ralan')->map(fn($v) => (int) $v)->toArray(),
            ];
        }

        if ($spanInDays > 60) {
            $data = (clone $query)
                ->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') as ym, count(*) as total, sum(case when status = 'Ranap' then 1 else 0 end) as ranap, sum(case when status = 'Ralan' then 1 else 0 end) as ralan")
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->keyBy('ym');

            $labels = [];
            $totals = [];
            $ranaps = [];
            $ralans = [];

            $start = \Carbon\Carbon::parse($startDate)->startOfMonth();
            $end = \Carbon\Carbon::parse($endDate)->endOfMonth();

            while ($start->lte($end)) {
                $ym = $start->format('Y-m');
                $labels[] = $start->translatedFormat('M Y');
                $totals[] = (int) ($data[$ym]->total ?? 0);
                $ranaps[] = (int) ($data[$ym]->ranap ?? 0);
                $ralans[] = (int) ($data[$ym]->ralan ?? 0);
                $start->addMonth();
            }

            return [
                'labels' => $labels,
                'totals' => $totals,
                'ranap' => $ranaps,
                'ralan' => $ralans,
            ];
        }

        $data = (clone $query)
            ->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m-%d') as dt, count(*) as total, sum(case when status = 'Ranap' then 1 else 0 end) as ranap, sum(case when status = 'Ralan' then 1 else 0 end) as ralan")
            ->groupBy('dt')
            ->orderBy('dt')
            ->get()
            ->keyBy('dt');

        $labels = [];
        $totals = [];
        $ranaps = [];
        $ralans = [];

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        while ($start->lte($end)) {
            $dt = $start->format('Y-m-d');
            $labels[] = $start->format('d/m');
            $totals[] = (int) ($data[$dt]->total ?? 0);
            $ranaps[] = (int) ($data[$dt]->ranap ?? 0);
            $ralans[] = (int) ($data[$dt]->ralan ?? 0);
            $start->addDay();
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
            'ranap' => $ranaps,
            'ralan' => $ralans,
        ];
    }
}
