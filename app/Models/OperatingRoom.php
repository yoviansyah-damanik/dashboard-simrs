<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class OperatingRoom extends Model
{
    use BaseModelTrait;

    const KODE_RUANG_OK = 'kd_ruang_ok';
    const NAMA_RUANG_OK = 'nm_ruang_ok';

    protected $connection = 'simrs';
    protected $table = 'ruang_ok';
}
