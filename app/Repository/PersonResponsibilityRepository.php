<?php

namespace App\Repository;

use App\Helpers\ConfigurationHelper;
use App\Models\PersonResponsibility;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PersonResponsibilityInterface {}

class PersonResponsibilityRepository implements PersonResponsibilityInterface
{
    const LIMIT_DEFAULT = 25; // JUMLAH DATA PER HALAMAN DITAMPILKAN DEFAULT

    private static $withRelations = true;

    private bool $withInactivePersonResponsibility;

    public function __construct()
    {
        $this->withInactivePersonResponsibility = ConfigurationHelper::get('WITH_INACTIVE_PERSON_RESPONSIBILITY');
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
    private function reconstruction(PersonResponsibility $personResponsibility): array
    {
        // Menghilangkan variabel ganda
        $exceptPersonResponsibilityData = [];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang

        $includesRelation = [];

        if (static::$withRelations) {
            $result = collect([
                ...collect($personResponsibility)
                    ->except($exceptPersonResponsibilityData)
                    ->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($personResponsibility) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($personResponsibility->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $personResponsibility->{$key}->toArray();
                        }
                    )->collapse()
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                ...collect($personResponsibility)
                    ->except($exceptPersonResponsibilityData)
                    ->toArray(),
            ])->except($exceptData)
                ->toArray();
        }

        return $result;
    }

    private function mapping(array $data): array
    {
        return [
            'kode_penanggungjawab' => $data[PersonResponsibility::KODE_PENANGGUNGJAWAB],
            'penanggungjawab' => $data[PersonResponsibility::PENANGGUNGJAWAB],
            'nama' => $data[PersonResponsibility::NAMA],
            'alamat' => $data[PersonResponsibility::ALAMAT],
            'no_telp' => $data[PersonResponsibility::NO_TELP],
            'attn' => $data[PersonResponsibility::ATTN],
            'status' => $data[PersonResponsibility::STATUS] == 1 ? 'Aktif' : 'Nonaktif',
        ];
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

        $result = PersonResponsibility::whereAny([PersonResponsibility::PENANGGUNGJAWAB], 'like', $search . '%')
            ->when(
                !is_null($status) && in_array($status, PersonResponsibility::KELOMPOK_STATUS),
                fn($q) => $q->where(PersonResponsibility::STATUS, $status),
                fn($q) => $q->when(
                    (new static)->withInactivePersonResponsibility == false,
                    fn($r) => $r->where(PersonResponsibility::STATUS, 1)
                )
            );

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
