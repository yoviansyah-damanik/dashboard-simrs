<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Icd10 extends Model
{
    use BaseModelTrait;

    const KD_PENYAKIT = 'kd_penyakit';
    const NM_PENYAKIT = 'nm_penyakit';
    const CIRI_CIRI = 'ciri_ciri';
    const KETERANGAN = 'keterangan';
    const KD_KTG = 'kd_ktg';
    const STATUS = 'status';

    protected $connection = 'simrs';
    protected $table = 'penyakit';

    public function penyakit(): HasMany
    {
        return $this->hasMany(Icd10::class, Icd10::KD_PENYAKIT, self::KD_PENYAKIT);
    }
}
