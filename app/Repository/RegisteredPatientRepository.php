<?php

namespace App\Repository;

use App\Models\Tni;
use App\Models\Polri;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MobileJkn;
use App\Models\Polyclinic;
use App\Helpers\DateHelper;
use App\Models\RegisteredPatient;
use App\Helpers\ConfigurationHelper;
use App\Models\PersonResponsibility;
use App\Repository\PolyclinicRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface RegisteredPatientInterface {}

class RegisteredPatientRepository implements RegisteredPatientInterface
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
        ?string $mobileJkn = null,
        ?string $search = null,
        ?string $ageCategory = null,
        ?string $gender = null,
        ?string $serviceStatus = null,
        ?string $status = null,
        ?string $advanceStatus = null,
        ?string $type = null,
        ?string $payType = null,
        ?string $polyclinic = null,
        ?string $doctor = null,
        ?string $tniGroup = null,
        ?string $tniUnit = null,
        ?string $polriGroup = null,
        ?string $polriUnit = null,
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
            ->whereHas('pasien', function ($q) use ($search, $type, $gender, $tniGroup, $tniUnit, $polriGroup, $polriUnit) {
                $q->when(
                    (in_array($gender, collect(Patient::KELOMPOK_JENIS_KELAMIN)->keys()->toArray())),
                    fn($r) => $r->where(Patient::JENIS_KELAMIN, $gender)
                )->when(
                    in_array($type, collect(Patient::KELOMPOK_PASIEN)->keys()->toArray()),
                    fn($r) =>
                    // Sesuaikan dengan KELOMPOK PASIEN
                    $r->when(
                        $type == 'umum',
                        fn($s) => $s->doesntHave('polri')
                            ->doesntHave('tni')
                    )->when(
                        $type == 'tni',
                        fn($r) => $r->whereHas('tni')
                            ->when(
                                !is_null($tniGroup) && $tniGroup != 'semua',
                                fn($s) => $s->whereHas('tni', fn($s) => $s->where(Tni::GOLONGAN, $tniGroup))
                            )
                            ->when(
                                !is_null($tniUnit) && $tniUnit != 'semua',
                                fn($s) => $s->whereHas('tni', fn($s) => $s->where(Tni::SATUAN, $tniUnit))
                            )
                    )->when(
                        $type == 'polri',
                        fn($r) => $r->whereHas('polri')
                            ->when(
                                !is_null($polriGroup) && $polriGroup != 'semua',
                                fn($s) => $s->whereHas('polri', fn($s) => $s->where(Polri::GOLONGAN, $polriGroup))
                            )
                            ->when(
                                !is_null($polriUnit) && $polriUnit != 'semua',
                                fn($s) => $s->whereHas('polri', fn($s) => $s->where(Polri::SATUAN, $polriUnit))
                            )
                    )
                )
                    ->whereAny([RegisteredPatient::NO_REKAM_MEDIS, Patient::NAMA_PASIEN, Patient::NIK, Patient::NOKA], 'like', $search . "%");
            })
            ->when(
                $ageCategory,
                fn($q) => $q->whereHas(
                    'pasien',
                    fn($r) =>
                    $r
                        ->when($ageCategory == 'balita', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) < 5'))
                        ->when($ageCategory == 'anak', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 5 and 11'))
                        ->when($ageCategory == 'remaja', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 12 and 25'))
                        ->when($ageCategory == 'dewasa', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 26 and 45'))
                        ->when($ageCategory == 'lansia', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 46 and 65'))
                        ->when($ageCategory == 'lainnya', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) > 65'))
                )

                //    ->when($ageCategory == 'balita', fn($r) => $r->whereRaw('((' . RegisteredPatient::UMUR_MENDAFTAR . ' < 5 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\') or ' . RegisteredPatient::STATUS_UMUR . ' = \'Bl\')'))
                //         ->when($ageCategory == 'anak', fn($r) => $r->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 5 and 11 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\''))
                //         ->when($ageCategory == 'remaja', fn($r) => $r->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 12 and 25 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\''))
                //         ->when($ageCategory == 'dewasa', fn($r) => $r->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 26 and 45 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\''))
                //         ->when($ageCategory == 'lansia', fn($r) => $r->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' between 46 and 65 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\''))
                //         ->when($ageCategory == 'lainnya', fn($r) => $r->whereRaw(RegisteredPatient::UMUR_MENDAFTAR . ' > 65 and ' . RegisteredPatient::STATUS_UMUR . ' = \'Th\''))
            )
            ->when(!is_null($doctor) && $doctor != 'semua', fn($q) => $q->where(RegisteredPatient::KODE_DOKTER, $doctor))
            ->when(in_array($serviceStatus, RegisteredPatient::KELOMPOK_STATUS_PELAYANAN), fn($q) => $q->where(RegisteredPatient::STATUS_PELAYANAN, $serviceStatus))
            ->when(in_array($status, RegisteredPatient::KELOMPOK_STATUS), fn($q) => $q->where(RegisteredPatient::STATUS_DAFTAR, $status))
            ->when(in_array($advanceStatus, RegisteredPatient::KELOMPOK_STATUS_LANJUT), fn($q) => $q->where(RegisteredPatient::STATUS_LANJUT, $advanceStatus))
            ->when(!is_null($payType) && $payType != 'semua', fn($q) => $q->where(RegisteredPatient::KODE_PENANGGUNGJAWAB, $payType))
            ->when(!is_null($polyclinic) && $polyclinic != 'semua', fn($q) => $q->where(RegisteredPatient::KODE_POLIKLINIK, $polyclinic))
            ->when($startDate, function (Builder $query, string $startDate) {
                $query->where(RegisteredPatient::TGL_REGISTRASI, '>=', $startDate);
            })
            ->when($endDate, function (Builder $query, string $endDate) {
                $query->where(RegisteredPatient::TGL_REGISTRASI, '<=', $endDate);
            })
            ->when(
                $mobileJkn != 'semua',
                fn($q) => $q->when(
                    $mobileJkn == 'mobileJkn',
                    fn($r) => $r->whereHas('mobileJkn'),
                    fn($r) => $r->whereDoesntHave('mobileJkn')
                ),
            )
            ->orderBy(RegisteredPatient::TGL_REGISTRASI, 'desc')
            ->orderBy(RegisteredPatient::JAM_REGISTRASI, 'desc')
            ->paginate($limit);

        return tap(
            $result,
            fn($paginatedInstance) => $paginatedInstance->getCollection()->transform(
                fn($value) => (new self)
                    ->mapping((new self)->reconstruction($value))
            )
        );
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
        return RegisteredPatient::when($startDate, function (Builder $query, string $startDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '>=', $startDate);
        })->when($endDate, function (Builder $query, string $endDate) {
            $query->where(RegisteredPatient::TGL_REGISTRASI, '<=', $endDate);
        })->when(
            !static::$personResponsibilityData,
            fn($q) => $q->whereIn(
                RegisteredPatient::getTableName() . '.' . RegisteredPatient::KODE_PENANGGUNGJAWAB,
                collect(PersonResponsibilityRepository::getAll(limit: 0))->pluck('kode_penanggungjawab')->toArray()
            )
        )->when(
            !static::$polyclinicData,
            fn($q) => $q->whereIn(RegisteredPatient::getTableName() . '.' . RegisteredPatient::KODE_POLIKLINIK, collect(PolyclinicRepository::getAll(limit: 0))->pluck('kode_poliklinik')->toArray())
        )->when(
            $type,
            fn($q) => $q->when($type == 'ageGroup', function ($r) {
                $r->selectRaw(
                    'IFNULL(SUM(CASE WHEN (SELECT COUNT(' .  Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ') FROM ' . Patient::getTableName() . ' WHERE TIMESTAMPDIFF(year, ' . Patient::getTableName() . '.' . Patient::TANGGAL_LAHIR . ', now()) < 5 AND ' . Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ' = ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS . ')  THEN 1 ELSE 0 END),0) AS \'balita\','
                        . 'IFNULL(SUM(CASE WHEN (SELECT COUNT(' .  Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ') FROM ' . Patient::getTableName() . ' WHERE TIMESTAMPDIFF(year, ' . Patient::getTableName() . '.' . Patient::TANGGAL_LAHIR . ', now()) between 5 and 11 AND ' . Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ' = ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS . ') THEN 1 ELSE 0 END),0) AS \'anak\','
                        . 'IFNULL(SUM(CASE WHEN (SELECT COUNT(' .  Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ') FROM ' . Patient::getTableName() . ' WHERE TIMESTAMPDIFF(year, ' . Patient::getTableName() . '.' . Patient::TANGGAL_LAHIR . ', now()) between 12 and 25 AND ' . Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ' = ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS . ') THEN 1 ELSE 0 END),0) AS \'remaja\','
                        . 'IFNULL(SUM(CASE WHEN (SELECT COUNT(' .  Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ') FROM ' . Patient::getTableName() . ' WHERE TIMESTAMPDIFF(year, ' . Patient::getTableName() . '.' . Patient::TANGGAL_LAHIR . ', now()) between 26 and 45 AND ' . Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ' = ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS . ') THEN 1 ELSE 0 END),0) AS \'dewasa\','
                        . 'IFNULL(SUM(CASE WHEN (SELECT COUNT(' .  Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ') FROM ' . Patient::getTableName() . ' WHERE TIMESTAMPDIFF(year, ' . Patient::getTableName() . '.' . Patient::TANGGAL_LAHIR . ', now()) between 46 and 65 AND ' . Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ' = ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS . ') THEN 1 ELSE 0 END),0) AS \'lansia\','
                        . 'IFNULL(SUM(CASE WHEN (SELECT COUNT(' .  Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ') FROM ' . Patient::getTableName() . ' WHERE TIMESTAMPDIFF(year, ' . Patient::getTableName() . '.' . Patient::TANGGAL_LAHIR . ', now()) > 65 AND ' . Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS . ' = ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS . ')  THEN 1 ELSE 0 END),0) AS \'lainnya\''
                );
            })->when($type == 'mobileJknGroup', function ($r) {
                $r->selectRaw(
                    'IFNULL(SUM(CASE WHEN (select count(' . RegisteredPatient::NO_RAWAT . ') from ' . MobileJkn::getTableName() . ' where ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_RAWAT . ' = ' . MobileJkn::getTableName() . '.' . MobileJkn::NO_RAWAT . ') > 0 THEN 1 ELSE 0 END),0) AS \'Mobile JKN\','
                        . 'IFNULL(SUM(CASE WHEN (select count(' . RegisteredPatient::NO_RAWAT . ') from ' . MobileJkn::getTableName() . ' where ' . RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_RAWAT . ' = ' . MobileJkn::getTableName() . '.' . MobileJkn::NO_RAWAT . ') = 0 THEN 1 ELSE 0 END),0) AS \'Non Mobile JKN\''
                );
            })->when($type == 'status', function ($r) {
                $r->selectRaw(
                    'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::STATUS_DAFTAR . ' = \'Lama\' THEN 1 ELSE 0 END),0) AS \'Lama\','
                        . 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::STATUS_DAFTAR . ' = \'Baru\' THEN 1 ELSE 0 END),0) AS \'Baru\''
                );
            })->when($type == 'genderGroup', function ($r) {
                $condition = '';

                foreach (Patient::KELOMPOK_JENIS_KELAMIN as $key => $jenisKelamin) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . Patient::getTableName() . '.' . Patient::JENIS_KELAMIN . ' = \'' . $key . '\' THEN 1 ELSE 0 END),0) AS \'' . $key . '\'';

                    if (array_search($key, array_values(array_keys(Patient::KELOMPOK_JENIS_KELAMIN))) < count(Patient::KELOMPOK_JENIS_KELAMIN) - 1)
                        $condition .= ',';
                }

                $r->join(Patient::getTableName(), Patient::getTableName() . '.' . Patient::NO_REKAM_MEDIS, '=', RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS)
                    ->selectRaw($condition);
            })->when($type == 'typeGroup', function ($r) {
                $r->selectRaw(
                    'IFNULL(SUM(CASE WHEN ' . Polri::PANGKAT . ' IS NOT NULL THEN 1 ELSE 0 END), 0) as \'polri\','
                        . 'IFNULL(SUM(CASE WHEN ' . Tni::PANGKAT . ' IS NOT NULL THEN 1 ELSE 0 END), 0) as \'tni\','
                        . 'IFNULL(SUM(CASE WHEN ' . Tni::PANGKAT . ' IS NULL AND ' . Polri::PANGKAT . ' IS NULL THEN 1 ELSE 0 END), 0) as \'umum\''
                )
                    ->join(Tni::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Tni::getTableName() . '.' . Tni::NO_REKAM_MEDIS, 'left')
                    ->join(Polri::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Polri::getTableName() . '.' . Polri::NO_REKAM_MEDIS, 'left');
            })->when($type == 'serviceStatus', function ($r) {
                $condition = '';
                foreach (RegisteredPatient::KELOMPOK_STATUS_PELAYANAN as $idx => $status) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::STATUS_PELAYANAN . ' = \'' . $status . '\' THEN 1 ELSE 0 END),0) AS \'' . $status . '\'';

                    if ($idx < count(RegisteredPatient::KELOMPOK_STATUS_PELAYANAN) - 1)
                        $condition .= ',';
                }

                $r->selectRaw($condition);
            })->when($type == 'advanceStatusGroup', function ($r) {
                $condition = '';
                foreach (RegisteredPatient::KELOMPOK_STATUS_LANJUT as $idx => $status) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::STATUS_LANJUT . ' = \'' . $status . '\' THEN 1 ELSE 0 END),0) AS \'' . $status . '\'';

                    if ($idx < count(RegisteredPatient::KELOMPOK_STATUS_LANJUT) - 1)
                        $condition .= ',';
                }

                $r->selectRaw($condition);
            })->when($type == 'payTypes', function ($r) {
                $condition = '';
                $personResponsibilityData = PersonResponsibilityRepository::getAll(limit: 0);
                foreach ($personResponsibilityData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::KODE_PENANGGUNGJAWAB . ' = \'' . $item['kode_penanggungjawab'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['penanggungjawab'] . '\'';

                    if ($idx < count($personResponsibilityData) - 1)
                        $condition .= ',';
                }

                $r->selectRaw($condition);
            })->when($type == 'polyclinicGroup', function ($r) {
                $condition = '';
                $polyclinicData = PolyclinicRepository::getAll(limit: 0);
                foreach ($polyclinicData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::KODE_POLIKLINIK . ' = \'' . $item['kode_poliklinik'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_poliklinik'] . '\'';

                    if ($idx < count($polyclinicData) - 1)
                        $condition .= ',';
                }

                $r->selectRaw($condition);
            })->when($type == 'doctorGroup', function ($r) {
                $condition = '';
                $doctorData = DoctorRepository::getAll(limit: 0);
                foreach ($doctorData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . RegisteredPatient::KODE_DOKTER . ' = \'' . $item['kode_dokter'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_dokter'] . '\'';

                    if ($idx < count($doctorData) - 1)
                        $condition .= ',';
                }

                $r->selectRaw($condition);
            })->when($type == 'tniGroup', function ($r) {
                $condition = '';
                $tniData = TniGroupRepository::getAll(limit: 0);
                foreach ($tniData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . Tni::getTableName() . '.' . Tni::GOLONGAN . ' = \'' . $item['kode_golongan'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_golongan'] . '\'';

                    if ($idx < count($tniData) - 1)
                        $condition .= ',';
                }

                $r->join(Tni::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Tni::getTableName() . '.' . Tni::NO_REKAM_MEDIS)
                    ->selectRaw($condition);
            })->when($type == 'tniUnit', function ($r) {
                $condition = '';
                $tniData = TniUnitRepository::getAll(limit: 0);
                foreach ($tniData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . Tni::getTableName() . '.' . Tni::SATUAN . ' = \'' . $item['kode_satuan'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_satuan'] . '\'';

                    if ($idx < count($tniData) - 1)
                        $condition .= ',';
                }

                $r->join(Tni::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Tni::getTableName() . '.' . Tni::NO_REKAM_MEDIS)
                    ->selectRaw($condition);
            })->when($type == 'polriGroup', function ($r) {
                $condition = '';
                $polriData = PolriGroupRepository::getAll(limit: 0);
                foreach ($polriData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . Polri::getTableName() . '.' . Polri::GOLONGAN . ' = \'' . $item['kode_golongan'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_golongan'] . '\'';

                    if ($idx < count($polriData) - 1)
                        $condition .= ',';
                }

                $r->join(Polri::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Polri::getTableName() . '.' . Polri::NO_REKAM_MEDIS)
                    ->selectRaw($condition);
            })->when($type == 'polriUnit', function ($r) {
                $condition = '';
                $polriData = PolriUnitRepository::getAll(limit: 0);
                foreach ($polriData as $idx => $item) {
                    $condition .= 'IFNULL(SUM(CASE WHEN ' . Polri::getTableName() . '.' . Polri::SATUAN . ' = \'' . $item['kode_satuan'] . '\' THEN 1 ELSE 0 END),0) AS \'' . $item['nama_satuan'] . '\'';

                    if ($idx < count($polriData) - 1)
                        $condition .= ',';
                }

                $r->join(Polri::getTableName(), RegisteredPatient::getTableName() . '.' . RegisteredPatient::NO_REKAM_MEDIS, '=', Polri::getTableName() . '.' . Polri::NO_REKAM_MEDIS)
                    ->selectRaw($condition);
            })->first()
                ->toArray(),
            fn($q) => $q->selectRaw('IFNULL(count(' . RegisteredPatient::NO_RAWAT . '),0) as count')
                ->first()['count']
        );
    }
}
