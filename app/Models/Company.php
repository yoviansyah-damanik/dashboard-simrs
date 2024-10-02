<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use BaseModelTrait;

    const KODE_PERUSAHAAN = 'kode_perusahaan';
    const NAMA_PERUSAHAAN = 'nama_perusahaan';

    public $routeKeyName = self::KODE_PERUSAHAAN;
    protected $connection = 'simrs';
    protected $table = 'perusahaan_pasien';
}
