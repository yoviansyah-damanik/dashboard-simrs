<?php

namespace App\Repository;

use App\Models\Doctor;
use App\Models\Specialist;
use App\Helpers\GeneralHelper;
use Illuminate\Pagination\LengthAwarePaginator;

interface DoctorInterface
{
    public static function getMapping(Doctor | int $doctor): array;
    public static function getRelations(): array;
}

class DoctorRepository implements DoctorInterface
{
    const LIMIT_DEFAULT = 25;

    private static $withRelations = true;

    private bool $withInactiveDoctor;

    public function __construct()
    {
        $this->withInactiveDoctor = GeneralHelper::getWithInactiveDoctorStatus();
    }

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
            'spesialis' => [
                'table_name' => Specialist::getTableName(),
                'column_added' => [Specialist::NAMA_SPESIALIS]
            ],
        ];
    }

    /**
     * Fungsi ini digunakan untuk menstrukturisasi ulang struktur data menjadi satu metadata yang utuh.
     * Perhatikan setiap key harus berbeda. Jika terdapat key yang sama, mapping ulang key tersebut.
     * @return array
     */
    private function reconstruction(Doctor $doctor): array
    {
        // Menghilangkan variabel ganda
        $exceptDoctorData = ['kd_sps', 'spesialis'];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang
        $includesRelation = ['spesialis'];

        if (static::$withRelations === true) {
            $result = collect([
                ...collect($doctor)
                    ->except($exceptDoctorData)
                    ->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn ($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($doctor) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($doctor->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $doctor->{$key}->toArray();
                        }
                    )->collapse()
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                ...collect($doctor)
                    ->except($exceptDoctorData)
                    ->toArray(),
            ])->except($exceptData)
                ->toArray();
        }

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
            'kode_dokter' => $data[Doctor::KODE_DOKTER],
            'nama_dokter' => $data[Doctor::NAMA_DOKTER],
            'jenis_kelamin' => $data[Doctor::JENIS_KELAMIN] == 'L' ? 'Laki-laki' : 'Perempuan',
            'tempat_lahir' => $data[Doctor::TEMPAT_LAHIR],
            'tanggal_lahir' => $data[Doctor::TANGGAL_LAHIR],
            'golongan_darah' => $data[Doctor::GOL_DARAH],
            'agama' => $data[Doctor::AGAMA],
            'alamat' => $data[Doctor::ALAMAT],
            'no_telp' => $data[Doctor::NO_TELP],
            'status_nikah' => $data[Doctor::STATUS_NIKAH],
            'alumni' => $data[Doctor::ALUMNI],
            'no_izin_praktek' => $data[Doctor::NO_IZIN_PRAKTEK],
            'status' => $data[Doctor::STATUS] == 1 ? "Aktif" : "Nonaktif",
            'spesialis' => $data[Specialist::NAMA_SPESIALIS] ?? '-',
        ];
    }

    /**
     * Fungsi ini untuk mapping data dokter dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(Doctor | int $doctor): array
    {
        if (is_integer($doctor)) {
            $doctor = Doctor::where(Doctor::KODE_DOKTER, $doctor)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($doctor));
    }

    /**
     * Fungsi ini digunakan untuk memberikan variabel relasi ketika dipanggil dari class lain.
     * @return void
     */
    public static function getRelations(bool $withRelations = true): array
    {
        if (is_bool($withRelations))
            static::$withRelations = $withRelations;

        return (new static)->relations();
    }

    /**
     * Fungsi untuk memanggil seluruh data.
     * @param ?int $limit Batasan data yang ditampilkan
     * @param ?string $search
     * @param ?string $status Status aktif atau nonaktif
     * @param ?bool $withPagination Menampilkan data dengan pagination
     * @return array|LengthAwarePaginator
     */
    public static function getAll(
        int $limit = self::LIMIT_DEFAULT,
        ?string $spesialisCode = null,
        ?bool $withRelations = null,
        ?string $search = null,
        ?string $status = null,
        ?bool $withPagination = false,
    ): array | LengthAwarePaginator {
        if (is_bool($withRelations))
            static::$withRelations = $withRelations;

        $result = Doctor::with([
            'spesialis'
        ])
            ->whereAny([Doctor::NAMA_DOKTER, Doctor::KODE_DOKTER], 'like', $search . '%');

        if (!is_null($status) && in_array($status, Doctor::KELOMPOK_STATUS)) {
            $result = $result->where(Doctor::STATUS, $status);
        } else {
            if ((new static)->withInactiveDoctor === false) {
                $result = $result->where(Doctor::STATUS, 1);
            }
        }

        if (!is_null($spesialisCode)) {
            $result = $result->where(Doctor::KODE_SPESIALIS, $spesialisCode);
        }

        if (!$withPagination) {
            if ($limit > 0)
                $result = $result->limit($limit);

            return $result->get()
                ->map(fn ($item) => (new self)->mapping((new self)->reconstruction($item)))
                ->toArray();
        }

        return tap($result->paginate($limit), function ($paginatedInstance) {
            return $paginatedInstance->getCollection()->transform(function ($value) {
                return (new self)
                    ->mapping((new self)->reconstruction($value));
            });
        });
    }
}
