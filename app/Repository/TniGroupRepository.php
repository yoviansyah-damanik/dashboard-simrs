<?php

namespace App\Repository;

use App\Models\TniGroup;
use Illuminate\Pagination\LengthAwarePaginator;

interface TniGroupInterface
{
    public static function getMapping(TniGroup | int $tniGroup): array;
    public static function getRelations(): array;
}

class TniGroupRepository implements TniGroupInterface
{
    const LIMIT_DEFAULT = 25;

    private static $withRelations = true;

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
    private function reconstruction(TniGroup $tniGroup): array
    {
        // Menghilangkan variabel ganda
        $exceptTniGroupData = [];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang
        $includesRelation = [];

        if (static::$withRelations === true) {
            $result = collect([
                ...collect($tniGroup)
                    ->except($exceptTniGroupData)
                    ->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($tniGroup) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($tniGroup->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $tniGroup->{$key}->toArray();
                        }
                    )->collapse()
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                ...collect($tniGroup)
                    ->except($exceptTniGroupData)
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
            'kode_golongan' => $data['id'],
            'nama_golongan' => $data['nama_golongan'],
        ];
    }

    /**
     * Fungsi ini untuk mapping data dokter dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(TniGroup | int $tniGroup): array
    {
        if (is_integer($tniGroup)) {
            $tniGroup = TniGroup::where(TniGroup::KODE_GOLONGAN, $tniGroup)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($tniGroup));
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
        ?bool $withPagination = false,
    ): array | LengthAwarePaginator {
        if (is_bool($withRelations))
            static::$withRelations = $withRelations;

        $result = TniGroup::whereAny([TniGroup::NAMA_GOLONGAN], 'like', $search . '%');

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
