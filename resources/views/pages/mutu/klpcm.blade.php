<x-content>
    <x-breadcrumb title="Rekam Medis & KLPCM" :items="[['title' => 'Mutu & Akreditasi'], ['title' => 'Rekam Medis & KLPCM']]" />

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="icon-[solar--notes-bold-duotone] text-violet-600 dark:text-violet-400 text-3xl"></span>
                <span>Kelengkapan Rekam Medis & KLPCM</span>
            </h1>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                Evaluasi Ketidaklengkapan Pengisian Catatan Medis (KLPCM) 1x24 Jam Pasien Pulang Rawat Inap & Kepatuhan Resume Medis per DPJP
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
                    <span class="icon-[solar--calendar-bold-duotone] text-violet-600 text-base"></span>
                    <span>Tahun:</span>
                </span>
                <select wire:model.live="selectedYear"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-violet-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-violet-600 text-base"></span>
                    <span>Periode:</span>
                </span>
                <select wire:model.live="selectedMonth"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-violet-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($months as $mKey => $mLabel)
                        <option value="{{ $mKey }}">{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
            Standar Akreditasi Kemenkes: Resume Terisi Lengkap 1x24 Jam
        </div>
    </div>

    {{-- 4 Executive Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase">Total Berkas Pasien Pulang</span>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($summary['total_berkas']) }}</h3>
            <span class="text-[11px] text-gray-400">Berkas rawat inap</span>
        </div>
        <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200 dark:border-emerald-800">
            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase">Berkas Lengkap (≤ 24 Jam)</span>
            <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">{{ number_format($summary['berkas_lengkap']) }}</h3>
            <span class="text-[11px] text-emerald-600">{{ round(($summary['berkas_lengkap'] / $summary['total_berkas']) * 100, 1) }}% Kelengkapan</span>
        </div>
        <div class="p-4 bg-rose-50/60 dark:bg-rose-950/20 rounded-2xl border border-rose-200 dark:border-rose-800">
            <span class="text-xs font-bold text-rose-700 dark:text-rose-400 uppercase">Berkas KLPCM (Belum Lengkap)</span>
            <h3 class="text-2xl font-black text-rose-700 dark:text-rose-300 mt-1">{{ number_format($summary['berkas_klpcm']) }}</h3>
            <span class="text-[11px] text-rose-500">Perlu dilengkapi DPJP</span>
        </div>
        <div class="p-4 bg-violet-50/60 dark:bg-violet-950/20 rounded-2xl border border-violet-200 dark:border-violet-800">
            <span class="text-xs font-bold text-violet-700 dark:text-violet-400 uppercase">Angka KLPCM</span>
            <h3 class="text-2xl font-black text-violet-700 dark:text-violet-300 mt-1">{{ $summary['angka_klpcm'] }}%</h3>
            <span class="text-[11px] text-violet-600">Standar toleransi: ≤ 5%</span>
        </div>
    </div>

    {{-- Komponen Analisis Kuantitatif Rekam Medis --}}
    <div class="mb-6 p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
            <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-violet-600 text-xl"></span>
            <span>Analisis Kuantitatif Kelengkapan Komponen Berkas Rekam Medis</span>
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
            Evaluasi kepatuhan pengisian 6 komponen wajib formulir rekam medis rawat inap.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($summary['komponen'] as $kKey => $komp)
                <div class="p-3.5 rounded-xl border border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4/30">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $komp['nama'] }}</span>
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">{{ $komp['persen'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-violet-500 h-full rounded-full" style="width: {{ $komp['persen'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Kepatuhan per Dokter / DPJP --}}
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-stroke dark:border-strokedark flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="icon-[solar--users-group-rounded-bold-duotone] text-violet-600 text-lg"></span>
                <span>Kepatuhan Pengisian Resume Medis per Dokter Penanggung Jawab Pelayanan (DPJP)</span>
            </h3>
            <span class="text-xs text-gray-500">Standar Kepatuhan: ≥ 90%</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-meta-4 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-stroke dark:border-strokedark">
                    <tr>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama DPJP / Dokter Spesialis</th>
                        <th class="py-3 px-4 text-center">Total Berkas</th>
                        <th class="py-3 px-4 text-center">Lengkap</th>
                        <th class="py-3 px-4 text-center">KLPCM</th>
                        <th class="py-3 px-4 text-center">% Kelengkapan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($doctorCompliance as $doc)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/30 transition-colors">
                            <td class="py-3 px-4 font-mono text-[11px]">{{ $doc['kd_dokter'] }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">{{ $doc['nm_dokter'] }}</td>
                            <td class="py-3 px-4 text-center">{{ $doc['total_berkas'] }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-emerald-600">{{ $doc['berkas_lengkap'] }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-rose-600">{{ $doc['berkas_klpcm'] }}</td>
                            <td class="py-3 px-4 text-center font-black">{{ $doc['persen_lengkap'] }}%</td>
                            <td class="py-3 px-4 text-center">
                                @if ($doc['is_patuh'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Patuh (≥ 90%)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        Perlu Peringatan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
