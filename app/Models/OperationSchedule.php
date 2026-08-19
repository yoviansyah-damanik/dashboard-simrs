<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationSchedule extends Model
{
    use BaseModelTrait;

    const KELOMPOK_STATUS = ['Menunggu', 'Proses Operasi', 'Selesai'];

    const NO_RAWAT = 'no_rawat';
    const KODE_PAKET = 'kode_paket';
    const TANGGAL = 'tanggal';
    const JAM_MULAI = 'jam_mulai';
    const JAM_SELESAI = 'jam_selesai';
    const STATUS = 'status';
    const KODE_DOKTER = 'kd_dokter';
    const KODE_RUANG_OK = 'kd_ruang_ok';

    public $incrementing = false;
    protected $connection = 'simrs';
    protected $table = 'booking_operasi';

    protected function casts(): array
    {
        return [
            self::TANGGAL => 'date',
        ];
    }

    public function registeredPatient(): BelongsTo
    {
        return $this->belongsTo(RegisteredPatient::class, self::NO_RAWAT, RegisteredPatient::NO_RAWAT);
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(OperationPackage::class, self::KODE_PAKET, OperationPackage::KODE_PAKET);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, self::KODE_DOKTER, Doctor::KODE_DOKTER);
    }

    public function ruangOperasi(): BelongsTo
    {
        return $this->belongsTo(OperatingRoom::class, self::KODE_RUANG_OK, OperatingRoom::KODE_RUANG_OK);
    }
}
