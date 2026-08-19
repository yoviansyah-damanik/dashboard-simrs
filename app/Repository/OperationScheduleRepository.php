<?php

namespace App\Repository;

use App\Models\OperationSchedule;
use Illuminate\Pagination\LengthAwarePaginator;

interface OperationScheduleInterface
{
    public static function getAll(
        string $startDate,
        string $endDate,
        ?string $search = null,
        ?string $status = null,
        ?string $room = null,
        ?string $doctor = null,
        int $limit = 25
    ): LengthAwarePaginator | \Illuminate\Support\Collection;
}

class OperationScheduleRepository implements OperationScheduleInterface
{
    const LIMIT_DEFAULT = 25;

    /**
     * Fungsi ini untuk memanggil data jadwal operasi beserta relasi pasien, paket, dokter, dan ruang operasi.
     */
    public static function getAll(
        string $startDate,
        string $endDate,
        ?string $search = null,
        ?string $status = null,
        ?string $room = null,
        ?string $doctor = null,
        int $limit = self::LIMIT_DEFAULT
    ): LengthAwarePaginator | \Illuminate\Support\Collection {
        $result = OperationSchedule::with(['registeredPatient.pasien', 'paket', 'dokter', 'ruangOperasi'])
            ->whereBetween(OperationSchedule::TANGGAL, [$startDate, $endDate])
            ->when(
                in_array($status, OperationSchedule::KELOMPOK_STATUS),
                fn($q) => $q->where(OperationSchedule::STATUS, $status)
            )
            ->when(
                $room && $room != 'semua',
                fn($q) => $q->where(OperationSchedule::KODE_RUANG_OK, $room)
            )
            ->when(
                $doctor && $doctor != 'semua',
                fn($q) => $q->where(OperationSchedule::KODE_DOKTER, $doctor)
            )
            ->when(
                $search,
                fn($q) => $q->where(
                    fn($w) => $w->where(OperationSchedule::NO_RAWAT, 'like', '%' . $search . '%')
                        ->orWhereHas('registeredPatient.pasien', fn($r) => $r->where('nm_pasien', 'like', '%' . $search . '%'))
                )
            )
            ->orderBy(OperationSchedule::TANGGAL, 'desc')
            ->orderBy(OperationSchedule::JAM_MULAI, 'desc')
            ->when($limit > 0, fn($q) => $q->paginate($limit), fn($q) => $q->get());

        $collection = $result instanceof LengthAwarePaginator ? $result->getCollection() : $result;

        $collection->transform(fn($item) => [
            'no_rawat' => $item->no_rawat,
            'tanggal' => $item->tanggal,
            'jam_mulai' => $item->jam_mulai,
            'jam_selesai' => $item->jam_selesai,
            'status' => $item->status,
            'nama_pasien' => $item->registeredPatient?->pasien?->nm_pasien,
            'no_rekam_medis' => $item->registeredPatient?->no_rkm_medis,
            'nama_paket' => $item->paket?->nm_perawatan,
            'nama_dokter' => $item->dokter?->nm_dokter,
            'nama_ruang' => $item->ruangOperasi?->nm_ruang_ok,
        ]);

        return $result;
    }
}
