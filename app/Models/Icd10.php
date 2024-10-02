<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icd10 extends Model
{
    protected $connection = 'simrs';
    protected $table = 'diagnosa';
}
