<x-content>
    <x-breadcrumb title="Jadwal Operasi" :items="[['title' => 'Jadwal Operasi'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Sub-Header for Controls -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stroke dark:border-strokedark">
            <div class="flex items-center gap-4">
                <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl">
                    <button wire:click="$set('mainView', 'list')"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'list' ? 'bg-white dark:bg-boxdark shadow-sm text-primary-500' : 'text-gray-500' }}">
                        <span class="icon-[solar--list-bold-duotone] text-lg"></span>
                        List
                    </button>
                    <button wire:click="$set('mainView', 'chart')"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-primary-500' : 'text-gray-500' }}">
                        <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                        Grafik
                    </button>
                </div>

                <div class="relative min-w-[280px]">
                    <input type="text" wire:model.live.debounce.300ms="searchPackage"
                        placeholder="Cari Nama Paket Operasi..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm focus:border-primary-500 focus:ring-0 outline-none transition-all shadow-sm">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <span class="icon-[solar--magnifer-bold-duotone] text-xl"></span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select wire:model.live="period"
                        class="appearance-none pl-10 pr-10 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary-500 focus:ring-0 cursor-pointer outline-none transition-all">
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
                            class="px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary-500 outline-none">
                            @foreach ($months as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="selectedYear"
                            class="px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary-500 outline-none">
                            @foreach ($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($period === 'yearly')
                    <select wire:model.live="selectedYear"
                        class="px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary-500 outline-none">
                        @foreach ($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                @elseif($period === 'custom')
                    <div class="flex items-center gap-2 px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark shadow-sm">
                        <input type="date" wire:model.live="startDate"
                            class="bg-transparent border-none focus:ring-0 text-sm font-medium cursor-pointer" />
                        <span class="text-gray-300 font-bold">/</span>
                        <input type="date" wire:model.live="endDate"
                            class="bg-transparent border-none focus:ring-0 text-sm font-medium cursor-pointer" />
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-primary-500/10 text-primary-500 rounded-xl">
                    <span class="icon-[solar--folder-with-files-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Rekap Operasi per Paket</h3>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mt-0.5">Jumlah operasi berdasarkan paket tindakan</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-4xl font-black text-primary-500">{{ number_format($recapData->sum('jumlah_operasi')) }}</span>
                <span class="text-sm font-bold text-gray-400 block uppercase tracking-tighter">Total Operasi</span>
            </div>
        </div>

        @if ($mainView === 'list')
            <div class="bg-white rounded-2xl border border-stroke shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-meta-4">
                                <th class="px-6 py-4 text-sm font-black uppercase tracking-widest">Paket Operasi</th>
                                <th class="px-4 py-4 text-sm font-black uppercase tracking-widest text-center">Kategori</th>
                                <th class="px-4 py-4 text-sm font-black uppercase tracking-widest text-center bg-blue-50 dark:bg-blue-900/10">Jumlah Operasi</th>
                                <th class="px-4 py-4 text-sm font-black uppercase tracking-widest text-center text-emerald-600">Selesai</th>
                                <th class="px-4 py-4 text-sm font-black uppercase tracking-widest text-center text-primary-500">Proses</th>
                                <th class="px-4 py-4 text-sm font-black uppercase tracking-widest text-center text-amber-600">Menunggu</th>
                                <th class="px-6 py-4 text-sm font-black uppercase tracking-widest text-center">Proporsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke dark:divide-strokedark">
                            @php $total = $recapData->sum('jumlah_operasi'); @endphp
                            @forelse($recapData as $item)
                                @php $percent = $total > 0 ? ($item->jumlah_operasi / $total) * 100 : 0; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-primary-500 transition-colors">{{ $item->nama_paket }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-meta-4 rounded text-sm font-black text-gray-600 dark:text-gray-300 uppercase">{{ $item->kategori ?: '-' }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center bg-blue-50/30 dark:bg-blue-900/5 font-black text-gray-800 dark:text-white">
                                        {{ number_format($item->jumlah_operasi) }}</td>
                                    <td class="px-4 py-4 text-center text-emerald-600 font-bold">
                                        {{ number_format($item->jumlah_selesai) }}</td>
                                    <td class="px-4 py-4 text-center text-primary-500 font-bold">
                                        {{ number_format($item->jumlah_proses) }}</td>
                                    <td class="px-4 py-4 text-center text-amber-600 font-bold">
                                        {{ number_format($item->jumlah_menunggu) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 min-w-0 bg-gray-100 dark:bg-meta-4 h-2 rounded-full overflow-hidden">
                                                <div class="bg-primary-500 h-full rounded-full" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="shrink-0 whitespace-nowrap text-sm font-bold text-gray-500 dark:text-gray-400 w-14 text-right">{{ number_format($percent, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada data operasi untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($recapData->isNotEmpty())
                            <tfoot>
                                <tr class="bg-gray-100 dark:bg-meta-4 border-t-2 border-stroke dark:border-strokedark">
                                    <td class="px-6 py-4 font-black text-gray-800 dark:text-white" colspan="2">TOTAL</td>
                                    <td class="px-4 py-4 text-center font-black bg-blue-100 dark:bg-blue-900/20">
                                        {{ number_format($recapData->sum('jumlah_operasi')) }}</td>
                                    <td class="px-4 py-4 text-center font-black text-emerald-700">
                                        {{ number_format($recapData->sum('jumlah_selesai')) }}</td>
                                    <td class="px-4 py-4 text-center font-black text-indigo-700">
                                        {{ number_format($recapData->sum('jumlah_proses')) }}</td>
                                    <td class="px-4 py-4 text-center font-black text-amber-700">
                                        {{ number_format($recapData->sum('jumlah_menunggu')) }}</td>
                                    <td class="px-6 py-4 text-center font-black text-gray-500">100%</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                    <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                        <span class="icon-[solar--chart-square-bold-duotone] text-lg text-primary-500"></span>
                        Top 10 Paket Operasi Terbanyak
                    </h4>
                    <div class="h-96" wire:ignore wire:key="chart-recap-packages">
                        <x-chart chartId="chartRecapPackages" chartType="bar" barType="y" :labels="$chartData['packages']['labels']" :datasets="$chartData['packages']['datasets']" />
                    </div>
                </div>
                <div class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                    <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                        <span class="icon-[solar--pie-chart-bold-duotone] text-lg text-primary-500"></span>
                        Proporsi per Kategori
                    </h4>
                    <div class="h-96 flex items-center justify-center" wire:ignore wire:key="chart-recap-category">
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
