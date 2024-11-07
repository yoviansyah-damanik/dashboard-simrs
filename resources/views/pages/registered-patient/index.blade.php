<x-content>
    <x-breadcrumb title="Pendaftaran" :items="[['title' => 'Pendaftaran']]" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nomor RM atau Nama Pasien" />
        <div class="flex flex-1 gap-3">
            <x-form.input block type="date" wire:model.live='startDate' :max="date('Y-m-d')" />
            <x-form.input block type="date" wire:model.live='endDate' :max="date('Y-m-d')" />
        </div>
    </div>
    <div class="grid grid-flow-col grid-rows-4 gap-3 sm:grid-rows-3 lg:grid-rows-2 !mb-7">
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
        <x-form.select label="Status Poli" block :items="$statuses" wire:model.live='status' />
        <x-form.select label="Status Pelayanan" block :items="$serviceStatuses" wire:model.live='serviceStatus' />
        <x-form.select label="Status Lanjut" block :items="$advanceStatusGroup" wire:model.live='advanceStatus' />
        <x-form.select label="Jenis Kelamin" block :items="$genders" wire:model.live='gender' />
        <x-form.select label="Kategori Umur" block :items="$ageCategories" wire:model.live='ageCategory' />
        <x-form.select label="Jenis Pasien" block :items="$types" wire:model.live='type' />
        @if ($type == 'tni')
            <x-form.select label="Golongan TNI" block :items="$tniGroups" wire:model.live='tniGroup' />
            <x-form.select label="Satuan TNI" block :items="$tniUnits" wire:model.live='tniUnit' />
        @endif
        @if ($type == 'polri')
            <x-form.select label="Golongan Polri" block :items="$polriGroups" wire:model.live='polriGroup' />
            <x-form.select label="Satuan Polri" block :items="$polriUnits" wire:model.live='polriUnit' />
        @endif
        <x-form.select label="Jenis Bayar" block :items="$payTypes" wire:model.live='payType' />
        <x-form.select label="Poliklinik" block :items="$polyclinics" wire:model.live='polyclinic' />
        <x-form.select label="DPJP" block :items="$doctors" wire:model.live='doctor' />
        <x-form.select label="Mobile JKN" block :items="$mobileJknStatuses" wire:model.live='mobileJknStatus' />
    </div>

    @if ($patients->count() > 0)
        <div class="space-y-4">
            @foreach ($patients as $patient)
                <x-registered-patient-item :$patient />
            @endforeach
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $patients->links() }}
    </x-pagination>
</x-content>
