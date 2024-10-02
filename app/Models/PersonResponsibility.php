<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonResponsibility extends Model
{
    use BaseModelTrait;

    const KELOMPOK_STATUS = [0, 1];

    const KODE_PENANGGUNGJAWAB = 'kd_pj';
    const PENANGGUNGJAWAB = 'png_jawab';
    const NAMA = 'nama_perusahaan';
    const ALAMAT = 'alamat_asuransi';
    const NO_TELP = 'no_telp';
    const ATTN = 'attn';
    const STATUS = 'status';

    protected $connection = 'simrs';
    protected $table = 'penjab';

    public function pasien(): HasMany
    {
        return $this->hasMany(Patient::class, 'kd_pj', 'kd_pj');
    }
}
