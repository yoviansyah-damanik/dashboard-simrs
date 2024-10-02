<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tni extends Model
{
    use BaseModelTrait;

    const NO_REKAM_MEDIS = 'no_rkm_medis';
    const GOLONGAN = 'golongan_tni';
    const PANGKAT = 'pangkat_tni';
    const SATUAN = 'satuan_tni';
    const JABATAN = 'jabatan_tni';

    protected $connection = 'simrs';
    protected $table = 'pasien_tni';

    public function golongan(): HasOne
    {
        return $this->hasOne(TniGroup::class, TniGroup::KODE_GOLONGAN, self::GOLONGAN);
    }
}
