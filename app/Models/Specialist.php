<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialist extends Model
{
    use BaseModelTrait;

    const KODE_SPESIALIS = 'kd_sps';
    const NAMA_SPESIALIS = 'nm_sps';

    protected $connection = 'simrs';
    protected $table = 'spesialis';

    public function dokter(): HasMany
    {
        return $this->hasMany(Doctor::class, 'kd_sps', 'kd_sps');
    }
}
