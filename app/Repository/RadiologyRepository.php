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
        ?string $gender = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $query = DB::connection('simrs')
            ->table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'pr.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('jns_perawatan_radiologi as jpr', 'pr.kd_jenis_prw', '=', 'jpr.kd_jenis_prw')
            ->leftJoin('dokter as d', 'pr.kd_dokter', '=', 'd.kd_dokter')
            ->select([
                'pr.no_rawat',
                'pr.kd_jenis_prw',
                'pr.tgl_periksa',
                'pr.jam',
                'pr.biaya',
                'pr.status',
                'jpr.nm_perawatan as jenis_pemeriksaan',
                'rp.no_rkm_medis',
                'p.nm_pasien as nama_pasien',
                'p.jk as jenis_kelamin',
                'd.nm_dokter as nama_dokter',
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
                'nama_dokter' => $row->nama_dokter ?? '-',
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
}
