<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Polyclinic extends Model
{
    use BaseModelTrait;

    const KELOMPOK_STATUS = [0, 1];
    const KODE_POLIKLINIK = 'kd_poli';
    const NAMA_POLIKLINIK = 'nm_poli';
    const REGISTRASI = 'registrasi';
    const REGISTRASI_LAMA = 'registrasilama';
    const STATUS = 'status';

    protected $connection = 'simrs';
    protected $table = 'poliklinik';
}
