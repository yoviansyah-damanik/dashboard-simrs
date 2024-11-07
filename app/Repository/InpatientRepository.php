<?php

namespace App\Repository;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Inpatient;
use App\Helpers\DateHelper;
use App\Models\Disease;
use App\Models\Icd10;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InpatientInterface
{
    public static function getMapping(Inpatient | int $inpatient): array;
    public static function getRelations(): array;
}

class InpatientRepository implements InpatientInterface
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
        return [
            'kamar' => [
                'table_name' => Room::getTableName(),
                'column_added' => null
            ],
            'icd10' => [
                'table_name' => Disease::getTableName(),
                'column_added' => null
            ],
            'icd9' => [
                'table_name' => Disease::getTableName(),
                'column_added' => null
            ]
        ];
    }

    /**
     * Fungsi ini digunakan untuk menstrukturisasi ulang struktur data menjadi satu metadata yang utuh.
     * Perhatikan setiap key harus berbeda. Jika terdapat key yang sama, mapping ulang key tersebut.
     * @return array
     */
    private function reconstruction(Inpatient $inpatient): array
    {
        // Menghilangkan variabel ganda
        $exceptInpatientData = ['kamar'];

        $exceptData = []; // Tambahkan jika ada data yang tidak ingin dimapping ulang
        $exceptRoomData = [];

        $includesRelation = ['icd10', 'icd9'];

        $result = collect([
            'ranap' => collect($inpatient)
                ->except($exceptInpatientData)
                ->toArray(),
            'kamar' => collect(RoomRepository::getMapping($inpatient->kamar))->except($exceptRoomData)->toArray(),
            ...collect((new static)->relations())
                ->filter(fn($item, $key) => in_array($key, $includesRelation))
                ->map(
                    function ($item, $key) use ($inpatient) {
                        if (!empty($item['column_added']) && is_array($item['column_added']))
                            return collect($inpatient->{$key})
                                ->only($item['column_added'])
                                ->toArray();

                        return $inpatient->{$key}?->toArray();
                    }
                )
        ])->except($exceptData)
            ->toArray();

        return $result;
    }

    /**
     * Fungsi ini bersifat final. Merubah mapping berakibat pada struktur data yang ditampilkan.
     * Jika ingin merubah isi data, ubahlah pada fungsi reconstruction().
     * @param $patient array
     * @return array|Inpatient
     */
    private function mapping(array $data): array
    {
        return [
            'nomor_rawat' => $data['ranap'][Inpatient::NO_RAWAT],
            'kode_kamar' => $data['ranap'][Inpatient::KODE_KAMAR],
            'tanggal_masuk' => Carbon::parse($data['ranap'][Inpatient::TANGGAL_MASUK])->format('d/m/Y'),
            'jam_masuk' => $data['ranap'][Inpatient::JAM_MASUK],
            'waktu_masuk' => DateHelper::dateFormat($data['ranap'][Inpatient::TANGGAL_MASUK], isTranslated: true, translatedFormat: 'd M Y') . ' ' . $data['ranap'][Inpatient::JAM_MASUK],
            'tanggal_keluar' => DateHelper::dateFormat($data['ranap'][Inpatient::TANGGAL_KELUAR], isTranslated: true, translatedFormat: 'd M Y'),
            'jam_keluar' => $data['ranap'][Inpatient::JAM_KELUAR],
            'waktu_keluar' => $data['ranap'][Inpatient::TANGGAL_KELUAR] ? DateHelper::dateFormat($data['ranap'][Inpatient::TANGGAL_KELUAR], isTranslated: true, translatedFormat: 'd M Y') . ' ' . $data['ranap'][Inpatient::JAM_KELUAR] : '-',
            'lama' => $data['ranap'][Inpatient::LAMA],
            'lama_sistem' => DateHelper::getDiffInDays($data['ranap'][Inpatient::TANGGAL_MASUK], $data['ranap'][Inpatient::TANGGAL_KELUAR]),
            'tarif_kamar' => $data['ranap'][Inpatient::TARIF_KAMAR],
            'total_biaya' => $data['ranap'][Inpatient::TOTAL_BIAYA],
            'status_pulang' => $data['ranap'][Inpatient::STATUS_PULANG],
            'status_ranap' => $data['ranap'][Inpatient::STATUS_PULANG] == '-' ? 'Masa Perawatan' : 'Pulang',
            'diagnosa_awal' => $data['ranap'][Inpatient::DIAGNOSA_AWAL],
            'diagnosa_akhir' => $data['ranap'][Inpatient::DIAGNOSA_AKHIR],
            ...collect($data)->except('ranap')->toArray()
        ];
    }

    /**
     * Fungsi ini untuk mapping data poli dari luar repo.
     * Data yang diberikan berupa Data Dokter
     * @return array
     */
    public static function getMapping(Inpatient | int $inpatient): array
    {
        if (is_integer($inpatient)) {
            $inpatient = Inpatient::where(Inpatient::NO_RAWAT, $inpatient)
                ->first();
        }

        return (new self)
            ->mapping((new self)->reconstruction($inpatient));
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
        ?string $search = null,
        ?bool $withPagination = false,
    ): array | LengthAwarePaginator {
        $withs = [
            ...collect((new static)->relations())->keys()->toArray(),
            ...collect(RoomRepository::getRelations())
                ->map(function ($item, $key) {
                    return 'kamar.' . $key;
                })
                ->values()
                ->toArray()
        ];
        $result = Inpatient::with($withs);

        if (!$withPagination) {
            if ($limit > 0)
                $result = $result->limit($limit);

            return $result->get()
                ->map(fn($item) => (new self)->mapping($item))
                ->toArray();
        }

        return tap($result->paginate($limit), function ($paginatedInstance) {
            return $paginatedInstance->getCollection()->transform(function ($value) {
                return (new self)
                    ->mapping($value);
            });
        });
    }
}
