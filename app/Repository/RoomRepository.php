<?php

namespace App\Repository;

use App\Models\Room;
use App\Models\Ward;
use App\Helpers\GeneralHelper;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoomInterface {}

class RoomRepository implements RoomInterface
{
    const LIMIT_DEFAULT = 25;

    private static $withRelations = true;

    private bool $withInactiveRoom;

    public function __construct()
    {
        $this->withInactiveRoom = GeneralHelper::getWithInactiveRoomStatus();
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
            'bangsal' => [
                'table_name' => Ward::getTableName(),
                'column_added' => null
            ],
        ];
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
    private function reconstruction(Room $room): array
    {
        // Menghilangkan variabel ganda
        $exceptRoomData = [];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang

        $includesRelation = [];

        if (static::$withRelations) {
            $result = collect([
                'kamar' => collect($room)
                    ->except($exceptRoomData)
                    ->toArray(),
                'bangsal' => collect(WardRepository::getMapping($room->bangsal))->except($exceptRoomData)->toArray(),
                ...collect((new static)->relations())
                    ->filter(fn($item, $key) => in_array($key, $includesRelation))
                    ->map(
                        function ($item, $key) use ($room) {
                            if (!empty($item['column_added']) && is_array($item['column_added']))
                                return collect($room->{$key})
                                    ->only($item['column_added'])
                                    ->toArray();

                            return $room->{$key}->toArray();
                        }
                    )
            ])->except($exceptData)
                ->toArray();
        } else {
            $result = collect([
                'kamar' => collect($room)
                    ->except($exceptRoomData)
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
            'kode_kamar' => $data['kamar'][Room::KODE_KAMAR],
            'kode_bangsal' => $data['kamar'][Room::KODE_BANGSAL],
            'tarif_kamar' => $data['kamar'][Room::TARIF_KAMAR],
            'status' => $data['kamar'][Room::STATUS],
            'kelas' => $data['kamar'][Room::KELAS],
            'status_aktif' => $data['kamar'][Room::STATUS_KAMAR] == 1 ? 'Aktif' : 'Nonaktif',
            ...collect($data)->except('kamar')->toArray()
        ];
    }

    /**
     * Fungsi ini untuk mapping data poli dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(Room | string  $room): array
    {
        if (is_string($room)) {
            $room = Room::where(Room::KODE_KAMAR, $room)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($room));
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

        $result = Room::whereAny([Room::KODE_KAMAR], 'like', $search . '%');

        if (!is_null($status) && in_array($status, Room::KELOMPOK_STATUS)) {
            $result = $result->where(Room::STATUS_KAMAR, $status);
        } else {
            if ((new static)->withInactiveRoom === false) {
                $result = $result->where(Room::STATUS_KAMAR, 1);
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
