<?php

namespace App\Repository;

use App\Helpers\DateHelper;
use App\Models\LabExam;
use App\Models\Patient;
use App\Models\RegisteredPatient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

interface LaboratoryInterface {}

class LaboratoryRepository implements LaboratoryInterface
{
    const LIMIT_DEFAULT = 25;

    /**
     * Ambil data pemeriksaan laboratorium
     */
    public static function getAll(
        string $startDate,
        string $endDate,
        int $limit = self::LIMIT_DEFAULT,
        ?string $search = null,
        ?string $status = null,
        ?string $kategori = null,
        ?string $gender = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $pmlSub = DB::connection('simrs')->table('permintaan_lab')
            ->select('no_rawat', 'tgl_hasil', 'jam_hasil', DB::raw('MAX(dokter_perujuk) as dokter_perujuk'))
            ->groupBy('no_rawat', 'tgl_hasil', 'jam_hasil');

        $query = DB::connection('simrs')
            ->table('periksa_lab as pl')
            ->join('reg_periksa as rp', 'pl.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('jns_perawatan_lab as jpl', 'pl.kd_jenis_prw', '=', 'jpl.kd_jenis_prw')
            ->leftJoinSub($pmlSub, 'pml', function ($join) {
                $join->on('pl.no_rawat', '=', 'pml.no_rawat')
                    ->on('pl.tgl_periksa', '=', 'pml.tgl_hasil')
                    ->on('pl.jam', '=', 'pml.jam_hasil');
            })
            ->leftJoin('dokter as d', DB::raw('COALESCE(pml.dokter_perujuk, pl.dokter_perujuk)'), '=', 'd.kd_dokter')
            ->select([
                'pl.no_rawat',
                'pl.kd_jenis_prw',
                'pl.tgl_periksa',
                'pl.jam',
                'pl.biaya',
                'pl.status',
                'pl.kategori',
                'jpl.nm_perawatan as jenis_pemeriksaan',
                'rp.no_rkm_medis',
                'p.nm_pasien as nama_pasien',
                'p.jk as jenis_kelamin',
                'd.nm_dokter as nama_dokter_perujuk',
            ])
            ->whereBetween('pl.tgl_periksa', [$startDate, $endDate]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.nm_pasien', 'like', "%$search%")
                    ->orWhere('rp.no_rkm_medis', 'like', "%$search%")
                    ->orWhere('pl.no_rawat', 'like', "%$search%");
            });
        }

        if ($status && $status !== 'semua') {
            $query->where('pl.status', $status);
        }

        if ($kategori && $kategori !== 'semua') {
            $query->where('pl.kategori', $kategori);
        }

        if ($gender && $gender !== 'semua') {
            $query->where('p.jk', $gender);
        }

        $query->orderByDesc('pl.tgl_periksa')->orderByDesc('pl.jam');

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
                'tgl_periksa' => DateHelper::dateFormat($row->tgl_periksa, isTranslated: true, translatedFormat: 'd F Y'),
                'jam' => $row->jam,
                'biaya' => $row->biaya,
                'status' => ucfirst(strtolower($row->status)),
                'kategori' => $row->kategori,
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

    public static function getLabStatuses(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect(LabExam::KELOMPOK_STATUS)->map(fn($s) => ['title' => $s, 'value' => $s])->toArray(),
        ];
    }

    public static function getLabKategori(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect(LabExam::KELOMPOK_KATEGORI)->map(fn($k) => ['title' => $k, 'value' => $k])->toArray(),
        ];
    }

    /**
     * Ambil data rekapitulasi dan analisis diagnostik laboratorium
     */
    public static function getRecapData(?string $startDate = null, ?string $endDate = null): array
    {
        $base = DB::connection('simrs')->table('periksa_lab as pl')
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('pl.tgl_periksa', [$startDate, $endDate]));

        $totalPemeriksaan = (clone $base)->count();

        $uniquePatients = (clone $base)
            ->join('reg_periksa as rp', 'pl.no_rawat', '=', 'rp.no_rawat')
            ->distinct('rp.no_rkm_medis')
            ->count('rp.no_rkm_medis');

        $statusStats = (clone $base)
            ->selectRaw('sum(case when pl.status = "Ralan" then 1 else 0 end) as ralan, sum(case when pl.status = "Ranap" then 1 else 0 end) as ranap')
            ->first();

        $kategoriStats = (clone $base)
            ->select('pl.kategori', DB::raw('count(*) as total'))
            ->groupBy('pl.kategori')
            ->get();

        $topPemeriksaan = (clone $base)
            ->join('jns_perawatan_lab as jpl', 'pl.kd_jenis_prw', '=', 'jpl.kd_jenis_prw')
            ->select('jpl.nm_perawatan', DB::raw('count(*) as total'))
            ->groupBy('jpl.nm_perawatan')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Dokter pengirim diambil dari permintaan_lab.dokter_perujuk
        $pmlSub = DB::connection('simrs')->table('permintaan_lab')
            ->select('no_rawat', 'tgl_hasil', 'jam_hasil', DB::raw('MAX(dokter_perujuk) as dokter_perujuk'))
            ->groupBy('no_rawat', 'tgl_hasil', 'jam_hasil');

        $topDokter = (clone $base)
            ->leftJoinSub($pmlSub, 'pml', function ($join) {
                $join->on('pl.no_rawat', '=', 'pml.no_rawat')
                    ->on('pl.tgl_periksa', '=', 'pml.tgl_hasil')
                    ->on('pl.jam', '=', 'pml.jam_hasil');
            })
            ->leftJoin('dokter as d', DB::raw('COALESCE(pml.dokter_perujuk, pl.dokter_perujuk)'), '=', 'd.kd_dokter')
            ->select(DB::raw('COALESCE(d.nm_dokter, pml.dokter_perujuk, pl.dokter_perujuk, "-") as nama_dokter'), DB::raw('count(*) as total'))
            ->groupBy('nama_dokter')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $topPenjamin = (clone $base)
            ->join('reg_periksa as rp', 'pl.no_rawat', '=', 'rp.no_rawat')
            ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->select('pj.png_jawab', DB::raw('count(*) as total'))
            ->groupBy('pj.png_jawab')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $trendData = self::getTrendData((clone $base), 'pl.tgl_periksa', $startDate, $endDate);

        return [
            'total_pemeriksaan' => $totalPemeriksaan,
            'unique_patients' => $uniquePatients,
            'status' => [
                'ralan' => (int) ($statusStats->ralan ?? 0),
                'ranap' => (int) ($statusStats->ranap ?? 0),
            ],
            'kategori' => $kategoriStats,
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
                            'borderColor' => '#059669',
                            'backgroundColor' => 'rgba(5, 150, 105, 0.12)',
                            'fill' => true,
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
                        [
                            'label' => 'Rawat Inap',
                            'data' => $trendData['ranap'],
                            'borderColor' => '#8b5cf6',
                            'backgroundColor' => 'transparent',
                            'borderDash' => [2, 2],
                            'tension' => 0.4,
                        ],
                    ],
                ],
                'status' => [
                    'labels' => ['Rawat Jalan', 'Rawat Inap'],
                    'datasets' => [[
                        'data' => [(int) ($statusStats->ralan ?? 0), (int) ($statusStats->ranap ?? 0)],
                        'backgroundColor' => ['#0284c7', '#8b5cf6'],
                    ]],
                ],
                'top_pemeriksaan' => [
                    'labels' => $topPemeriksaan->pluck('nm_perawatan')->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Pemeriksaan',
                        'data' => $topPemeriksaan->pluck('total')->toArray(),
                        'backgroundColor' => '#10b981',
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
     * Hitung rangkaian waktu kontinu tren pemeriksaan laboratorium
     */
    private static function getTrendData($query, string $dateColumn, ?string $startDate, ?string $endDate): array
    {
        $hasRange = $startDate && $endDate;
        $spanInDays = $hasRange ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) : null;

        if (!$hasRange) {
            $data = (clone $query)
                ->selectRaw("YEAR({$dateColumn}) as label, count(*) as total, sum(case when status = 'Ralan' then 1 else 0 end) as ralan, sum(case when status = 'Ranap' then 1 else 0 end) as ranap")
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            return [
                'labels' => $data->pluck('label')->map(fn($v) => (string) $v)->toArray(),
                'totals' => $data->pluck('total')->map(fn($v) => (int) $v)->toArray(),
                'ralan' => $data->pluck('ralan')->map(fn($v) => (int) $v)->toArray(),
                'ranap' => $data->pluck('ranap')->map(fn($v) => (int) $v)->toArray(),
            ];
        }

        if ($spanInDays > 60) {
            $data = (clone $query)
                ->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') as ym, count(*) as total, sum(case when status = 'Ralan' then 1 else 0 end) as ralan, sum(case when status = 'Ranap' then 1 else 0 end) as ranap")
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->keyBy('ym');

            $labels = [];
            $totals = [];
            $ralans = [];
            $ranaps = [];

            $start = \Carbon\Carbon::parse($startDate)->startOfMonth();
            $end = \Carbon\Carbon::parse($endDate)->endOfMonth();

            while ($start->lte($end)) {
                $ym = $start->format('Y-m');
                $labels[] = $start->translatedFormat('M Y');
                $totals[] = (int) ($data[$ym]->total ?? 0);
                $ralans[] = (int) ($data[$ym]->ralan ?? 0);
                $ranaps[] = (int) ($data[$ym]->ranap ?? 0);
                $start->addMonth();
            }

            return [
                'labels' => $labels,
                'totals' => $totals,
                'ralan' => $ralans,
                'ranap' => $ranaps,
            ];
        }

        $data = (clone $query)
            ->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m-%d') as dt, count(*) as total, sum(case when status = 'Ralan' then 1 else 0 end) as ralan, sum(case when status = 'Ranap' then 1 else 0 end) as ranap")
            ->groupBy('dt')
            ->orderBy('dt')
            ->get()
            ->keyBy('dt');

        $labels = [];
        $totals = [];
        $ralans = [];
        $ranaps = [];

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        while ($start->lte($end)) {
            $dt = $start->format('Y-m-d');
            $labels[] = $start->format('d/m');
            $totals[] = (int) ($data[$dt]->total ?? 0);
            $ralans[] = (int) ($data[$dt]->ralan ?? 0);
            $ranaps[] = (int) ($data[$dt]->ranap ?? 0);
            $start->addDay();
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
            'ralan' => $ralans,
            'ranap' => $ranaps,
        ];
    }
}
