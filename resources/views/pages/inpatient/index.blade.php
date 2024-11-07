<x-content>
    <x-breadcrumb title="Rawat Inap" :items="[['title' => 'Rawat Inap']]" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nomor RM atau Nama Pasien" />
        <div class="flex flex-1 gap-3">
            <x-form.select block :items="$inpatientStatuses" wire:model.live='inpatientStatus' />
            <x-form.input :loading="in_array($inpatientStatus, ['masih_perawatan'])" block type="date" wire:model.live='startDate' :max="date('Y-m-d')" />
            <x-form.input :loading="in_array($inpatientStatus, ['masih_perawatan'])" block type="date" wire:model.live='endDate' :max="date('Y-m-d')" />
        </div>
    </div>
    <div class="grid grid-flow-col grid-rows-5 gap-3 sm:grid-rows-3 lg:grid-rows-2">
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
        <x-form.select label="Status Pelayanan" block :items="$statusGroup" wire:model.live='status' />
        <x-form.select label="Jenis Kelamin" block :items="$genders" wire:model.live='gender' />
        <x-form.select label="Kategori Umur" block :items="$ageCategories" wire:model.live='ageCategory' />
        <x-form.select label="Jenis Pasien" block :items="$types" wire:model.live='type' />
        <x-form.select label="Pasien Dinas TNI" block :items="$tniGroups" wire:model.live='tniGroup' />
        <x-form.select label="Jenis Bayar" block :items="$payTypes" wire:model.live='payType' />
        <x-form.select label="DPJP" block :items="$doctors" wire:model.live='doctor' />
        <x-form.select label="Bangsal" block :items="$wards" wire:model.live='ward' />
        <x-form.select label="Kamar" block :items="$rooms" wire:model.live='room' />
    </div>

    @if ($patients->count() > 0)
        <div class="space-y-4">
            @foreach ($patients as $patient)
                <x-inpatient-item :$patient />
            @endforeach
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $patients->links() }}
    </x-pagination>
</x-content>
