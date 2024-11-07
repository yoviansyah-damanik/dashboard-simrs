<?php

namespace App\Repository;

use App\Models\Polyclinic;
use App\Helpers\ConfigurationHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PolyclinicInterface
{
    public static function getMapping(Polyclinic | int $polyclinic): array;
    public static function getRelations(): array;
}

class PolyclinicRepository implements PolyclinicInterface
{
    const LIMIT_DEFAULT = 25;

    private static $withRelations = true;

    private bool $withInactivePolyclinic;

    public function __construct()
    {
        $this->withInactivePolyclinic = ConfigurationHelper::get('WITH_INACTIVE_POLYCLINIC');
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
        return [];
    }

    /**
     * Fungsi ini digunakan untuk menstrukturisasi ulang struktur data menjadi satu metadata yang utuh.
     * Perhatikan setiap key harus berbeda. Jika terdapat key yang sama, mapping ulang key tersebut.
     * @return array
     */
    private function reconstruction(Polyclinic $polyclinic): array
    {
        // Menghilangkan variabel ganda
        $exceptPolyclinicData = [];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang

        $includesRelation = [];

        if (static::$withRelations) {
            $result = collect([
                ...collect($polyclinic)
                    ->except($exceptPolyclinicData)
                    ->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($polyclinic) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($polyclinic->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $polyclinic->{$key}->toArray();
                        }
                    )->collapse()
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                ...collect($polyclinic)
                    ->except($exceptPolyclinicData)
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
            'kode_poliklinik' => $data[Polyclinic::KODE_POLIKLINIK],
            'nama_poliklinik' => $data[Polyclinic::NAMA_POLIKLINIK],
            'nomor_registrasi' => $data[Polyclinic::REGISTRASI],
            'nomor_registrasi_lama' => $data[Polyclinic::REGISTRASI_LAMA],
            'status' => $data[Polyclinic::STATUS] == 1 ? "Aktif" : "Nonaktif",
        ];
    }

    /**
     * Fungsi ini untuk mapping data poli dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(Polyclinic | int $polyclinic): array
    {
        if (is_integer($polyclinic)) {
            $polyclinic = Polyclinic::where(Polyclinic::KODE_POLIKLINIK, $polyclinic)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($polyclinic));
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
        ?bool $withRelations = null,
        ?string $search = null,
        ?string $status = null,
        ?bool $withPagination = false,
    ): array | LengthAwarePaginator {
        if (is_bool($withRelations))
            static::$withRelations = $withRelations;

        $result = Polyclinic::whereAny([Polyclinic::NAMA_POLIKLINIK], 'like', $search . '%');

        if (!is_null($status) && in_array($status, Polyclinic::KELOMPOK_STATUS)) {
            $result = $result->where(Polyclinic::STATUS, $status);
        } else {
            if ((new static)->withInactivePolyclinic === false) {
                $result = $result->where(Polyclinic::STATUS, 1);
            }
        }

        if (!$withPagination) {
            if ($limit > 0)
                $result = $result->limit($limit);

            return $result->get()
                ->map(fn($item) => (new self)->mapping((new self)->reconstruction($item)))
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
