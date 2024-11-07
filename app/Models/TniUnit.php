<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TniUnit extends Model
{
    use BaseModelTrait;

    const KODE_SATUAN = 'id';
    const NAMA_SATUAN = 'nama_satuan';

    protected $table = 'satuan_tni';
    protected $connection = 'simrs';
}
