<?php

namespace App\Repository;

use App\Models\Tni;
use App\Models\Polri;
use App\Models\Ethnic;
use App\Models\Company;
use App\Models\Patient;
use App\Models\Regency;
use App\Models\Village;
use App\Models\District;
use App\Models\Language;
use App\Models\Province;
use App\Models\TniGroup;
use App\Models\Disability;
use App\Models\PolriGroup;
use App\Helpers\DateHelper;
use App\Models\PersonResponsibility;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientInterface
{
    public static function getPatient(Patient $patient): array;
    public static function getAll(
        int $limit,
        ?string $ageCategory = null,
        ?string $search = null,
        ?string $gender = null
    ): LengthAwarePaginator | array;
    public static function getMapping(Patient $patient): array;
    public static function getRelations();
}

class PatientRepository implements PatientInterface
{
    const LIMIT_DEFAULT = 25; // JUMLAH DATA PER HALAMAN DITAMPILKAN DEFAULT

    /**
     * Fungsi untuk menentukan relasi tabel.
     * Seluruh relasi didenefisikan pada fungsi ini untuk menghindari N+1.
     * Key dari setiap relasi merupakan relasi yang telah diimplementasi pada model Patient.
     * Null untuk menampilkan seluruh kolom atau array berisi nama-nama kolom.
     * @return array
     */
    private function relations(): array
    {
        return [
            'kelurahan' => [
                'table_name' => Village::getTableName(),
                'column_added' => [Village::NAMA_KELURAHAN]
            ],
            'kecamatan' => [
                'table_name' => District::getTableName(),
                'column_added' => [District::NAMA_KECAMATAN]
            ],
            'kabupaten' => [
                'table_name' => Regency::getTableName(),
                'column_added' => [Regency::NAMA_KABUPATEN]
            ],
            'propinsi' => [
                'table_name' => Province::getTableName(),
                'column_added' => [Province::NAMA_PROPINSI]
            ],
            'suku' => [
                'table_name' => Ethnic::getTableName(),
                'column_added' => [Ethnic::NAMA_SUKU_BANGSA]
            ],
            'cacat' => [
                'table_name' => Disability::getTableName(),
                'column_added' => [Disability::NAMA_CACAT]
            ],
            'bahasa' => [
                'table_name' => Language::getTableName(),
                'column_added' => [Language::NAMA_BAHASA]
            ],
            'penanggungjawab' => [
                'table_name' => PersonResponsibility::getTableName(),
                'column_added' => [PersonResponsibility::PENANGGUNGJAWAB, PersonResponsibility::NAMA, PersonResponsibility::ALAMAT]
            ],
            'perusahaan_pasien' => [
                'table_name' => Company::getTableName(),
                'column_added' => [Company::NAMA_PERUSAHAAN]
            ],
            'tni' => [
                'table_name' => Tni::getTableName(),
                'column_added' => [
                    Tni::GOLONGAN,
                    Tni::PANGKAT,
                    Tni::SATUAN,
                    Tni::JABATAN
                ]
            ],
            'polri' => [
                'table_name' => Polri::getTableName(),
                'column_added' => [
                    Polri::GOLONGAN,
                    Polri::PANGKAT,
                    Polri::SATUAN,
                    Polri::JABATAN
                ]
            ],
            'tni.golongan' => [
                'table_name' => TniGroup::getTableName(),
                'column_added' => []
            ],
            'polri.golongan' => [
                'table_name' => PolriGroup::getTableName(),
                'column_added' => []
            ]
        ];
    }

    /**
     * Fungsi ini digunakan untuk menstrukturisasi ulang struktur data pasien
     * menjadi satu metadata yang utuh.
     * Perhatikan setiap key harus berbeda. Jika terdapat key yang sama, mapping ulang key tersebut.
     * @return array
     */
    private function reconstruction(Patient $patient): array
    {
        // Menghilangkan variabel ganda
        $exceptPatientData = [
            ...collect($this->relations())->map(fn($item, $key) => $key)
        ];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang

        $result = collect([
            ...collect($patient)
                ->except($exceptPatientData)
                ->toArray(),
            ...collect((new static)->relations())->map(
                function ($item, $key) use ($patient) {
                    if (!empty($item['column_added']) && is_array($item['column_added']))
                        return collect($patient->{$key})
                            ->only($item['column_added'])
                            ->toArray();

                    return $patient->{$key}?->toArray();
                },
            )->collapse(),
            ...collect($patient->tni ? $patient->tni->golongan->toArray() : []),
            ...collect($patient->polri ? $patient->polri->golongan->toArray() : []),
        ])->except($exceptData)
            ->toArray();

        return $result;
    }

    /**
     * Fungsi ini bersifat final. Merubah mapping berakibat pada struktur data yang ditampilkan.
     * Jika ingin merubah isi data, ubahlah pada fungsi reconstruction().
     * @param $patient array
     * @return array
     */
    private function mapping(array $data): array
    {
        return [
            'data' => [
                'no_rekam_medis' => $data[Patient::NO_REKAM_MEDIS],
                'nik' => $data[Patient::NIK],
                'nama' => $data[Patient::NAMA_PASIEN],
                'tanggal_lahir' => DateHelper::dateFormat($data[Patient::TANGGAL_LAHIR], isTranslated: true),
                'tempat_lahir' => $data[Patient::TEMPAT_LAHIR],
                'umur' => DateHelper::getAge($data[Patient::TANGGAL_LAHIR], true, true),
                'jenis_kelamin' => $data[Patient::JENIS_KELAMIN] == 'L' ? 'Laki-laki' : 'Perempuan',
                'nama_ibu' => $data[Patient::NAMA_IBU],
                'gol_darah' => $data[Patient::GOL_DARAH],
                'pekerjaan' => $data[Patient::PEKERJAAN],
                'status_nikah' => $data[Patient::STATUS_NIKAH],
                'agama' => $data[Patient::AGAMA],
                'no_telp' => $data[Patient::NO_TELP],
                'pendidikan' => $data[Patient::PENDIDIKAN],
                'alamat' => $data[Patient::ALAMAT] . ', ' . $data[Patient::KELURAHAN] . ', ' . $data[Patient::KECAMATAN] . ', ' . $data[Patient::KABUPATEN] . ', ' . $data[Patient::PROPINSI],
                'suku_bangsa' => $data[Ethnic::NAMA_SUKU_BANGSA],
                'bahasa' => $data[Patient::BAHASA],
                'email' => $data[Patient::EMAIL],
                'nip' => $data[Patient::NIP],
                'perusahaan' => $data[Company::NAMA_PERUSAHAAN],
                'cacat_fisik' => $data[Disability::NAMA_CACAT],
                'no_peserta' => $data[Patient::NOKA],
                'jenis_bayar' => $data[PersonResponsibility::PENANGGUNGJAWAB],
                'jenis_pasien' => empty($data[Polri::PANGKAT]) && empty($data[Tni::PANGKAT]) ? 'Umum' : (!empty($data[Polri::PANGKAT]) ? 'Polri' : 'TNI (' . $data[TniGroup::NAMA_GOLONGAN] . ')'),
            ],
            'penanggungjawab' => [
                'nama' => $data[Patient::NAMA_PJ],
                'status' => $data[Patient::STATUS_PJ],
                'pekerjaan' => $data[Patient::PEKERJAAN_PJ],
                'alamat' => $data[Patient::ALAMAT_PJ],
                'kelurahan' => $data[Patient::KELURAHAN_PJ],
                'kecamatan' => $data[Patient::KECAMATAN_PJ],
                'kabupaten' => $data[Patient::KABUPATEN_PJ],
                'propinsi' => $data[Patient::PROPINSI_PJ],
            ],
            'tgl_daftar' => DateHelper::dateFormat($data[Patient::TGL_DAFTAR]),
        ];
    }

    /**
     * Fungsi ini untuk mapping data pasien dari luar repo.
     * Data yang diberikan berupa Data Pasien atau Nomor Rekam Medis Pasien
     * @return array
     */
    public static function getMapping(Patient | int $patient): array
    {
        if (is_integer($patient)) {
            $patient = Patient::where(Patient::NO_REKAM_MEDIS, $patient)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($patient));
    }

    /**
     * Fungsi ini digunakan untuk memberikan variabel relasi ketika dipanggil dari class lain.
     * @return void
     */
    public static function getRelations()
    {
        return (new static)->relations();
    }

    /**
     * Fungsi ini untuk memanggil data per pasien.
     * @param $patient Patient
     * @return array
     */
    public static function getPatient(Patient $patient): array
    {
        return (new self)
            ->mapping((new self)->reconstruction($patient->load(collect((new static)->relations())->keys()->toArray())));
    }

    /**
     * Fungsi ini untuk memanggil data pasien
     * @param string $ageCategory null, 'balita' | 'anak-anak' | 'remaja' | 'dewasa' | 'lansia' | 'lainnya'
     * @param string $gender Hanya 'L' | 'P' atau kosongkan jika semua
     * @return array
     */
    public static function getAll(
        int $limit = self::LIMIT_DEFAULT,
        ?string $ageCategory = null,
        ?string $search = null,
        ?string $gender = null,
        ?string $type = null,
        ?string $payType = null,
        ?string $tniGroup = null,
        ?string $polriGroup = null
    ): LengthAwarePaginator | array {
        $result = Patient::with([
            ...collect((new static)->relations())->keys()->toArray()
        ]);

        switch ($ageCategory) {
            case 'balita':
                $result = $result->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) < 5');
                break;
            case 'anak':
                $result = $result->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 5 and 11');
                break;
            case 'remaja':
                $result = $result->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 12 and 25');
                break;
            case 'dewasa':
                $result = $result->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 26 and 45');
                break;
            case 'lansia':
                $result = $result->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 46 and 65');
                break;
            case 'lainnya':
                $result = $result->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) > 65');
                break;
            default:
                break;
        }

        if (in_array($gender, collect(Patient::KELOMPOK_JENIS_KELAMIN)->keys()->toArray())) {
            $result = $result->where(Patient::JENIS_KELAMIN, $gender);
        }

        if (in_array($type, collect(Patient::KELOMPOK_PASIEN)->keys()->toArray())) {
            // Sesuaikan dengan KELOMPOK PASIEN
            if ($type == 'umum')
                $result = $result->doesntHave('polri')
                    ->doesntHave('tni');
            if ($type == 'polri')
                $result = $result->has('polri');
            if ($type == 'tni')
                $result = $result->has('tni');
        }

        if (in_array($payType, collect(PersonResponsibilityRepository::getAll())->pluck('kode_penanggungjawab')->toArray())) {
            $result = $result->where(Patient::PENANGGUNGJAWAB, $payType);
        }

        if (!is_null($tniGroup) && $tniGroup != 'semua') {
            $result = $result->whereHas('tni', fn($q) => $q->where(Tni::GOLONGAN, $tniGroup));
        };

        if (!is_null($polriGroup) && $polriGroup != 'semua') {
            $result = $result->whereHas('polri', fn($q) => $q->where(Polri::GOLONGAN, $polriGroup));
        };

        $result = $result->orderBy(Patient::NO_REKAM_MEDIS, 'ASC')
            ->orderBy(Patient::NAMA_PASIEN, 'ASC')
            ->whereAny([Patient::NO_REKAM_MEDIS, Patient::NAMA_PASIEN, Patient::NIK, Patient::NOKA], 'like', $search . "%")
            ->paginate($limit);

        return tap($result, function ($paginatedInstance) {
            return $paginatedInstance->getCollection()->transform(function ($value) {
                return (new self)
                    ->mapping((new self)->reconstruction($value));
            });
        });
    }
}
