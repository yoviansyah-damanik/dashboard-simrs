<?php

namespace App\Repository;

use App\Models\Tni;
use App\Models\Polri;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Helpers\DateHelper;
use App\Helpers\GeneralHelper;
use App\Models\Inpatient;
use App\Models\RegisteredPatient;
use App\Models\PersonResponsibility;
use App\Models\Room;
use App\Models\Ward;
use App\Repository\PolyclinicRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface InpatientsInterface {}

class InpatientsRepository implements InpatientsInterface
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
            'ranap' => [
                'table_name' => Inpatient::getTableName(),
                'column_added' => null
            ],
            'ranap.kamar' => [
                'table_name' => Room::getTableName(),
                'column_added' => null
            ],
            'ranap.kamar.bangsal' => [
                'table_name' => Ward::getTableName(),
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
        $exceptPatientData = ['tgl_bayar'];
        $exceptPolyclinicData = [];
        $exceptDoctorData = [];
        $exceptInpatientData = [];

        $includesRelation = ['jenis_bayar'];
        $result = collect([
            'pendaftaran' => collect($patient->withoutRelations())->except($exceptRegistrationData)->toArray(),
            'pasien' => collect(PatientRepository::getMapping($patient->pasien))->except($exceptPatientData)->toArray(),
            'poliklinik' => collect(PolyclinicRepository::getMapping($patient->poliklinik))->except($exceptPolyclinicData)->toArray(),
            'dokter' => collect(DoctorRepository::getMapping($patient->dokter))->except($exceptDoctorData)->toArray(),
            'ranap' => collect(InpatientRepository::getMapping($patient->ranap))->except($exceptInpatientData)->toArray(),
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
                'waktu_pendaftaran' => DateHelper::dateFormat($data['pendaftaran'][RegisteredPatient::TGL_REGISTRASI], isTranslated: true, translatedFormat: 'd M Y') . ' ' . $data['pendaftaran'][RegisteredPatient::JAM_REGISTRASI],
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
        ?string $room = null,
        ?string $ward = null,
        ?string $doctor = null,
        ?string $tniGroup = null,
        ?string $polriGroup = null,
        ?string $inpatientStatus = null
    ): LengthAwarePaginator | array {
        $withs =   [
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
        ];

        $result = RegisteredPatient::with($withs)
            ->whereHas('ranap', function ($q) use ($room, $startDate, $endDate, $inpatientStatus) {
                $q->when(
                    !is_null($room) && $room != 'semua',
                    fn($r) => $r->where(Inpatient::KODE_KAMAR, $room)
                )
                    ->when(
                        in_array($inpatientStatus, ['masuk', 'pulang', 'semua']),
                        fn($q) => $q->where(Inpatient::TANGGAL_MASUK, '>=', $startDate)
                            ->where(Inpatient::TANGGAL_MASUK, '<=', $endDate),
                        fn($q) => $q->whereNull(Inpatient::TANGGAL_KELUAR)
                            ->orWhere(Inpatient::TANGGAL_KELUAR, '')
                    );
            })
            ->whereHas('pasien', function ($q) use ($search, $type, $gender, $ageCategory) {
                $q->when(
                    $ageCategory && $ageCategory != 'semua',
                    fn($r) => $r
                        ->when($ageCategory == 'balita', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) < 5'))
                        ->when($ageCategory == 'anak', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 5 and 11'))
                        ->when($ageCategory == 'remaja', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 12 and 25'))
                        ->when($ageCategory == 'dewasa', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 26 and 45'))
                        ->when($ageCategory == 'lansia', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) between 46 and 65'))
                        ->when($ageCategory == 'lainnya', fn($r) => $r->whereRaw('TIMESTAMPDIFF(year, ' . Patient::TANGGAL_LAHIR . ', now()) > 65'))
                )

                    ->when(in_array($gender, collect(Patient::KELOMPOK_JENIS_KELAMIN)->keys()->toArray()), fn($r) => $r->where(Patient::JENIS_KELAMIN, $gender))
                    ->when(
                        in_array($type, collect(Patient::KELOMPOK_PASIEN)->keys()->toArray()),
                        fn($r) =>
                        // Sesuaikan dengan KELOMPOK PASIEN
                        $r->when($type == 'umum', fn($s) => $s->doesntHave('polri')
                            ->doesntHave('tni'))
                            ->when($type == 'polri', fn($s) => $s->has('polri'))
                            ->when($type == 'tni', fn($s) => $s->has('tni'))
                    )
                    ->whereAny([RegisteredPatient::NO_REKAM_MEDIS, Patient::NAMA_PASIEN], 'like', $search . "%");
            })
            ->when($doctor && $doctor != 'semua', fn($q) => $q->where(RegisteredPatient::KODE_DOKTER, $doctor))
            ->when(in_array($status, RegisteredPatient::KELOMPOK_STATUS_PELAYANAN), fn($q) => $q->where(RegisteredPatient::STATUS_PELAYANAN, $status))
            ->when($payType && $payType != 'semua', fn($q) => $q->where(RegisteredPatient::KODE_PENANGGUNGJAWAB, $payType))
            ->when($polyclinic && $polyclinic != 'semua', fn($q) => $q->where(RegisteredPatient::KODE_POLIKLINIK, $polyclinic))
            ->when($tniGroup && $tniGroup != 'semua', fn($q) => $q->whereHas('tni', fn($q) => $q->where(Tni::GOLONGAN, $tniGroup)))
            ->when($polriGroup && $polriGroup != 'semua', fn($q) => $q->whereHas('polri', fn($q) => $q->where(Polri::GOLONGAN, $polriGroup)))
            ->where(RegisteredPatient::STATUS_LANJUT, RegisteredPatient::STATUS_RANAP)
            ->orderBy(RegisteredPatient::TGL_REGISTRASI, 'desc')
            ->orderBy(RegisteredPatient::JAM_REGISTRASI, 'desc')
            ->paginate($limit);

        return tap(
            $result,
            fn($paginatedInstance)
            => $paginatedInstance->getCollection()->transform(
                fn($value) => (new self)
                    ->mapping((new self)->reconstruction($value))
            )
        );
    }
}
