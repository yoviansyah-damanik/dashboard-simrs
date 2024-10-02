<?php

namespace App\Repository;

use App\Models\Ward;
use App\Helpers\GeneralHelper;
use Illuminate\Pagination\LengthAwarePaginator;

interface WardInterface
{
}

class WardRepository implements WardInterface
{
    const LIMIT_DEFAULT = 25;

    private static $withRelations = true;

    private bool $withInactiveWard;

    public function __construct()
    {
        $this->withInactiveWard = GeneralHelper::getWithInactiveWardStatus();
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
     * Fungsi ini digunakan untuk menstrukturisasi ulang struktur data menjadi satu metadata yang utuh.
     * Perhatikan setiap key harus berbeda. Jika terdapat key yang sama, mapping ulang key tersebut.
     * @return array
     */
    private function reconstruction(Ward $ward): array
    {
        // Menghilangkan variabel ganda
        $exceptWardData = [];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang

        $includesRelation = [];

        if (static::$withRelations) {
            $result = collect([
                'bangsal' => collect($ward)
                    ->except($exceptWardData)
                    ->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn ($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($ward) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($ward->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $ward->{$key}->toArray();
                        }
                    )->collapse()
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                'bangsal' => collect($ward)
                    ->except($exceptWardData)
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
            'kode_bangsal' => $data['bangsal'][Ward::KODE_BANGSAL],
            'nama_bangsal' => $data['bangsal'][Ward::NAMA_BANGSAL],
            'status' => $data['bangsal'][Ward::STATUS] == 1 ? 'Aktif' : 'Nonaktif',
        ];
    }

    /**
     * Fungsi ini untuk mapping data poli dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(Ward | int $ward): array
    {
        if (is_integer($ward)) {
            $ward = Ward::where(Ward::KODE_BANGSAL, $ward)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($ward));
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

        $result = Ward::whereAny([Ward::NAMA_BANGSAL], 'like', $search . '%');

        if (!is_null($status) && in_array($status, Ward::KELOMPOK_STATUS)) {
            $result = $result->where(Ward::STATUS, $status);
        } else {
            if ((new static)->withInactiveWard === false) {
                $result = $result->where(Ward::STATUS, 1);
            }
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
