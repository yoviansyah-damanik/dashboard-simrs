<x-content>
    <x-breadcrumb title="Matriks Indikator Penunjang" :items="[['title' => 'Laporan'], ['title' => 'Matriks Indikator Penunjang']]" />

    <x-sirs.report-header title="Matriks Indikator Tahunan Layanan Penunjang"
        :subtitle="'Evaluasi Terpadu Kinerja & Pelayanan Laboratorium, Radiologi, Farmasi, dan Gizi Tahun ' . $tahun"
        :profil="$profil"
        bulan=""
        :tahun="$tahun" />

    {{-- Filter Bar & Aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Filter Tahun Evaluasi --}}
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 select-none pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-emerald-600 dark:text-emerald-400 text-base"></span>
                    <span>Tahun Evaluasi:</span>
                </span>
                <div class="relative">
                    <select wire:model.live="tahun"
                        class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--history-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memperbarui matriks...</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-button color="default" icon="i-ph-file-pdf" wire:click="exportPdf" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf">Cetak PDF Data</span>
                <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
                    <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
                    <span>Menyiapkan PDF...</span>
                </span>
            </x-button>
        </div>
    </div>

    {{-- Executive KPI Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
        {{-- Card 1: Total Pelayanan Penunjang --}}
        <div class="p-5 bg-gradient-to-br from-emerald-600 to-teal-800 rounded-2xl text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-200">Total Pelayanan Penunjang</span>
                <span class="p-2 bg-white/15 rounded-xl text-white backdrop-blur-sm">
                    <span class="icon-[solar--pulse-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black tracking-tight mb-1">
                {{ number_format($summary['total_pelayanan'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-emerald-100 font-medium">
                <span>Rata-rata: <strong>{{ number_format($summary['avg_per_month'], 1, ',', '.') }}</strong>/bln</span>
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-[11px] font-bold">Puncak: {{ $summary['peak_month_name'] }}</span>
            </div>
        </div>

        {{-- Card 2: Total Pengunjung --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pasien Terlayani</span>
                <span class="p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_pasien'], 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
                <span>Total kunjungan 4 unit penunjang</span>
                <span class="font-bold text-blue-600 dark:text-blue-400">Tahun {{ $tahun }}</span>
            </div>
        </div>

        {{-- Card 3: Distribusi Asal Pasien (Poli, IGD, Akumulasi Ralan, Ranap) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Asal Pasien</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                </span>
            </div>
            @php
                $totAll = $summary['total_pelayanan'] > 0 ? $summary['total_pelayanan'] : 1;
                $pctRalan = round(($summary['total_ralan'] / $totAll) * 100, 1);
                $pctRanap = round(($summary['total_ranap'] / $totAll) * 100, 1);
            @endphp
            <div class="space-y-1.5">
                <div class="flex justify-between items-baseline">
                    <span class="text-xs font-bold text-sky-600 dark:text-sky-400">Ralan: {{ number_format($summary['total_ralan'], 0, ',', '.') }} ({{ $pctRalan }}%)</span>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Ranap: {{ number_format($summary['total_ranap'], 0, ',', '.') }} ({{ $pctRanap }}%)</span>
                </div>
                <div class="flex justify-between text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                    <span>Poli: <strong>{{ number_format($summary['total_poli'], 0, ',', '.') }}</strong></span>
                    <span>IGD: <strong>{{ number_format($summary['total_igd'], 0, ',', '.') }}</strong></span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-meta-4 h-2 rounded-full overflow-hidden flex">
                    <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $pctRalan }}%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $pctRanap }}%"></div>
                </div>
            </div>
        </div>

        {{-- Card 4: Rasio Kontribusi 4 Unit Penunjang --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kontribusi Penunjang</span>
                <span class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <span class="icon-[solar--pie-chart-2-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="space-y-1.5">
                <div class="grid grid-cols-2 gap-1 text-[11px] font-bold">
                    <span class="text-emerald-600 dark:text-emerald-400 truncate">Farmasi: {{ $summary['contrib_farmasi_percent'] }}%</span>
                    <span class="text-sky-600 dark:text-sky-400 truncate">Lab: {{ $summary['contrib_lab_percent'] }}%</span>
                    <span class="text-purple-600 dark:text-purple-400 truncate">Rad: {{ $summary['contrib_rad_percent'] }}%</span>
                    <span class="text-amber-600 dark:text-amber-400 truncate">Gizi: {{ $summary['contrib_gizi_percent'] }}%</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-meta-4 h-2 rounded-full overflow-hidden flex">
                    <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $summary['contrib_farmasi_percent'] }}%"></div>
                    <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $summary['contrib_lab_percent'] }}%"></div>
                    <div class="bg-purple-500 h-full transition-all duration-500" style="width: {{ $summary['contrib_rad_percent'] }}%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $summary['contrib_gizi_percent'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Visual Section: Charts --}}
    <div x-data="ancillaryMatrixCharts(@js($charts), @js($tahun))" wire:key="ancillary-charts-{{ $tahun }}"
        class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm no-print">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="icon-[solar--graph-new-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">
                        Grafik Analisis Tren & Distribusi Penunjang (Tahun {{ $tahun }})
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Komparasi volume bulanan 4 layanan (Farmasi, Lab, Radiologi, Gizi) dan distribusi asal pasien
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Chart Type Toggle (Line vs Bar) --}}
                <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50">
                    <button type="button" @click="setMainChartType('line')"
                        :class="mainChartType === 'line' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                        <span class="icon-[solar--graph-new-bold-duotone] text-sm text-emerald-600 dark:text-emerald-400"></span>
                        <span>Garis</span>
                    </button>
                    <button type="button" @click="setMainChartType('bar')"
                        :class="mainChartType === 'bar' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                        <span class="icon-[solar--chart-2-bold-duotone] text-sm text-emerald-600 dark:text-emerald-400"></span>
                        <span>Batang</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Trend Chart --}}
        <div class="mb-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Tren Pelayanan Bulanan 4 Unit Penunjang (Januari - Desember {{ $tahun }})
            </h4>
            <div class="relative w-full h-[320px]" x-ref="mainTrendContainer">
                <canvas x-ref="mainTrendCanvas"></canvas>
            </div>
        </div>

        {{-- Sub Distribution Charts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-stroke/60 dark:border-strokedark/60">
            {{-- Sub Chart 1: Donut Proporsi Layanan (4 Layanan) --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Proporsi 4 Layanan Penunjang</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="serviceRatioCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 2: Donut Asal Pasien (Poli, IGD, Ranap) --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Asal Pasien (Poli, IGD, Ranap)</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="careSettingCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 3: Bar Jenis Resep Farmasi --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Jenis Resep Farmasi</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="farmasiJenisCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 4: Bar Distribusi Waktu Makan Gizi --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Waktu Pemberian Makan Gizi</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="giziWaktuCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Multi-Tab Indicator Matrix Table Section --}}
    <div class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
        {{-- Section Header & Tabs --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-xl text-emerald-600 dark:text-emerald-400"></span>
                    Tabel Matriks Bulanan Layanan Penunjang (12 Bulan)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Data rincian Januari s.d. Desember, akumulasi tahunan, dan rata-rata per bulan
                </p>
            </div>

            {{-- Tab Controls (5 Tabs) --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 no-print flex-wrap gap-1">
                <button type="button" wire:click="setTab('all')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'all' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm"></span>
                    <span>Semua Penunjang</span>
                </button>
                <button type="button" wire:click="setTab('farmasi')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'farmasi' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--pill-bold-duotone] text-sm"></span>
                    <span>Farmasi</span>
                </button>
                <button type="button" wire:click="setTab('lab')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'lab' ? 'bg-sky-600 text-white shadow-sm font-bold ring-1 ring-sky-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--test-tube-minimalistic-bold-duotone] text-sm"></span>
                    <span>Laboratorium</span>
                </button>
                <button type="button" wire:click="setTab('rad')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'rad' ? 'bg-purple-600 text-white shadow-sm font-bold ring-1 ring-purple-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--scanner-bold-duotone] text-sm"></span>
                    <span>Radiologi</span>
                </button>
                <button type="button" wire:click="setTab('gizi')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'gizi' ? 'bg-amber-600 text-white shadow-sm font-bold ring-1 ring-amber-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--chef-hat-bold-duotone] text-sm"></span>
                    <span>Gizi</span>
                </button>
            </div>
        </div>

        {{-- TAB CONTENT 1: GABUNGAN (SEMUA PENUNJANG) --}}
        @if ($activeTab === 'all')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Penunjang</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Pelayanan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold bg-emerald-50/40 dark:bg-emerald-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-emerald-700 dark:text-emerald-400">Total Pelayanan Penunjang</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['gabungan']['total_pelayanan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['total_pelayanan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['total_pelayanan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Section: Volume Pelayanan per Unit --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Volume Pelayanan per Unit Penunjang</td>
                        </tr>
                        {{-- Farmasi --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 font-semibold">↳ Farmasi (Lembar Resep)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['resep'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['resep'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['resep'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Lab --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 font-semibold">↳ Laboratorium (Pemeriksaan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['pemeriksaan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['laboratorium']['pemeriksaan'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Rad --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-700 dark:text-purple-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 font-semibold">↳ Radiologi (Pemeriksaan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['pemeriksaan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['radiologi']['pemeriksaan'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Gizi --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 font-semibold">↳ Gizi (Porsi Makan/Diet)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['porsi'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['porsi'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['gizi']['porsi'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Pasien Terlayani --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Total Pasien Terlayani</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-semibold">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Total Pengunjung</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gabungan']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['pasien'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['pasien'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Section: Asal Pasien --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Distribusi Asal Pasien (Poli, IGD, Ralan, Ranap)</td>
                        </tr>
                        {{-- Poli --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Poli (Rawat Jalan Poliklinik)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gabungan']['poli'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gabungan']['poli'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['gabungan']['poli'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- IGD --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-rose-700 dark:text-rose-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ IGD (Gawat Darurat)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gabungan']['igd'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gabungan']['igd'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['gabungan']['igd'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Akumulasi Ralan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-800 dark:text-sky-300 font-semibold bg-sky-50/30 dark:bg-sky-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Akumulasi Ralan (Poli + IGD)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['gabungan']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['ralan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['ralan'], 1, ',', '.') }}
                            </td>
                        </tr>
                        {{-- Ranap --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-800 dark:text-amber-300 font-semibold bg-amber-50/30 dark:bg-amber-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['gabungan']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['ranap'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['ranap'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Section: Rasio Kontribusi (%) --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Rasio Kontribusi per Unit Penunjang (%)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-emerald-600 dark:text-emerald-400 font-medium">Kontribusi Farmasi (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-600 dark:text-gray-400">{{ $m['gabungan']['rasio_farmasi_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold text-emerald-600 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">{{ $summary['contrib_farmasi_percent'] }}%</td>
                            <td class="py-2.5 px-3 text-center text-gray-500 border-l border-stroke dark:border-strokedark">-</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-sky-600 dark:text-sky-400 font-medium">Kontribusi Laboratorium (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-600 dark:text-gray-400">{{ $m['gabungan']['rasio_lab_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold text-sky-600 dark:text-sky-400 border-l border-stroke dark:border-strokedark">{{ $summary['contrib_lab_percent'] }}%</td>
                            <td class="py-2.5 px-3 text-center text-gray-500 border-l border-stroke dark:border-strokedark">-</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-purple-600 dark:text-purple-400 font-medium">Kontribusi Radiologi (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-600 dark:text-gray-400">{{ $m['gabungan']['rasio_rad_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold text-purple-600 dark:text-purple-400 border-l border-stroke dark:border-strokedark">{{ $summary['contrib_rad_percent'] }}%</td>
                            <td class="py-2.5 px-3 text-center text-gray-500 border-l border-stroke dark:border-strokedark">-</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-amber-600 dark:text-amber-400 font-medium">Kontribusi Gizi (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-600 dark:text-gray-400">{{ $m['gabungan']['rasio_gizi_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold text-amber-600 dark:text-amber-400 border-l border-stroke dark:border-strokedark">{{ $summary['contrib_gizi_percent'] }}%</td>
                            <td class="py-2.5 px-3 text-center text-gray-500 border-l border-stroke dark:border-strokedark">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB CONTENT 2: FARMASI --}}
        @if ($activeTab === 'farmasi')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-emerald-50/70 dark:bg-meta-4/60 text-emerald-900 dark:text-emerald-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-emerald-50 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Farmasi</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-emerald-100/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-emerald-200/50 dark:bg-emerald-900/30 text-emerald-900 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Resep --}}
                        <tr class="hover:bg-emerald-50/50 dark:hover:bg-meta-4/20 font-bold bg-emerald-50/40 dark:bg-emerald-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-emerald-700 dark:text-emerald-400">Total Lembar Resep</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['farmasi']['resep'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-100/70 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['farmasi']['resep'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-200/50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['farmasi']['resep'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Total Pasien --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200 font-semibold">Total Pasien Mendapat Resep</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['pasien'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Asal Peresepan --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Asal Peresepan (Poli, IGD, Ranap Harian, Resep Pulang)</td>
                        </tr>
                        {{-- Poli --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep Poli (Rawat Jalan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['poli'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['poli'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['poli'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- IGD --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-rose-700 dark:text-rose-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep IGD (Gawat Darurat)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['igd'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['igd'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['igd'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Akumulasi Ralan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-800 dark:text-sky-300 font-semibold bg-sky-50/30 dark:bg-sky-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Akumulasi Resep Ralan (Poli + IGD)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['farmasi']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['ralan'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Ranap Harian --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep Ranap Harian (Selama Rawat)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['ranap_harian'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['ranap_harian'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['ranap_harian'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Resep Pulang --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-teal-700 dark:text-teal-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep Pulang (Pasien Keluar)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['resep_pulang'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['resep_pulang'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['resep_pulang'], 1, ',', '.') }}</td>
                        </tr>
                        {{-- Total Ranap --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-800 dark:text-amber-300 font-semibold bg-amber-50/30 dark:bg-amber-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Total Resep Ranap (Harian + Pulang)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['farmasi']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['farmasi']['ranap'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Mutu Pelayanan & Waktu Tunggu --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Indikator Mutu Pelayanan & Waktu Tunggu</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-emerald-700 dark:text-emerald-400">Resep Diserahkan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['diserahkan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['diserahkan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['farmasi']['diserahkan'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-600 dark:text-gray-400">Rata-rata Waktu Tunggu (Menit)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ $m['farmasi']['waktu_tunggu'] > 0 ? $m['farmasi']['waktu_tunggu'] . ' m' : '-' }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold text-gray-500 border-l border-stroke dark:border-strokedark">-</td>
                            <td class="py-2.5 px-3 text-center font-bold text-gray-500 border-l border-stroke dark:border-strokedark">-</td>
                        </tr>

                        {{-- Section: Klasifikasi Resep --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Klasifikasi Jenis Resep (Biasa, Kronis, CITO, PRB)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep Biasa (Reguler)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['biasa'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['biasa'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['farmasi']['biasa'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep Kronis (Obat Rutin)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['kronis'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['kronis'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['farmasi']['kronis'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-rose-600 dark:text-rose-400">↳ Resep CITO (Gawat Darurat)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['cito'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['cito'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['farmasi']['cito'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Resep PRB (Program Rujuk Balik)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['farmasi']['prb'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['farmasi']['prb'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['farmasi']['prb'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB CONTENT 3: LABORATORIUM --}}
        @if ($activeTab === 'lab')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-sky-50/70 dark:bg-meta-4/60 text-sky-900 dark:text-sky-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-sky-50 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Laboratorium</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-sky-100/70 dark:bg-sky-950/40 text-sky-900 dark:text-sky-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-sky-200/50 dark:bg-sky-900/30 text-sky-900 dark:text-sky-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Pemeriksaan Lab --}}
                        <tr class="hover:bg-sky-50/50 dark:hover:bg-meta-4/20 font-bold bg-sky-50/40 dark:bg-sky-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-sky-700 dark:text-sky-400">Total Pemeriksaan Lab</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['laboratorium']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-100/70 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['pemeriksaan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-200/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['laboratorium']['pemeriksaan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Total Pasien --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200 font-semibold">Total Pengunjung</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['laboratorium']['pasien'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Asal Pasien --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Distribusi Asal Pasien (Poli, IGD, Ralan, Ranap)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Poli (Rawat Jalan Poliklinik)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['poli'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['poli'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['laboratorium']['poli'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-rose-700 dark:text-rose-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ IGD (Gawat Darurat)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['igd'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['igd'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['laboratorium']['igd'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-800 dark:text-sky-300 font-semibold bg-sky-50/30 dark:bg-sky-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Akumulasi Ralan (Poli + IGD)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['laboratorium']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['laboratorium']['ralan'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-800 dark:text-amber-300 font-semibold bg-amber-50/30 dark:bg-amber-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['laboratorium']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['laboratorium']['ranap'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Kategori Lab --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Kategori Pemeriksaan Laboratorium (PK, PA, MB)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Patologi Klinik (PK)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['pk'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['pk'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['laboratorium']['pk'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Patologi Anatomi (PA)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['pa'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['pa'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['laboratorium']['pa'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Mikrobiologi (MB)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['mb'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['laboratorium']['mb'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['laboratorium']['mb'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB CONTENT 4: RADIOLOGI --}}
        @if ($activeTab === 'rad')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-purple-50/70 dark:bg-meta-4/60 text-purple-900 dark:text-purple-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-purple-50 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Radiologi</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-purple-100/70 dark:bg-purple-950/40 text-purple-900 dark:text-purple-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-purple-200/50 dark:bg-purple-900/30 text-purple-900 dark:text-purple-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Pemeriksaan Radiologi --}}
                        <tr class="hover:bg-purple-50/50 dark:hover:bg-meta-4/20 font-bold bg-purple-50/40 dark:bg-purple-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-purple-700 dark:text-purple-400">Total Pemeriksaan Radiologi</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['radiologi']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-purple-100/70 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['pemeriksaan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-200/50 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['radiologi']['pemeriksaan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Total Pasien --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200 font-semibold">Total Pengunjung</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['radiologi']['pasien'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Asal Pasien --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Distribusi Asal Pasien (Poli, IGD, Ralan, Ranap)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Poli (Rawat Jalan Poliklinik)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['poli'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['poli'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['radiologi']['poli'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-rose-700 dark:text-rose-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ IGD (Gawat Darurat)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['igd'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['igd'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['radiologi']['igd'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-800 dark:text-sky-300 font-semibold bg-sky-50/30 dark:bg-sky-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Akumulasi Ralan (Poli + IGD)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['radiologi']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['radiologi']['ralan'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-800 dark:text-amber-300 font-semibold bg-amber-50/30 dark:bg-amber-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['radiologi']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['radiologi']['ranap'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Modalitas Radiologi --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Modalitas Radiologi (CR, CT, USG, MRI, PX, MG)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ CR / X-Ray Konvensional</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['cr'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['cr'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['radiologi']['cr'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ CT Scan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['ct'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['ct'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['radiologi']['ct'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ USG (Ultrasonografi)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['us'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['us'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['radiologi']['us'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ MRI (Magnetic Resonance)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['mr'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['mr'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['radiologi']['mr'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Panoramic & Dental X-Ray</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['px'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['px'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['radiologi']['px'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Mammography</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['mg'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['radiologi']['mg'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['radiologi']['mg'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB CONTENT 5: GIZI / NUTRISI KLINIS --}}
        @if ($activeTab === 'gizi')
            {{-- Quick Links to Nutrition Module --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5 p-3.5 bg-amber-50/60 dark:bg-amber-950/20 rounded-2xl border border-amber-200/60 dark:border-amber-800/40 no-print">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <span class="icon-[solar--chef-hat-bold-duotone] text-lg"></span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-800 dark:text-white">Modul Gizi & Diet Pasien</h4>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Pencatatan harian permintaan diet dan evaluasi asuhan gizi</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('nutrition') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-amber-800 dark:text-amber-300 bg-white dark:bg-boxdark border border-amber-300 dark:border-amber-700 hover:bg-amber-100/50 transition shadow-xs">
                        <span class="icon-[solar--cup-first-bold-duotone] text-sm text-amber-600"></span>
                        <span>Permintaan Diet</span>
                    </a>
                    <a href="{{ route('nutrition.recap') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition">
                        <span class="icon-[solar--chart-bold-duotone] text-sm"></span>
                        <span>Rekap Permintaan Diet</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-amber-50/70 dark:bg-meta-4/60 text-amber-900 dark:text-amber-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-amber-50 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Pelayanan Gizi</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-amber-100/70 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-amber-200/50 dark:bg-amber-900/30 text-amber-900 dark:text-amber-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Porsi Diet --}}
                        <tr class="hover:bg-amber-50/50 dark:hover:bg-meta-4/20 font-bold bg-amber-50/40 dark:bg-amber-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-amber-700 dark:text-amber-400">Total Porsi Makanan/Diet Disajikan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['gizi']['porsi'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-100/70 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gizi']['porsi'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-200/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gizi']['porsi'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Pasien Mendapat Diet --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200 font-semibold">Total Pengunjung Mendapat Diet</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['gizi']['pasien'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Waktu Makan --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Distribusi Jadwal Waktu Makan Pasien</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-amber-700 dark:text-amber-400">↳ Sarapan Pagi</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['pagi'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['pagi'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['gizi']['pagi'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-cyan-700 dark:text-cyan-400">↳ Makan Siang</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['siang'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['siang'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['gizi']['siang'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-purple-700 dark:text-purple-400">↳ Makan Sore / Malam</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['sore'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['sore'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['gizi']['sore'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Asal Pasien Gizi --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Distribusi Asal Pasien (Rawat Inap & Rawat Jalan)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-800 dark:text-amber-300 font-semibold bg-amber-50/30 dark:bg-amber-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-800 dark:text-gray-200 font-bold">{{ number_format($m['gizi']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">{{ number_format($averages['gizi']['ranap'], 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Rawat Jalan / Konsultasi Gizi (Poli + IGD)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($averages['gizi']['ralan'], 1, ',', '.') }}</td>
                        </tr>

                        {{-- Section: Asuhan Gizi Klinis (ADIME) --}}
                        <tr class="bg-gray-50/70 dark:bg-meta-4/40 font-bold text-[11px] text-gray-500 uppercase tracking-wider">
                            <td colspan="15" class="py-1 px-3">Asuhan Gizi & Nutrisi Klinis (ADIME)</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Asuhan Gizi ADIME (Pemeriksaan & Catatan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['asuhan_adime'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['asuhan_adime'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['gizi']['asuhan_adime'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6">↳ Pasien Dianalisis Asuhan Gizi</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gizi']['pasien_adime'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format($totals['gizi']['pasien_adime'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold border-l border-stroke dark:border-strokedark">{{ number_format(round($totals['gizi']['pasien_adime'] / 12, 1), 1, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Top Jenis Diet Section --}}
            @if (!empty($matrix['top_diets']))
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--cup-hot-bold-duotone] text-amber-600 dark:text-amber-400 text-sm"></span>
                        <span>Distribusi Jenis Diet & Menu Pasien Terbanyak Tahun {{ $tahun }}</span>
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach ($matrix['top_diets'] as $d)
                            <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark shadow-xs flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-800 dark:text-white truncate pr-2">{{ $d['nama_diet'] }}</span>
                                <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-black shrink-0">
                                    {{ number_format($d['total_porsi'], 0, ',', '.') }} porsi
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Top Bangsal Section --}}
            @if (!empty($matrix['top_bangsal']))
                <div class="p-4 mt-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--bed-bold-duotone] text-amber-600 dark:text-amber-400 text-sm"></span>
                        <span>Sebaran Bangsal / Ruangan Penerima Diet Terbanyak Tahun {{ $tahun }}</span>
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach ($matrix['top_bangsal'] as $b)
                            <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark shadow-xs flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-800 dark:text-white truncate pr-2">{{ $b['nm_bangsal'] }}</span>
                                <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-black shrink-0">
                                    {{ number_format($b['total_porsi'], 0, ',', '.') }} porsi
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Executive Insights & Catatan Operasional (Footer) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 no-print">
        {{-- Insight 1: Farmasi --}}
        <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200/50 dark:border-emerald-800/30">
            <div class="flex items-center gap-2 mb-2 text-emerald-800 dark:text-emerald-300 font-bold text-xs">
                <span class="icon-[solar--pill-bold-duotone] text-base"></span>
                <span>Instalasi Farmasi</span>
            </div>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed">
                Total <strong>{{ number_format($totals['farmasi']['resep'], 0, ',', '.') }}</strong> lembar resep diproses dengan 
                <strong>{{ number_format($totals['farmasi']['diserahkan'], 0, ',', '.') }}</strong> resep diserahkan ke pasien ralan & ranap (termasuk resep pulang).
            </p>
        </div>

        {{-- Insight 2: Lab --}}
        <div class="p-4 bg-sky-50/50 dark:bg-sky-950/20 rounded-2xl border border-sky-200/50 dark:border-sky-800/30">
            <div class="flex items-center gap-2 mb-2 text-sky-800 dark:text-sky-300 font-bold text-xs">
                <span class="icon-[solar--test-tube-minimalistic-bold-duotone] text-base"></span>
                <span>Instalasi Laboratorium</span>
            </div>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed">
                Menyumbang <strong>{{ number_format($totals['laboratorium']['pemeriksaan'], 0, ',', '.') }}</strong> parameter uji laboratorium
                ({{ $summary['contrib_lab_percent'] }}% dari total penunjang), didominasi Patologi Klinik.
            </p>
        </div>

        {{-- Insight 3: Radiologi --}}
        <div class="p-4 bg-purple-50/50 dark:bg-purple-950/20 rounded-2xl border border-purple-200/50 dark:border-purple-800/30">
            <div class="flex items-center gap-2 mb-2 text-purple-800 dark:text-purple-300 font-bold text-xs">
                <span class="icon-[solar--scanner-bold-duotone] text-base"></span>
                <span>Instalasi Radiologi</span>
            </div>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed">
                Mencatat <strong>{{ number_format($totals['radiologi']['pemeriksaan'], 0, ',', '.') }}</strong> pemeriksaan pencitraan diagnostik
                lintas modalitas CR X-Ray, CT Scan, USG, MRI, dan Panoramic.
            </p>
        </div>

        {{-- Insight 4: Gizi --}}
        <div class="p-4 bg-amber-50/50 dark:bg-amber-950/20 rounded-2xl border border-amber-200/50 dark:border-amber-800/30">
            <div class="flex items-center gap-2 mb-2 text-amber-800 dark:text-amber-300 font-bold text-xs">
                <span class="icon-[solar--chef-hat-bold-duotone] text-base"></span>
                <span>Instalasi Gizi</span>
            </div>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed">
                Menyajikan <strong>{{ number_format($totals['gizi']['porsi'], 0, ',', '.') }}</strong> porsi makanan diet pasien rawat inap dan
                <strong>{{ number_format($totals['gizi']['asuhan_adime'], 0, ',', '.') }}</strong> catatan evaluasi asuhan gizi klinis ADIME.
            </p>
        </div>
    </div>

    {{-- Tanda Tangan & Footer Pengesahan untuk Cetak --}}
    <div class="hidden print:grid grid-cols-2 gap-8 pt-8 mt-12 text-xs text-gray-800">
        <div></div>
        <div class="text-center space-y-16">
            <p>{{ $profil['kabupaten'] ?? 'Padangsidimpuan' }}, 31 Desember {{ $tahun }}<br><strong>Kepala Instalasi Penunjang Medis</strong></p>
            <p class="font-bold underline">(..........................................................)</p>
        </div>
    </div>

    {{-- Script Chart.js dengan Alpine.js --}}
    @script
        <script>
            Alpine.data('ancillaryMatrixCharts', (chartPayload, year) => {
                let mainTrendInstance = null;
                let serviceRatioInstance = null;
                let careSettingInstance = null;
                let farmasiJenisInstance = null;
                let giziWaktuInstance = null;

                return {
                    chartPayload: chartPayload,
                    year: year,
                    mainChartType: 'line',

                    init() {
                        this.$nextTick(() => {
                            this.renderAllCharts();
                        });
                    },

                    isDark() {
                        return document.documentElement.classList.contains('dark');
                    },

                    setMainChartType(type) {
                        this.mainChartType = type;
                        this.renderMainTrendChart();
                    },

                    renderAllCharts() {
                        this.renderMainTrendChart();
                        this.renderServiceRatioChart();
                        this.renderCareSettingChart();
                        this.renderFarmasiJenisChart();
                        this.renderGiziWaktuChart();
                    },

                    renderMainTrendChart() {
                        const canvas = this.$refs.mainTrendCanvas;
                        if (!canvas) return;

                        if (mainTrendInstance) {
                            try { mainTrendInstance.destroy(); } catch (e) {}
                            mainTrendInstance = null;
                        }

                        const isDark = this.isDark();
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        const rawDatasets = this.chartPayload.trend_monthly.datasets || [];
                        const datasets = JSON.parse(JSON.stringify(rawDatasets)).map(ds => {
                            if (this.mainChartType === 'bar') {
                                return {
                                    ...ds,
                                    borderRadius: 6,
                                    backgroundColor: ds.borderColor,
                                };
                            }
                            return ds;
                        });

                        try {
                            mainTrendInstance = new Chart(canvas, {
                                type: this.mainChartType,
                                data: {
                                    labels: this.chartPayload.trend_monthly.labels,
                                    datasets: datasets
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 400 },
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                font: { size: 11, weight: '600' }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                            titleColor: isDark ? '#f1f5f9' : '#0f172a',
                                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: textColor, font: { size: 11, weight: 'bold' } }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: { color: textColor, font: { size: 11 } }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Error main trend chart:', err);
                        }
                    },

                    renderServiceRatioChart() {
                        const canvas = this.$refs.serviceRatioCanvas;
                        if (!canvas) return;
                        if (serviceRatioInstance) { try { serviceRatioInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            serviceRatioInstance = new Chart(canvas, {
                                type: 'doughnut',
                                data: JSON.parse(JSON.stringify(this.chartPayload.service_ratio)),
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: { color: textColor, font: { size: 10, weight: 'bold' }, boxWidth: 10 }
                                        }
                                    },
                                    cutout: '65%'
                                }
                            });
                        } catch (e) {}
                    },

                    renderCareSettingChart() {
                        const canvas = this.$refs.careSettingCanvas;
                        if (!canvas) return;
                        if (careSettingInstance) { try { careSettingInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            careSettingInstance = new Chart(canvas, {
                                type: 'doughnut',
                                data: JSON.parse(JSON.stringify(this.chartPayload.care_setting)),
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: { color: textColor, font: { size: 10, weight: 'bold' }, boxWidth: 10 }
                                        }
                                    },
                                    cutout: '65%'
                                }
                            });
                        } catch (e) {}
                    },

                    renderFarmasiJenisChart() {
                        const canvas = this.$refs.farmasiJenisCanvas;
                        if (!canvas) return;
                        if (farmasiJenisInstance) { try { farmasiJenisInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            farmasiJenisInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.farmasi_jenis)),
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        x: { ticks: { color: textColor, font: { size: 9, weight: 'bold' } }, grid: { display: false } },
                                        y: { beginAtZero: true, ticks: { color: textColor, font: { size: 9 } } }
                                    }
                                }
                            });
                        } catch (e) {}
                    },

                    renderGiziWaktuChart() {
                        const canvas = this.$refs.giziWaktuCanvas;
                        if (!canvas) return;
                        if (giziWaktuInstance) { try { giziWaktuInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            giziWaktuInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.gizi_waktu)),
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        x: { ticks: { color: textColor, font: { size: 9, weight: 'bold' } }, grid: { display: false } },
                                        y: { beginAtZero: true, ticks: { color: textColor, font: { size: 9 } } }
                                    }
                                }
                            });
                        } catch (e) {}
                    }
                };
            });
        </script>
    @endscript
</x-content>
