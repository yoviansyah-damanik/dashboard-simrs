<x-content>
    <x-breadcrumb title="Matriks Indikator Farmasi" :items="[['title' => 'Laporan'], ['title' => 'Matriks Indikator Farmasi']]" />

    <x-sirs.report-header title="Matriks Indikator Tahunan Pelayanan Farmasi"
        :subtitle="'Evaluasi Kinerja Peresepan, Utilisasi Obat, dan Mutu Waktu Pelayanan Resep Tahun ' . $tahun"
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
                <span>Memperbarui matriks farmasi...</span>
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
        {{-- Card 1: Total Lembar Resep --}}
        <div class="p-5 bg-gradient-to-br from-emerald-600 via-teal-700 to-emerald-900 rounded-2xl text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-200">Total Lembar Resep</span>
                <span class="p-2 bg-white/15 rounded-xl text-white backdrop-blur-sm">
                    <span class="icon-[solar--diploma-verified-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black tracking-tight mb-1">
                {{ number_format($summary['total_resep'], 0, ',', '.') }}
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
                <span>Kunjungan unik terlayani</span>
                <span class="font-bold text-blue-600 dark:text-blue-400">Tahun {{ $tahun }}</span>
            </div>
        </div>

        {{-- Card 3: Distribusi Ralan vs Ranap --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Asal Peresepan</span>
                <span class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-baseline">
                    <span class="text-xs font-bold text-sky-600 dark:text-sky-400">Ralan: {{ number_format($summary['total_ralan'], 0, ',', '.') }} ({{ $summary['persen_ralan'] }}%)</span>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Ranap: {{ number_format($summary['total_ranap'], 0, ',', '.') }} ({{ $summary['persen_ranap'] }}%)</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-meta-4 h-2 rounded-full overflow-hidden flex">
                    <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $summary['persen_ralan'] }}%"></div>
                    <div class="bg-purple-500 h-full transition-all duration-500" style="width: {{ $summary['persen_ranap'] }}%"></div>
                </div>
                <div class="flex justify-between items-center text-[11px] text-gray-500 dark:text-gray-400 pt-1 border-t border-stroke/40 dark:border-strokedark/40">
                    <span>Harian: {{ number_format($summary['total_ranap_harian'], 0, ',', '.') }}</span>
                    <span>Resep Pulang: <strong class="text-purple-700 dark:text-purple-400">{{ number_format($summary['total_resep_pulang'], 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Card 4: Waktu Tunggu & Mutu SPM --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Mutu Pelayanan (SPM)</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="flex items-baseline justify-between mb-1">
                <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                    {{ $summary['avg_waktu_tunggu'] }} <span class="text-sm font-semibold text-gray-500">menit</span>
                </div>
                @php
                    $isSpmIdeal = $summary['avg_waktu_tunggu'] <= 30 && $summary['avg_waktu_tunggu'] > 0;
                @endphp
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $isSpmIdeal ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' }}">
                    {{ $isSpmIdeal ? 'Sesuai SPM (≤30m)' : 'Standar SPM ≤30m' }}
                </span>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
                <span>Penyerahan Resep:</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $summary['persen_diserahkan'] }}% Terlayani</span>
            </div>
        </div>
    </div>

    {{-- Interactive Visual Section: Charts --}}
    <div x-data="pharmacyMatrixCharts(@js($charts), @js($tahun))" wire:key="pharmacy-charts-{{ $tahun }}"
        class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm no-print">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="icon-[solar--graph-new-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">
                        Grafik Analisis Tren & Indikator Farmasi (Tahun {{ $tahun }})
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Volume resep bulanan, rasio asal pelayanan, kategori peresepan, dan evaluasi waktu tunggu SPM
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
                Tren Lembar Resep Bulanan (Januari - Desember {{ $tahun }})
            </h4>
            <div class="relative w-full h-[320px]" x-ref="mainTrendContainer">
                <canvas x-ref="mainTrendCanvas"></canvas>
            </div>
        </div>

        {{-- Sub Distribution Charts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-stroke/60 dark:border-strokedark/60">
            {{-- Sub Chart 1: Donut Asal Resep --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Asal Peresepan</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="careSettingCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 2: Donut Status Penyerahan --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Status Penyerahan</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="deliveryStatusCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 3: Bar Jenis Resep --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Klasifikasi Jenis Resep</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="prescriptionTypeCanvas"></canvas>
                </div>
            </div>

            {{-- Sub Chart 4: Line Tren Waktu Tunggu SPM --}}
            <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/40 dark:border-strokedark/40 flex flex-col items-center">
                <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">Waktu Tunggu vs SPM (30m)</h5>
                <div class="w-full h-[180px] relative">
                    <canvas x-ref="waitingTimeCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Multi-Tab 12-Month Indicator Matrix Table --}}
    <div class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
        {{-- Section Header & Tabs --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-xl text-emerald-600 dark:text-emerald-400"></span>
                    Tabel Matriks Bulanan Indikator Farmasi (12 Bulan)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Data rincian Januari s.d. Desember, akumulasi tahunan, dan rata-rata per bulan
                </p>
            </div>

            {{-- Tab Controls --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 no-print">
                <button type="button" wire:click="setTab('summary')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'summary' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm"></span>
                    <span>Ringkasan Utama</span>
                </button>
                <button type="button" wire:click="setTab('care_setting')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'care_setting' ? 'bg-sky-600 text-white shadow-sm font-bold ring-1 ring-sky-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--bed-bold-duotone] text-sm"></span>
                    <span>Ralan vs Ranap</span>
                </button>
                <button type="button" wire:click="setTab('prescription_type')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'prescription_type' ? 'bg-amber-600 text-white shadow-sm font-bold ring-1 ring-amber-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--document-medicine-bold-duotone] text-sm"></span>
                    <span>Jenis Resep</span>
                </button>
                <button type="button" wire:click="setTab('quality')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'quality' ? 'bg-purple-600 text-white shadow-sm font-bold ring-1 ring-purple-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-sm"></span>
                    <span>Mutu & Waktu Tunggu</span>
                </button>
            </div>
        </div>

        {{-- TAB 1: RINGKASAN UTAMA --}}
        @if ($activeTab === 'summary')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Pelayanan</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Total Resep --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold bg-emerald-50/30 dark:bg-emerald-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-emerald-700 dark:text-emerald-400">Total Lembar Resep</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-900 dark:text-white font-bold">{{ number_format($m['total_resep'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['total_resep'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['total_resep'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Total Pasien --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-800 dark:text-gray-200">Total Pengunjung</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center text-gray-700 dark:text-gray-300">{{ number_format($m['total_pasien'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['total_pasien'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 text-gray-800 dark:text-gray-200 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['total_pasien'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Ralan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Rawat Jalan (Ralan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['ralan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['ralan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Ranap Harian --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-gray-600 dark:text-gray-400">
                            <td class="py-2 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-[11px]">
                                ↳ Ranap Harian (Selama Dirawat)
                            </td>
                            @foreach ($months as $m)
                                <td class="py-2 px-2 text-center">{{ number_format($m['ranap_harian'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2 px-3 text-center font-semibold bg-emerald-50 dark:bg-emerald-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['ranap_harian'], 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 text-center font-semibold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['ranap_harian'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Pulang --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-600 dark:text-purple-300">
                            <td class="py-2 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-[11px] font-medium">
                                ↳ Resep Pulang (Pasien Keluar)
                            </td>
                            @foreach ($months as $m)
                                <td class="py-2 px-2 text-center">{{ number_format($m['resep_pulang'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2 px-3 text-center font-semibold bg-emerald-50 dark:bg-emerald-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['resep_pulang'], 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 text-center font-semibold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['resep_pulang'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Ranap Total --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-700 dark:text-purple-400 font-bold bg-purple-50/30 dark:bg-purple-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['ranap'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['ranap'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Diserahkan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Diserahkan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['diserahkan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['diserahkan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['diserahkan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Persentase Penyerahan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Tingkat Penyerahan (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ $m['persen_penyerahan'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-l border-stroke dark:border-strokedark">
                                {{ $summary['persen_diserahkan'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                -
                            </td>
                        </tr>

                        {{-- Waktu Tunggu --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Rata-rata Waktu Tunggu (Menit)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center font-semibold">{{ $m['waktu_tunggu'] > 0 ? $m['waktu_tunggu'] . ' m' : '-' }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-emerald-50 dark:bg-emerald-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">
                                {{ $summary['avg_waktu_tunggu'] }} m
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ $averages['waktu_tunggu'] }} m
                            </td>
                        </tr>

                        {{-- Item Obat Terlayani --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 text-gray-700 dark:text-gray-300">Total Item R/ Obat</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['item_obat'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-50 dark:bg-emerald-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['item_obat'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-emerald-100/50 dark:bg-emerald-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['item_obat'], 1, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 2: RALAN VS RANAP --}}
        @if ($activeTab === 'care_setting')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Kategori Perawatan</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-sky-100/50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Resep Ralan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-700 dark:text-sky-400 font-bold">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Rawat Jalan (Ralan)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['ralan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['ralan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['ralan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Rasio Ralan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-sky-600 dark:text-sky-300">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Proporsi Rawat Jalan (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center font-semibold">{{ $m['rasio_ralan_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ $summary['persen_ralan'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                -
                            </td>
                        </tr>

                        {{-- Ranap Harian --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-gray-600 dark:text-gray-400">
                            <td class="py-2 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-[11px]">
                                ↳ Ranap Harian (Selama Dirawat)
                            </td>
                            @foreach ($months as $m)
                                <td class="py-2 px-2 text-center">{{ number_format($m['ranap_harian'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2 px-3 text-center font-semibold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['ranap_harian'], 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 text-center font-semibold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['ranap_harian'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Pulang --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-600 dark:text-purple-300">
                            <td class="py-2 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 pl-6 text-[11px] font-medium">
                                ↳ Resep Pulang (Pasien Keluar)
                            </td>
                            @foreach ($months as $m)
                                <td class="py-2 px-2 text-center">{{ number_format($m['resep_pulang'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2 px-3 text-center font-semibold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['resep_pulang'], 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 text-center font-semibold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['resep_pulang'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Ranap --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-700 dark:text-purple-400 font-bold bg-purple-50/30 dark:bg-purple-950/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Rawat Inap (Ranap)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['ranap'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['ranap'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['ranap'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Rasio Ranap --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-purple-600 dark:text-purple-300">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Proporsi Rawat Inap (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center font-semibold">{{ $m['rasio_ranap_persen'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-50 dark:bg-sky-950/40 border-l border-stroke dark:border-strokedark">
                                {{ $summary['persen_ranap'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-sky-100/50 dark:bg-sky-900/30 border-l border-stroke dark:border-strokedark">
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 3: JENIS RESEP --}}
        @if ($activeTab === 'prescription_type')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Jenis / Klasifikasi Resep</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-amber-100/50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Resep Biasa --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Biasa (Reguler)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['biasa'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-50 dark:bg-amber-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['biasa'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['biasa'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Kronis --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-blue-700 dark:text-blue-400 font-bold">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Kronis (Obat Rutin)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['kronis'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-amber-50 dark:bg-amber-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['kronis'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['kronis'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep CITO --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-rose-700 dark:text-rose-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep CITO (Segera)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['cito'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-50 dark:bg-amber-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['cito'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['cito'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep PRB --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep PRB (Rujuk Balik)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['prb'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-50 dark:bg-amber-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['prb'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-amber-100/50 dark:bg-amber-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['prb'], 1, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 4: MUTU & WAKTU TUNGGU --}}
        @if ($activeTab === 'quality')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-52 sticky left-0 bg-gray-100 dark:bg-meta-4/90 z-10 shadow-sm">Indikator Mutu Pelayanan</th>
                            @foreach ($months as $m)
                                <th class="py-3 px-2 text-center">{{ $m['nama_pendek'] }}</th>
                            @endforeach
                            <th class="py-3 px-3 text-center bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">Total</th>
                            <th class="py-3 px-3 text-center bg-purple-100/50 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        {{-- Resep Diserahkan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-emerald-700 dark:text-emerald-400 font-bold">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Sudah Diserahkan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['diserahkan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['diserahkan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['diserahkan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Resep Belum Diserahkan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-rose-700 dark:text-rose-400">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Resep Belum Diserahkan</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ number_format($m['belum_diserahkan'], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ number_format($totals['belum_diserahkan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ number_format($averages['belum_diserahkan'], 1, ',', '.') }}
                            </td>
                        </tr>

                        {{-- % Penyerahan --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 font-bold text-gray-800 dark:text-gray-200">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Tingkat Penyerahan (%)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ $m['persen_penyerahan'] }}%</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border-l border-stroke dark:border-strokedark">
                                {{ $summary['persen_diserahkan'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                -
                            </td>
                        </tr>

                        {{-- Waktu Tunggu --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 text-amber-700 dark:text-amber-400 font-bold">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10">Waktu Tunggu Pelayanan (Menit)</td>
                            @foreach ($months as $m)
                                <td class="py-2.5 px-2 text-center">{{ $m['waktu_tunggu'] > 0 ? $m['waktu_tunggu'] . ' m' : '-' }}</td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                {{ $summary['avg_waktu_tunggu'] }} m
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                {{ $averages['waktu_tunggu'] }} m
                            </td>
                        </tr>

                        {{-- Status SPM --}}
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                            <td class="py-2.5 px-3 sticky left-0 bg-white dark:bg-boxdark z-10 font-bold">Kepatuhan Target SPM (≤ 30 Menit)</td>
                            @foreach ($months as $m)
                                @php
                                    $isIdeal = $m['waktu_tunggu'] > 0 && $m['waktu_tunggu'] <= 30;
                                @endphp
                                <td class="py-2.5 px-2 text-center">
                                    @if ($m['waktu_tunggu'] > 0)
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold {{ $isIdeal ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' }}">
                                            {{ $isIdeal ? 'Tercapai' : 'Evaluasi' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-50 dark:bg-purple-950/40 border-l border-stroke dark:border-strokedark">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold {{ $summary['avg_waktu_tunggu'] <= 30 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' }}">
                                    {{ $summary['avg_waktu_tunggu'] <= 30 ? 'Tercapai' : 'Evaluasi' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold bg-purple-100/50 dark:bg-purple-900/30 border-l border-stroke dark:border-strokedark">
                                Standar ≤ 30m
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
            <p>{{ $profil['kabupaten'] ?? 'Padangsidimpuan' }}, 31 Desember {{ $tahun }}<br><strong>Kepala Instalasi Farmasi Rumah Sakit</strong></p>
            <p class="font-bold underline">(..........................................................)</p>
        </div>
    </div>

    {{-- Script Chart.js dengan Alpine.js --}}
    @script
        <script>
            Alpine.data('pharmacyMatrixCharts', (chartPayload, year) => {
                let mainTrendInstance = null;
                let careSettingInstance = null;
                let deliveryStatusInstance = null;
                let prescriptionTypeInstance = null;
                let waitingTimeInstance = null;

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
                        this.renderCareSettingChart();
                        this.renderDeliveryStatusChart();
                        this.renderPrescriptionTypeChart();
                        this.renderWaitingTimeChart();
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

                    renderDeliveryStatusChart() {
                        const canvas = this.$refs.deliveryStatusCanvas;
                        if (!canvas) return;
                        if (deliveryStatusInstance) { try { deliveryStatusInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            deliveryStatusInstance = new Chart(canvas, {
                                type: 'doughnut',
                                data: JSON.parse(JSON.stringify(this.chartPayload.delivery_status)),
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

                    renderPrescriptionTypeChart() {
                        const canvas = this.$refs.prescriptionTypeCanvas;
                        if (!canvas) return;
                        if (prescriptionTypeInstance) { try { prescriptionTypeInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            prescriptionTypeInstance = new Chart(canvas, {
                                type: 'bar',
                                data: JSON.parse(JSON.stringify(this.chartPayload.prescription_type)),
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

                    renderWaitingTimeChart() {
                        const canvas = this.$refs.waitingTimeCanvas;
                        if (!canvas) return;
                        if (waitingTimeInstance) { try { waitingTimeInstance.destroy(); } catch (e) {} }

                        const isDark = this.isDark();
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                        const textColor = isDark ? '#94a3b8' : '#64748b';

                        try {
                            waitingTimeInstance = new Chart(canvas, {
                                type: 'line',
                                data: JSON.parse(JSON.stringify(this.chartPayload.waiting_time_trend)),
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            labels: { color: textColor, font: { size: 9, weight: 'bold' }, boxWidth: 10 }
                                        }
                                    },
                                    scales: {
                                        x: { ticks: { color: textColor, font: { size: 9 } }, grid: { display: false } },
                                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: { size: 9 } } }
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
