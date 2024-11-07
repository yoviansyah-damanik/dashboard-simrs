<?php

namespace App\Models;

use App\Casts\TimeCast;
use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisteredPatient extends Model
{
    use BaseModelTrait;

    const KODE_IGD = 'IGDK';
    const STATUS_RANAP = 'Ranap';
    const STATUS_RALAN = 'Ralan';

    const KELOMPOK_STATUS_PELAYANAN = ['Sudah', 'Belum', 'Batal', 'Dirujuk', 'Berkas Diterima', 'Dirawat', 'Meninggal', 'Pulang Paksa'];
    const KELOMPOK_STATUS_LANJUT = ['Ranap', 'Ralan'];
    const KELOMPOK_STATUS = ['Lama', 'Baru'];

    const NO_REGISTRASI = 'no_reg';
    const NO_REKAM_MEDIS = 'no_rkm_medis';
    const NO_RAWAT = 'no_rawat';
    const TGL_REGISTRASI = 'tgl_registrasi';
    const JAM_REGISTRASI = 'jam_reg';
    const BIAYA_REGISTRASI = 'biaya_reg';
    const STATUS_PELAYANAN = 'stts';
    const STATUS_DAFTAR = 'stts_daftar';
    const STATUS_LANJUT = 'status_lanjut';
    const UMUR_MENDAFTAR = 'umurdaftar';
    const STATUS_UMUR = 'sttsumur';
    const STATUS_BAYAR = 'status_bayar';
    const STATUS_POLI = 'status_poli';
    const KODE_PENANGGUNGJAWAB = 'kd_pj';
    const KODE_DOKTER = 'kd_dokter';
    const KODE_POLIKLINIK = 'kd_poli';

    public $routeKeyName = 'no_rawat';
    protected $connection = 'simrs';
    protected $table = 'reg_periksa';

    protected function casts(): array
    {
        return [
            self::TGL_REGISTRASI => 'date',
            self::JAM_REGISTRASI => TimeCast::class,
        ];
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Patient::class, self::NO_REKAM_MEDIS, Patient::NO_REKAM_MEDIS);
    }

    public function poliklinik(): BelongsTo
    {
        return $this->belongsTo(Polyclinic::class, self::KODE_POLIKLINIK, Polyclinic::KODE_POLIKLINIK);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, self::KODE_DOKTER, Doctor::KODE_DOKTER);
    }

    public function jenis_bayar(): BelongsTo
    {
        return $this->belongsTo(PersonResponsibility::class, self::KODE_PENANGGUNGJAWAB, PersonResponsibility::KODE_PENANGGUNGJAWAB);
    }

    public function ranap(): HasOne
    {
        return $this->hasOne(Inpatient::class, Inpatient::NO_RAWAT, self::NO_RAWAT);
    }

    public function tni(): HasOne
    {
        return $this->hasOne(Tni::class, Tni::NO_REKAM_MEDIS, self::NO_REKAM_MEDIS);
    }

    public function polri(): HasOne
    {
        return $this->hasOne(Polri::class, Polri::NO_REKAM_MEDIS, self::NO_REKAM_MEDIS);
    }

    public function mobileJkn(): HasOne
    {
        return $this->hasOne(MobileJkn::class, MobileJkn::NO_RAWAT, self::NO_RAWAT);
    }
}
