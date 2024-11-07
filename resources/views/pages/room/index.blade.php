<x-content>
    <x-breadcrumb title="Kamar" :items="[['title' => 'Kamar']]" />

    <x-room-item-container>
        @foreach ($rooms as $key => $room)
            <x-room-by-class-item :title="$key" :isActive="$roomActive === $key" :total="$room['total']" :available="$room['tersedia']"
                :filled="$room['terisi']" />
        @endforeach
    </x-room-item-container>

    <div class="block h-1 !my-9 bg-primary-500"></div>

    <x-under-maintenance />
    {{-- @if ($roomList)
    @else
        Silahkan pilih kelas terlebih dahulu.
    @endif --}}
</x-content>
