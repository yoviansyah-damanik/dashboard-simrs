<?php

namespace App\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

interface NonmedicalPersonnelInterface {}

class NonmedicalPersonnelRepository implements NonmedicalPersonnelInterface
{
    const LIMIT_DEFAULT = 24;

    const KELOMPOK_STATUS_AKTIF = ['AKTIF', 'CUTI', 'KELUAR', 'TENAGA LUAR'];

    /**
     * Ambil data tenaga non medis (pegawai)
     */
    public static function getAll(
        int $limit = self::LIMIT_DEFAULT,
        ?string $search = null,
        ?string $statusAktif = null,
        ?string $departemen = null,
        ?string $gender = null
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $query = DB::connection('simrs')
            ->table('pegawai as pg')
            ->leftJoin('departemen as dep', 'pg.departemen', '=', 'dep.dep_id')
            ->leftJoin('stts_kerja as sk', 'pg.stts_kerja', '=', 'sk.stts')
            ->select([
                'pg.id',
                'pg.nik',
                'pg.nama',
                'pg.jk',
                'pg.jbtn',
                'pg.departemen as kode_departemen',
                'dep.nama as nama_departemen',
                'pg.stts_aktif',
                'sk.ktg as status_kerja',
                'pg.pendidikan',
                'pg.mulai_kerja',
                'pg.ms_kerja',
                'pg.no_ktp',
            ])
            ->orderBy('pg.nama');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pg.nama', 'like', "%$search%")
                    ->orWhere('pg.nik', 'like', "%$search%")
                    ->orWhere('pg.no_ktp', 'like', "%$search%");
            });
        }

        if ($statusAktif && $statusAktif !== 'semua') {
            $query->where('pg.stts_aktif', $statusAktif);
        }

        if ($departemen && $departemen !== 'semua') {
            $query->where('pg.departemen', $departemen);
        }

        if ($gender && $gender !== 'semua') {
            $query->where('pg.jk', $gender);
        }

        return $limit > 0 ? $query->paginate($limit) : $query->get();
    }

    public static function getStatusOptions(): array
    {
        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect(self::KELOMPOK_STATUS_AKTIF)
                ->map(fn($s) => ['title' => $s, 'value' => $s])
                ->toArray(),
        ];
    }

    public static function getDepartemenOptions(): array
    {
        $rows = DB::connection('simrs')->table('departemen')->orderBy('nama')->get();

        return [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect($rows)->map(fn($r) => ['title' => $r->nama, 'value' => $r->dep_id])->toArray(),
        ];
    }
}
