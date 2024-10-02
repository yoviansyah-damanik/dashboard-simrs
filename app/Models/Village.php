<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use BaseModelTrait;

    const KODE_KELURAHAN = 'kd_kel';
    const NAMA_KELURAHAN = 'nm_kel';

    protected $connection = 'simrs';
    protected $table = 'kelurahan';
}
