<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class PolriUnit extends Model
{
    use BaseModelTrait;

    const KODE_SATUAN = 'id';
    const NAMA_SATUAN = 'nama_satuan';

    protected $table = 'satuan_polri';
    protected $connection = 'simrs';
}
