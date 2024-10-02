<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class Disability extends Model
{
    use BaseModelTrait;

    const KODE_CACAT = 'id';
    const NAMA_CACAT = 'nama_suku_bangsa';

    protected $connection = 'simrs';
    protected $table = 'cacat_fisik';
}
