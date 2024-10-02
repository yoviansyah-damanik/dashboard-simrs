<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    protected $connection = 'simrs';
    protected $table = 'diagnosa_pasien';
}
