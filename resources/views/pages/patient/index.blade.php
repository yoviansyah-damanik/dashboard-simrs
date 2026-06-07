<x-content>
    <x-breadcrumb title="Data Pasien" :items="[['title' => 'Data Pasien']]" />

    <x-export-loading wire:target="exportCsv, exportPdf" />

    <div class="flex items-center gap-3">
        <x-form.input block class="flex-1" type="search" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nomor RM, Nama Pasien, atau NIK" />
        <div class="flex gap-2">
            <x-button color="primary" icon="i-ph-file-csv" wire:click="exportCsv">
                CSV
            </x-button>
            <x-button color="red" icon="i-ph-file-pdf" wire:click="exportPdf">

                PDF
            </x-button>
        </div>
    </div>

    <div class="grid grid-flow-col grid-rows-3 gap-3 sm:grid-rows-2">
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
        <x-form.select label="Jenis Kelamin" block :items="$genders" wire:model.live='gender' />
        <x-form.select label="Kategori Umur" block :items="$ageCategories" wire:model.live='ageCategory' />
        <x-form.select label="Jenis Pasien" block :items="$types" wire:model.live='type' />
        @if ($type == 'tni')
            <x-form.select label="Golongan TNI" block :items="$tniGroups" wire:model.live='tniGroup' />
            <x-form.select label="Satuan TNI" block :items="$tniUnits" wire:model.live='tniUnit' />
        @endif
        <x-form.select label="Pasien Dinas" block :items="$tniGroups" wire:model.live='tniGroup' />
        <x-form.select label="Jenis Bayar" block :items="$payTypes" wire:model.live='payType' />
    </div>

    <x-table :columns="[
        '#',
        'No Rekam Medis',
        'Nama Pasien',
        'Jenis Kelamin',
        'Tempat Lahir',
        'Tanggal Lahir',
        'Umur',
        'Jenis Bayar',
        'Jenis Pasien',
        '',
    ]">
        <x-slot name="body">
            @forelse ($patients as $patient)
                <x-table.tr>
                    <x-table.td centered>
                        {{ $patients->perPage() * ($patients->currentPage() - 1) + $loop->iteration }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $patient['data']['no_rekam_medis'] }}
                    </x-table.td>
                    <x-table.td>
                        {{ $patient['data']['nama'] }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $patient['data']['jenis_kelamin'] }}
                    </x-table.td>
                    <x-table.td>
                        {{ $patient['data']['tempat_lahir'] }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $patient['data']['tanggal_lahir'] }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $patient['data']['umur'] }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $patient['data']['jenis_bayar'] }}
                    </x-table.td>
                    <x-table.td centered>
                        @if (str_contains(strtoupper($patient['data']['jenis_pasien']), 'TNI'))
                            <span class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-md uppercase">
                                {{ $patient['data']['jenis_pasien'] }}
                            </span>
                        @else
                            {{ $patient['data']['jenis_pasien'] }}
                        @endif
                    </x-table.td>
                    <x-table.td centered>
                        <x-tooltip title="Lihat Data">
                            <x-button size="sm" color="primary" icon="i-ph-list"
                                x-on:click="$dispatch('toggle-show-patient-modal')"
                                wire:click="$dispatch('setPatient',{ patient: '{{ $patient['data']['no_rekam_medis'] }}' })"
                                wire:loading.attr="disabled">
                            </x-button>
                        </x-tooltip>
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.tr>
                    <x-table.td centered colspan="10">
                        <x-no-data />
                    </x-table.td>
                </x-table.tr>
            @endforelse
        </x-slot>

        <x-slot name="paginate">
            {{ $patients->links(data: ['scrollTo' => 'table']) }}
        </x-slot>
    </x-table>

    <div wire:ignore>
        <x-modal name="show-patient-modal" size="4xl" modalTitle="Data Pasien">
            <livewire:patient.show-data />
        </x-modal>
    </div>
</x-content>
