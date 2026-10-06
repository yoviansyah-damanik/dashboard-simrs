<x-content>
    <x-breadcrumb title="Jadwal Operasi" :items="[['title' => 'Jadwal Operasi'], ['title' => 'Rekap']]" />

    @php
        $totalOperasi = $recapData->sum('jumlah_operasi');
        $totalSelesai = $recapData->sum('jumlah_selesai');
        $totalProses = $recapData->sum('jumlah_proses');
        $totalMenunggu = $recapData->sum('jumlah_menunggu');
    @endphp

    <div class="space-y-5">
        <!-- Control Toolbar: Switcher, Pencarian, & Filter Periode -->
        <div class="p-3 sm:p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                <!-- Sisi Kiri: Switcher Tampilan & Pencarian Paket -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Pill Switcher (List / Grafik) -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl shrink-0">
                        <button wire:click="$set('mainView', 'list')"
                            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainView === 'list' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                            <span class="icon-[solar--list-bold-duotone] text-base"></span>
                            <span>Tabel</span>
                        </button>
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-base"></span>
                            <span>Grafik</span>
                        </button>
                    </div>

                    <!-- Input Pencarian -->
                    <div class="relative flex-1 sm:w-72 min-w-[200px]">
                        <input type="text" wire:model.live.debounce.300ms="searchPackage"
                            placeholder="Cari nama paket operasi..."
                            class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <span class="icon-[solar--magnifer-bold-duotone] text-base"></span>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Pilihan Periode Waktu -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 cursor-pointer outline-none transition-all">
                            <option value="today">Hari Ini</option>
                            <option value="last_7_days">7 Hari Terakhir</option>
                            <option value="last_30_days">30 Hari Terakhir</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="this_year">Tahun Ini</option>
                            <option value="monthly">Pilih Bulan</option>
                            <option value="yearly">Pilih Tahun</option>
                            <option value="custom">Rentang Kustom</option>
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--calendar-minimalistic-bold] text-base"></span>
                        </div>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <span class="icon-[solar--alt-arrow-down-bold] text-xs"></span>
                        </div>
                    </div>

                    @if ($period === 'monthly')
                        <div class="flex items-center gap-2">
                            <select wire:model.live="selectedMonth"
                                class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 outline-none">
                                @foreach ($months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 outline-none">
                                @foreach ($years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 outline-none">
                            @foreach ($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    @elseif($period === 'custom')
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs font-medium">
                            <input type="date" wire:model.live="startDate"
                                class="bg-transparent border-none text-xs font-semibold text-slate-700 dark:text-slate-200 focus:ring-0 p-0 cursor-pointer" />
                            <span class="text-slate-400 font-bold px-1">&rarr;</span>
                            <input type="date" wire:model.live="endDate"
                                class="bg-transparent border-none text-xs font-semibold text-slate-700 dark:text-slate-200 focus:ring-0 p-0 cursor-pointer" />
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards (4 Cards Grid) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            <!-- 1. Total Operasi -->
            <div class="p-4 sm:p-5 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Operasi</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 tracking-tight">
                            {{ number_format($totalOperasi) }}
                        </h4>
                    </div>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--stethoscope-bold-duotone] text-2xl"></span>
                    </div>
                </div>
                <div class="mt-2.5 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                    <span>Semua paket tindakan</span>
                </div>
            </div>

            <!-- 2. Selesai -->
            <div class="p-4 sm:p-5 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tindakan Selesai</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight">
                            {{ number_format($totalSelesai) }}
                        </h4>
                    </div>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--check-circle-bold-duotone] text-2xl"></span>
                    </div>
                </div>
                <div class="mt-2.5 text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                    <span>{{ $totalOperasi > 0 ? number_format(($totalSelesai / $totalOperasi) * 100, 1) : 0 }}% dari total</span>
                </div>
            </div>

            <!-- 3. Proses Operasi -->
            <div class="p-4 sm:p-5 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dalam Proses</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 mt-1 tracking-tight">
                            {{ number_format($totalProses) }}
                        </h4>
                    </div>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--play-circle-bold-duotone] text-2xl"></span>
                    </div>
                </div>
                <div class="mt-2.5 text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                    <span>Sedang berlangsung di OK</span>
                </div>
            </div>

            <!-- 4. Menunggu -->
            <div class="p-4 sm:p-5 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Menunggu</p>
                        <h4 class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-1 tracking-tight">
                            {{ number_format($totalMenunggu) }}
                        </h4>
                    </div>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--clock-circle-bold-duotone] text-2xl"></span>
                    </div>
                </div>
                <div class="mt-2.5 text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                    <span>Antrean terjadwal</span>
                </div>
            </div>
        </div>

        @if ($mainView === 'list')
            <!-- Tampilan Tabel Data Rekap Operasi -->
            <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white">Daftar Paket Tindakan Operasi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rincian kuantitas, status tindakan, dan proporsi per paket</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ $recapData->count() }} Paket
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[850px]">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Paket Operasi</th>
                                <th class="px-3 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Kategori</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200 text-center bg-emerald-500/5 dark:bg-emerald-500/10">Jumlah</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 text-center">Selesai</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 text-center">Proses</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 text-center">Menunggu</th>
                                <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Proporsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($recapData as $item)
                                @php $percent = $totalOperasi > 0 ? ($item->jumlah_operasi / $totalOperasi) * 100 : 0; @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="px-5 py-3.5">
                                        <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                            {{ $item->nama_paket }}
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            Kode: {{ $item->kode_paket }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $item->kategori ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-black text-xs sm:text-sm text-slate-900 dark:text-white bg-emerald-500/5 dark:bg-emerald-500/10 tabular-nums">
                                        {{ number_format($item->jumlah_operasi) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                                        {{ number_format($item->jumlah_selesai) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-bold text-sky-600 dark:text-sky-400 tabular-nums">
                                        {{ number_format($item->jumlah_proses) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                        {{ number_format($item->jumlah_menunggu) }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-2.5">
                                            <div class="w-24 sm:w-28 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 w-11 text-right tabular-nums">
                                                {{ number_format($percent, 1) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <span class="icon-[solar--clipboard-remove-bold-duotone] text-4xl mb-2 text-slate-300 dark:text-slate-600"></span>
                                            <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Tidak ada data jadwal operasi untuk periode ini.</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau rentang tanggal.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($recapData->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50/90 dark:bg-slate-900/90 border-t-2 border-slate-200 dark:border-slate-800 text-xs font-black">
                                    <td class="px-5 py-3.5 text-slate-800 dark:text-white uppercase tracking-wider" colspan="2">TOTAL KESELURUHAN</td>
                                    <td class="px-4 py-3.5 text-center text-slate-900 dark:text-white bg-emerald-500/10 dark:bg-emerald-500/20 tabular-nums">
                                        {{ number_format($totalOperasi) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-emerald-700 dark:text-emerald-300 tabular-nums">
                                        {{ number_format($totalSelesai) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-sky-700 dark:text-sky-300 tabular-nums">
                                        {{ number_format($totalProses) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-amber-700 dark:text-amber-300 tabular-nums">
                                        {{ number_format($totalMenunggu) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-slate-700 dark:text-slate-300 tabular-nums">
                                        100%
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @else
            <!-- Tampilan Grafik Rekap Operasi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-2 bg-white dark:bg-slate-900/80 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="icon-[solar--chart-square-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">Top 10 Paket Operasi Terbanyak</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Berdasarkan volume tindakan periode terpilih</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-80 sm:h-96" wire:ignore wire:key="chart-recap-packages">
                        <x-chart chartId="chartRecapPackages" chartType="bar" barType="y" :labels="$chartData['packages']['labels']" :datasets="$chartData['packages']['datasets']" />
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/80 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="icon-[solar--pie-chart-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">Proporsi per Kategori</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Distribusi tindakan per spesialisasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-80 sm:h-96 flex items-center justify-center" wire:ignore wire:key="chart-recap-category">
                        <x-chart chartId="chartRecapCategory" chartType="doughnut" :labels="$chartData['category']['labels']" :datasets="$chartData['category']['datasets']" />
                    </div>
                </div>
            </div>
        @endif
    </div>

    @script
        <script>
            Alpine.data('chartComponent', (chartId, chartType, barType, initialLabels, initialDatasets) => ({
                chart: null,
                _updateHandler: null,
                _initTimeout: null,
                init() {
                    this.initChart({
                        labels: JSON.parse(JSON.stringify(initialLabels)),
                        datasets: JSON.parse(JSON.stringify(initialDatasets))
                    });

                    this._updateHandler = (event) => {
                        const payload = JSON.parse(JSON.stringify(event.detail));
                        if (!payload || !payload.labels) return;

                        const canvas = this.$refs.chartContainer ? this.$refs.chartContainer.querySelector('canvas') : null;
                        const existingChart = canvas ? Chart.getChart(canvas) : null;

                        if (existingChart && document.body.contains(canvas)) {
                            try {
                                existingChart.data.labels = payload.labels;
                                existingChart.data.datasets = payload.datasets;
                                existingChart.update();
                                this.chart = existingChart;
                            } catch (e) {
                                this.initChart(payload);
                            }
                        } else {
                            this.initChart(payload);
                        }
                    };

                    window.addEventListener(`refreshChartData-${chartId}`, this._updateHandler);
                },
                destroy() {
                    if (this._initTimeout) clearTimeout(this._initTimeout);
                    window.removeEventListener(`refreshChartData-${chartId}`, this._updateHandler);
                    if (this.chart) {
                        try {
                            this.chart.destroy();
                        } catch (e) {}
                        this.chart = null;
                    }
                },
                initChart(data) {
                    if (this._initTimeout) clearTimeout(this._initTimeout);

                    this._initTimeout = setTimeout(() => {
                        if (!this.$refs.chartContainer) return;

                        let canvas = this.$refs.chartContainer.querySelector('canvas');
                        if (!canvas) {
                            canvas = document.createElement('canvas');
                            canvas.id = chartId;
                            canvas.className = 'w-full h-full';
                            this.$refs.chartContainer.appendChild(canvas);
                        }

                        if (this.chart) {
                            try {
                                this.chart.destroy();
                            } catch (e) {}
                            this.chart = null;
                        }

                        const ctx = canvas.getContext('2d');
                        if (!ctx) return;

                        try {
                            this.chart = new Chart(ctx, {
                                type: chartType,
                                data: {
                                    labels: [...(data.labels || [])],
                                    datasets: (data.datasets || []).map(ds => ({ ...ds }))
                                },
                                options: {
                                    scales: chartType === 'doughnut' ? {} : {
                                        y: { beginAtZero: true },
                                        x: { beginAtZero: true },
                                    },
                                    indexAxis: barType,
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 500 }
                                }
                            });
                        } catch (e) {
                            console.error(`Chart init error [${chartId}]:`, e);
                        }
                    }, 50);
                }
            }));

            Livewire.on('refresh-recap-charts', (eventData) => {
                try {
                    let payload = null;
                    if (eventData && eventData.charts) {
                        payload = eventData.charts;
                    } else if (Array.isArray(eventData) && eventData[0] && eventData[0].charts) {
                        payload = eventData[0].charts;
                    } else if (eventData && typeof eventData === 'object' && !Array.isArray(eventData)) {
                        payload = eventData.charts || Object.values(eventData).find(v => v && v.charts)?.charts;
                    }

                    if (!payload) return;

                    const cleanCharts = JSON.parse(JSON.stringify(payload));

                    Alpine.nextTick(() => {
                        setTimeout(() => {
                            [{ name: 'chartRecapPackages', prop: 'packages' }, { name: 'chartRecapCategory', prop: 'category' }].forEach(mapping => {
                                const chartData = cleanCharts[mapping.prop];
                                if (chartData) {
                                    window.dispatchEvent(new CustomEvent(`refreshChartData-${mapping.name}`, { detail: chartData }));
                                }
                            });
                        }, 150);
                    });
                } catch (e) {
                    console.error('Error refreshing recap charts:', e);
                }
            });
        </script>
    @endscript
</x-content>
