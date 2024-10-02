<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use BaseModelTrait;

    const KELOMPOK_STATUS = [0, 1];

    const KODE_KAMAR = 'kd_kamar';
    const KODE_BANGSAL = 'kd_bangsal';
    const TARIF_KAMAR = 'trf_kamar';
    const STATUS = 'status';
    const KELAS = 'kelas';
    const STATUS_KAMAR = 'statusdata';

    protected $connection = 'simrs';
    protected $table = 'kamar';

    public function bangsal(): BelongsTo
    {
        return $this->belongsTo(Ward::class, self::KODE_BANGSAL, Ward::KODE_BANGSAL);
    }
}
