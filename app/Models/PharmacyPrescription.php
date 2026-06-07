<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;

class PharmacyPrescription extends Model
{
    use BaseModelTrait;

    const NO_RESEP = 'no_resep';
    const NO_RAWAT = 'no_rawat';
    const TGL_PERAWATAN = 'tgl_perawatan';
    const JAM = 'jam';
    const KODE_DOKTER = 'kd_dokter';
    const TGL_PERESEPAN = 'tgl_peresepan';
    const JAM_PERESEPAN = 'jam_peresepan';
    const STATUS = 'status'; // ralan | ranap
    const TGL_PENYERAHAN = 'tgl_penyerahan';
    const JAM_PENYERAHAN = 'jam_penyerahan';
    const JENIS_RESEP = 'jenis_resep'; // Biasa | Kronis | CITO | PRB | Kemoterapi

    const KELOMPOK_STATUS = ['ralan', 'ranap'];
    const KELOMPOK_JENIS_RESEP = ['Biasa', 'Kronis', 'CITO', 'PRB', 'Kemoterapi'];

    protected $connection = 'simrs';
    protected $table = 'resep_obat';
    public $timestamps = false;
}
