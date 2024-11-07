<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disease extends Model
{
    use BaseModelTrait;

    const NO_RAWAT = 'no_rawat';
    const KD_PENYAKIT = 'kd_penyakit';
    const STATUS = 'status';
    const PRIORITAS = 'prioritas';
    const STATUS_PENYAKIT = 'status_penyakit';

    protected $connection = 'simrs';
    protected $table = 'diagnosa_pasien';

    public function icd10(): BelongsTo
    {
        return $this->belongsTo(Disease::class, self::KD_PENYAKIT, Disease::KD_PENYAKIT);
    }
}
