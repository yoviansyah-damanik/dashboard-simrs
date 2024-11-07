<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Icd9 extends Model
{
    const KD = 'kode';
    const DESC = 'deskripsi_pendek';
    const DESC_LONG = 'deskripsi_panjang';

    protected $connection = 'simrs';
    protected $table = 'icd9';

    public function tindakan(): BelongsTo
    {
        return $this->belongsTo(Procedure::class, self::KD, Procedure::KD);
    }
}
