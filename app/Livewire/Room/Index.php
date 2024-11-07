<?php

namespace App\Livewire\Room;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Helpers\FilterHelper;
use App\Repository\RoomRepository;

class Index extends Component
{
    public array $limits;

    public array $roomList;
    public ?string $roomActive = null;

    public function render()
    {
        $rooms = collect(RoomRepository::getAll(
            limit: 9999,
            withRelations: true,
            status: 1
        ))
            ->mapToGroups(fn($item) => [$item['kelas'] => $item])
            ->map(fn($q) => [
                'tersedia' => collect($q)->where('status', 'KOSONG')->count(),
                'terisi' => collect($q)->where('status', '!=', 'KOSONG')->count(),
                'total' => collect($q)->count()
            ]);

        return view('pages.room.index', compact('rooms'));
    }

    public function setShow($room)
    {
        if ($this->roomActive) {
            if ($this->roomActive == $room) {
                $this->reset('roomList', 'roomActive');
                return;
            }
        }
        $this->roomActive = $room;
        $this->roomList = RoomRepository::getAll(
            limit: 9999,
            withRelations: true,
            class: $room,
            status: 1
        );
    }
}
