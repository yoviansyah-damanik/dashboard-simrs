<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    use BaseModelTrait;

    const KODE_KABUPATEN = 'kd_kab';
    const NAMA_KABUPATEN = 'nm_kab';

    protected $connection = 'simrs';
    protected $table = 'kabupaten';
}
