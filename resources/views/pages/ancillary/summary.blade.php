<x-content>
    <x-breadcrumb title="Ringkasan Layanan Penunjang" :items="[['title' => 'Layanan Penunjang Medis'], ['title' => 'Ringkasan']]" />

    <x-sirs.report-header title="Ringkasan Eksekutif Layanan Penunjang Medis"
        :subtitle="'Evaluasi Terpadu Farmasi, Laboratorium, Radiologi, dan Gizi Periode ' . \App\Helpers\DateHelper::dateFormat($startDate, 'd/m/Y') . ' s.d. ' . \App\Helpers\DateHelper::dateFormat($endDate, 'd/m/Y')"
        :profil="$profil"
        bulan=""
        :tahun="date('Y', strtotime($startDate))" />

    {{-- Filter Bar & Aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Filter Periode Cepat --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 select-none pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-emerald-600 dark:text-emerald-400 text-base"></span>
                    <span>Periode:</span>
                </span>
                <select wire:model.live="period"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer">
                    <option value="today">Hari Ini</option>
                    <option value="yesterday">Kemarin</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="last_7_days">7 Hari Terakhir</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="this_year">Tahun Ini</option>
                    <option value="custom">Rentang Kustom</option>
                </select>
            </div>

            {{-- Custom Date Inputs --}}
            @if ($period === 'custom')
                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="startDate"
                        class="py-1.5 px-3 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-semibold text-gray-800 dark:text-white focus:border-emerald-500 outline-none">
                    <span class="text-xs text-gray-400">s.d.</span>
                    <input type="date" wire:model.live="endDate"
                        class="py-1.5 px-3 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-semibold text-gray-800 dark:text-white focus:border-emerald-500 outline-none">
                </div>
            @endif

            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memperbarui ringkasan...</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-button color="default" icon="i-ph-printer" onclick="window.print()">Cetak Ringkasan</x-button>
        </div>
    </div>

    {{-- Executive KPI Summary Banner Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6 no-print">
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
                <span>Total Pengunjung: <strong>{{ number_format($summary['total_pasien'], 0, ',', '.') }}</strong></span>
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-[11px] font-bold">{{ $report['period']['days'] }} Hari</span>
            </div>
        </div>

        {{-- Card 2: Farmasi --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Farmasi (Resep)</span>
                <span class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <span class="icon-[solar--pill-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_farmasi'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Kontribusi: <strong class="text-emerald-600 dark:text-emerald-400">{{ $summary['pct_farmasi'] }}%</strong></span>
                <span>{{ number_format($farmasi['diserahkan'], 0, ',', '.') }} diserahkan</span>
            </div>
        </div>

        {{-- Card 3: Laboratorium --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Laboratorium (Pemeriksaan)</span>
                <span class="p-2 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl">
                    <span class="icon-[solar--test-tube-minimalistic-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_lab'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Kontribusi: <strong class="text-sky-600 dark:text-sky-400">{{ $summary['pct_lab'] }}%</strong></span>
                <span>{{ number_format($laboratorium['pasien'], 0, ',', '.') }} pengunjung</span>
            </div>
        </div>

        {{-- Card 4: Radiologi --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Radiologi (Pemeriksaan)</span>
                <span class="p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                    <span class="icon-[solar--scanner-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_rad'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Kontribusi: <strong class="text-purple-600 dark:text-purple-400">{{ $summary['pct_rad'] }}%</strong></span>
                <span>{{ number_format($radiologi['pasien'], 0, ',', '.') }} pengunjung</span>
            </div>
        </div>

        {{-- Card 5: Gizi --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Gizi (Porsi Diet)</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <span class="icon-[solar--chef-hat-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_gizi'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Kontribusi: <strong class="text-amber-600 dark:text-amber-400">{{ $summary['pct_gizi'] }}%</strong></span>
                <span>{{ number_format($gizi['pasien'], 0, ',', '.') }} pengunjung</span>
            </div>
        </div>
    </div>

    {{-- Interactive Visual Section: Charts --}}
    <div x-data="ancillarySummaryCharts(@js($charts))" wire:key="ancillary-summary-charts-{{ $startDate }}-{{ $endDate }}"
        class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm no-print">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="icon-[solar--graph-new-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">
                        Grafik Analisis Tren & Proporsi Layanan Penunjang
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Tren harian 4 unit penunjang (Farmasi, Lab, Radiologi, Gizi) dan distribusi asal pasien
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
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
                Tren Harian 4 Unit Layanan Penunjang
            </h4>
            <div class="relative w-full h-[320px]" x-ref="mainTrendContainer">
                <canvas x-ref="mainTrendCanvas"></canvas>
            </div>
        </div>

        {{-- Sub Charts (2 Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-stroke/60 dark:border-strokedark/60">
            {{-- Sub Chart 1: Proporsi Layanan Penunjang --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Proporsi 4 Layanan Penunjang</h5>
                <div class="w-full h-[200px] relative">
                    <canvas x-ref="proportionCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 2: Asal Pasien --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Asal Pasien (Poli, IGD, Ranap)</h5>
                <div class="w-full h-[200px] relative">
                    <canvas x-ref="careSettingCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Multi-Tab Detailed Table Section --}}
    <div class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-xl text-emerald-600 dark:text-emerald-400"></span>
                    Tabel Rincian Layanan Penunjang
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Data rincian komparasi, indikator farmasi, laboratorium, radiologi, dan gizi
                </p>
            </div>

            {{-- Tab Controls (5 Tabs) --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 no-print flex-wrap gap-1">
                <button type="button" wire:click="setTab('overview')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'overview' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm"></span>
                    <span>Ringkasan Komparasi</span>
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

        {{-- TAB 1: OVERVIEW --}}
        @if ($activeTab === 'overview')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-4">Unit Layanan Penunjang</th>
                            <th class="py-3 px-3 text-center">Volume Pelayanan</th>
                            <th class="py-3 px-3 text-center">Total Pengunjung</th>
                            <th class="py-3 px-3 text-center">Ralan (Poli & IGD)</th>
                            <th class="py-3 px-3 text-center">Rawat Inap (Ranap)</th>
                            <th class="py-3 px-4 text-center">Proporsi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400">
                            <td class="py-2.5 px-4 font-bold">1. Farmasi (Lembar Resep)</td>
                            <td class="py-2.5 px-3 text-center font-bold">{{ number_format($farmasi['total'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($farmasi['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($farmasi['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($farmasi['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold">{{ $summary['pct_farmasi'] }}%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-4 font-bold">2. Laboratorium (Pemeriksaan)</td>
                            <td class="py-2.5 px-3 text-center font-bold">{{ number_format($laboratorium['total'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($laboratorium['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($laboratorium['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($laboratorium['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold">{{ $summary['pct_lab'] }}%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-700 dark:text-purple-400">
                            <td class="py-2.5 px-4 font-bold">3. Radiologi (Pemeriksaan)</td>
                            <td class="py-2.5 px-3 text-center font-bold">{{ number_format($radiologi['total'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($radiologi['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($radiologi['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($radiologi['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold">{{ $summary['pct_rad'] }}%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-4 font-bold">4. Gizi (Porsi Makanan Diet)</td>
                            <td class="py-2.5 px-3 text-center font-bold">{{ number_format($gizi['total'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($gizi['pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($gizi['ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($gizi['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold">{{ $summary['pct_gizi'] }}%</td>
                        </tr>
                        <tr class="font-black bg-emerald-50/40 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300">
                            <td class="py-2.5 px-4">Total Penunjang Medis</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($summary['total_pelayanan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($summary['total_pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($summary['total_ralan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">{{ number_format($summary['total_ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 2: FARMASI --}}
        @if ($activeTab === 'farmasi')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--pill-bold-duotone] text-emerald-600 text-base"></span>
                        <span>Asal Peresepan & Penyerahan Obat</span>
                    </h5>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Resep Poli (Rawat Jalan)</span>
                            <span class="font-bold text-sky-600">{{ number_format($farmasi['poli'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Resep IGD (Gawat Darurat)</span>
                            <span class="font-bold text-rose-600">{{ number_format($farmasi['igd'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Resep Ranap Harian</span>
                            <span class="font-bold text-amber-600">{{ number_format($farmasi['ranap_harian'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Resep Pulang (Pasien Keluar)</span>
                            <span class="font-bold text-teal-600">{{ number_format($farmasi['resep_pulang'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                            <span class="font-bold text-emerald-800 dark:text-emerald-300">Resep Diserahkan</span>
                            <span class="font-black text-emerald-800 dark:text-emerald-300">{{ number_format($farmasi['diserahkan'], 0, ',', '.') }} ({{ $farmasi['total'] > 0 ? round(($farmasi['diserahkan'] / $farmasi['total']) * 100, 1) : 0 }}%)</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--checklist-bold-duotone] text-emerald-600 text-base"></span>
                        <span>Klasifikasi Resep & Waktu Tunggu SPM</span>
                    </h5>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-center">
                            <span class="text-[11px] text-gray-500">Resep Biasa</span>
                            <div class="text-lg font-bold text-gray-800 dark:text-white mt-0.5">{{ number_format($farmasi['biasa'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-center">
                            <span class="text-[11px] text-gray-500">Resep Kronis</span>
                            <div class="text-lg font-bold text-blue-600 mt-0.5">{{ number_format($farmasi['kronis'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-center">
                            <span class="text-[11px] text-gray-500">Resep CITO</span>
                            <div class="text-lg font-bold text-rose-600 mt-0.5">{{ number_format($farmasi['cito'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-center">
                            <span class="text-[11px] text-gray-500">Resep PRB</span>
                            <div class="text-lg font-bold text-purple-600 mt-0.5">{{ number_format($farmasi['prb'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between items-center text-xs">
                        <span class="text-gray-600 dark:text-gray-400">Rata-rata Waktu Tunggu</span>
                        <span class="font-black text-gray-800 dark:text-white">{{ $farmasi['waktu_tunggu'] > 0 ? $farmasi['waktu_tunggu'] . ' Menit' : '-' }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 3: LABORATORIUM --}}
        @if ($activeTab === 'lab')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--test-tube-bold-duotone] text-sky-600 text-base"></span>
                        <span>Kategori Pemeriksaan Laboratorium</span>
                    </h5>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Patologi Klinik (PK)</span>
                            <span class="font-bold text-sky-600">{{ number_format($laboratorium['pk'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Patologi Anatomi (PA)</span>
                            <span class="font-bold text-teal-600">{{ number_format($laboratorium['pa'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Mikrobiologi (MB)</span>
                            <span class="font-bold text-amber-600">{{ number_format($laboratorium['mb'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--ranking-bold-duotone] text-sky-600 text-base"></span>
                        <span>Parameter Pemeriksaan Lab Terbanyak</span>
                    </h5>
                    <div class="space-y-2">
                        @forelse ($laboratorium['top_tests'] as $t)
                            <div class="flex justify-between items-center p-2 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-xs">
                                <span class="font-bold text-gray-800 dark:text-white truncate">{{ $t->nm_perawatan }}</span>
                                <span class="px-2 py-0.5 bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 rounded-lg font-black shrink-0">
                                    {{ number_format($t->total, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada data lab pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 4: RADIOLOGI --}}
        @if ($activeTab === 'rad')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--scanner-bold-duotone] text-purple-600 text-base"></span>
                        <span>Modalitas Pencitraan Radiologi</span>
                    </h5>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between">
                            <span class="text-gray-600">CR / X-Ray</span>
                            <span class="font-bold text-purple-600">{{ number_format($radiologi['cr'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between">
                            <span class="text-gray-600">CT Scan</span>
                            <span class="font-bold text-purple-600">{{ number_format($radiologi['ct'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between">
                            <span class="text-gray-600">USG</span>
                            <span class="font-bold text-purple-600">{{ number_format($radiologi['us'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between">
                            <span class="text-gray-600">MRI</span>
                            <span class="font-bold text-purple-600">{{ number_format($radiologi['mr'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between">
                            <span class="text-gray-600">Panoramic</span>
                            <span class="font-bold text-purple-600">{{ number_format($radiologi['px'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark flex justify-between">
                            <span class="text-gray-600">Mammography</span>
                            <span class="font-bold text-purple-600">{{ number_format($radiologi['mg'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--ranking-bold-duotone] text-purple-600 text-base"></span>
                        <span>Pemeriksaan Radiologi Terbanyak</span>
                    </h5>
                    <div class="space-y-2">
                        @forelse ($radiologi['top_tests'] as $t)
                            <div class="flex justify-between items-center p-2 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-xs">
                                <span class="font-bold text-gray-800 dark:text-white truncate">{{ $t->nm_perawatan }}</span>
                                <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-lg font-black shrink-0">
                                    {{ number_format($t->total, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada data radiologi pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 5: GIZI --}}
        @if ($activeTab === 'gizi')
            {{-- Quick Links to Nutrition Module --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5 p-3.5 bg-amber-50/60 dark:bg-amber-950/20 rounded-2xl border border-amber-200/60 dark:border-amber-800/40 no-print">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <span class="icon-[solar--chef-hat-bold-duotone] text-lg"></span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-800 dark:text-white">Modul Gizi SIMRS</h4>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Akses langsung pencatatan permintaan diet dan analisis rekap gizi</p>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Col 1: Waktu Makan & Asuhan Klinis --}}
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--chef-hat-bold-duotone] text-amber-600 text-base"></span>
                        <span>Distribusi Waktu Makan & Asuhan Klinis</span>
                    </h5>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Sarapan Pagi</span>
                            <span class="font-bold text-amber-600">{{ number_format($gizi['pagi'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Makan Siang</span>
                            <span class="font-bold text-cyan-600">{{ number_format($gizi['siang'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Makan Sore / Malam</span>
                            <span class="font-bold text-purple-600">{{ number_format($gizi['sore'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-amber-50/60 dark:bg-amber-950/20 rounded-xl border border-amber-200 dark:border-amber-800">
                            <span class="font-bold text-amber-800 dark:text-amber-300">Evaluasi Asuhan Gizi (ADIME)</span>
                            <span class="font-black text-amber-800 dark:text-amber-300">{{ number_format($gizi['asuhan_adime'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Col 2: Jenis Diet Terbanyak --}}
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--cup-hot-bold-duotone] text-amber-600 text-base"></span>
                        <span>Jenis Diet Terbanyak</span>
                    </h5>
                    <div class="space-y-2">
                        @forelse ($gizi['top_diets'] as $d)
                            <div class="flex justify-between items-center p-2 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-xs">
                                <span class="font-bold text-gray-800 dark:text-white truncate">{{ $d->nama_diet }}</span>
                                <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg font-black shrink-0">
                                    {{ number_format($d->total_porsi, 0, ',', '.') }} porsi
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada data diet pada periode ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Col 3: Sebaran Bangsal Terbanyak --}}
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--bed-bold-duotone] text-amber-600 text-base"></span>
                        <span>Sebaran Bangsal / Ruangan Terbanyak</span>
                    </h5>
                    <div class="space-y-2">
                        @forelse ($gizi['top_bangsal'] ?? [] as $b)
                            <div class="flex justify-between items-center p-2 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-xs">
                                <span class="font-bold text-gray-800 dark:text-white truncate">{{ $b->nm_bangsal }}</span>
                                <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg font-black shrink-0">
                                    {{ number_format($b->total_porsi, 0, ',', '.') }} porsi
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada data bangsal pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tanda Tangan & Footer Pengesahan untuk Cetak --}}
    <div class="hidden print:grid grid-cols-2 gap-8 pt-8 mt-12 text-xs text-gray-800">
        <div></div>
        <div class="text-center space-y-16">
            <p>{{ $profil['kabupaten'] ?? 'Padangsidimpuan' }}, {{ date('d F Y', strtotime($endDate)) }}<br><strong>Kepala Bidang Penunjang Medis</strong></p>
            <p class="font-bold underline">(..........................................................)</p>
        </div>
    </div>

    {{-- Script Chart.js dengan Alpine.js --}}
    @script
        <script>
            Alpine.data('ancillarySummaryCharts', (chartPayload) => {
                let mainTrendInstance = null;
                let proportionInstance = null;
                let careSettingInstance = null;

                return {
                    chartPayload: chartPayload,
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
                        this.renderProportionChart();
                        this.renderCareSettingChart();
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

                        const rawDatasets = this.chartPayload.trend.datasets || [];
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
                                    labels: this.chartPayload.trend.labels,
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
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: textColor, font: { size: 10, weight: 'bold' } }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: { color: textColor, font: { size: 10 } }
                                        }
                                    }
                                }
                            });
                        } catch (e) {}
                    },

                    renderProportionChart() {
                        const canvas = this.$refs.proportionCanvas;
                        if (!canvas) return;
                        if (proportionInstance) { try { proportionInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            proportionInstance = new Chart(canvas, {
                                type: 'doughnut',
                                data: JSON.parse(JSON.stringify(this.chartPayload.proportion)),
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
                    }
                };
            });
        </script>
    @endscript
</x-content>
