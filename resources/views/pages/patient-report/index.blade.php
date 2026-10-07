<x-content>
    <x-breadcrumb title="Laporan Kunjungan dan Pengunjung" :items="[['title' => 'Laporan Kunjungan dan Pengunjung']]" />

    {{-- Filter Periode (Bulanan & Tahunan) --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Mode Periode: Bulanan / Tahunan --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 shrink-0">
                <button type="button" wire:click="setPeriod('monthly')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $period === 'monthly' ? 'bg-emerald-600 text-white font-bold shadow-sm ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
                    <span class="icon-[solar--calendar-minimalistic-bold] text-base {{ $period === 'monthly' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}"></span>
                    <span>Periode Bulanan</span>
                </button>
                <button type="button" wire:click="setPeriod('yearly')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $period === 'yearly' ? 'bg-emerald-600 text-white font-bold shadow-sm ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
                    <span class="icon-[solar--calendar-bold-duotone] text-base {{ $period === 'yearly' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}"></span>
                    <span>Periode Tahunan</span>
                </button>
            </div>

            {{-- Selector Tanggal / Dropdown --}}
            @if ($period === 'monthly')
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <select wire:model.live="selectedMonth"
                            class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                            @foreach ($months as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--calendar-date-bold-duotone] text-sm"></span>
                        </div>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                        </div>
                    </div>

                    <div class="relative">
                        <select wire:model.live="selectedYear"
                            class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                            @foreach ($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--history-bold-duotone] text-sm"></span>
                        </div>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                        </div>
                    </div>
                </div>
            @else
                <div class="relative">
                    <select wire:model.live="selectedYear"
                        class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                        @foreach ($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--history-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            @endif

            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-emerald-600">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memuat data...</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-button color="default" icon="i-ph-printer" onclick="window.print()">Cetak</x-button>
        </div>
    </div>

    @php $s = $summary; @endphp

    {{-- Ringkasan Cepat KPI --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-4 xl:grid-cols-8">
        @php
            $stats = [
                [
                    'label' => 'Total Pengunjung',
                    'value' => $s['total_pengunjung'],
                    'sub'   => 'Ralan: ' . number_format($s['total_pengunjung_total_ralan'], 0, ',', '.'),
                    'icon'  => 'icon-[solar--users-group-rounded-bold-duotone]',
                    'color' => 'text-primary bg-primary/10'
                ],
                [
                    'label' => 'Total Kunjungan',
                    'value' => $s['total_kunjungan'],
                    'sub'   => 'Ralan: ' . number_format($s['total_rawat_jalan'], 0, ',', '.'),
                    'icon'  => 'icon-[solar--clipboard-list-bold-duotone]',
                    'color' => 'text-cyan-600 bg-cyan-500/10'
                ],
                [
                    'label' => 'Rawat Jalan (Poli)',
                    'value' => $s['rawat_jalan'],
                    'sub'   => 'Pengunjung: ' . number_format($s['total_pengunjung_ralan'], 0, ',', '.'),
                    'icon'  => 'icon-[solar--walking-round-bold-duotone]',
                    'color' => 'text-emerald-600 bg-emerald-500/10'
                ],
                [
                    'label' => 'IGD',
                    'value' => $s['igd'],
                    'sub'   => 'Pengunjung: ' . number_format($s['total_pengunjung_igd'], 0, ',', '.'),
                    'icon'  => 'icon-[ph--ambulance-duotone]',
                    'color' => 'text-amber-600 bg-amber-500/10'
                ],
                [
                    'label' => 'Total Rawat Jalan',
                    'value' => $s['total_rawat_jalan'],
                    'sub'   => 'Poli + IGD (' . number_format($s['total_pengunjung_total_ralan'], 0, ',', '.') . ' Org)',
                    'icon'  => 'icon-[solar--stethoscope-bold-duotone]',
                    'color' => 'text-teal-600 bg-teal-500/10 ring-1 ring-teal-500/20'
                ],
                [
                    'label' => 'Rawat Inap',
                    'value' => $s['rawat_inap'],
                    'sub'   => null,
                    'icon'  => 'icon-[solar--bed-bold-duotone]',
                    'color' => 'text-violet-600 bg-violet-500/10'
                ],
                [
                    'label' => 'Dirujuk',
                    'value' => $s['rujukan'],
                    'sub'   => null,
                    'icon'  => 'icon-[solar--map-arrow-right-bold-duotone]',
                    'color' => 'text-indigo-600 bg-indigo-500/10'
                ],
                [
                    'label' => 'Meninggal',
                    'value' => $s['meninggal'],
                    'sub'   => null,
                    'icon'  => 'icon-[solar--heart-broken-bold-duotone]',
                    'color' => 'text-rose-600 bg-rose-500/10'
                ],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-3.5 flex items-center gap-3 transition hover:shadow-md">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $stat['color'] }}">
                    <span class="{{ $stat['icon'] }} text-lg"></span>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate">{{ $stat['label'] }}</p>
                    <p class="text-lg font-black text-gray-800 dark:text-white leading-tight">{{ number_format($stat['value'], 0, ',', '.') }}</p>
                    @if (!empty($stat['sub']))
                        <p class="text-[9px] text-gray-400 dark:text-gray-500 font-medium truncate mt-0.5" title="{{ $stat['sub'] }}">{{ $stat['sub'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Interactive Charts Section --}}
    <div x-data="patientReportCharts(@js($chartData))" wire:key="patient-report-charts-{{ $period }}-{{ $startDate }}-{{ $endDate }}"
        class="space-y-6 no-print">

        {{-- Grafik Utama: Tren Kunjungan & Pengunjung --}}
        <div class="p-5 sm:p-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
            <div class="flex flex-col gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-primary/10 text-primary dark:text-primary">
                        <span class="icon-[solar--chart-2-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 sm:text-lg dark:text-white">
                            Tren Kunjungan & Pengunjung Pasien
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            @if ($period === 'yearly')
                                Tren bulanan kunjungan pasien tahun {{ $selectedYear }} ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }})
                            @else
                                Tren harian kunjungan pasien bulan {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }} ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }})
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Filter Metrik --}}
                    <div class="inline-flex items-center p-1 bg-gray-100 rounded-xl dark:bg-meta-4/60 border border-stroke/50 dark:border-strokedark/50">
                        <button type="button" @click="setMetricView('all')"
                            :class="metricView === 'all' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer">
                            Semua
                        </button>
                        <button type="button" @click="setMetricView('visits_visitors')"
                            :class="metricView === 'visits_visitors' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer">
                            Kunjungan vs Pengunjung
                        </button>
                        <button type="button" @click="setMetricView('ralan_igd_ranap')"
                            :class="metricView === 'ralan_igd_ranap' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer">
                            Ralan / IGD / Ranap
                        </button>
                    </div>

                    {{-- Switcher Tipe Chart (Line / Bar) --}}
                    <div class="inline-flex items-center p-1 bg-gray-100 rounded-xl dark:bg-meta-4/60 border border-stroke/50 dark:border-strokedark/50">
                        <button type="button" @click="setTrendChartType('line')"
                            :class="trendChartType === 'line' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer">
                            <span class="icon-[solar--graph-up-bold] text-sm text-primary"></span>
                            <span>Garis</span>
                        </button>
                        <button type="button" @click="setTrendChartType('bar')"
                            :class="trendChartType === 'bar' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer">
                            <span class="icon-[solar--chart-bold] text-sm text-cyan-600"></span>
                            <span>Batang</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Insight Badges --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-4">
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-meta-4/40 rounded-xl border border-stroke/60 dark:border-strokedark/60">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center shrink-0">
                        <span class="icon-[solar--stopwatch-play-bold-duotone] text-lg"></span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rata-Rata Kunjungan</p>
                        <p class="text-sm font-extrabold text-gray-800 dark:text-white">
                            {{ number_format($chartData['trend']['stats']['avg_kunjungan_per_day'] ?? 0, 1, ',', '.') }}
                            <span class="text-xs font-normal text-gray-500">/ {{ ($chartData['trend']['mode'] ?? 'daily') === 'daily' ? 'hari' : 'bulan' }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-meta-4/40 rounded-xl border border-stroke/60 dark:border-strokedark/60">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="icon-[solar--star-rainbow-bold-duotone] text-lg"></span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Puncak Kunjungan</p>
                        <p class="text-sm font-extrabold text-gray-800 dark:text-white">
                            {{ number_format($chartData['trend']['stats']['peak_kunjungan'] ?? 0, 0, ',', '.') }}
                            <span class="text-xs font-normal text-gray-500">({{ $chartData['trend']['stats']['peak_date'] ?? '-' }})</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-meta-4/40 rounded-xl border border-stroke/60 dark:border-strokedark/60">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0">
                        <span class="icon-[solar--pie-chart-2-bold-duotone] text-lg"></span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rasio Kunjungan / Pengunjung</p>
                        <p class="text-sm font-extrabold text-gray-800 dark:text-white">
                            {{ number_format($chartData['trend']['stats']['rasio_kunjungan_per_pengunjung'] ?? 0, 2, ',', '.') }}x
                            <span class="text-xs font-normal text-gray-500">frekuensi kedatangan</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Canvas Container --}}
            <div class="relative w-full h-80 sm:h-96 mt-2" wire:ignore>
                <div x-ref="trendChartContainer" class="w-full h-full"></div>
            </div>
        </div>

        {{-- Grid 2 Kolom: Proporsi Pasien & Distribusi Angkatan TNI --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Chart 2: Komposisi Pasien (Doughnut) --}}
            <div class="lg:col-span-5 p-5 sm:p-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600">
                                <span class="icon-[solar--pie-chart-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">
                                    Proporsi Kelompok Pasien
                                </h4>
                                <p class="text-xs text-gray-400">TNI vs POLRI vs Pasien Umum</p>
                            </div>
                        </div>

                        {{-- Switch Kunjungan / Pengunjung --}}
                        <div class="inline-flex items-center p-0.5 bg-gray-100 rounded-lg dark:bg-meta-4/60 border border-stroke/50 dark:border-strokedark/50">
                            <button type="button" @click="setCompositionMode('kunjungan')"
                                :class="compositionMode === 'kunjungan' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 font-medium'"
                                class="px-2.5 py-1 rounded-md text-[11px] transition-all cursor-pointer">
                                Kunjungan
                            </button>
                            <button type="button" @click="setCompositionMode('pengunjung')"
                                :class="compositionMode === 'pengunjung' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 font-medium'"
                                class="px-2.5 py-1 rounded-md text-[11px] transition-all cursor-pointer">
                                Pengunjung
                            </button>
                        </div>
                    </div>

                    {{-- Canvas Doughnut --}}
                    <div class="relative w-full h-64 mt-4" wire:ignore>
                        <div x-ref="compositionChartContainer" class="w-full h-full"></div>
                    </div>
                </div>

                {{-- Detail Breakdown List --}}
                <div class="pt-4 border-t border-stroke/70 dark:border-strokedark/70 mt-4 space-y-2">
                    <template x-for="item in getCompositionStats()" :key="item.label">
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: item.color }"></span>
                                <span class="font-medium text-gray-700 dark:text-gray-300" x-text="item.label"></span>
                            </div>
                            <div class="flex items-center gap-2 font-bold">
                                <span class="text-gray-800 dark:text-white" x-text="item.value.toLocaleString('id-ID')"></span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300" x-text="item.percentage + '%'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Chart 3: Distribusi Pasien TNI (Stacked Bar) --}}
            <div class="lg:col-span-7 p-5 sm:p-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600">
                                <span class="icon-[solar--shield-user-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">
                                    Distribusi Pasien TNI
                                </h4>
                                <p class="text-xs text-gray-400">TNI AD, AL, AU dirinci per kelompok personel</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg">
                                Total TNI: {{ number_format(($s['tni']['total']['rawat_jalan'] ?? 0) + ($s['tni']['total']['igd'] ?? 0) + ($s['tni']['total']['rawat_inap'] ?? 0), 0, ',', '.') }} Kunjungan
                            </span>
                        </div>
                    </div>

                    {{-- Canvas TNI Breakdown --}}
                    <div class="relative w-full h-64 sm:h-72 mt-4" wire:ignore>
                        <div x-ref="tniChartContainer" class="w-full h-full"></div>
                    </div>
                </div>

                {{-- Legend Summary Note --}}
                <div class="pt-4 border-t border-stroke/70 dark:border-strokedark/70 mt-4 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <span class="icon-[solar--info-circle-bold-duotone] text-primary"></span>
                        Data mencakup rawat jalan, IGD & rawat inap seluruh matra TNI.
                    </span>
                    <span class="font-bold text-gray-700 dark:text-gray-300">
                        AD: {{ number_format(($s['tni']['angkatan']['ad']['total']['rawat_jalan'] ?? 0) + ($s['tni']['angkatan']['ad']['total']['igd'] ?? 0) + ($s['tni']['angkatan']['ad']['total']['rawat_inap'] ?? 0), 0, ',', '.') }} |
                        AL: {{ number_format(($s['tni']['angkatan']['al']['total']['rawat_jalan'] ?? 0) + ($s['tni']['angkatan']['al']['total']['igd'] ?? 0) + ($s['tni']['angkatan']['al']['total']['rawat_inap'] ?? 0), 0, ',', '.') }} |
                        AU: {{ number_format(($s['tni']['angkatan']['au']['total']['rawat_jalan'] ?? 0) + ($s['tni']['angkatan']['au']['total']['igd'] ?? 0) + ($s['tni']['angkatan']['au']['total']['rawat_inap'] ?? 0), 0, ',', '.') }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    {{-- Tabel Rekapitulasi Kunjungan dan Pengunjung Per Bulan (Khusus Periode Tahunan) --}}
    @if ($period === 'yearly' && !empty($monthlyBreakdown))
        <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden mb-6">
            <div class="px-6 pt-6 pb-4 border-b border-stroke dark:border-strokedark flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        <span class="icon-[solar--calendar-date-bold-duotone]"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">
                            Rekapitulasi Kunjungan dan Pengunjung Per Bulan
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Rincian data kunjungan (rawat jalan & rawat inap) dan pengunjung per bulan untuk Tahun {{ $selectedYear }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--clipboard-list-bold-duotone] text-sm"></span>
                        <span>Total: {{ number_format($monthlyBreakdown['totals']['kunjungan'], 0, ',', '.') }} Kunjungan</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">
                        <span class="icon-[solar--users-group-rounded-bold-duotone] text-sm"></span>
                        <span>Rata-rata: {{ number_format($monthlyBreakdown['totals']['avg_kunjungan_per_bulan'], 1, ',', '.') }} / Bulan</span>
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-meta-4 text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                            <th rowspan="2" class="px-5 py-3 text-left w-36 align-bottom">Bulan</th>
                            <th colspan="4" class="px-4 py-2 text-center border-l border-stroke dark:border-strokedark">Pengunjung</th>
                            <th colspan="5" class="px-4 py-2 text-center border-l border-stroke dark:border-strokedark">Kunjungan</th>
                            <th colspan="3" class="px-4 py-2 text-center border-l border-stroke dark:border-strokedark">Kelompok Pasien</th>
                            <th rowspan="2" class="px-4 py-3 text-right align-bottom border-l border-stroke dark:border-strokedark">Rujukan</th>
                            <th rowspan="2" class="px-4 py-3 text-right align-bottom">Meninggal</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-meta-4 text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                            <th class="px-3 py-2 text-right border-l border-stroke dark:border-strokedark">Poli</th>
                            <th class="px-3 py-2 text-right">IGD</th>
                            <th class="px-3 py-2 text-right bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-black">Total Ralan</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-3 py-2 text-right border-l border-stroke dark:border-strokedark">Poli</th>
                            <th class="px-3 py-2 text-right">IGD</th>
                            <th class="px-3 py-2 text-right bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-black">Total Ralan</th>
                            <th class="px-3 py-2 text-right">Rawat Inap</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-4 py-2 text-right border-l border-stroke dark:border-strokedark">TNI</th>
                            <th class="px-4 py-2 text-right">POLRI</th>
                            <th class="px-4 py-2 text-right">Umum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @foreach ($monthlyBreakdown['months'] as $m => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors">
                                <td class="px-5 py-3 font-bold text-gray-800 dark:text-white">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-meta-4/80 text-[11px] font-black text-gray-500 dark:text-gray-400 flex items-center justify-center font-mono">
                                             {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span>{{ $item['nama_bulan'] }}</span>
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right text-emerald-600 dark:text-emerald-400 font-mono border-l border-stroke dark:border-strokedark">
                                    {{ number_format($item['pengunjung_ralan'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-amber-600 dark:text-amber-400 font-mono">
                                    {{ number_format($item['pengunjung_igd'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">
                                    {{ number_format($item['pengunjung_total_ralan'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">
                                    {{ number_format($item['pengunjung'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-emerald-600 dark:text-emerald-400 font-mono border-l border-stroke dark:border-strokedark">
                                    {{ number_format($item['rawat_jalan'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-amber-600 dark:text-amber-400 font-mono">
                                    {{ number_format($item['igd'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">
                                    {{ number_format($item['total_rawat_jalan'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-violet-600 dark:text-violet-400 font-mono">
                                    {{ number_format($item['rawat_inap'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">
                                    {{ number_format($item['kunjungan'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-mono border-l border-stroke dark:border-strokedark">
                                    {{ number_format($item['tni'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-cyan-600 dark:text-cyan-400 font-mono">
                                    {{ number_format($item['polri'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-indigo-600 dark:text-indigo-400 font-mono">
                                    {{ number_format($item['umum'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-amber-600 dark:text-amber-400 font-mono border-l border-stroke dark:border-strokedark">
                                    {{ number_format($item['rujukan'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-rose-600 dark:text-rose-400 font-mono">
                                    {{ number_format($item['meninggal'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary/10 dark:bg-primary/20 font-black text-sm border-t-2 border-primary/30">
                            <td class="px-5 py-3.5 text-primary uppercase tracking-wider text-xs">
                                TOTAL TAHUN {{ $selectedYear }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-emerald-600 font-mono border-l border-primary/30">
                                {{ number_format($monthlyBreakdown['totals']['pengunjung_ralan'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-amber-600 font-mono">
                                {{ number_format($monthlyBreakdown['totals']['pengunjung_igd'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">
                                {{ number_format($monthlyBreakdown['totals']['pengunjung_total_ralan'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-primary font-mono text-base">
                                {{ number_format($s['total_pengunjung'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-emerald-600 font-mono border-l border-primary/30">
                                {{ number_format($monthlyBreakdown['totals']['rawat_jalan'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-amber-600 font-mono">
                                {{ number_format($monthlyBreakdown['totals']['igd'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">
                                {{ number_format($monthlyBreakdown['totals']['total_rawat_jalan'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-violet-600 font-mono">
                                {{ number_format($monthlyBreakdown['totals']['rawat_inap'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3.5 text-right text-primary font-mono text-base">
                                {{ number_format($monthlyBreakdown['totals']['kunjungan'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-emerald-600 font-mono border-l border-primary/30">
                                {{ number_format($monthlyBreakdown['totals']['tni'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-cyan-600 font-mono">
                                {{ number_format($monthlyBreakdown['totals']['polri'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-indigo-600 font-mono">
                                {{ number_format($monthlyBreakdown['totals']['umum'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-amber-600 font-mono border-l border-primary/30">
                                {{ number_format($monthlyBreakdown['totals']['rujukan'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-rose-600 font-mono">
                                {{ number_format($monthlyBreakdown['totals']['meninggal'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- Tabel Laporan Formal --}}
    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        {{-- Judul Formal --}}
        <div class="px-6 pt-6 pb-4 border-b border-stroke dark:border-strokedark text-center">
            <p class="text-base font-black text-gray-800 dark:text-white uppercase tracking-widest">
                Laporan Kunjungan dan Pengunjung
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Pendataan Pasien Berobat Berdasarkan Kelompok Pasien & Status Rawat
            </p>
            <p class="text-xs font-semibold text-primary dark:text-primary-400 mt-1">
                @if ($period === 'yearly')
                    Periode: Tahun {{ $selectedYear }} ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }})
                @else
                    Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }} ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }})
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4 text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                        <th rowspan="2" class="px-5 py-3 text-left w-1/4 align-bottom">Kelompok Pasien</th>
                        <th colspan="4" class="px-4 py-2 text-center border-l border-stroke dark:border-strokedark">Pengunjung</th>
                        <th colspan="5" class="px-4 py-2 text-center border-l border-stroke dark:border-strokedark">Kunjungan</th>
                        <th rowspan="2" class="px-4 py-3 text-right align-bottom border-l border-stroke dark:border-strokedark">Rujukan</th>
                        <th rowspan="2" class="px-4 py-3 text-right align-bottom">Meninggal</th>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-meta-4 text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                        <th class="px-3 py-2 text-right border-l border-stroke dark:border-strokedark">Poli</th>
                        <th class="px-3 py-2 text-right">IGD</th>
                        <th class="px-3 py-2 text-right bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-black">Total Ralan</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-right border-l border-stroke dark:border-strokedark">Poli</th>
                        <th class="px-3 py-2 text-right">IGD</th>
                        <th class="px-3 py-2 text-right bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-black">Total Ralan</th>
                        <th class="px-3 py-2 text-right">Rawat Inap</th>
                        <th class="px-3 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">

                    {{-- ===== TNI ===== --}}
                    <tr class="bg-primary/10 dark:bg-primary/20">
                        <td colspan="12" class="px-5 py-2 text-xs font-black text-primary uppercase tracking-widest">
                            TNI
                        </td>
                    </tr>
                    @foreach ($s['tni']['angkatan'] as $angkatan)
                        <tr class="bg-primary/5 dark:bg-primary/10">
                            <td colspan="12" class="px-5 py-2 pl-8 text-xs font-black text-primary/80 uppercase tracking-widest">
                                {{ $loop->iteration }}. {{ $angkatan['nama'] }}
                            </td>
                        </tr>
                        @foreach ($angkatan['rincian'] as $rincian)
                            <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                                <td class="px-5 py-3 pl-14 text-gray-600 dark:text-gray-400 text-sm">
                                    {{ chr(97 + $loop->index) }}. {{ $rincian['label'] }}
                                </td>
                                <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($rincian['pengunjung_ralan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($rincian['pengunjung_igd'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">{{ number_format($rincian['pengunjung_total_ralan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">{{ number_format($rincian['pengunjung'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($rincian['rawat_jalan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($rincian['igd'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">{{ number_format($rincian['total_rawat_jalan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right text-violet-600 font-mono">{{ number_format($rincian['rawat_inap'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">{{ number_format($rincian['rawat_jalan'] + $rincian['igd'] + $rincian['rawat_inap'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-amber-600 border-l border-stroke dark:border-strokedark">{{ number_format($rincian['rujukan'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-rose-600">{{ number_format($rincian['meninggal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50 dark:bg-meta-4 font-black">
                            <td class="px-5 py-3 pl-10 text-gray-700 dark:text-white text-sm">Jumlah {{ $angkatan['nama'] }}</td>
                            <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($angkatan['total']['pengunjung_ralan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($angkatan['total']['pengunjung_igd'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">{{ number_format($angkatan['total']['pengunjung_total_ralan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-gray-800 dark:text-white font-mono bg-gray-100/50 dark:bg-meta-4/40">{{ number_format($angkatan['total']['pengunjung'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($angkatan['total']['rawat_jalan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($angkatan['total']['igd'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">{{ number_format($angkatan['total']['total_rawat_jalan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-violet-600 font-mono">{{ number_format($angkatan['total']['rawat_inap'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-gray-800 dark:text-white font-mono bg-gray-100/50 dark:bg-meta-4/40">{{ number_format($angkatan['total']['rawat_jalan'] + $angkatan['total']['igd'] + $angkatan['total']['rawat_inap'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-amber-600 border-l border-stroke dark:border-strokedark">{{ number_format($angkatan['total']['rujukan'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">{{ number_format($angkatan['total']['meninggal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-primary/10 dark:bg-primary/20 font-black">
                        <td class="px-5 py-3 pl-6 text-primary text-sm">Jumlah TNI</td>
                        <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-primary/30">{{ number_format($s['tni']['total']['pengunjung_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($s['tni']['total']['pengunjung_igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">{{ number_format($s['tni']['total']['pengunjung_total_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-primary font-mono">{{ number_format($s['tni']['total']['pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-primary/30">{{ number_format($s['tni']['total']['rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($s['tni']['total']['igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">{{ number_format($s['tni']['total']['total_rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-violet-600 font-mono">{{ number_format($s['tni']['total']['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-primary font-mono">{{ number_format($s['tni']['total']['rawat_jalan'] + $s['tni']['total']['igd'] + $s['tni']['total']['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-amber-600 border-l border-stroke dark:border-strokedark">{{ number_format($s['tni']['total']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-rose-600">{{ number_format($s['tni']['total']['meninggal'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== POLRI ===== --}}
                    <tr class="bg-cyan-500/10 dark:bg-cyan-500/20">
                        <td colspan="12" class="px-5 py-2 text-xs font-black text-cyan-600 uppercase tracking-widest">
                            POLRI
                        </td>
                    </tr>
                    @foreach ($s['polri']['rincian'] as $rincian)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-5 py-3 pl-10 text-gray-600 dark:text-gray-400 text-sm">
                                {{ $loop->iteration }}. {{ $rincian['label'] }}
                            </td>
                            <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($rincian['pengunjung_ralan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($rincian['pengunjung_igd'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">{{ number_format($rincian['pengunjung_total_ralan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">{{ number_format($rincian['pengunjung'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($rincian['rawat_jalan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($rincian['igd'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">{{ number_format($rincian['total_rawat_jalan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-violet-600 font-mono">{{ number_format($rincian['rawat_inap'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">{{ number_format($rincian['rawat_jalan'] + $rincian['igd'] + $rincian['rawat_inap'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-amber-600 border-l border-stroke dark:border-strokedark">{{ number_format($rincian['rujukan'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">{{ number_format($rincian['meninggal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-meta-4 font-black">
                        <td class="px-5 py-3 pl-10 text-gray-700 dark:text-white text-sm">Jumlah POLRI</td>
                        <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($s['polri']['total']['pengunjung_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($s['polri']['total']['pengunjung_igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">{{ number_format($s['polri']['total']['pengunjung_total_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-gray-800 dark:text-white font-mono bg-gray-100/50 dark:bg-meta-4/40">{{ number_format($s['polri']['total']['pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($s['polri']['total']['rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($s['polri']['total']['igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/10">{{ number_format($s['polri']['total']['total_rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-violet-600 font-mono">{{ number_format($s['polri']['total']['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-gray-800 dark:text-white font-mono bg-gray-100/50 dark:bg-meta-4/40">{{ number_format($s['polri']['total']['rawat_jalan'] + $s['polri']['total']['igd'] + $s['polri']['total']['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-amber-600 border-l border-stroke dark:border-strokedark">{{ number_format($s['polri']['total']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-rose-600">{{ number_format($s['polri']['total']['meninggal'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== PASIEN UMUM ===== --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="px-5 py-3 font-bold text-gray-700 dark:text-white">Pasien Umum</td>
                        <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($s['umum']['pengunjung_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($s['umum']['pengunjung_igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">{{ number_format($s['umum']['pengunjung_total_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">{{ number_format($s['umum']['pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-emerald-600 font-mono border-l border-stroke dark:border-strokedark">{{ number_format($s['umum']['rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-amber-600 font-mono">{{ number_format($s['umum']['igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-500/5 dark:bg-emerald-500/10">{{ number_format($s['umum']['total_rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-violet-600 font-mono">{{ number_format($s['umum']['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white font-mono bg-gray-50/50 dark:bg-meta-4/20">{{ number_format($s['umum']['rawat_jalan'] + $s['umum']['igd'] + $s['umum']['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-amber-600 border-l border-stroke dark:border-strokedark">{{ number_format($s['umum']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-rose-600">{{ number_format($s['umum']['meninggal'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== TOTAL ===== --}}
                    <tr class="bg-primary/10 dark:bg-primary/20 font-black text-base border-t-2 border-primary/30">
                        <td class="px-5 py-4 text-primary uppercase tracking-widest text-xs">Total Keseluruhan</td>
                        <td class="px-3 py-4 text-right text-emerald-600 text-base font-mono border-l border-primary/30">{{ number_format($s['total_pengunjung_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-amber-600 text-base font-mono">{{ number_format($s['total_pengunjung_igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-emerald-700 dark:text-emerald-400 text-base font-mono bg-emerald-500/10">{{ number_format($s['total_pengunjung_total_ralan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-primary text-base font-mono">{{ number_format($s['total_pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-emerald-600 text-base font-mono border-l border-primary/30">{{ number_format($s['rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-amber-600 text-base font-mono">{{ number_format($s['igd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-emerald-700 dark:text-emerald-400 text-base font-mono bg-emerald-500/10">{{ number_format($s['total_rawat_jalan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-violet-600 text-base font-mono">{{ number_format($s['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-right text-primary text-base font-mono">{{ number_format($s['rawat_jalan'] + $s['igd'] + $s['rawat_inap'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-amber-600 text-base border-l border-primary/30">{{ number_format($s['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-rose-600 text-base">{{ number_format($s['meninggal'], 0, ',', '.') }}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media print {
            nav, aside, header, footer, .no-print { display: none !important; }
            body { background: white !important; }
            .bg-boxdark { background: white !important; color: black !important; }
        }
    </style>

    @script
        <script>
            Alpine.data('patientReportCharts', (initialData) => {
                let trendChartInstance = null;
                let compChartInstance = null;
                let tniChartInstance = null;
                let themeObserver = null;

                return {
                    chartData: initialData,
                    trendChartType: 'line',
                    metricView: 'all', // 'all', 'visits_visitors', 'ralan_igd_ranap'
                    compositionMode: 'kunjungan', // 'kunjungan' or 'pengunjung'

                    init() {
                        const self = this;
                        this.$nextTick(() => {
                            self.renderAllCharts();
                        });

                        // Observer dark mode
                        themeObserver = new MutationObserver(() => {
                            self.renderAllCharts();
                        });
                        themeObserver.observe(document.documentElement, {
                            attributes: true,
                            attributeFilter: ['class']
                        });

                        this.$cleanup(() => {
                            if (trendChartInstance) {
                                try { trendChartInstance.destroy(); } catch (e) {}
                                trendChartInstance = null;
                            }
                            if (compChartInstance) {
                                try { compChartInstance.destroy(); } catch (e) {}
                                compChartInstance = null;
                            }
                            if (tniChartInstance) {
                                try { tniChartInstance.destroy(); } catch (e) {}
                                tniChartInstance = null;
                            }
                            if (themeObserver) {
                                themeObserver.disconnect();
                                themeObserver = null;
                            }
                        });
                    },

                    isDark() {
                        return document.documentElement.classList.contains('dark');
                    },

                    setTrendChartType(type) {
                        this.trendChartType = type;
                        this.renderTrendChart();
                    },

                    setMetricView(view) {
                        this.metricView = view;
                        this.renderTrendChart();
                    },

                    setCompositionMode(mode) {
                        this.compositionMode = mode;
                        this.renderCompositionChart();
                    },

                    getCompositionStats() {
                        const comp = this.chartData.composition[this.compositionMode];
                        const total = comp.total > 0 ? comp.total : 1;
                        return [
                            {
                                label: 'TNI',
                                value: comp.tni,
                                percentage: comp.total > 0 ? ((comp.tni / total) * 100).toFixed(1) : '0.0',
                                color: '#10b981'
                            },
                            {
                                label: 'POLRI',
                                value: comp.polri,
                                percentage: comp.total > 0 ? ((comp.polri / total) * 100).toFixed(1) : '0.0',
                                color: '#06b6d4'
                            },
                            {
                                label: 'Pasien Umum',
                                value: comp.umum,
                                percentage: comp.total > 0 ? ((comp.umum / total) * 100).toFixed(1) : '0.0',
                                color: '#6366f1'
                            },
                        ];
                    },

                    renderAllCharts() {
                        this.renderTrendChart();
                        this.renderCompositionChart();
                        this.renderTniChart();
                    },

                    // ==========================================
                    // 1. CHART TREN KUNJUNGAN & PENGUNJUNG
                    // ==========================================
                    renderTrendChart() {
                        const container = this.$refs.trendChartContainer;
                        if (!container) return;

                        if (trendChartInstance) {
                            try { trendChartInstance.destroy(); } catch (e) {}
                            trendChartInstance = null;
                        }

                        container.innerHTML = '';
                        const canvas = document.createElement('canvas');
                        canvas.className = 'w-full h-full';
                        container.appendChild(canvas);

                        const isDark = this.isDark();
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                        const textColor = isDark ? '#94a3b8' : '#64748b';
                        const trend = this.chartData.trend;

                        const datasets = [];

                        if (this.metricView === 'all' || this.metricView === 'visits_visitors') {
                            datasets.push({
                                label: 'Total Kunjungan',
                                data: [...(trend.kunjungan || [])],
                                borderColor: '#0284c7',
                                backgroundColor: this.trendChartType === 'line' ? 'rgba(2, 132, 199, 0.12)' : 'rgba(2, 132, 199, 0.85)',
                                fill: this.trendChartType === 'line',
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: trend.labels.length > 31 ? 1 : 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#0284c7',
                            });

                            datasets.push({
                                label: 'Total Pengunjung',
                                data: [...(trend.pengunjung || [])],
                                borderColor: '#10b981',
                                backgroundColor: this.trendChartType === 'line' ? 'rgba(16, 185, 129, 0.08)' : 'rgba(16, 185, 129, 0.85)',
                                fill: this.trendChartType === 'line',
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: trend.labels.length > 31 ? 1 : 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#10b981',
                            });
                        }

                        if (this.metricView === 'all' || this.metricView === 'ralan_igd_ranap') {
                            datasets.push({
                                label: 'Total Ralan (Poli+IGD)',
                                data: [...(trend.totalRawatJalan || [])],
                                borderColor: '#059669',
                                backgroundColor: this.trendChartType === 'line' ? 'rgba(5, 150, 105, 0.08)' : 'rgba(5, 150, 105, 0.85)',
                                fill: false,
                                borderDash: this.metricView === 'all' ? [4, 4] : [],
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: trend.labels.length > 31 ? 1 : 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#059669',
                            });

                            datasets.push({
                                label: 'Rawat Jalan (Poli)',
                                data: [...(trend.rawatJalan || [])],
                                borderColor: '#0d9488',
                                backgroundColor: this.trendChartType === 'line' ? 'rgba(13, 148, 136, 0.08)' : 'rgba(13, 148, 136, 0.85)',
                                fill: false,
                                borderDash: this.metricView === 'all' ? [4, 4] : [],
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: trend.labels.length > 31 ? 1 : 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#0d9488',
                            });

                            datasets.push({
                                label: 'IGD',
                                data: [...(trend.igd || [])],
                                borderColor: '#d97706',
                                backgroundColor: this.trendChartType === 'line' ? 'rgba(217, 119, 6, 0.08)' : 'rgba(217, 119, 6, 0.85)',
                                fill: false,
                                borderDash: this.metricView === 'all' ? [4, 4] : [],
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: trend.labels.length > 31 ? 1 : 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#d97706',
                            });

                            datasets.push({
                                label: 'Rawat Inap',
                                data: [...(trend.rawatInap || [])],
                                borderColor: '#9333ea',
                                backgroundColor: this.trendChartType === 'line' ? 'rgba(147, 51, 234, 0.08)' : 'rgba(147, 51, 234, 0.85)',
                                fill: false,
                                borderDash: this.metricView === 'all' ? [4, 4] : [],
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: trend.labels.length > 31 ? 1 : 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#9333ea',
                            });
                        }

                        try {
                            trendChartInstance = new Chart(canvas, {
                                type: this.trendChartType,
                                data: {
                                    labels: [...trend.labels],
                                    datasets: datasets
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 400, easing: 'easeOutQuart' },
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 12,
                                                font: { size: 11, weight: '600' }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                            titleColor: isDark ? '#f1f5f9' : '#0f172a',
                                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 12,
                                            boxPadding: 6,
                                            usePointStyle: true,
                                            callbacks: {
                                                label: function(context) {
                                                    return ' ' + context.dataset.label + ': ' + Number(context.raw).toLocaleString('id-ID') + ' pasien';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: {
                                                color: textColor,
                                                font: { size: 11, weight: '600' },
                                                maxRotation: 45,
                                                autoSkip: true,
                                                maxTicksLimit: 15
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: {
                                                color: textColor,
                                                font: { size: 11 },
                                                callback: function(value) {
                                                    return Number(value).toLocaleString('id-ID');
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Error rendering Trend Chart:', err);
                        }
                    },

                    // ==========================================
                    // 2. CHART KOMPOSISI PASIEN (DOUGHNUT)
                    // ==========================================
                    renderCompositionChart() {
                        const container = this.$refs.compositionChartContainer;
                        if (!container) return;

                        if (compChartInstance) {
                            try { compChartInstance.destroy(); } catch (e) {}
                            compChartInstance = null;
                        }

                        container.innerHTML = '';
                        const canvas = document.createElement('canvas');
                        canvas.className = 'w-full h-full';
                        container.appendChild(canvas);

                        const isDark = this.isDark();
                        const textColor = isDark ? '#94a3b8' : '#64748b';
                        const comp = this.chartData.composition[this.compositionMode];

                        try {
                            compChartInstance = new Chart(canvas, {
                                type: 'doughnut',
                                data: {
                                    labels: ['TNI', 'POLRI', 'Pasien Umum'],
                                    datasets: [{
                                        data: [comp.tni, comp.polri, comp.umum],
                                        backgroundColor: [
                                            '#10b981', // TNI
                                            '#06b6d4', // POLRI
                                            '#6366f1'  // Umum
                                        ],
                                        borderWidth: 2,
                                        borderColor: isDark ? '#1c2434' : '#ffffff',
                                        hoverOffset: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '65%',
                                    animation: { duration: 400, easing: 'easeOutQuart' },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'bottom',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 12,
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
                                            callbacks: {
                                                label: function(context) {
                                                    const total = comp.total > 0 ? comp.total : 1;
                                                    const val = context.raw || 0;
                                                    const pct = comp.total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                                    return ' ' + context.label + ': ' + Number(val).toLocaleString('id-ID') + ' (' + pct + '%)';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Error rendering Composition Chart:', err);
                        }
                    },

                    // ==========================================
                    // 3. CHART DISTRIBUSI ANGKATAN TNI (STACKED BAR)
                    // ==========================================
                    renderTniChart() {
                        const container = this.$refs.tniChartContainer;
                        if (!container) return;

                        if (tniChartInstance) {
                            try { tniChartInstance.destroy(); } catch (e) {}
                            tniChartInstance = null;
                        }

                        container.innerHTML = '';
                        const canvas = document.createElement('canvas');
                        canvas.className = 'w-full h-full';
                        container.appendChild(canvas);

                        const isDark = this.isDark();
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                        const textColor = isDark ? '#94a3b8' : '#64748b';
                        const tni = this.chartData.tniBreakdown;

                        try {
                            tniChartInstance = new Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: [...tni.labels],
                                    datasets: [
                                        {
                                            label: 'Militer',
                                            data: [...tni.militer],
                                            backgroundColor: '#059669',
                                            borderRadius: 4,
                                            stack: 'TNI'
                                        },
                                        {
                                            label: 'PNS / ASN',
                                            data: [...tni.asn],
                                            backgroundColor: '#0d9488',
                                            borderRadius: 4,
                                            stack: 'TNI'
                                        },
                                        {
                                            label: 'Keluarga',
                                            data: [...tni.keluarga],
                                            backgroundColor: '#f59e0b',
                                            borderRadius: 4,
                                            stack: 'TNI'
                                        },
                                        {
                                            label: 'Purnawirawan',
                                            data: [...tni.purnawirawan],
                                            backgroundColor: '#8b5cf6',
                                            borderRadius: 4,
                                            stack: 'TNI'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 400, easing: 'easeOutQuart' },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 10,
                                                font: { size: 10, weight: '600' }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                            titleColor: isDark ? '#f1f5f9' : '#0f172a',
                                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                            callbacks: {
                                                label: function(context) {
                                                    return ' ' + context.dataset.label + ': ' + Number(context.raw).toLocaleString('id-ID') + ' kunjungan';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            stacked: true,
                                            grid: { display: false },
                                            ticks: {
                                                color: textColor,
                                                font: { size: 12, weight: 'bold' }
                                            }
                                        },
                                        y: {
                                            stacked: true,
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: {
                                                color: textColor,
                                                font: { size: 11 },
                                                callback: function(value) {
                                                    return Number(value).toLocaleString('id-ID');
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Error rendering TNI Chart:', err);
                        }
                    }
                };
            });
        </script>
    @endscript
</x-content>
