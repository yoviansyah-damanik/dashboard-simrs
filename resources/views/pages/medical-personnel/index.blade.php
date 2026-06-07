<x-content>
    <x-breadcrumb title="Tenaga Medis" :items="[['title' => 'SDM'], ['title' => 'Tenaga Medis']]" />

    {{-- Search --}}
    <x-form.input type="search" block wire:model.live.debounce.750ms="search"
        placeholder="Cari berdasarkan nama atau kode dokter..." />

    {{-- Filters --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        <x-form.select label="Spesialis" block :items="$spesialisOptions" wire:model.live='spesialis' />
        <x-form.select label="Status" block :items="$statusOptions" wire:model.live='status' />
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
    </div>

    {{-- Summary --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <span class="icon-[solar--users-group-rounded-bold-duotone] text-primary text-lg"></span>
        <span class="font-bold text-gray-700 dark:text-white">{{ $records->total() }}</span>
        <span>tenaga medis ditemukan</span>
    </div>

    @if ($records->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach ($records as $dokter)
                <div
                    class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300 group overflow-hidden">
                    {{-- Card Header --}}
                    <div class="p-5 pb-4">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500">
                                <span class="icon-[solar--stethoscope-bold-duotone] text-2xl"></span>
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span @class([
                                    'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest border',
                                    'bg-emerald-50 text-emerald-600 border-emerald-100' => $dokter['status'] === 'Aktif',
                                    'bg-red-50 text-red-600 border-red-100' => $dokter['status'] !== 'Aktif',
                                ])>
                                    {{ $dokter['status'] }}
                                </span>
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-sky-50 text-sky-600 border border-sky-100">
                                    {{ $dokter['jenis_kelamin'] === 'Laki-laki' ? 'L' : 'P' }}
                                </span>
                            </div>
                        </div>

                        <h3
                            class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-2 min-h-[2.5rem]">
                            {{ $dokter['nama_dokter'] }}
                        </h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                            {{ $dokter['kode_dokter'] }}
                        </p>
                    </div>

                    {{-- Card Footer --}}
                    <div
                        class="px-5 py-3 bg-gray-50/50 dark:bg-meta-4/20 border-t border-stroke dark:border-strokedark space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="icon-[solar--diploma-bold-duotone] text-primary text-base shrink-0"></span>
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 truncate">
                                {{ $dokter['spesialis'] ?? 'Umum' }}
                            </span>
                        </div>
                        @if ($dokter['no_telp'])
                            <div class="flex items-center gap-2">
                                <span class="icon-[solar--phone-bold-duotone] text-gray-400 text-base shrink-0"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $dokter['no_telp'] }}
                                </span>
                            </div>
                        @endif
                        @if ($dokter['no_izin_praktek'])
                            <div class="flex items-center gap-2">
                                <span
                                    class="icon-[solar--document-bold-duotone] text-gray-400 text-base shrink-0"></span>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                    SIP: {{ $dokter['no_izin_praktek'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $records->links() }}
    </x-pagination>
</x-content>
