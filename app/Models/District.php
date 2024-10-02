<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use BaseModelTrait;

    const KODE_KECAMATAN = 'kd_kec';
    const NAMA_KECAMATAN = 'nm_kec';

    protected $connection = 'simrs';
    protected $table = 'kecamatan';
}
