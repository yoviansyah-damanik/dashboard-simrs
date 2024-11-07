<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Polri extends Model
{
    use BaseModelTrait;

    const NO_REKAM_MEDIS = 'no_rkm_medis';
    const GOLONGAN = 'golongan_polri';
    const PANGKAT = 'pangkat_polri';
    const SATUAN = 'satuan_polri';
    const JABATAN = 'jabatan_polri';

    protected $connection = 'simrs';
    protected $table = 'pasien_polri';

    public function golongan(): HasOne
    {
        return $this->hasOne(PolriGroup::class, PolriGroup::KODE_GOLONGAN, self::GOLONGAN);
    }

    public function satuan(): HasOne
    {
        return $this->hasOne(PolriUnit::class, PolriUnit::KODE_SATUAN, self::SATUAN);
    }
}
