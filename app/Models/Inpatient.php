<?php

namespace App\Models;

use App\Casts\TimeCast;
use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inpatient extends Model
{
    use BaseModelTrait;

    const NO_RAWAT = 'no_rawat';
    const KODE_KAMAR = 'kd_kamar';
    const TARIF_KAMAR = 'trf_kamar';
    const DIAGNOSA_AWAL = 'diagnosa_awal';
    const DIAGNOSA_AKHIR = 'diagnosa_akhir';
    const TANGGAL_MASUK = 'tgl_masuk';
    const JAM_MASUK = 'jam_masuk';
    const TANGGAL_KELUAR = 'tgl_keluar';
    const JAM_KELUAR = 'tgl_keluar';
    const LAMA = 'lama';
    const TOTAL_BIAYA = 'ttl_biaya';
    const STATUS_PULANG = 'stts_pulang';

    protected $connection = 'simrs';
    protected $table = 'kamar_inap';

    protected function casts(): array
    {
        return [
            self::TANGGAL_MASUK => 'date',
            self::JAM_MASUK => TimeCast::class,
            self::TANGGAL_KELUAR => 'date',
            self::JAM_KELUAR => TimeCast::class,
        ];
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Room::class, self::KODE_KAMAR, Room::KODE_KAMAR);
    }
}
