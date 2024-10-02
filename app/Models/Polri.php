<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasOne(TniGroup::class, TniGroup::KODE_GOLONGAN, self::GOLONGAN);
    }
}
