<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use BaseModelTrait;

    const KODE_PROPINSI = 'kd_prop';
    const NAMA_PROPINSI = 'nm_prop';

    protected $connection = 'simrs';
    protected $table = 'propinsi';
}
