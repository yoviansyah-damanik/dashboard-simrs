<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class NutritionCare extends Model
{
    use BaseModelTrait;

    const NO_RAWAT = 'no_rawat';
    const TANGGAL = 'tanggal';
    const NIP = 'nip';

    protected $connection = 'simrs';
    protected $table = 'asuhan_gizi';
    public $timestamps = false;
}
