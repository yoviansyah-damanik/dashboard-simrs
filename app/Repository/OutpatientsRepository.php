<?php

namespace App\Repository;

use App\Models\Tni;
use App\Models\Polri;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Helpers\DateHelper;
use App\Models\RegisteredPatient;
use App\Models\PersonResponsibility;
use App\Repository\PolyclinicRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface OutpatientsInterface {}

class OutpatientsRepository implements OutpatientsInterface
{
    const LIMIT_DEFAULT = 25; // JUMLAH DATA PER HALAMAN DITAMPILKAN DEFAULT

    private static $personResponsibilityData;
    private static $polyclinicData;

    /**
     * Fungsi untuk menentukan relasi tabel.
     * Seluruh relasi didenefisikan pada fungsi ini untuk menghindari N+1.
     * Key dari setiap relasi merupakan relasi yang telah diimplementasi pada model Patient.
     * Null untuk menampilkan seluruh kolom atau array berisi nama-nama kolom.
     * @return void
     */
    private function relations(): array
    {
        // Wajib Relasi ke tabel pasien, poli, dan dokter.
        // Jangan hapus relasi ini. Cukup sesuaikan saja.
        return [
            'pasien' => [
                'table_name' => Patient::getTableName(),
                'column_added' => null
            ],
            'poliklinik' => [
                'table_name' => Polyclinic::getTableName(),
                'column_added' => null
            ],
            'dokter' => [
                'table_name' => Doctor::getTableName(),
                'column_added' => null
            ],
            'jenis_bayar' => [
                'table_name' => PersonResponsibility::getTableName(),
                'column_added' => ['png_jawab', 'nama_perusahaan', 'alamat_asuransi']
            ],
        ];
    }

    /**
     * Fungsi ini digunakan untuk menstrukturisasi ulang struktur data pasien
     * menjadi satu metadata yang utuh.
     * Perhatikan setiap key harus berbeda. Jika terdapat key yang sama, mapping ulang key tersebut.
     * @return array
     */
    private function reconstruction(RegisteredPatient $patient): array
    {
        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang

        // Menghilangkan variabel ganda
        $exceptRegistrationData = ["kd_dokter", "kd_poli", "p_jawab", "almt_pj", "hubunganpj", "kd_pj"];
        $exceptPatientData = ['tgl_daftar'];
        $exceptPolyclinicData = [];
        $exceptDoctorData = [];

        $includesRelation = ['jenis_bayar'];

        $result = collect([
            'pendaftaran' => collect($patient->withoutRelations())->except($exceptRegistrationData)->toArray(),
            'pasien' => collect(PatientRepository::getMapping($patient->pasien))->except($exceptPatientData)->toArray(),
            'poliklinik' => collect(PolyclinicRepository::getMapping($patient->poliklinik))->except($exceptPolyclinicData)->toArray(),
            'dokter' => collect(DoctorRepository::getMapping($patient->dokter))->except($exceptDoctorData)->toArray(),
            ...collect((new static)->relations())
                ->filter(fn($item, $key) => in_array($key, $includesRelation))
                ->map(
                    function ($item, $key) use ($patient) {
                        if (!empty($item['column_added']) && is_array($item['column_added']))
                            return collect($patient->{$key})
                                ->only($item['column_added'])
                                ->toArray();

                        return $patient->{$key}->toArray();
                    }
                )
        ])
            ->except($exceptData)
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
            'pendaftaran' => [
                'no_pendaftaran' => $data['pendaftaran'][RegisteredPatient::NO_REGISTRASI],
                'no_rekam_medis' => $data['pendaftaran'][RegisteredPatient::NO_REKAM_MEDIS],
                'no_rawat' => $data['pendaftaran'][RegisteredPatient::NO_RAWAT],
                'tgl_pendaftaran' => DateHelper::dateFormat($data['pendaftaran'][RegisteredPatient::TGL_REGISTRASI]),
                'jam_pendaftaran' => $data['pendaftaran'][RegisteredPatient::JAM_REGISTRASI],
                'waktu_pendaftaran' => DateHelper::dateFormat($data['pendaftaran'][RegisteredPatient::TGL_REGISTRASI], isTranslated: true, translatedFormat: 'd F Y') . ' ' . $data['pendaftaran'][RegisteredPatient::JAM_REGISTRASI],
                'biaya_pendaftaran' => $data['pendaftaran'][RegisteredPatient::BIAYA_REGISTRASI],
                'status_pelayanan' => $data['pendaftaran'][RegisteredPatient::STATUS_PELAYANAN], // Sudah | Belum | Batal | Dirujuk | Berkas Diterima | Dirawat | Meninggal | Pulang Paksa
                'status_daftar' => $data['pendaftaran'][RegisteredPatient::STATUS_DAFTAR], // Lama | Baru
                'status_lanjut' => $data['pendaftaran'][RegisteredPatient::STATUS_LANJUT], // Ranap | Ralan
                'umur_mendaftar' => $data['pendaftaran'][RegisteredPatient::UMUR_MENDAFTAR] . ' ' . ($data['pendaftaran'][RegisteredPatient::STATUS_UMUR] == 'Th' ? 'Tahun' : 'Bulan'),
                'status_umur' => $data['pendaftaran'][RegisteredPatient::STATUS_UMUR], // Bl | Th
                'status_bayar' => $data['pendaftaran'][RegisteredPatient::STATUS_BAYAR], // Sudah Bayar | Belum Bayar
                'status_poli' => $data['pendaftaran'][RegisteredPatient::STATUS_POLI], // Baru | Lama
                'jenis_bayar' => $data['jenis_bayar'][PersonResponsibility::PENANGGUNGJAWAB],
            ],
            ...collect($data)->except('pendaftaran')->toArray()
        ];
    }

    /**
     * Fungsi ini untuk memanggil data pasien terdaftar
     * @param string $startDate Variabel untuk menentukan tanggal mulai
     * @param string $endDate Variabel untuk menentukan tanggal akhir
     * @param string $gender Hanya 'L' | 'P' atau kosongkan jika semua
     * @param string $ageCategory null, 'balita' | 'anak-anak' | 'remaja' | 'dewasa' | 'lansia' | 'lainnya' atau kosongkan jika semua
     * @param string $status Sudah | Belum | Batal | Dirujuk | Berkas Diterima | Dirawat | Meninggal | Pulang Paksa atau kosongkan jika semua
     * @return LengthAwarePaginator | array
     */
    public static function getAll(
        string $startDate,
        string $endDate,
        int $limit = self::LIMIT_DEFAULT,
        ?string $search = null,
        ?string $ageCategory = null,
        ?string $gender = null,
        ?string $status = null,
        ?string $type = null,
        ?string $payType = null,
        ?string $polyclinic = null,
        ?string $doctor = null,
        ?string $tniGroup = null,
        ?string $polriGroup = null
    ): LengthAwarePaginator | array {
        $result = RegisteredPatient::with(
            [
                ...collect((new static)->relations())->keys()->toArray(),
                ...collect(PatientRepository::getRelations())
                    ->map(function ($item, $key) {
                        return 'pasien.' . $key;
                    })
                    ->values()
                    ->toArray(),
                ...collect(DoctorRepository::getRelations())
                    ->map(function ($item, $key) {
                        return 'dokter.' . $key;
                    })
                    ->values()
                    ->toArray(),
                ...collect(PolyclinicRepository::getRelations())
                    ->map(function ($item, $key) {
                        return 'poliklinik.' . $key;
                    })
                    ->values()
                    ->toArray(),
            ]
        )
            ->whereHas('pasien', function ($q) use ($search, $type, $gender) {
                if (in_array($gender, collect(Patient::KELOMPOK_JENIS_KELAMIN)->keys()->toArray()))
                    $q->where(Patient::JENIS_KELAMIN, $gender);

                if (in_array($type, collect(Patient::KELOMPOK_PASIEN)->keys()->toArray())) {
                    // Sesuaikan dengan KELOMPOK PASIEN
                    if ($type == 'umum')
                        $q->doesntHave('polri')
                            ->doesntHave('tni');
                    if ($type == 'polri')
                        $q->has('polri');
                    if ($type == 'tni')
                        $q->has('tni');
                }
                $q->whereAny([RegisteredPatient::NO_REKAM_MEDIS, Patient::NAMA_PASIEN, Patient::NIK, Patient::NOKA], 'like', $search . "%");
            });

        switch ($ageCategory) {
            case 'balita':
                $result = $result->whereRaw('((' . RegisteredPatient::UMUR_MENDAFTAR . ' < 5 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\') or ' . RegisteredPatient::STATUS_UMUR . ' = \'Bl\')');
                break;
            case 'anak':
                $result = $result->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 5 and 11 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\'');
                break;
            case 'remaja':
                $result = $result->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 12 and 25 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\'');
                break;
            case 'dewasa':
                $result = $result->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 26 and 45 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\'');
                break;
            case 'lansia':
                $result = $result->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 46 and 65 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\'');
                break;
            case 'lainnya':
                $result = $result->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' > 65 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\'');
                break;
            default:
                break;
        }

        if (!is_null($doctor) && $doctor != 'semua') {
            $result = $result->where(RegisteredPatient::KODE_DOKTER, $doctor);
        }

        if (in_array($status, RegisteredPatient::KELOMPOK_STATUS_PELAYANAN)) {
            $result = $result->where(RegisteredPatient::STATUS_PELAYANAN, $status);
        }

        if (!is_null($payType) && $payType != 'semua') {
            $result = $result->where(RegisteredPatient::KODE_PENANGGUNGJAWAB, $payType);
        }

        if (!is_null($polyclinic) && $polyclinic != 'semua') {
            $result = $result->where(RegisteredPatient::KODE_POLIKLINIK, $polyclinic);
        }

        if (!is_null($tniGroup) && $tniGroup != 'semua') {
            $result = $result->whereHas('tni', fn($q) => $q->where(Tni::GOLONGAN, $tniGroup));
        };

        if (!is_null($polriGroup) && $polriGroup != 'semua') {
            $result = $result->whereHas('polri', fn($q) => $q->where(Polri::GOLONGAN, $polriGroup));
        };

        $result = $result->when($startDate, function (Builder $query, string $startDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '>=', $startDate);
        });

        $result = $result->when($endDate, function (Builder $query, string $endDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '<=', $endDate);
        });

        $result = $result
            ->where(RegisteredPatient::STATUS_LANJUT, RegisteredPatient::STATUS_RALAN)
            ->whereNot(RegisteredPatient::KODE_POLIKLINIK, RegisteredPatient::KODE_IGD)
            ->orderBy(RegisteredPatient::TGL_REGISTRASI, 'desc')
            ->orderBy(RegisteredPatient::JAM_REGISTRASI, 'desc')
            ->paginate($limit);

        return tap($result, function ($paginatedInstance) {
            return $paginatedInstance->getCollection()->transform(function ($value) {
                return (new self)
                    ->mapping((new self)->reconstruction($value));
            });
        });
    }

    /**
     * Fungsi ini untuk memanggil data pasien terdaftar
     * @param string $startDate Variabel untuk menentukan tanggal mulai
     * @param string $endDate Variabel untuk menentukan tanggal akhir
     * @return void
     */
    public static function getRecap(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $type = null,
    ) {
        $result = new RegisteredPatient;

        $result = $result->when($startDate, function (Builder $query, string $startDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '>=', $startDate);
        });

        $result = $result->when($endDate, function (Builder $query, string $endDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '<=', $endDate);
        });

        if (!static::$personResponsibilityData)
            static::$personResponsibilityData = collect(PersonResponsibilityRepository::getAll(limit: 0))->pluck('kode_penanggungjawab')->toArray();

        $result = $result->whereIn(RegisteredPatient::getTableName() . '.' . RegisteredPatient::KODE_PENANGGUNGJAWAB, static::$personResponsibilityData);

        if (!static::$polyclinicData)
            static::$polyclinicData = collect(PolyclinicRepository::getAll(limit: 0))->pluck('kode_poliklinik')->toArray();

        $result = $result->whereIn(RegisteredPatient::getTableName() . '.' . RegisteredPatient::KODE_POLIKLINIK, static::$polyclinicData);

        switch ($type) {
            case 'ageGroup':
                $result = $result
                    ->selectRaw(
                        'IFNULL(SUM(CASE WHEN ((' . RegisteredPatient::UMUR_MENDAFTAR . ' < 5 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\') or ' . RegisteredPatient::STATUS_UMUR . ' = \'Bl\') THEN 1 ELSE 0 END),0) AS \'balita\','
                            . 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::UMUR_MENDAFTAR . ' between 5 and 11 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\' THEN 1 ELSE 0 END),0) AS \'anak\','
                            . 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::UMUR_MENDAFTAR . ' between 12 and 25 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\' THEN 1 ELSE 0 END),0) AS \'remaja\','
                            . 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::UMUR_MENDAFTAR . ' between 26 and 45 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\' THEN 1 ELSE 0 END),0) AS \'dewasa\','
                            . 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::UMUR_MENDAFTAR . ' between 46 and 65 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\' THEN 1 ELSE 0 END),0) AS \'lansia\','
                            . 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::UMUR_MENDAFTAR . ' > 65 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\' THEN 1 ELSE 0 END),0) AS \'lainnya\''
                    )
                    ->first()
                    ->toArray();
                break;
            case 'genderGroup':
                $condition = '';

                foreach (Patient::KELOMPOK_JENIS_KELAMIN as $key => $jenisKelamin) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . Patient::getTableName() . '.' . Patient::JENIS_KELAMIN . ' = \'' . $key . '\' THEN 1 ELSE 0 END),0) AS \'' . $key . '\'';

                    if (array_search($key, array_values(array_keys(Patient::KELOMPOK_JENIS_KELAMIN))) < count(Patient::KELOMPOK_JENIS_KELAMIN) - 1)
                        $condition .= ',';
                }

                $result = $result
                    ->join(Patient::getTableName(), Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS, '=', RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS)
                    ->selectRaw($condition)
                    ->first()
                    ->toArray();
                break;
            case 'typeGroup':
                $result = $result
                    ->selectRaw(
                        'IFNULL(SUM(CASE WHEN ' . Polri::PANGKAT . ' IS NOT NULL THEN 1 ELSE 0 END), 0) as \'polri\','
                            . 'IFNULL(SUM(CASE WHEN ' . Tni::PANGKAT . ' IS NOT NULL THEN 1 ELSE 0 END), 0) as \'tni\','
                            . 'IFNULL(SUM(CASE WHEN ' . Tni::PANGKAT . ' IS NULL AND ' . Polri::PANGKAT . ' IS NULL THEN 1 ELSE 0 END), 0) as \'umum\''
                    )
                    ->join(Tni::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Tni::getTableName() . '.' . Tni::NO_REKAM_MEDIS, 'left')
                    ->join(Polri::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Polri::getTableName() . '.' . Polri::NO_REKAM_MEDIS, 'left')
                    ->first()
                    ->toArray();
                break;
            case 'statusGroup':
                $condition = '';
                foreach (RegisteredPatient::KELOMPOK_STATUS_PELAYANAN as $idx => $status) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::STATUS_PELAYANAN . ' = \'' . $status . '\' THEN 1 ELSE 0 END),0) AS \'' . $status . '\'';

                    if ($idx < count(RegisteredPatient::KELOMPOK_STATUS_PELAYANAN) - 1)
                        $condition .= ',';
                }

                $result = $result
                    ->selectRaw($condition)
                    ->first()
                    ->toArray();
                break;
            case 'payTypes':
                $condition = '';
                $personResponsibilityData = PersonResponsibilityRepository::getAll(limit: 0);
                foreach ($personResponsibilityData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::KODE_PENANGGUNGJAWAB . ' = \'' . $item['kode_penanggungjawab'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['penanggungjawab'] . '\'';

                    if ($idx < count($personResponsibilityData) - 1)
                        $condition .= ',';
                }

                $result = $result
                    ->selectRaw($condition)
                    ->first()
                    ->toArray();
                break;
            case 'polyclinicGroup':
                $condition = '';
                $polyclinicData = PolyclinicRepository::getAll(limit: 0);
                foreach ($polyclinicData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::KODE_POLIKLINIK . ' = \'' . $item['kode_poliklinik'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_poliklinik'] . '\'';

                    if ($idx < count($polyclinicData) - 1)
                        $condition .= ',';
                }

                $result = $result
                    ->selectRaw($condition)
                    ->first()
                    ->toArray();
                break;
            case 'doctorGroup':
                $condition = '';
                $doctorData = DoctorRepository::getAll(limit: 0);
                foreach ($doctorData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::KODE_DOKTER . ' = \'' . $item['kode_dokter'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_dokter'] . '\'';

                    if ($idx < count($doctorData) - 1)
                        $condition .= ',';
                }

                $result = $result
                    ->selectRaw($condition)
                    ->first()
                    ->toArray();
                break;
            default:
                $result = $result->selectRaw('IFNULL(count(' . RegisteredPatient::NO_RAWAT . '),0) as count')
                    ->first()['count'];
                break;
        }

        return $result;
    }
}
