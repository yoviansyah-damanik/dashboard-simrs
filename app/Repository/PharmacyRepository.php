<?php

namespace App\Repository;

use App\Helpers\DateHelper;
use App\Models\PharmacyPrescription;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

interface PharmacyInterface {}

class PharmacyRepository implements PharmacyInterface
{
    const LIMIT_DEFAULT = 25;

    /**
     * Ambil data resep obat (farmasi)
     */
    public static function getAll(
        string $startDate,
        string $endDate,
        int $limit = self::LIMIT_DEFAULT,
        ?string $search = null,
        ?string $status = null,
        ?string $jenisResep = null,
        ?string $gender = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $query = DB::connection('simrs')
            ->table('resep_obat as ro')
            ->join('reg_periksa as rp', 'ro.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('dokter as d', 'ro.kd_dokter', '=', 'd.kd_dokter')
            ->select([
                'ro.no_resep',
                'ro.no_rawat',
                'ro.tgl_perawatan',
                'ro.jam',
                'ro.status',
                'ro.jenis_resep',
                'ro.tgl_peresepan',
                'ro.jam_peresepan',
                'ro.tgl_penyerahan',
                'ro.jam_penyerahan',
                'rp.no_rkm_medis',
                'p.nm_pasien as nama_pasien',
                'p.jk as jenis_kelamin',
                'd.nm_dokter as nama_dokter',
            ])
            ->whereBetween('ro.tgl_perawatan', [$startDate, $endDate]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.nm_pasien', 'like', "%$search%")
                    ->orWhere('rp.no_rkm_medis', 'like', "%$search%")
                    ->orWhere('ro.no_rawat', 'like', "%$search%")
                    ->orWhere('ro.no_resep', 'like', "%$search%");
            });
        }

        if ($status && $status !== 'semua') {
            $query->where('ro.status', $status);
        }

        if ($jenisResep && $jenisResep !== 'semua') {
            $query->where('ro.jenis_resep', $jenisResep);
        }

        if ($gender && $gender !== 'semua') {
            $query->where('p.jk', $gender);
        }

        $query->orderByDesc('ro.tgl_perawatan')->orderByDesc('ro.jam');

        $result = $limit > 0 ? $query->paginate($limit) : $query->get();

        $collection = $result instanceof LengthAwarePaginator ? $result->getCollection() : $result;

        $collection->transform(fn($row) => self::mapping($row));

        return $result;
    }

    private static function mapping(object $row): array
    {
        return [
            'resep' => [
                'no_resep' => $row->no_resep,
                'no_rawat' => $row->no_rawat,
                'tgl_perawatan' => DateHelper::dateFormat($row->tgl_perawatan, isTranslated: true, translatedFormat: 'd F Y'),
                'jam' => $row->jam,
                'status' => ucfirst(strtolower($row->status)),
                'jenis_resep' => $row->jenis_resep,
                'tgl_peresepan' => $row->tgl_peresepan
                    ? DateHelper::dateFormat($row->tgl_peresepan, isTranslated: true, translatedFormat: 'd F Y')
                    : '-',
                'jam_peresepan' => $row->jam_peresepan ?? '-',
                'tgl_penyerahan' => $row->tgl_penyerahan
                    ? DateHelper::dateFormat($row->tgl_penyerahan, isTranslated: true, translatedFormat: 'd F Y')
                    : '-',
                'jam_penyerahan' => $row->jam_penyerahan ?? '-',
            ],
            'pasien' => [
                'no_rekam_medis' => $row->no_rkm_medis,
                'nama' => $row->nama_pasien,
                'jenis_kelamin' => $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            ],
            'dokter' => [
                'nama_dokter' => $row->nama_dokter ?? '-',
            ],
        ];
    }

    public static function getPharmacyStatuses(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ['title' => 'Ralan', 'value' => 'ralan'],
            ['title' => 'Ranap', 'value' => 'ranap'],
        ];
    }

    public static function getJenisResep(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect(PharmacyPrescription::KELOMPOK_JENIS_RESEP)
                ->map(fn($j) => ['title' => $j, 'value' => $j])
                ->toArray(),
        ];
    }
}
