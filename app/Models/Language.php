<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use BaseModelTrait;

    const KODE_BAHASA = 'id';
    const NAMA_BAHASA = 'nama_bahasa';

    protected $connection = 'simrs';
    protected $table = 'bahasa_pasien';
}
