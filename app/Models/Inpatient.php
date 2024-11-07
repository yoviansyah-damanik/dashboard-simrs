<?php

namespace App\Models;

use App\Casts\DateCast;
use App\Casts\TimeCast;
use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
    const JAM_KELUAR = 'jam_keluar';
    const LAMA = 'lama';
    const TOTAL_BIAYA = 'ttl_biaya';
    const STATUS_PULANG = 'stts_pulang';

    protected $connection = 'simrs';
    protected $table = 'kamar_inap';

    protected function casts(): array
    {
        return [
            self::TANGGAL_MASUK => DateCast::class,
            self::JAM_MASUK => TimeCast::class,
            self::TANGGAL_KELUAR => DateCast::class,
            self::JAM_KELUAR => TimeCast::class,
        ];
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Room::class, self::KODE_KAMAR, Room::KODE_KAMAR);
    }

    public function icd10(): HasOneThrough
    {
        return $this->hasOneThrough(Icd10::class, Disease::class, Disease::NO_RAWAT, Icd10::KD_PENYAKIT, self::NO_RAWAT, Disease::KD_PENYAKIT);
    }

    public function penyakit(): HasOne
    {
        return $this->hasOne(Disease::class, Disease::NO_RAWAT, self::NO_RAWAT);
    }

    public function icd9(): HasOneThrough
    {
        return $this->hasOneThrough(Icd9::class, Procedure::class, Procedure::NO_RAWAT, Icd9::KD, self::NO_RAWAT, Procedure::KD);
    }

    public function tindakan(): HasOne
    {
        return $this->hasOne(Procedure::class, Procedure::NO_RAWAT, self::NO_RAWAT);
    }
}
