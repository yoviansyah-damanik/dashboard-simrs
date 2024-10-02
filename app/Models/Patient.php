<?php

namespace App\Models;

use App\Traits\BaseModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use BaseModelTrait;

    const KELOMPOK_PENDIDIKAN = ['TS', 'TK', 'SD', 'SMP', 'SMA', 'SLTA/SEDERAJAT', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', '-'];
    const KELOMPOK_GOLONGAN_DARAH = ['A', 'B', 'O', 'AB', '-'];
    const KELOMPOK_STATUS_NIKAH = ['BELUM MENIKAH', 'MENIKAH', 'JANDA', 'DUDHA', 'JOMBLO'];
    const KELOMPOK_PENANGGUNGJAWAB = ['AYAH', 'IBU', 'ISTRI', 'SUAMI', 'SAUDARA', 'ANAK', 'DIRI SENDIRI', 'LAIN-LAIN'];
    const KELOMPOK_STATUS_KELUAR = ['AYAH', 'IBU', 'ISTRI', 'SUAMI', 'SAUDARA', 'ANAK', 'DIRI SENDIRI', 'LAIN-LAIN'];

    const KELOMPOK_JENIS_KELAMIN = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan'
    ];
    const KELOMPOK_PASIEN = [
        'umum' => 'UMUM',
        'polri' => 'POLRI',
        'tni' => 'TNI'
    ];
    const KELOMPOK_UMUR = [
        'balita' => 'Balita (<5 Tahun)',
        'anak' => 'Anak-anak (5-11 Tahun)',
        'remaja' => 'Remaja (12-25 Tahun)',
        'dewasa' => 'Dewasa (26-45 Tahun)',
        'lansia' => 'Lansia (46-65 Tahun)',
        'lainnya' => 'Lainnya (>65 Tahun)'
    ];

    const NO_REKAM_MEDIS = 'no_rkm_medis';
    const NAMA_PASIEN = 'nm_pasien';
    const NIK = 'no_ktp';
    const NOKA = 'no_peserta';
    const TANGGAL_LAHIR = 'tgl_lahir';
    const TEMPAT_LAHIR = 'tmp_lahir';
    const UMUR = 'umur';
    const JENIS_KELAMIN = 'jk';
    const NAMA_IBU = 'nm_ibu';
    const GOL_DARAH = 'gol_darah';
    const PEKERJAAN = 'pekerjaan';
    const STATUS_NIKAH = 'stts_nikah';
    const AGAMA = 'agama';
    const NO_TELP = 'no_tlp';
    const PENDIDIKAN = 'pnd';
    const ALAMAT = 'alamat';
    const KELURAHAN = 'kd_kel';
    const KECAMATAN = 'kd_kec';
    const KABUPATEN = 'kd_kab';
    const PROPINSI = 'kd_prop';
    const EMAIL = 'email';
    const NIP = 'nip';
    const KODE_PERUSAHAAN = 'perusahaan_pasien';
    const TGL_DAFTAR = 'tgl_daftar';
    const NAMA_CACAT = 'nama_cacat';
    const NAMA_PJ = 'namakeluarga';
    const STATUS_PJ = 'keluarga';
    const PEKERJAAN_PJ = 'pekerjaanpj';
    const ALAMAT_PJ = 'alamatpj';
    const KELURAHAN_PJ = 'kelurahanpj';
    const KECAMATAN_PJ = 'kecamatanpj';
    const KABUPATEN_PJ = 'kabupatenpj';
    const PROPINSI_PJ = 'propinsipj';
    const PENANGGUNGJAWAB = 'kd_pj';
    const SUKU = 'suku_bangsa';
    const BAHASA = 'bahasa_pasien';
    const CACAT_FISIK = 'cacat_fisik';

    public $routeKeyName = self::NO_REKAM_MEDIS;
    protected $connection = 'simrs';
    protected $table = 'pasien';

    protected function casts(): array
    {
        return [
            self::TGL_DAFTAR => 'date',
            self::TANGGAL_LAHIR => 'date',
        ];
    }

    public function propinsi(): HasOne
    {
        return $this->hasOne(Province::class, Province::KODE_PROPINSI, self::PROPINSI);
    }

    public function kabupaten(): HasOne
    {
        return $this->hasOne(Regency::class, Regency::KODE_KABUPATEN, self::KABUPATEN);
    }

    public function kecamatan(): HasOne
    {
        return $this->hasOne(District::class, District::KODE_KECAMATAN, self::KECAMATAN);
    }

    public function kelurahan(): HasOne
    {
        return $this->hasOne(Village::class, Village::KODE_KELURAHAN, self::KELURAHAN);
    }

    public function penanggungjawab(): HasOne
    {
        return $this->hasOne(PersonResponsibility::class, PersonResponsibility::KODE_PENANGGUNGJAWAB, self::PENANGGUNGJAWAB);
    }

    public function perusahaan_pasien(): HasOne
    {
        return $this->hasOne(Company::class, Company::KODE_PERUSAHAAN, self::KODE_PERUSAHAAN);
    }

    public function suku(): HasOne
    {
        return $this->hasOne(Ethnic::class, Ethnic::KODE_SUKU_BANGSA, self::SUKU);
    }

    public function bahasa(): HasOne
    {
        return $this->hasOne(Language::class, Language::KODE_BAHASA, self::BAHASA);
    }

    public function cacat(): HasOne
    {
        return $this->hasOne(Disability::class, Disability::KODE_CACAT, self::CACAT_FISIK);
    }

    public function tni(): HasOne
    {
        return $this->hasOne(Tni::class, Tni::NO_REKAM_MEDIS, self::NO_REKAM_MEDIS);
    }

    public function polri(): HasOne
    {
        return $this->hasOne(Polri::class, Polri::NO_REKAM_MEDIS, self::NO_REKAM_MEDIS);
    }
}
