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
}
