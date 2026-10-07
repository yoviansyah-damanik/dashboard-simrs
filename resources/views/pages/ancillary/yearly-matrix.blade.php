<x-content>
    <x-breadcrumb title="Matriks Indikator Penunjang" :items="[['title' => 'Laporan'], ['title' => 'Matriks Indikator Penunjang']]" />

    <x-sirs.report-header title="Matriks Indikator Tahunan Layanan Penunjang"
        :subtitle="'Evaluasi Kinerja & Distribusi Pelayanan Diagnostik Laboratorium dan Radiologi Tahun ' . $tahun"
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
            <x-button color="default" icon="i-ph-printer" onclick="window.print()">Cetak Matriks</x-button>
        </div>
    </div>

    {{-- Executive KPI Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
        {{-- Card 1: Total Pemeriksaan Penunjang --}}
        <div class="p-5 bg-gradient-to-br from-emerald-600 to-teal-800 rounded-2xl text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-200">Total Pemeriksaan</span>
                <span class="p-2 bg-white/15 rounded-xl text-white backdrop-blur-sm">
                    <span class="icon-[solar--pulse-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black tracking-tight mb-1">
                {{ number_format($summary['total_pemeriksaan'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-emerald-100 font-medium">
                <span>Rata-rata: <strong>{{ number_format($summary['avg_per_month'], 1, ',', '.') }}</strong>/bln</span>
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-[11px] font-bold">Puncak: {{ $summary['peak_month_name'] }}</span>
            </div>
        </div>

        {{-- Card 2: Total Pasien Unik --}}
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
                <span>Total kunjungan pasien unik</span>
                <span class="font-bold text-blue-600 dark:text-blue-400">Tahun {{ $tahun }}</span>
            </div>
        </div>

        {{-- Card 3: Distribusi Ralan vs Ranap --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Asal Pasien</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                </span>
            </div>
            @php
                $totAll = $summary['total_pemeriksaan'] > 0 ? $summary['total_pemeriksaan'] : 1;
                $pctRalan = round(($summary['total_ralan'] / $totAll) * 100, 1);
                $pctRanap = round(($summary['total_ranap'] / $totAll) * 100, 1);
            @endphp
            <div class="space-y-2">
                <div class="flex justify-between items-baseline">
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Ralan: {{ number_format($summary['total_ralan'], 0, ',', '.') }} ({{ $pctRalan }}%)</span>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Ranap: {{ number_format($summary['total_ranap'], 0, ',', '.') }} ({{ $pctRanap }}%)</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-meta-4 h-2 rounded-full overflow-hidden flex">
                    <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $pctRalan }}%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $pctRanap }}%"></div>
                </div>
            </div>
        </div>

        {{-- Card 4: Rasio Kontribusi Lab vs Radiologi --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rasio Pelayanan</span>
                <span class="p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                    <span class="icon-[solar--pie-chart-2-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-baseline">
                    <span class="text-xs font-bold text-sky-600 dark:text-sky-400">Lab: {{ number_format($summary['total_lab'], 0, ',', '.') }} ({{ $summary['contrib_lab_percent'] }}%)</span>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Rad: {{ number_format($summary['total_rad'], 0, ',', '.') }} ({{ $summary['contrib_rad_percent'] }}%)</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-meta-4 h-2 rounded-full overflow-hidden flex">
                    <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $summary['contrib_lab_percent'] }}%"></div>
                    <div class="bg-purple-500 h-full transition-all duration-500" style="width: {{ $summary['contrib_rad_percent'] }}%"></div>
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
                        Komparasi volume bulanan, rasio layanan, dan distribusi modalitas diagnostik
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
                Tren Pemeriksaan Bulanan (Januari - Desember {{ $tahun }})
            </h4>
            <div class="relative w-full h-[320px]" x-ref="mainTrendContainer">
                <canvas x-ref="mainTrendCanvas"></canvas>
            </div>
        </div>

        {{-- Sub Distribution Charts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-stroke/60 dark:border-strokedark/60">
            {{-- Sub Chart 1: Donut Lab vs Rad --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Proporsi Layanan</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="serviceRatioCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 2: Donut Ralan vs Ranap --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Ralan vs Ranap</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="careSettingCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 3: Bar Kategori Lab --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Kategori Lab (PK, PA, MB)</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="labCategoryCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 4: Bar Modality Radiologi --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Modalitas Radiologi</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="radModalityCanvas"></canvas>
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

            {{-- Tab Controls --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 no-print">
                <button type="button" wire:click="setTab('all')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'all' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm"></span>
                    <span>Semua Penunjang</span>
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
            </div>
        </div>

        {{-- TAB CONTENT 1: GABUNGAN (SEMUA PENUNJANG) --}}
        @if ($activeTab === 'all')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-48 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Penunjang</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Pemeriksaan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold bg-emerald-50/30 dark:bg-emerald-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-emerald-700 dark:text-emerald-400">Total Pemeriksaan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['gabungan']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['pemeriksaan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['pemeriksaan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Total Pasien Unik --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Total Pasien Unik</td>
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

                        {{-- Row: Rawat Jalan (Ralan) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Rawat Jalan (Ralan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gabungan']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['ralan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['ralan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Rawat Inap (Ranap) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['gabungan']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['gabungan']['ranap'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['gabungan']['ranap'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Kontribusi Laboratorium (%) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 font-bold">Kontribusi Lab (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center font-bold">{{ $m['gabungan']['rasio_lab_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">
                                {{ $summary['contrib_lab_percent'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">
                                -
                            </td>
                        </tr>

                        {{-- Row: Kontribusi Radiologi (%) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-700 dark:text-purple-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 font-bold">Kontribusi Radiologi (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center font-bold">{{ $m['gabungan']['rasio_rad_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">
                                {{ $summary['contrib_rad_percent'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB CONTENT 2: LABORATORIUM --}}
        @if ($activeTab === 'lab')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-48 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Laboratorium</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-sky-100/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Pemeriksaan Lab --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold bg-sky-50/30 dark:bg-sky-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-sky-700 dark:text-sky-400">Total Pemeriksaan Lab</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['laboratorium']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['pemeriksaan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['laboratorium']['pemeriksaan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Pasien Lab --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Pasien Lab</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['pasien'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['laboratorium']['pasien'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Rawat Jalan (Ralan) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Rawat Jalan (Ralan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['ralan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['laboratorium']['ralan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Rawat Inap (Ranap) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['laboratorium']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['ranap'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['laboratorium']['ranap'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Patologi Klinik (PK) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Patologi Klinik (PK)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['laboratorium']['pk'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['pk'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['laboratorium']['pk'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Patologi Anatomi (PA) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Patologi Anatomi (PA)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['laboratorium']['pa'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['pa'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['laboratorium']['pa'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Mikrobiologi (MB) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Mikrobiologi (MB)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['laboratorium']['mb'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['laboratorium']['mb'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['laboratorium']['mb'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB CONTENT 3: RADIOLOGI --}}
        @if ($activeTab === 'rad')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-48 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Radiologi</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-purple-100/50 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Row: Total Pemeriksaan Radiologi --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold bg-purple-50/30 dark:bg-purple-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-purple-700 dark:text-purple-400">Total Pemeriksaan Radiologi</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['radiologi']['pemeriksaan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['pemeriksaan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['radiologi']['pemeriksaan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Pasien Radiologi --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Pasien Radiologi</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['pasien'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['radiologi']['pasien'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Rawat Jalan (Ralan) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Rawat Jalan (Ralan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['ralan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['radiologi']['ralan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Row: Rawat Inap (Ranap) --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['radiologi']['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['ranap'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['radiologi']['ranap'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Modalities Breakdown --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-cyan-700 dark:text-cyan-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">CR / X-Ray Konvensional</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['radiologi']['cr'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['cr'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['radiologi']['cr'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-700 dark:text-purple-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">CT Scan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['radiologi']['ct'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['ct'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['radiologi']['ct'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">USG (Ultrasonografi)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['radiologi']['us'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['us'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['radiologi']['us'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">MRI (Magnetic Resonance)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['radiologi']['mr'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['mr'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['radiologi']['mr'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-pink-700 dark:text-pink-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Panoramic / Dental</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['radiologi']['px'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['px'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['radiologi']['px'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-blue-700 dark:text-blue-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Mammography</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['radiologi']['mg'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['radiologi']['mg'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($totals['radiologi']['mg'] / 12, 1), 1, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
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
                let labCategoryInstance = null;
                let radModalityInstance = null;

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
                        this.renderLabCategoryChart();
                        this.renderRadModalityChart();
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

                    renderLabCategoryChart() {
                        const canvas = this.$refs.labCategoryCanvas;
                        if (!canvas) return;
                        if (labCategoryInstance) { try { labCategoryInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            labCategoryInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.lab_kategori)),
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

                    renderRadModalityChart() {
                        const canvas = this.$refs.radModalityCanvas;
                        if (!canvas) return;
                        if (radModalityInstance) { try { radModalityInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            radModalityInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.rad_modality)),
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
