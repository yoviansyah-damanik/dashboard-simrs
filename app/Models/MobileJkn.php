<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class MobileJkn extends Model
{
    use BaseModelTrait;

    const NO_RAWAT = 'no_rawat';

    protected $connection = 'simrs';
    protected $table = 'referensi_mobilejkn_bpjs';
}
