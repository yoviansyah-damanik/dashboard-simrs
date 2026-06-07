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
        $query = DB::connection('simrs')
            ->table('periksa_lab as pl')
            ->join('reg_periksa as rp', 'pl.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('jns_perawatan_lab as jpl', 'pl.kd_jenis_prw', '=', 'jpl.kd_jenis_prw')
            ->leftJoin('dokter as d', 'pl.kd_dokter', '=', 'd.kd_dokter')
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
                'd.nm_dokter as nama_dokter',
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
                'nama_dokter' => $row->nama_dokter ?? '-',
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
}
