<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Ethnic extends Model
{
    use BaseModelTrait;

    const KODE_SUKU_BANGSA = 'id';
    const NAMA_SUKU_BANGSA = 'nama_suku_bangsa';

    protected $connection = 'simrs';
    protected $table = 'suku_bangsa';
}
