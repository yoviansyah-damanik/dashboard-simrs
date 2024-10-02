<x-content>
    <x-breadcrumb title="Instalasi Gawat Darurat (IGD)" :items="[['title' => 'Instalasi Gawat Darurat (IGD)']]" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nomor RM, Nama Pasien, atau NIK" />
        <x-form.input block type="date" wire:model.live='startDate' />
        <x-form.input block type="date" wire:model.live='endDate' />
    </div>
    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
        <x-form.select label="Status Pelayanan" block :items="$statusGroup" wire:model.live='status' />
        <x-form.select label="Jenis Kelamin" block :items="$genders" wire:model.live='gender' />
        <x-form.select label="Kategori Umur" block :items="$ageCategories" wire:model.live='ageCategory' />
        <x-form.select label="Jenis Pasien" block :items="$types" wire:model.live='type' />
        <x-form.select label="Jenis Bayar" block :items="$payTypes" wire:model.live='payType' />
        <x-form.select label="DPJP" block :items="$doctors" wire:model.live='doctor' />
    </div>

    @if ($patients->count() > 0)
        <div class="space-y-4">
            @foreach ($patients as $patient)
                @if ($patient['pendaftaran']['status_lanjut'] == \App\Models\RegisteredPatient::STATUS_RANAP)
                    <x-inpatient-item :$patient />
                @else
                    <x-registered-patient-item :$patient />
                @endif
            @endforeach
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $patients->links() }}
    </x-pagination>
</x-content>
