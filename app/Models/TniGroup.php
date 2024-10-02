<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class TniGroup extends Model
{
    use BaseModelTrait;

    const KODE_GOLONGAN = 'id';
    const NAMA_GOLONGAN = 'nama_golongan';

    protected $table = 'golongan_tni';
    protected $connection = 'simrs';
}
