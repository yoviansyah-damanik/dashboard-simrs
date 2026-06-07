<x-content>
    <x-breadcrumb title="Laboratorium" :items="[['title' => 'Layanan Penunjang Medis'], ['title' => 'Laboratorium']]" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nomor RM, Nama Pasien, atau No Rawat" />
        <div class="flex flex-1 gap-3">
            <x-form.input block type="date" wire:model.live='startDate' :max="date('Y-m-d')" />
            <x-form.input block type="date" wire:model.live='endDate' :max="date('Y-m-d')" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
        <x-form.select label="Status" block :items="$statuses" wire:model.live='status' />
        <x-form.select label="Kategori" block :items="$kategoriList" wire:model.live='kategori' />
        <x-form.select label="Jenis Kelamin" block :items="$genders" wire:model.live='gender' />
    </div>

    @if ($records->count() > 0)
        <div class="space-y-4">
            @foreach ($records as $record)
                <x-laboratory-item :$record />
            @endforeach
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $records->links() }}
    </x-pagination>
</x-content>
