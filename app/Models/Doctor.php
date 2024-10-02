<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Doctor extends Model
{
    use BaseModelTrait;
    const KELOMPOK_STATUS = [0, 1];
    const KODE_DOKTER_UMUM = 'S0016';

    const KODE_DOKTER = 'kd_dokter';
    const NAMA_DOKTER = 'nm_dokter';
    const JENIS_KELAMIN = 'jk';
    const TEMPAT_LAHIR = 'tmp_lahir';
    const TANGGAL_LAHIR = 'tgl_lahir';
    const GOL_DARAH = 'gol_drh';
    const AGAMA = 'agama';
    const ALAMAT = 'almt_tgl';
    const NO_TELP = 'no_telp';
    const STATUS_NIKAH = 'stts_nikah';
    const ALUMNI = 'alumni';
    const NO_IZIN_PRAKTEK = 'no_ijn_praktek';
    const STATUS = 'status';
    const KODE_SPESIALIS = 'kd_sps';

    protected $connection = 'simrs';
    protected $table = 'dokter';

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date',
        ];
    }

    public function spesialis(): HasOne
    {
        return $this->hasOne(Specialist::class, 'kd_sps', 'kd_sps');
    }
}
