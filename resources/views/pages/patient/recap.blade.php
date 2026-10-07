<x-content>
    <x-breadcrumb title="Data Pasien" :items="[['title' => 'Data Pasien'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Infografis dan demografis
                        pasien secara menyeluruh.</p>
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
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Total Pasien
                        Terdaftar</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-6xl font-black leading-none">{{ number_format($this->summary['total']) }}</h3>
                        <span class="text-sm font-bold text-indigo-300">Jiwa</span>
                    </div>
                    <div class="flex items-center gap-6 mt-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-indigo-200 uppercase tracking-widest">Laki-laki</span>
                            <span
                                class="text-2xl font-black text-blue-300">{{ number_format($this->summary['gender']['laki']) }}</span>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-indigo-200 uppercase tracking-widest">Perempuan</span>
                            <span
                                class="text-2xl font-black text-pink-300">{{ number_format($this->summary['gender']['perempuan']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Age Demographics Grid -->
                <div class="relative z-10 text-white flex-1 border-x border-white/10 px-8 hidden lg:block">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-200 mb-4 text-center">
                        Rincian Kelompok Usia &amp; Gender</p>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ($this->summary['age_groups'] as $age)
                            <div
                                class="flex flex-col p-2.5 bg-white/10 rounded-xl border border-white/10 backdrop-blur-sm group/card hover:bg-white/20 transition-all">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span
                                        class="text-sm font-black text-indigo-100 uppercase tracking-tighter truncate w-3/4">{{ $age->kelompok }}</span>
                                    <span
                                        class="text-sm font-black bg-white/20 px-1.5 py-0.5 rounded-md">{{ number_format($age->total) }}</span>
                                </div>
                                <div class="flex items-center gap-2 pt-1.5 border-t border-white/5">
                                    <div class="flex-1 flex items-center gap-1 justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                        <span
                                            class="text-sm font-black text-blue-200">{{ number_format($age->laki) }}</span>
                                    </div>
                                    <div class="flex-1 flex items-center gap-1 justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-pink-400"></span>
                                        <span
                                            class="text-sm font-black text-pink-200">{{ number_format($age->perempuan) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Panel 3: Average Age -->
                <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center items-center text-center">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Rata-rata Usia</p>
                    <h3 class="text-5xl font-black leading-none">{{ $this->summary['average_age'] }}</h3>
                    <div class="mt-4 px-4 py-1.5 bg-white/10 rounded-full border border-white/10">
                        <span class="text-sm font-black text-indigo-100 uppercase tracking-[0.1em]">Tahun</span>
                    </div>
                    <span
                        class="icon-[solar--users-group-two-rounded-bold-duotone] text-6xl opacity-20 absolute -right-4 -bottom-4 rotate-12"></span>
                </div>
            </div>

            <!-- 4-Card KPI Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-indigo-500 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Umum</span>
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-2xl font-black text-indigo-600">{{ number_format($this->summary['type']['umum']) }}
                        </h4>
                        <span class="icon-[solar--user-bold-duotone] text-sm text-indigo-400"></span>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-600 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">TNI</span>
                    <h4 class="text-2xl font-black text-emerald-600">{{ number_format($this->summary['type']['tni']) }}
                    </h4>
                </div>
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-amber-500 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">POLRI</span>
                    <h4 class="text-2xl font-black text-amber-600">{{ number_format($this->summary['type']['polri']) }}
                    </h4>
                </div>
                <div
                    class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-gray-500 transition-all">
                    <span class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Provinsi
                        Terdata</span>
                    <h4 class="text-2xl font-black text-gray-700 dark:text-gray-300">
                        {{ count($this->summary['charts']['region']['labels']) }}</h4>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="space-y-8">
            @if ($mainView === 'chart')
                <div class="space-y-6">
                    <!-- Trend & Region Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                            <h4
                                class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                                <span class="icon-[solar--graph-bold-duotone] text-lg text-primary"></span>Tren
                                Pendaftaran Pasien
                            </h4>
                            <div class="h-96" wire:ignore wire:key="chart-pat-trend">
                                <x-chart chartId="chartPatTrend" chartType="line"
                                    :labels="$this->summary['charts']['trend']['labels']"
                                    :datasets="$this->summary['charts']['trend']['datasets']" />
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                            <h4
                                class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                                <span class="icon-[solar--map-point-bold-duotone] text-lg text-primary"></span>10 Provinsi
                                Terbanyak
                            </h4>
                            <div class="h-96" wire:ignore wire:key="chart-pat-region">
                                <x-chart chartId="chartPatRegion" chartType="bar" barType="y"
                                    :labels="$this->summary['charts']['region']['labels']"
                                    :datasets="$this->summary['charts']['region']['datasets']" />
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
                            <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                Analisis Demografi Pasien</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Sebaran Jenis Kelamin</h4>
                                <div class="h-64 flex items-center justify-center" wire:ignore
                                    wire:key="chart-pat-gender">
                                    <x-chart chartId="chartPatGender" chartType="doughnut"
                                        :labels="$this->demographics['charts']['gender']['labels']"
                                        :datasets="$this->demographics['charts']['gender']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Kelompok Usia per Gender</h4>
                                <div class="h-64" wire:ignore wire:key="chart-pat-age">
                                    <x-chart chartId="chartPatAge" chartType="bar" barType="x"
                                        :labels="$this->demographics['charts']['age']['labels']"
                                        :datasets="$this->demographics['charts']['age']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Top 10 Penjamin / Pembayar</h4>
                                <div class="h-64" wire:ignore wire:key="chart-pat-paytype">
                                    <x-chart chartId="chartPatPayType" chartType="bar" barType="y"
                                        :labels="$this->demographics['charts']['pay_type']['labels']"
                                        :datasets="$this->demographics['charts']['pay_type']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Sebaran Pendidikan</h4>
                                <div class="h-64" wire:ignore wire:key="chart-pat-education">
                                    <x-chart chartId="chartPatEducation" chartType="bar" barType="x"
                                        :labels="$this->demographics['charts']['education']['labels']"
                                        :datasets="$this->demographics['charts']['education']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Status Pernikahan</h4>
                                <div class="h-64 flex items-center justify-center" wire:ignore
                                    wire:key="chart-pat-marital">
                                    <x-chart chartId="chartPatMarital" chartType="pie"
                                        :labels="$this->demographics['charts']['marital_status']['labels']"
                                        :datasets="$this->demographics['charts']['marital_status']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Golongan Darah</h4>
                                <div class="h-64 flex items-center justify-center" wire:ignore
                                    wire:key="chart-pat-blood">
                                    <x-chart chartId="chartPatBlood" chartType="doughnut"
                                        :labels="$this->demographics['charts']['blood_type']['labels']"
                                        :datasets="$this->demographics['charts']['blood_type']['datasets']" />
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col md:col-span-2">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Sebaran Agama</h4>
                                <div class="h-64" wire:ignore wire:key="chart-pat-religion">
                                    <x-chart chartId="chartPatReligion" chartType="bar" barType="x"
                                        :labels="$this->demographics['charts']['religion']['labels']"
                                        :datasets="$this->demographics['charts']['religion']['datasets']" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Dalam Angka View -->
                <div class="space-y-8">
                    <x-recap.in-figures title="Jenis Kelamin">
                        <x-box title="Laki-laki" :value="number_format($this->summary['gender']['laki'])"
                            icon="icon-[solar--men-bold-duotone]" />
                        <x-box title="Perempuan" :value="number_format($this->summary['gender']['perempuan'])"
                            icon="icon-[solar--women-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Kelompok Usia">
                        @foreach ($this->summary['age_groups'] as $age)
                            <x-box :title="$age->kelompok ?: 'Tidak Diketahui'" :value="number_format($age->total)"
                                icon="icon-[solar--calendar-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Jenis Pasien">
                        <x-box title="Umum" :value="number_format($this->summary['type']['umum'])"
                            icon="icon-[solar--user-bold-duotone]" />
                        <x-box title="TNI" :value="number_format($this->summary['type']['tni'])"
                            icon="icon-[solar--shield-star-bold-duotone]" />
                        <x-box title="POLRI" :value="number_format($this->summary['type']['polri'])"
                            icon="icon-[solar--shield-check-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Penjamin / Pembayar">
                        @foreach ($this->demographics['pay_type'] as $item)
                            <x-box :title="$item->png_jawab ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--wallet-money-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Pendidikan">
                        @foreach ($this->demographics['education'] as $item)
                            <x-box :title="$item->pnd ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--square-academic-cap-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Status Pernikahan">
                        @foreach ($this->demographics['marital_status'] as $item)
                            <x-box :title="$item->stts_nikah ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--heart-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Golongan Darah">
                        @foreach ($this->demographics['blood_type'] as $item)
                            <x-box :title="$item->gol_darah ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--waterdrop-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Agama">
                        @foreach ($this->demographics['religion'] as $item)
                            <x-box :title="$item->agama ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--hand-heart-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Sebaran Provinsi">
                        @foreach ($this->summary['charts']['region']['labels'] as $index => $label)
                            <x-box :title="$label ?: '-'" :value="number_format($this->summary['charts']['region']['datasets'][0]['data'][$index] ?? 0)"
                                icon="icon-[solar--map-point-bold-duotone]" />
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
                { name: 'chartPatTrend', prop: 'trend' },
                { name: 'chartPatRegion', prop: 'region' }
            ]);

            handleRefresh('refresh-demo-charts', [
                { name: 'chartPatGender', prop: 'gender' },
                { name: 'chartPatAge', prop: 'age' },
                { name: 'chartPatPayType', prop: 'pay_type' },
                { name: 'chartPatEducation', prop: 'education' },
                { name: 'chartPatMarital', prop: 'marital_status' },
                { name: 'chartPatBlood', prop: 'blood_type' },
                { name: 'chartPatReligion', prop: 'religion' }
            ]);
        </script>
    @endscript
</x-content>
