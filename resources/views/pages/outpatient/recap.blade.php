<x-content>
    <x-breadcrumb title="Rawat Jalan" :items="[['title' => 'Rawat Jalan'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Tab Navigation -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Monitoring kunjungan dan rekapitulasi poliklinik secara real-time.</p>
                </div>

            <!-- Main Tabs -->
            <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-2xl border border-stroke dark:border-strokedark">
                <button wire:click="switchTab('recap')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $mainTab === 'recap' ? 'bg-white dark:bg-boxdark shadow-lg text-primary' : 'text-gray-500 hover:text-gray-700' }}">
                    <span class="icon-[solar--folder-with-files-bold-duotone] text-lg"></span>
                    Rekapitulasi
                </button>
            </div>
        </div>

        <!-- Sub-Header for Controls -->
        <div
            class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stroke dark:border-strokedark">
            <div class="flex items-center gap-4">
                @if ($mainTab === 'recap')
                    <div
                        class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl border border-stroke dark:border-strokedark">
                        <button wire:click="$set('mainView', 'list')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $mainView === 'list' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--list-bold-duotone] text-lg"></span>
                            List
                        </button>
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            Grafik
                        </button>
                    </div>
                @endif
            </div>

            @if ($mainTab === 'recap')
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
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
            @endif
        </div>
    </div>

    <!-- KPI Area: Exact Inpatient Style -->
    <div class="space-y-4">
        <!-- 3-Panel Summary Banner -->
        <div class="bg-gradient-to-br from-indigo-600 to-violet-800 p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-stretch gap-6 group border border-white/10">
            <!-- Panel 1: Total Volume -->
            <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Total Kunjungan</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-6xl font-black leading-none">{{ number_format($this->overallStats['total']) }}</h3>
                    <span class="text-sm font-bold text-indigo-300">Jiwa</span>
                </div>
                <div class="flex items-center gap-6 mt-6">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-indigo-200 uppercase tracking-widest">Laki-laki</span>
                        <span class="text-xl font-black text-blue-300">{{ number_format($this->overallStats['gender']['laki']) }}</span>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-indigo-200 uppercase tracking-widest">Perempuan</span>
                        <span class="text-xl font-black text-pink-300">{{ number_format($this->overallStats['gender']['perempuan']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Panel 2: Age Demographics Grid -->
            <div class="relative z-10 text-white flex-1 border-x border-white/10 px-8 hidden lg:block">
                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-4 text-center">Rincian Kelompok Usia & Gender</p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($this->overallStats['age_groups'] as $age)
                        <div class="flex flex-col p-2.5 bg-white/10 rounded-xl border border-white/10 backdrop-blur-sm group/card hover:bg-white/20 transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[9px] font-black text-indigo-100 uppercase tracking-tighter truncate w-3/4">{{ $age->kelompok }}</span>
                                <span class="text-xs font-black bg-white/20 px-1.5 py-0.5 rounded-md">{{ number_format($age->total) }}</span>
                            </div>
                            <div class="flex items-center gap-2 pt-1.5 border-t border-white/5">
                                <div class="flex-1 flex items-center gap-1 justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                    <span class="text-[10px] font-black text-blue-200">{{ number_format($age->laki) }}</span>
                                </div>
                                <div class="flex-1 flex items-center gap-1 justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-pink-400"></span>
                                    <span class="text-[10px] font-black text-pink-200">{{ number_format($age->perempuan) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Panel 3: Efficiency Metric -->
            <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center items-center text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Penyelesaian</p>
                <h3 class="text-5xl font-black leading-none">{{ number_format($this->overallStats['completion_rate'], 1) }}%</h3>
                <div class="mt-4 px-4 py-1.5 bg-white/10 rounded-full border border-white/10">
                    <span class="text-[9px] font-black text-indigo-100 uppercase tracking-[0.1em]">Total Rate Periode</span>
                </div>
                <span class="icon-[solar--chart-square-bold-duotone] text-6xl opacity-20 absolute -right-4 -bottom-4 rotate-12"></span>
            </div>
        </div>

        <!-- 4-Card KPI Row (Insurance removed) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-indigo-500 transition-all">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Baru</span>
                <div class="flex items-center gap-1.5">
                    <h4 class="text-xl font-black text-indigo-600">{{ number_format($this->recapData->sum('pasien_baru')) }}</h4>
                    <span class="icon-[solar--user-plus-bold-duotone] text-sm text-indigo-400"></span>
                </div>
            </div>
            <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-gray-500 transition-all">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Lama</span>
                <div class="flex items-center gap-1.5">
                    <h4 class="text-xl font-black text-gray-700 dark:text-gray-300">{{ number_format($this->recapData->sum('pasien_lama')) }}</h4>
                    <span class="icon-[solar--user-bold-duotone] text-sm text-gray-400"></span>
                </div>
            </div>
            <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-600 transition-all">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Sudah</span>
                <h4 class="text-xl font-black text-emerald-600">{{ number_format($this->recapData->sum('sudah_periksa')) }}</h4>
                <p class="text-[7px] font-bold text-gray-400 uppercase">Checked</p>
            </div>
            <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-red-500 transition-all">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Belum</span>
                <h4 class="text-xl font-black text-red-600">{{ number_format($this->recapData->sum('belum_periksa')) }}</h4>
                <p class="text-[7px] font-bold text-gray-400 uppercase">Pending</p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="space-y-8">
        @if ($mainView === 'chart')
            <div class="space-y-6">
                <!-- Trend & Poly Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--graph-bold-duotone] text-lg text-primary"></span>Tren Kunjungan
                            Harian
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-out-trend">
                            <x-chart chartId="chartOutTrend" chartType="line" :labels="$this->overallStats['charts']['trend']['labels']" :datasets="$this->overallStats['charts']['trend']['datasets']" />
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--hospital-bold-duotone] text-lg text-primary"></span>10 Poliklinik
                            Terbanyak
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-out-poly">
                            <x-chart chartId="chartOutPoly" chartType="bar" barType="x" :labels="$this->overallStats['charts']['poly_distribution']['labels']"
                                :datasets="$this->overallStats['charts']['poly_distribution']['datasets']" />
                        </div>
                    </div>
                </div>

                <!-- Demographics Section -->
                <div
                    class="bg-gray-50/50 dark:bg-meta-4/5 p-6 rounded-3xl border border-stroke dark:border-strokedark space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-primary/10 text-primary rounded-lg">
                            <span class="icon-[solar--chart-bold-duotone] text-xl"></span>
                        </div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-widest">Analisis
                            Demografi Pasien</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                Sebaran Jenis Kelamin</h4>
                            <div class="h-64 flex items-center justify-center" wire:ignore
                                wire:key="chart-out-gender">
                                <x-chart chartId="chartOutGender" chartType="doughnut" :labels="$this->patientDemographics['charts']['gender']['labels']"
                                    :datasets="$this->patientDemographics['charts']['gender']['datasets']" />
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                Top 5 Penjamin</h4>
                            <div class="h-64" wire:ignore wire:key="chart-out-insurance">
                                <x-chart chartId="chartOutInsurance" chartType="bar" barType="y" :labels="$this->patientDemographics['charts']['insurance']['labels']"
                                    :datasets="$this->patientDemographics['charts']['insurance']['datasets']" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- List Table View: Detailed Multi-Column -->
            <div
                class="bg-white dark:bg-boxdark rounded-[2rem] border border-stroke dark:border-strokedark shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-meta-4">
                                <th
                                    class="px-6 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-stroke dark:border-strokedark">
                                    Poliklinik</th>
                                <th
                                    class="px-6 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-stroke dark:border-strokedark">
                                    Dokter</th>
                                <th
                                    class="px-4 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-center bg-indigo-50/50 dark:bg-indigo-900/10 border-b border-stroke dark:border-strokedark text-primary">
                                    Total</th>
                                <th
                                    class="px-4 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-center border-b border-stroke dark:border-strokedark">
                                    Status Pasien</th>
                                <th
                                    class="px-4 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-center border-b border-stroke dark:border-strokedark">
                                    Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke dark:divide-strokedark">
                            @forelse($this->recapData as $item)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-meta-4/20 transition-all duration-300 text-[13px]">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary/40"></span>
                                            <span class="font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $item->nm_poli }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-600 dark:text-gray-400 italic">{{ $item->nm_dokter }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center bg-indigo-50/20 dark:bg-indigo-900/5">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-xl bg-indigo-600 text-white text-xs font-black shadow-lg shadow-indigo-200 dark:shadow-none">
                                            {{ number_format($item->total_reg) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-4">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[9px] font-black text-gray-400 uppercase">B:</span>
                                                <span class="text-blue-600 font-black">{{ number_format($item->pasien_baru) }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[9px] font-black text-gray-400 uppercase">L:</span>
                                                <span class="text-gray-500 font-black">{{ number_format($item->pasien_lama) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-4">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[9px] font-black text-emerald-500 uppercase">S:</span>
                                                <span class="text-emerald-600 font-black">{{ number_format($item->sudah_periksa) }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[9px] font-black text-amber-500 uppercase">B:</span>
                                                <span class="text-amber-600 font-black">{{ number_format($item->belum_periksa) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-32 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="icon-[solar--document-add-bold-duotone] text-6xl text-gray-200"></span>
                                            <p class="text-gray-400 italic text-sm">Tidak ada data kunjungan untuk periode ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                { name: 'chartOutTrend', prop: 'trend' },
                { name: 'chartOutPoly', prop: 'poly_distribution' }
            ]);

            handleRefresh('refresh-demo-charts', [
                { name: 'chartOutGender', prop: 'gender' },
                { name: 'chartOutInsurance', prop: 'insurance' }
            ]);
        </script>
    @endscript
</div>
</x-content>