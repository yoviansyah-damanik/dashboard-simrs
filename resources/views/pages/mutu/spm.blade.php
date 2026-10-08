<x-content>
    <x-breadcrumb title="Standar Pelayanan Minimal (SPM)" :items="[['title' => 'Mutu & Akreditasi'], ['title' => 'Standar Pelayanan Minimal (SPM)']]" />

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="icon-[solar--diploma-bold-duotone] text-indigo-600 dark:text-indigo-400 text-3xl"></span>
                <span>Standar Pelayanan Minimal (SPM) Rumah Sakit</span>
            </h1>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                Kinerja Indikator Pelayanan Berdasarkan Kepmenkes RI No. 129/Menkes/SK/II/2008 untuk IGD, Rawat Jalan, Rawat Inap, Farmasi, dan Penunjang
            </p>
        </div>

        <div class="flex items-center gap-2 no-print">
            <x-button color="default" icon="i-ph-file-pdf" wire:click="exportPdf" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf">Cetak PDF Data</span>
                <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
                    <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
                    <span>Menyiapkan PDF...</span>
                </span>
            </x-button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-indigo-600 text-base"></span>
                    <span>Tahun:</span>
                </span>
                <select wire:model.live="selectedYear"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-indigo-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-indigo-600 text-base"></span>
                    <span>Periode:</span>
                </span>
                <select wire:model.live="selectedMonth"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-indigo-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($months as $mKey => $mLabel)
                        <option value="{{ $mKey }}">{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                <span class="icon-[solar--check-circle-bold] text-base"></span>
                <span>Tercapai: {{ $summary['total_tercapai'] }} / {{ $summary['total_indikator'] }} SPM</span>
            </div>
            <div class="text-xs font-black px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                {{ $summary['persen_tercapai'] }}% Kepatuhan
            </div>
        </div>
    </div>

    {{-- SPM Sections Grid --}}
    <div class="space-y-6 mb-8">
        @foreach ($summary['sections'] as $secKey => $sec)
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-stroke dark:border-strokedark flex items-center justify-between bg-gray-50/50 dark:bg-meta-4/20">
                    <div class="flex items-center gap-2.5">
                        <span class="{{ $sec['icon'] }} text-indigo-600 text-xl"></span>
                        <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">
                            {{ $sec['unit'] }}
                        </h3>
                    </div>
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">
                        {{ count($sec['indicators']) }} Indikator Standar
                    </span>
                </div>

                <div class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($sec['indicators'] as $ind)
                        <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 hover:bg-gray-50/40 dark:hover:bg-meta-4/10 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $ind['nama'] }}
                                    </h4>
                                    @if ($ind['is_achieved'])
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            Sesuai Standar
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            Perlu Tindak Lanjut
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ $ind['keterangan'] }}
                                </p>
                            </div>

                            <div class="flex items-center gap-6 text-xs">
                                <div class="text-right">
                                    <span class="text-gray-400 block text-[10px] font-semibold">Standar SPM:</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $ind['standar'] }}</span>
                                </div>
                                <div class="text-right min-w-[70px]">
                                    <span class="text-gray-400 block text-[10px] font-semibold">Capaian:</span>
                                    <span class="text-base font-black {{ $ind['is_achieved'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $ind['capaian'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-content>
