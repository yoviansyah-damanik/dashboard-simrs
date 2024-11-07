<?php

namespace App\Repository;

use App\Models\PolriUnit;
use Illuminate\Pagination\LengthAwarePaginator;

interface PolriUnitInterface
{
    public static function getMapping(PolriUnit | int $polriUnit): array;
    public static function getRelations(): array;
}

class PolriUnitRepository implements PolriUnitInterface
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
    private function reconstruction(PolriUnit $polriUnit): array
    {
        // Menghilangkan variabel ganda
        $exceptPolriUnitData = [];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang
        $includesRelation = [];

        if (static::$withRelations === true) {
            $result = collect([
                ...collect($polriUnit)
                    ->except($exceptPolriUnitData)
                    ->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($polriUnit) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($polriUnit->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $polriUnit->{$key}->toArray();
                        }
                    )->collapse()
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                ...collect($polriUnit)
                    ->except($exceptPolriUnitData)
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
            'kode_satuan' => $data['id'],
            'nama_satuan' => $data['nama_satuan'],
        ];
    }

    /**
     * Fungsi ini untuk mapping data dokter dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(PolriUnit | int $polriUnit): array
    {
        if (is_integer($polriUnit)) {
            $polriUnit = PolriUnit::where(PolriUnit::KODE_SATUAN, $polriUnit)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($polriUnit));
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

        $result = PolriUnit::whereAny([PolriUnit::NAMA_SATUAN], 'like', $search . '%');

        if (!$withPagination) {
            if ($limit > 0)
                $result = $result->limit($limit);

            return $result->get()
                ->map(fn($item) => (new self)->mapping((new self)->reconstruction($item)))
                ->toArray();
        }

        return tap(
            $result->paginate($limit),
            fn($paginatedInstance)  =>
            $paginatedInstance->getCollection()->transform(
                fn($value) => (new self)
                    ->mapping((new self)->reconstruction($value))
            )
        );
    }
}
