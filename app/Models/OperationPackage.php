<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class OperationPackage extends Model
{
    use BaseModelTrait;

    const KODE_PAKET = 'kode_paket';
    const NAMA_PERAWATAN = 'nm_perawatan';
    const KATEGORI = 'kategori';

    protected $connection = 'simrs';
    protected $table = 'paket_operasi';
}
