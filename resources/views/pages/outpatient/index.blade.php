<x-content>
    <x-breadcrumb title="Rawat Jalan" :items="[['title' => 'Rawat Jalan']]" />

    <x-export-loading wire:target="exportCsv, exportPdf" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nomor RM, Nama Pasien, atau NIK" />
        <div class="flex flex-1 gap-3">
            <x-form.input block type="date" wire:model.live='startDate' :max="date('Y-m-d')" />
            <x-form.input block type="date" wire:model.live='endDate' :max="date('Y-m-d')" />
        </div>
        <div class="flex gap-2">
            <x-button color="primary" icon="i-ph-file-csv" wire:click="exportCsv">
                CSV
            </x-button>
            <x-button color="red" icon="i-ph-file-pdf" wire:click="exportPdf">

                PDF
            </x-button>
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
        <x-form.select label="Poliklinik" block :items="$polyclinics" wire:model.live='polyclinic' />
        <x-form.select label="DPJP" block :items="$doctors" wire:model.live='doctor' />
    </div>

    {{-- Keterangan Warna Border Status Pelayanan --}}
    <div class="p-3 bg-white dark:bg-boxdark rounded-md border border-stroke dark:border-strokedark shadow-xs">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs">
            <span class="font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                <span class="w-1.5 h-3.5 bg-primary rounded-full"></span>
                Keterangan Warna Border:
            </span>
            <div class="flex flex-wrap items-center gap-2">
                @foreach (\App\Helpers\StatusHelper::getBorderLegends() as $legend)
                    <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-gray-50 dark:bg-meta-4 border border-stroke/60 dark:border-strokedark text-gray-700 dark:text-gray-200">
                        <span class="w-1.5 h-3 rounded-full {{ $legend['color'] }}"></span>
                        <span class="text-[11px] font-semibold whitespace-nowrap">{{ $legend['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($patients->count() > 0)
        <div class="space-y-4">
            @foreach ($patients as $patient)
                <x-outpatient-item :$patient />
            @endforeach
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $patients->links() }}
    </x-pagination>
</x-content>
