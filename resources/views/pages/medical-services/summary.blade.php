<x-content>
    <x-breadcrumb title="Ringkasan Layanan Medis" :items="[['title' => 'Layanan Medis'], ['title' => 'Ringkasan']]" />

    <x-sirs.report-header title="Ringkasan Eksekutif Layanan Medis"
        :subtitle="'Evaluasi Terpadu Rawat Jalan (Poli), Gawat Darurat (IGD), dan Rawat Inap (Ranap) Periode ' . \App\Helpers\DateHelper::dateFormat($startDate, 'd/m/Y') . ' s.d. ' . \App\Helpers\DateHelper::dateFormat($endDate, 'd/m/Y')"
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
            <x-button color="default" icon="i-ph-file-pdf" wire:click="exportPdf" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf">Cetak PDF Data</span>
                <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
                    <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
                    <span>Menyiapkan PDF...</span>
                </span>
            </x-button>
        </div>
    </div>

    {{-- Executive KPI Summary Banner Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6 no-print">
        {{-- Card 1: Total Layanan Medis --}}
        <div class="p-5 bg-gradient-to-br from-emerald-600 to-teal-800 rounded-2xl text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-200">Total Kunjungan Medis</span>
                <span class="p-2 bg-white/15 rounded-xl text-white backdrop-blur-sm">
                    <span class="icon-[solar--pulse-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black tracking-tight mb-1">
                {{ number_format($summary['total_layanan'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-emerald-100 font-medium">
                <span>Total Pengunjung: <strong>{{ number_format($summary['total_pasien'], 0, ',', '.') }}</strong></span>
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-[11px] font-bold">{{ $report['period']['days'] }} Hari</span>
            </div>
        </div>

        {{-- Card 2: Rawat Jalan (Poli) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rawat Jalan (Poli)</span>
                <span class="p-2 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl">
                    <span class="icon-[solar--stethoscope-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['poli_kunjungan'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Rasio: <strong class="text-sky-600 dark:text-sky-400">{{ $summary['pct_poli'] }}%</strong></span>
                <span>{{ number_format($summary['poli_pasien'], 0, ',', '.') }} pengunjung</span>
            </div>
        </div>

        {{-- Card 3: Gawat Darurat (IGD) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Gawat Darurat (IGD)</span>
                <span class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                    <span class="icon-[solar--ambulance-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['igd_kunjungan'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Rasio: <strong class="text-rose-600 dark:text-rose-400">{{ $summary['pct_igd'] }}%</strong></span>
                <span>{{ number_format($summary['igd_pasien'], 0, ',', '.') }} pengunjung</span>
            </div>
        </div>

        {{-- Card 4: Rawat Inap (Ranap) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rawat Inap (Ranap)</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['ranap_kunjungan'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Masuk: <strong class="text-emerald-600">{{ $summary['ranap_admissions'] }}</strong></span>
                <span>Keluar: <strong class="text-amber-600">{{ $summary['ranap_discharges'] }}</strong></span>
            </div>
        </div>

        {{-- Card 5: Pasien Dinas (TNI / POLRI) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pasien Dinas</span>
                <span class="p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                    <span class="icon-[solar--shield-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-purple-700 dark:text-purple-400 tracking-tight mb-1">
                {{ number_format($dinas['total'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Proporsi: <strong class="text-purple-600 dark:text-purple-400">{{ $dinas['pct_dinas'] }}%</strong></span>
                <span>{{ number_format($dinas['pasien'], 0, ',', '.') }} orang</span>
            </div>
            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-stroke/60 dark:border-strokedark/60 text-xs font-semibold">
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($dinas['tni'], 0, ',', '.') }} TNI</span>
                <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                <span class="text-blue-600 dark:text-blue-400 font-bold">{{ number_format($dinas['polri'], 0, ',', '.') }} POLRI</span>
            </div>
        </div>
    </div>

    {{-- Interactive Visual Section: Charts --}}
    <div x-data="medicalSummaryCharts(@js($charts))" wire:key="medical-summary-charts-{{ $startDate }}-{{ $endDate }}"
        class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm no-print">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="icon-[solar--graph-new-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">
                        Grafik Analisis Tren & Distribusi Layanan Medis
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Komparasi volume kunjungan harian, proporsi layanan (Poli, IGD, Ranap), dan cara bayar
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
                Tren Harian Layanan Medis (Poli, IGD, Ranap)
            </h4>
            <div class="relative w-full h-[320px]" x-ref="mainTrendContainer">
                <canvas x-ref="mainTrendCanvas"></canvas>
            </div>
        </div>

        {{-- Sub Charts (3 Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-stroke/60 dark:border-strokedark/60">
            {{-- Sub Chart 1: Proporsi Poli vs IGD vs Ranap --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Proporsi Layanan Medis</h5>
                <div class="w-full h-[200px] relative">
                    <canvas x-ref="proportionCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 2: Cara Bayar --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Distribusi Cara Bayar / Penjamin</h5>
                <div class="w-full h-[200px] relative">
                    <canvas x-ref="caraBayarCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 3: Top Poliklinik --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Poliklinik Rawat Jalan Terbanyak</h5>
                <div class="w-full h-[200px] relative">
                    <canvas x-ref="clinicsCanvas"></canvas>
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
                    Tabel Rincian Layanan Medis
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Data rincian komparasi, breakdown per poliklinik, status gawat darurat, dan rawat inap
                </p>
            </div>

            {{-- Tab Controls --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 no-print flex-wrap gap-1">
                <button type="button" wire:click="setTab('overview')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'overview' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm"></span>
                    <span>Ringkasan Komparasi</span>
                </button>
                <button type="button" wire:click="setTab('poli')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'poli' ? 'bg-sky-600 text-white shadow-sm font-bold ring-1 ring-sky-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--stethoscope-bold-duotone] text-sm"></span>
                    <span>Rawat Jalan (Poli)</span>
                </button>
                <button type="button" wire:click="setTab('igd')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'igd' ? 'bg-rose-600 text-white shadow-sm font-bold ring-1 ring-rose-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--ambulance-bold-duotone] text-sm"></span>
                    <span>Gawat Darurat (IGD)</span>
                </button>
                <button type="button" wire:click="setTab('ranap')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'ranap' ? 'bg-amber-600 text-white shadow-sm font-bold ring-1 ring-amber-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--bed-bold-duotone] text-sm"></span>
                    <span>Rawat Inap (Ranap)</span>
                </button>
                <button type="button" wire:click="setTab('dinas')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'dinas' ? 'bg-purple-600 text-white shadow-sm font-bold ring-1 ring-purple-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--shield-bold-duotone] text-sm"></span>
                    <span>Pasien Dinas</span>
                </button>
            </div>
        </div>

        {{-- TAB 1: OVERVIEW (RINGKASAN KOMPARASI) --}}
        @if ($activeTab === 'overview')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-4">Indikator Komparasi</th>
                            <th class="py-3 px-3 text-center text-sky-700 dark:text-sky-400">Rawat Jalan (Poli)</th>
                            <th class="py-3 px-3 text-center text-rose-700 dark:text-rose-400">Gawat Darurat (IGD)</th>
                            <th class="py-3 px-3 text-center text-amber-700 dark:text-amber-400">Rawat Inap (Ranap)</th>
                            <th class="py-3 px-4 text-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark font-black">Total Layanan Medis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold bg-emerald-50/30 dark:bg-emerald-950/20">
                            <td class="py-2.5 px-4 text-emerald-700 dark:text-emerald-400">Total Kunjungan Pasien</td>
                            <td class="py-2.5 px-3 text-center font-bold text-sky-700 dark:text-sky-400">{{ number_format($summary['poli_kunjungan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold text-rose-700 dark:text-rose-400">{{ number_format($summary['igd_kunjungan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center font-bold text-amber-700 dark:text-amber-400">{{ number_format($summary['ranap_kunjungan'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($summary['total_layanan'], 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-4 text-gray-800 dark:text-gray-200">Total Pengunjung</td>
                            <td class="py-2.5 px-3 text-center text-gray-700 dark:text-gray-300">{{ number_format($summary['poli_pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-gray-700 dark:text-gray-300">{{ number_format($summary['igd_pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-gray-700 dark:text-gray-300">{{ number_format($summary['ranap_pasien'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($summary['total_pasien'], 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-4 text-gray-800 dark:text-gray-200">Proporsi / Rasio Pelayanan (%)</td>
                            <td class="py-2.5 px-3 text-center font-semibold text-sky-600 dark:text-sky-400">{{ $summary['pct_poli'] }}%</td>
                            <td class="py-2.5 px-3 text-center font-semibold text-rose-600 dark:text-rose-400">{{ $summary['pct_igd'] }}%</td>
                            <td class="py-2.5 px-3 text-center font-semibold text-amber-600 dark:text-amber-400">{{ $summary['pct_ranap'] }}%</td>
                            <td class="py-2.5 px-4 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">100%</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-4 text-gray-600 dark:text-gray-400">Rata-rata Kunjungan per Hari</td>
                            <td class="py-2.5 px-3 text-center text-gray-700 dark:text-gray-300">{{ number_format(round($summary['poli_kunjungan'] / max(1, $report['period']['days']), 1), 1, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-gray-700 dark:text-gray-300">{{ number_format(round($summary['igd_kunjungan'] / max(1, $report['period']['days']), 1), 1, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-gray-700 dark:text-gray-300">{{ number_format(round($summary['ranap_kunjungan'] / max(1, $report['period']['days']), 1), 1, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format(round($summary['total_layanan'] / max(1, $report['period']['days']), 1), 1, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 bg-emerald-50/30 dark:bg-emerald-950/20 font-semibold border-t border-emerald-200 dark:border-emerald-800/40">
                            <td class="py-2.5 px-4 text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 pl-6">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Pasien Dinas TNI</span>
                            </td>
                            <td class="py-2.5 px-3 text-center text-emerald-700 dark:text-emerald-400">{{ number_format($dinas['tni_poli'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-emerald-700 dark:text-emerald-400">{{ number_format($dinas['tni_igd'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-emerald-700 dark:text-emerald-400">{{ number_format($dinas['tni_ranap'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold bg-emerald-100/50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($dinas['tni'], 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 bg-blue-50/30 dark:bg-blue-950/20 font-semibold">
                            <td class="py-2.5 px-4 text-blue-800 dark:text-blue-300 flex items-center gap-1.5 pl-6">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                <span>Pasien Dinas POLRI</span>
                            </td>
                            <td class="py-2.5 px-3 text-center text-blue-700 dark:text-blue-400">{{ number_format($dinas['polri_poli'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-blue-700 dark:text-blue-400">{{ number_format($dinas['polri_igd'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-blue-700 dark:text-blue-400">{{ number_format($dinas['polri_ranap'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-bold bg-blue-100/50 dark:bg-blue-950/50 text-blue-800 dark:text-blue-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($dinas['polri'], 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 bg-purple-50/40 dark:bg-purple-950/30 font-bold border-t border-purple-200 dark:border-purple-800/40">
                            <td class="py-2.5 px-4 text-purple-700 dark:text-purple-400 flex items-center gap-1.5">
                                <span class="icon-[solar--shield-bold-duotone] text-sm"></span>
                                <span>Total Pasien Dinas (TNI & POLRI)</span>
                            </td>
                            <td class="py-2.5 px-3 text-center text-purple-700 dark:text-purple-400">{{ number_format($dinas['poli'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-purple-700 dark:text-purple-400">{{ number_format($dinas['igd'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center text-purple-700 dark:text-purple-400">{{ number_format($dinas['ranap'], 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center font-black bg-purple-100/60 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">
                                {{ number_format($dinas['total'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 2: POLIKLINIK (RAWAT JALAN) --}}
        @if ($activeTab === 'poli')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-sky-50 dark:bg-meta-4/60 text-sky-900 dark:text-sky-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Poliklinik Spesialis</th>
                            <th class="py-3 px-4 text-center">Total Kunjungan</th>
                            <th class="py-3 px-4 text-center">Pengunjung</th>
                            <th class="py-3 px-4 text-center">Proporsi Poli (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        @forelse ($outpatient['top_clinics'] as $index => $c)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                <td class="py-2.5 px-4 text-center text-gray-500">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-4 font-bold text-gray-800 dark:text-white">{{ $c->nm_poli }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-sky-700 dark:text-sky-400">{{ number_format($c->total, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-4 text-center text-gray-700 dark:text-gray-300">{{ number_format($c->pasien, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-4 text-center font-semibold text-gray-600 dark:text-gray-400">
                                    {{ $summary['poli_kunjungan'] > 0 ? round(($c->total / $summary['poli_kunjungan']) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada data rawat jalan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 3: GAWAT DARURAT (IGD) --}}
        @if ($activeTab === 'igd')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--shield-warning-bold-duotone] text-rose-600 text-base"></span>
                        <span>Status & Alur Keluar Pasien IGD</span>
                    </h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pasien Pulang / Selesai Pelayanan</span>
                            <span class="text-xs font-bold text-emerald-600">{{ number_format($emergency['pulang'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Tindak Lanjut Rawat Inap (Ranap)</span>
                            <span class="text-xs font-bold text-amber-600">{{ number_format($emergency['dirawat'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Dirujuk ke Faskes Lanjutan</span>
                            <span class="text-xs font-bold text-blue-600">{{ number_format($emergency['dirujuk'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Meninggal di IGD (DOA / IGD)</span>
                            <span class="text-xs font-bold text-rose-600">{{ number_format($emergency['meninggal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--pie-chart-bold-duotone] text-rose-600 text-base"></span>
                        <span>Ringkasan Beban Pelayanan Gawat Darurat</span>
                    </h5>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-[11px] text-gray-500">Total Kunjungan</span>
                            <div class="text-xl font-black text-rose-600 mt-1">{{ number_format($emergency['total'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-[11px] text-gray-500">Total Pengunjung</span>
                            <div class="text-xl font-black text-gray-800 dark:text-white mt-1">{{ number_format($emergency['pasien'], 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark col-span-2">
                            <span class="text-[11px] text-gray-500">Rasio Masuk Ranap dari IGD</span>
                            <div class="text-lg font-bold text-amber-600 mt-0.5">
                                {{ $emergency['total'] > 0 ? round(($emergency['dirawat'] / $emergency['total']) * 100, 1) : 0 }}% dari pasien IGD
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 4: RAWAT INAP (RANAP) --}}
        @if ($activeTab === 'ranap')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--door-open-bold-duotone] text-amber-600 text-base"></span>
                        <span>Status Pulang & Indikator Ranap</span>
                    </h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pasien Keluar Sembuh / Membaik</span>
                            <span class="text-xs font-bold text-emerald-600">{{ number_format($inpatient['sembuh'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pulang Paksa (APS)</span>
                            <span class="text-xs font-bold text-amber-600">{{ number_format($inpatient['pulang_paksa'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Dirujuk ke RS Lain</span>
                            <span class="text-xs font-bold text-blue-600">{{ number_format($inpatient['dirujuk'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pasien Meninggal di Ranap</span>
                            <span class="text-xs font-bold text-rose-600">{{ number_format($inpatient['meninggal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-amber-50/60 dark:bg-amber-950/20 rounded-xl border border-amber-200 dark:border-amber-800">
                            <span class="text-xs font-bold text-amber-800 dark:text-amber-300">Rata-rata Lama Dirawat (ALOS)</span>
                            <span class="text-xs font-black text-amber-800 dark:text-amber-300">{{ $summary['ranap_alos'] }} Hari</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40">
                    <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <span class="icon-[solar--bed-bold-duotone] text-amber-600 text-base"></span>
                        <span>Distribusi Ruang Perawatan / Bangsal</span>
                    </h5>
                    <div class="space-y-2">
                        @forelse ($inpatient['top_wards'] as $w)
                            <div class="flex justify-between items-center p-2 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark text-xs">
                                <span class="font-bold text-gray-800 dark:text-white truncate">{{ $w->nm_bangsal }}</span>
                                <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg font-black shrink-0">
                                    {{ number_format($w->total, 0, ',', '.') }} pasien
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada data admisi bangsal pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 5: PASIEN DINAS (TNI / POLRI) --}}
        @if ($activeTab === 'dinas')
            <div class="space-y-6">
                {{-- Quick Stats Pasien Dinas --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div class="p-3 bg-purple-50/70 dark:bg-purple-950/20 rounded-xl border border-purple-200 dark:border-purple-800/40">
                        <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 dark:text-purple-300">Total Kunjungan</span>
                        <div class="text-xl font-black text-purple-800 dark:text-purple-200 mt-0.5">
                            {{ number_format($dinas['total'], 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-medium">{{ $dinas['pct_dinas'] }}% dari total RS</span>
                    </div>

                    <div class="p-3 bg-white dark:bg-boxdark rounded-xl border border-stroke dark:border-strokedark">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Pasien Unik (RM)</span>
                        <div class="text-xl font-black text-gray-800 dark:text-white mt-0.5">
                            {{ number_format($dinas['pasien'], 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium">Orang Berobat</span>
                    </div>

                    <div class="p-3 bg-sky-50/70 dark:bg-sky-950/20 rounded-xl border border-sky-200 dark:border-sky-800/40">
                        <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 dark:text-sky-300">Rawat Jalan (Poli)</span>
                        <div class="text-xl font-black text-sky-800 dark:text-sky-200 mt-0.5">
                            {{ number_format($dinas['poli'], 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-sky-600 dark:text-sky-400 font-medium">Poliklinik Spesialis</span>
                    </div>

                    <div class="p-3 bg-rose-50/70 dark:bg-rose-950/20 rounded-xl border border-rose-200 dark:border-rose-800/40">
                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-300">Gawat Darurat (IGD)</span>
                        <div class="text-xl font-black text-rose-800 dark:text-rose-200 mt-0.5">
                            {{ number_format($dinas['igd'], 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-rose-600 dark:text-rose-400 font-medium">Kegawatdaruratan</span>
                    </div>

                    <div class="p-3 bg-amber-50/70 dark:bg-amber-950/20 rounded-xl border border-amber-200 dark:border-amber-800/40">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-300">Rawat Inap (Ranap)</span>
                        <div class="text-xl font-black text-amber-800 dark:text-amber-200 mt-0.5">
                            {{ number_format($dinas['ranap'], 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Dirawat di Bangsal</span>
                    </div>

                    <div class="p-3 bg-emerald-50/70 dark:bg-emerald-950/20 rounded-xl border border-emerald-200 dark:border-emerald-800/40">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">TNI vs POLRI</span>
                        <div class="text-sm font-black text-emerald-800 dark:text-emerald-200 mt-1">
                            {{ number_format($dinas['tni'], 0, ',', '.') }} <span class="text-[10px] font-normal text-gray-500">TNI</span>
                        </div>
                        <div class="text-xs font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($dinas['polri'], 0, ',', '.') }} <span class="text-[10px] font-normal text-gray-500">POLRI</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Tabel 1: Rekap Menurut Kategori Personel --}}
                    <div class="lg:col-span-7 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark p-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                            <span class="icon-[solar--users-group-rounded-bold-duotone] text-purple-600 text-base"></span>
                            <span>Rekapitulasi Pelayanan Pasien Dinas Berdasarkan Kategori Personel</span>
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse min-w-[500px]">
                                <thead>
                                    <tr class="bg-purple-50/60 dark:bg-meta-4/60 text-purple-900 dark:text-purple-300 font-bold border-b border-stroke dark:border-strokedark">
                                        <th class="py-2.5 px-3">Kategori Personel</th>
                                        <th class="py-2.5 px-2 text-center text-sky-700 dark:text-sky-400">Poli</th>
                                        <th class="py-2.5 px-2 text-center text-rose-700 dark:text-rose-400">IGD</th>
                                        <th class="py-2.5 px-2 text-center text-amber-700 dark:text-amber-400">Ranap</th>
                                        <th class="py-2.5 px-2 text-center text-emerald-700 dark:text-emerald-400">TNI</th>
                                        <th class="py-2.5 px-2 text-center text-blue-700 dark:text-blue-400">POLRI</th>
                                        <th class="py-2.5 px-3 text-center font-black">Total</th>
                                        <th class="py-2.5 px-2 text-center">Rasio</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                                    @php $totDinasCat = 0; @endphp
                                    @forelse ($dinas['categories'] as $cat)
                                        @php $totDinasCat += $cat->total; @endphp
                                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                            <td class="py-2 px-3 font-bold text-gray-800 dark:text-white">
                                                {{ $cat->kategori }}
                                            </td>
                                            <td class="py-2 px-2 text-center text-sky-600 dark:text-sky-400 font-semibold">{{ number_format($cat->poli, 0, ',', '.') }}</td>
                                            <td class="py-2 px-2 text-center text-rose-600 dark:text-rose-400 font-semibold">{{ number_format($cat->igd, 0, ',', '.') }}</td>
                                            <td class="py-2 px-2 text-center text-amber-600 dark:text-amber-400 font-semibold">{{ number_format($cat->ranap, 0, ',', '.') }}</td>
                                            <td class="py-2 px-2 text-center text-emerald-600 dark:text-emerald-400 font-semibold">{{ number_format($cat->tni, 0, ',', '.') }}</td>
                                            <td class="py-2 px-2 text-center text-blue-600 dark:text-blue-400 font-semibold">{{ number_format($cat->polri, 0, ',', '.') }}</td>
                                            <td class="py-2 px-3 text-center font-black text-purple-700 dark:text-purple-400">
                                                {{ number_format($cat->total, 0, ',', '.') }}
                                            </td>
                                            <td class="py-2 px-2 text-center text-gray-500 font-medium">
                                                {{ $dinas['total'] > 0 ? round(($cat->total / $dinas['total']) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="py-6 text-center text-gray-400 italic">Tidak ada kunjungan pasien dinas pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tabel 2: Top Kesatuan / Satuan Dinas --}}
                    <div class="lg:col-span-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark p-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                            <span class="icon-[solar--shield-check-bold-duotone] text-purple-600 text-base"></span>
                            <span>Sebaran Kesatuan / Satuan Pasien Dinas</span>
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                                        <th class="py-2.5 px-3">Nama Satuan</th>
                                        <th class="py-2.5 px-2 text-center text-sky-700 dark:text-sky-400">Poli</th>
                                        <th class="py-2.5 px-2 text-center text-rose-700 dark:text-rose-400">IGD</th>
                                        <th class="py-2.5 px-2 text-center text-amber-700 dark:text-amber-400">Ranap</th>
                                        <th class="py-2.5 px-3 text-center font-black">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                                    @forelse ($dinas['satuan'] as $sat)
                                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                            <td class="py-2 px-3 font-bold text-gray-800 dark:text-white truncate max-w-[150px]">
                                                {{ data_get($sat, 'nama_satuan') }}
                                            </td>
                                            <td class="py-2 px-2 text-center text-sky-600 dark:text-sky-400 font-semibold">{{ number_format(data_get($sat, 'poli', 0), 0, ',', '.') }}</td>
                                            <td class="py-2 px-2 text-center text-rose-600 dark:text-rose-400 font-semibold">{{ number_format(data_get($sat, 'igd', 0), 0, ',', '.') }}</td>
                                            <td class="py-2 px-2 text-center text-amber-600 dark:text-amber-400 font-semibold">{{ number_format(data_get($sat, 'ranap', 0), 0, ',', '.') }}</td>
                                            <td class="py-2 px-3 text-center font-black text-purple-700 dark:text-purple-400">
                                                {{ number_format(data_get($sat, 'total', 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-gray-400 italic">Tidak ada data satuan pasien dinas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tanda Tangan & Footer Pengesahan untuk Cetak --}}
    <div class="hidden print:grid grid-cols-2 gap-8 pt-8 mt-12 text-xs text-gray-800">
        <div></div>
        <div class="text-center space-y-16">
            <p>{{ $profil['kabupaten'] ?? 'Padangsidimpuan' }}, {{ date('d F Y', strtotime($endDate)) }}<br><strong>Kepala Bidang Pelayanan Medis</strong></p>
            <p class="font-bold underline">(..........................................................)</p>
        </div>
    </div>

    {{-- Script Chart.js dengan Alpine.js --}}
    @script
        <script>
            Alpine.data('medicalSummaryCharts', (chartPayload) => {
                let mainTrendInstance = null;
                let proportionInstance = null;
                let caraBayarInstance = null;
                let clinicsInstance = null;

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
                        this.renderCaraBayarChart();
                        this.renderClinicsChart();
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

                    renderCaraBayarChart() {
                        const canvas = this.$refs.caraBayarCanvas;
                        if (!canvas) return;
                        if (caraBayarInstance) { try { caraBayarInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            caraBayarInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.cara_bayar)),
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

                    renderClinicsChart() {
                        const canvas = this.$refs.clinicsCanvas;
                        if (!canvas) return;
                        if (clinicsInstance) { try { clinicsInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            clinicsInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.clinics)),
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
