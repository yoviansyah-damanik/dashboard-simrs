<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    const NO_RAWAT = 'no_rawat';
    const KD = 'kode';
    const STATUS = 'status';
    const PRIORITAS = 'prioritas';



    protected $connection = 'simrs';
    protected $table = 'prosedur_pasien';
}
