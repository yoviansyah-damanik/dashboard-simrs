<x-content>
    <x-breadcrumb title="Farmasi" :items="[['title' => 'Layanan Penunjang Medis'], ['title' => 'Farmasi'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Rekapitulasi resep obat dan
                        tren penggunaan obat farmasi.</p>
                </div>
            </div>

            <!-- Sub-Header for Controls -->
            <div
                class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stroke dark:border-strokedark">
                <div class="flex items-center gap-4">
                    <div
                        class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl border border-stroke dark:border-strokedark">
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            Grafik
                        </button>
                        <button wire:click="$set('mainView', 'figures')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'figures' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--document-text-bold-duotone] text-lg"></span>
                            Dalam Angka
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
                            <option value="all">Keseluruhan</option>
                            <option value="today">Hari Ini</option>
                            <option value="last_7_days">7 Hari Lalu</option>
                            <option value="last_30_days">30 Hari Lalu</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="this_year">Tahun Ini</option>
                            <option value="monthly">Pilih Bulan</option>
                            <option value="yearly">Pilih Tahun</option>
                            <option value="custom">Custom</option>
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <span class="icon-[solar--calendar-minimalistic-bold] text-lg"></span>
                        </div>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <span class="icon-[solar--alt-arrow-down-bold-duotone] text-lg"></span>
                        </div>
                    </div>

                    @if ($period === 'monthly')
                        <div class="flex items-center gap-2">
                            <select wire:model.live="selectedMonth"
                                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                                @foreach ($this->months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                                @foreach ($this->years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                            @foreach ($this->years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    @elseif($period === 'custom')
                        <div
                            class="flex items-center gap-2 px-4 py-1 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark shadow-sm">
                            <input type="date" wire:model.live="startDate"
                                class="bg-transparent border-none focus:ring-0 text-sm font-bold cursor-pointer text-gray-700 dark:text-white" />
                            <span class="text-gray-300 font-bold">/</span>
                            <input type="date" wire:model.live="endDate"
                                class="bg-transparent border-none focus:ring-0 text-sm font-bold cursor-pointer text-gray-700 dark:text-white" />
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI Area -->
        <div class="space-y-4">
            <!-- 3-Panel Summary Banner -->
            <div
                class="bg-gradient-to-br from-indigo-600 to-violet-800 p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-stretch gap-6 group border border-white/10">
                <!-- Panel 1: Total Volume -->
                <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Total Resep
                        Obat</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-6xl font-black leading-none">{{ number_format($this->summary['total']) }}</h3>
                        <span class="text-sm font-bold text-indigo-300">Resep</span>
                    </div>
                    <div class="flex items-center gap-6 mt-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-indigo-200 uppercase tracking-widest">Ralan</span>
                            <span
                                class="text-2xl font-black text-cyan-300">{{ number_format($this->summary['status']['ralan']) }}</span>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-indigo-200 uppercase tracking-widest">Ranap</span>
                            <span
                                class="text-2xl font-black text-violet-300">{{ number_format($this->summary['status']['ranap']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Jenis Resep Grid -->
                <div class="relative z-10 text-white flex-1 border-x border-white/10 px-8 hidden lg:block">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-200 mb-4 text-center">
                        Rincian Jenis Resep</p>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ($this->summary['jenis_resep'] as $item)
                            <div
                                class="flex flex-col p-2.5 bg-white/10 rounded-xl border border-white/10 backdrop-blur-sm group/card hover:bg-white/20 transition-all">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span
                                        class="text-sm font-black text-indigo-100 uppercase tracking-tighter truncate w-3/4">{{ $item->jenis_resep }}</span>
                                    <span
                                        class="text-sm font-black bg-white/20 px-1.5 py-0.5 rounded-md">{{ number_format($item->total) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Panel 3: Resep Pulang -->
                <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center items-center text-center">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Resep Pulang
                    </p>
                    <h3 class="text-5xl font-black leading-none">{{ number_format($this->summary['resep_pulang']) }}
                    </h3>
                    <div class="mt-4 px-4 py-1.5 bg-white/10 rounded-full border border-white/10">
                        <span class="text-sm font-black text-indigo-100 uppercase tracking-[0.1em]">Pasien Ranap
                            Pulang</span>
                    </div>
                    <span
                        class="icon-[solar--bag-heart-bold-duotone] text-6xl opacity-20 absolute -right-4 -bottom-4 rotate-12"></span>
                </div>
            </div>

            <!-- 4-Card KPI Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-600 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Sudah
                        Diserahkan</span>
                    <h4 class="text-2xl font-black text-emerald-600">
                        {{ number_format($this->summary['penyerahan']['sudah']) }}</h4>
                </div>
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-red-500 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Belum
                        Diserahkan</span>
                    <h4 class="text-2xl font-black text-red-600">
                        {{ number_format($this->summary['penyerahan']['belum']) }}</h4>
                </div>
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-indigo-500 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Item Obat
                        Terpakai</span>
                    <h4 class="text-2xl font-black text-indigo-600">
                        {{ number_format($this->drugUsage['total_qty']) }}</h4>
                </div>
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-amber-500 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Total Biaya
                        Obat</span>
                    <h4 class="text-xl font-black text-amber-600">
                        Rp{{ number_format($this->drugUsage['total_biaya']) }}</h4>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="space-y-8">
            @if ($mainView === 'chart')
                <div class="space-y-6">
                    <!-- Trend Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                            <h4
                                class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                                <span class="icon-[solar--graph-bold-duotone] text-lg text-primary"></span>Tren Jumlah
                                Resep
                            </h4>
                            <div class="h-96" wire:ignore wire:key="chart-pharm-trend">
                                <x-chart chartId="chartPharmTrend" chartType="line"
                                    :labels="$this->summary['charts']['trend']['labels']"
                                    :datasets="$this->summary['charts']['trend']['datasets']" />
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                            <h4
                                class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                                <span class="icon-[solar--pill-bold-duotone] text-lg text-primary"></span>Tren
                                Penggunaan Obat
                            </h4>
                            <div class="h-96" wire:ignore wire:key="chart-pharm-drug-trend">
                                <x-chart chartId="chartPharmDrugTrend" chartType="line"
                                    :labels="$this->drugUsage['charts']['trend']['labels']"
                                    :datasets="$this->drugUsage['charts']['trend']['datasets']" />
                            </div>
                        </div>
                    </div>

                    <!-- Analisis Resep Section -->
                    <div
                        class="bg-gray-50/50 dark:bg-meta-4/5 p-6 rounded-3xl border border-stroke dark:border-strokedark space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 text-primary rounded-lg">
                                <span class="icon-[solar--chart-bold-duotone] text-xl"></span>
                            </div>
                            <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                Analisis Resep Obat</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Status Kunjungan (Ralan / Ranap)</h4>
                                <div class="h-64 flex items-center justify-center" wire:ignore
                                    wire:key="chart-pharm-status">
                                    <x-chart chartId="chartPharmStatus" chartType="doughnut"
                                        :labels="$this->summary['charts']['status']['labels']"
                                        :datasets="$this->summary['charts']['status']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Status Penyerahan Obat</h4>
                                <div class="h-64 flex items-center justify-center" wire:ignore
                                    wire:key="chart-pharm-penyerahan">
                                    <x-chart chartId="chartPharmPenyerahan" chartType="doughnut"
                                        :labels="$this->summary['charts']['penyerahan']['labels']"
                                        :datasets="$this->summary['charts']['penyerahan']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col md:col-span-2">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Sebaran Jenis Resep</h4>
                                <div class="h-64" wire:ignore wire:key="chart-pharm-jenis">
                                    <x-chart chartId="chartPharmJenis" chartType="bar" barType="x"
                                        :labels="$this->summary['charts']['jenis_resep']['labels']"
                                        :datasets="$this->summary['charts']['jenis_resep']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col md:col-span-2">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    10 Obat Terbanyak Digunakan</h4>
                                <div class="h-96" wire:ignore wire:key="chart-pharm-top-obat">
                                    <x-chart chartId="chartPharmTopObat" chartType="bar" barType="y"
                                        :labels="$this->drugUsage['charts']['top_obat']['labels']"
                                        :datasets="$this->drugUsage['charts']['top_obat']['datasets']" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Dalam Angka View -->
                <div class="space-y-8">
                    <x-recap.in-figures title="Status Kunjungan">
                        <x-box title="Ralan" :value="number_format($this->summary['status']['ralan'])"
                            icon="icon-[solar--user-hand-up-bold-duotone]" />
                        <x-box title="Ranap" :value="number_format($this->summary['status']['ranap'])"
                            icon="icon-[solar--bed-bold-duotone]" />
                        <x-box title="Resep Pulang" :value="number_format($this->summary['resep_pulang'])"
                            icon="icon-[solar--bag-heart-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Jenis Resep">
                        @foreach ($this->summary['jenis_resep'] as $item)
                            <x-box :title="$item->jenis_resep" :value="number_format($item->total)"
                                icon="icon-[solar--pill-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Status Penyerahan">
                        <x-box title="Sudah Diserahkan" :value="number_format($this->summary['penyerahan']['sudah'])"
                            icon="icon-[solar--check-circle-bold-duotone]" />
                        <x-box title="Belum Diserahkan" :value="number_format($this->summary['penyerahan']['belum'])"
                            icon="icon-[solar--clock-circle-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Penggunaan Obat">
                        <x-box title="Item Obat Terpakai" :value="number_format($this->drugUsage['total_qty'])"
                            icon="icon-[solar--box-bold-duotone]" />
                        <x-box title="Total Biaya Obat" :value="'Rp' . number_format($this->drugUsage['total_biaya'])"
                            icon="icon-[solar--wallet-money-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="10 Obat Terbanyak Digunakan">
                        @foreach ($this->drugUsage['top_obat'] as $item)
                            <x-box :title="$item->nama_brng" :value="number_format($item->qty)"
                                icon="icon-[solar--pill-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>
                </div>
            @endif
        </div>
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
                        try { this.chart.destroy(); } catch (e) {}
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
                            try { this.chart.destroy(); } catch (e) {}
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
                                    scales: {
                                        y: { beginAtZero: true },
                                        x: { beginAtZero: true },
                                    },
                                    indexAxis: barType,
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 500 }
                                }
                            });
                        } catch (e) { console.error(`Chart init error [${chartId}]:`, e); }
                    }, 50);
                }
            }));

            const handleRefresh = (eventName, chartMappings) => {
                Livewire.on(eventName, (eventData) => {
                    try {
                        let payload = null;
                        if (eventData && eventData.charts) {
                            payload = eventData.charts;
                        } else if (Array.isArray(eventData) && eventData[0] && eventData[0].charts) {
                            payload = eventData[0].charts;
                        }

                        if (!payload) return;

                        const cleanCharts = JSON.parse(JSON.stringify(payload));
                        Alpine.nextTick(() => {
                            setTimeout(() => {
                                chartMappings.forEach(mapping => {
                                    const chartData = cleanCharts[mapping.prop];
                                    if (chartData) {
                                        window.dispatchEvent(new CustomEvent(`refreshChartData-${mapping.name}`, { detail: chartData }));
                                    }
                                });
                            }, 150);
                        });
                    } catch (e) { console.error(`Error refreshing ${eventName}:`, e); }
                });
            };

            handleRefresh('refresh-main-charts', [
                { name: 'chartPharmTrend', prop: 'trend' },
                { name: 'chartPharmStatus', prop: 'status' },
                { name: 'chartPharmJenis', prop: 'jenis_resep' },
                { name: 'chartPharmPenyerahan', prop: 'penyerahan' }
            ]);

            handleRefresh('refresh-drug-charts', [
                { name: 'chartPharmDrugTrend', prop: 'trend' },
                { name: 'chartPharmTopObat', prop: 'top_obat' }
            ]);
        </script>
    @endscript
</x-content>
