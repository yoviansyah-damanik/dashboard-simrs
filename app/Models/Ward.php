<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use BaseModelTrait;

    const KELOMPOK_STATUS = [0, 1];

    const KODE_BANGSAL = 'kd_bangsal';
    const NAMA_BANGSAL = 'nm_bangsal';
    const STATUS = 'status';

    protected $connection = 'simrs';
    protected $table = 'bangsal';
}
