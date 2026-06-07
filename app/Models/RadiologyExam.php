<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class RadiologyExam extends Model
{
    use BaseModelTrait;

    const NO_RAWAT = 'no_rawat';
    const KODE_JENIS_PERAWATAN = 'kd_jenis_prw';
    const TGL_PERIKSA = 'tgl_periksa';
    const JAM = 'jam';
    const KODE_DOKTER = 'kd_dokter';
    const STATUS = 'status'; // Ranap | Ralan
    const BIAYA = 'biaya';

    const KELOMPOK_STATUS = ['Ralan', 'Ranap'];

    protected $connection = 'simrs';
    protected $table = 'periksa_radiologi';
    public $timestamps = false;
}
