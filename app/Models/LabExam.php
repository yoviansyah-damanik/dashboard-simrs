<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class LabExam extends Model
{
    use BaseModelTrait;

    const NO_RAWAT = 'no_rawat';
    const KODE_JENIS_PERAWATAN = 'kd_jenis_prw';
    const TGL_PERIKSA = 'tgl_periksa';
    const JAM = 'jam';
    const KODE_DOKTER = 'kd_dokter';
    const STATUS = 'status'; // Ralan | Ranap
    const KATEGORI = 'kategori'; // PA | PK | MB
    const BIAYA = 'biaya';

    const KELOMPOK_STATUS = ['Ralan', 'Ranap'];
    const KELOMPOK_KATEGORI = ['PA', 'PK', 'MB'];

    protected $connection = 'simrs';
    protected $table = 'periksa_lab';
    public $timestamps = false;
}
