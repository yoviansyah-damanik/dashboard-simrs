<x-content>
    <x-breadcrumb title="Insiden Keselamatan Pasien (IKP)" :items="[['title' => 'Mutu & Akreditasi'], ['title' => 'Insiden Keselamatan Pasien (IKP)']]" />

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="icon-[solar--shield-warning-bold-duotone] text-amber-500 text-3xl"></span>
                <span>Insiden Keselamatan Pasien (IKP)</span>
            </h1>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                Monitoring Pelaporan Insiden (KTD, KNC, KTC, KPC, Sentinel), Matriks Grading Risiko, dan Tindak Lanjut Investigasi Sederhana / RCA
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
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-amber-500 text-base"></span>
                    <span>Tahun:</span>
                </span>
                <select wire:model.live="selectedYear"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-amber-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-amber-500 text-base"></span>
                    <span>Periode:</span>
                </span>
                <select wire:model.live="selectedMonth"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-amber-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($months as $mKey => $mLabel)
                        <option value="{{ $mKey }}">{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Jenis Insiden --}}
            <div class="flex items-center gap-1.5 pl-2 border-l border-stroke dark:border-strokedark">
                <button type="button" wire:click="filterJenis('all')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedJenis === 'all' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                    Semua Insiden
                </button>
                <button type="button" wire:click="filterJenis('ktd')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedJenis === 'ktd' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                    KTD
                </button>
                <button type="button" wire:click="filterJenis('knc')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedJenis === 'knc' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                    KNC
                </button>
                <button type="button" wire:click="filterJenis('ktc')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedJenis === 'ktc' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                    KTC
                </button>
                <button type="button" wire:click="filterJenis('sentinel')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedJenis === 'sentinel' ? 'bg-red-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                    Sentinel
                </button>
            </div>
        </div>
    </div>

    {{-- Executive Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase">Total Laporan Insiden</span>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $summary['counts']['total'] }}</h3>
            <span class="text-[11px] text-gray-400">Tahun {{ $selectedYear }}</span>
        </div>
        <div class="p-4 bg-rose-50/60 dark:bg-rose-950/20 rounded-2xl border border-rose-200 dark:border-rose-800">
            <span class="text-xs font-bold text-rose-700 dark:text-rose-400 uppercase">Sentinel</span>
            <h3 class="text-2xl font-black text-rose-700 dark:text-rose-300 mt-1">{{ $summary['counts']['sentinel'] }}</h3>
            <span class="text-[11px] text-rose-500">Cedera parah/kematian</span>
        </div>
        <div class="p-4 bg-amber-50/60 dark:bg-amber-950/20 rounded-2xl border border-amber-200 dark:border-amber-800">
            <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase">KTD (Tidak Diharapkan)</span>
            <h3 class="text-2xl font-black text-amber-700 dark:text-amber-300 mt-1">{{ $summary['counts']['ktd'] }}</h3>
            <span class="text-[11px] text-amber-600">Insiden terjadi & cedera</span>
        </div>
        <div class="p-4 bg-blue-50/60 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-800">
            <span class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase">KNC (Nyaris Cedera)</span>
            <h3 class="text-2xl font-black text-blue-700 dark:text-blue-300 mt-1">{{ $summary['counts']['knc'] }}</h3>
            <span class="text-[11px] text-blue-600">Terpapar namun dicegah</span>
        </div>
        <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200 dark:border-emerald-800">
            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase">Selesai Investigasi</span>
            <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">{{ $summary['persen_selesai'] }}%</h3>
            <span class="text-[11px] text-emerald-600">{{ $summary['selesai_investigasi'] }} kasus bertindak lanjut</span>
        </div>
    </div>

    {{-- Risk Grading Matrix Banner --}}
    <div class="mb-6 p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
        <h3 class="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <span class="icon-[solar--layers-bold-duotone] text-amber-500 text-base"></span>
            <span>Matriks Grading Risiko Standar Kemenkes RI</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ($gradingMatrix as $gradeKey => $gradeItem)
                <div class="p-3 rounded-xl border border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4/30">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-black {{ $gradeKey === 'merah' ? 'text-red-600' : ($gradeKey === 'kuning' ? 'text-amber-600' : ($gradeKey === 'hijau' ? 'text-emerald-600' : 'text-blue-600')) }}">
                            {{ $gradeItem['nama'] }}
                        </span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-white dark:bg-boxdark border border-stroke dark:border-strokedark">
                            {{ $summary['grading'][$gradeKey] ?? 0 }} kasus
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-tight">
                        {{ $gradeItem['tindakan'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Incident Table --}}
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-stroke dark:border-strokedark flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="icon-[solar--checklist-bold-duotone] text-amber-500 text-lg"></span>
                <span>Daftar Insiden Keselamatan Pasien Terlaporkan</span>
            </h3>
            <span class="text-xs text-gray-500">{{ count($incidents) }} Laporan Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-meta-4 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-stroke dark:border-strokedark">
                    <tr>
                        <th class="py-3 px-4">Tgl & Jam</th>
                        <th class="py-3 px-4">No. Rawat</th>
                        <th class="py-3 px-4">Nama Insiden</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Dampak</th>
                        <th class="py-3 px-4">Grading</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($incidents as $inc)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/30 transition-colors">
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $inc['tanggal'] }} <span class="text-gray-400 font-normal">{{ $inc['jam'] }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px]">
                                {{ $inc['no_rawat'] }}
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">
                                {{ $inc['nama_insiden'] }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-gray-100 dark:bg-meta-4 border border-stroke dark:border-strokedark">
                                    {{ $inc['jenis_insiden'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                {{ $inc['dampak'] }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $g = $inc['grading_risiko'];
                                    $gClass = $g === 'merah' ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' : ($g === 'kuning' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($g === 'hijau' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'));
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $gClass }}">
                                    {{ $g }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $inc['status'] === 'Selesai' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                                    {{ $inc['status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button type="button" wire:click="openIncidentDetail('{{ $inc['id'] }}', {{ json_encode($inc) }})"
                                    class="py-1 px-2.5 rounded-lg text-xs font-bold bg-white dark:bg-boxdark hover:bg-amber-50 text-gray-700 hover:text-amber-600 border border-stroke dark:border-strokedark transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                Tidak ada laporan insiden keselamatan pasien untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail Insiden (Notice !mt-0) --}}
    @if ($activeIncidentModal)
        <div class="fixed inset-0 z-50 flex items-center !mt-0 justify-center p-3 sm:p-5 md:p-8 bg-slate-950/75 backdrop-blur-md overflow-hidden animate-in fade-in duration-200"
            wire:keydown.escape="closeIncidentDetail">
            <div class="bg-white dark:bg-boxdark w-full max-w-2xl max-h-[90vh] rounded-3xl border border-stroke dark:border-strokedark shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-200 !mt-0">
                <div class="p-5 border-b border-stroke dark:border-strokedark flex items-start justify-between bg-gray-50/50 dark:bg-meta-4/20">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded bg-amber-100 text-amber-800">
                            {{ $activeIncidentModal['jenis_insiden'] }} - Grading: {{ strtoupper($activeIncidentModal['grading_risiko']) }}
                        </span>
                        <h3 class="text-base font-black text-gray-900 dark:text-white mt-1">
                            {{ $activeIncidentModal['nama_insiden'] }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeIncidentDetail" class="text-gray-400 hover:text-gray-600">
                        <span class="icon-[solar--close-circle-bold] text-2xl"></span>
                    </button>
                </div>

                <div class="p-5 overflow-y-auto space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3 bg-gray-50 dark:bg-meta-4/30 p-3 rounded-xl">
                        <div>
                            <span class="text-gray-400 block font-semibold">Waktu Kejadian:</span>
                            <span class="font-bold text-gray-800 dark:text-white">{{ $activeIncidentModal['tanggal'] }} {{ $activeIncidentModal['jam'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-semibold">Lokasi / Unit:</span>
                            <span class="font-bold text-gray-800 dark:text-white">{{ $activeIncidentModal['lokasi'] }} ({{ $activeIncidentModal['unit_terkait'] }})</span>
                        </div>
                    </div>

                    <div>
                        <span class="font-bold text-gray-700 dark:text-gray-300 block mb-1">Kronologis Kejadian:</span>
                        <p class="p-3 rounded-xl bg-gray-50 dark:bg-meta-4/30 text-gray-600 dark:text-gray-300 leading-relaxed border border-stroke dark:border-strokedark">
                            {{ $activeIncidentModal['kronologis'] }}
                        </p>
                    </div>

                    <div>
                        <span class="font-bold text-gray-700 dark:text-gray-300 block mb-1">Tindakan Langsung yang Dilakukan:</span>
                        <p class="p-3 rounded-xl bg-gray-50 dark:bg-meta-4/30 text-gray-600 dark:text-gray-300 leading-relaxed border border-stroke dark:border-strokedark">
                            {{ $activeIncidentModal['tindakan'] }}
                        </p>
                    </div>

                    <div>
                        <span class="font-bold text-gray-700 dark:text-gray-300 block mb-1">Rencana Tindak Lanjut (RTL) / RCA:</span>
                        <p class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 leading-relaxed border border-emerald-200 dark:border-emerald-800">
                            {{ $activeIncidentModal['rtl'] }}
                        </p>
                    </div>
                </div>

                <div class="p-4 border-t border-stroke dark:border-strokedark flex justify-end bg-gray-50/50 dark:bg-meta-4/20">
                    <button type="button" wire:click="closeIncidentDetail"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-white dark:bg-boxdark border border-stroke text-gray-700 dark:text-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-content>
