<x-content>
    <x-breadcrumb title="Laboratorium" :items="[['title' => 'Layanan Penunjang Medis'], ['title' => 'Laboratorium'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">
                        Rekapitulasi pemeriksaan laboratorium dan analisis tren diagnostik pasien.
                    </p>
                </div>
            </div>

            <!-- Sub-Header Controls & Filter Periode -->
            <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stroke dark:border-strokedark">
                <div class="flex items-center gap-4">
                    <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl border border-stroke dark:border-strokedark">
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-emerald-600 dark:text-emerald-400' : 'text-gray-500' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            Grafik
                        </button>
                        <button wire:click="$set('mainView', 'figures')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'figures' ? 'bg-white dark:bg-boxdark shadow-sm text-emerald-600 dark:text-emerald-400' : 'text-gray-500' }}">
                            <span class="icon-[solar--document-text-bold-duotone] text-lg"></span>
                            Dalam Angka
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-emerald-500 focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
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
                                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-emerald-500 outline-none shadow-sm">
                                @foreach ($this->months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-emerald-500 outline-none shadow-sm">
                                @foreach ($this->years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-emerald-500 outline-none shadow-sm">
                            @foreach ($this->years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    @elseif($period === 'custom')
                        <div class="flex items-center gap-2 px-4 py-1 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark shadow-sm">
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

        <!-- Banner & Kartu KPI Metrik Utama -->
        <div class="space-y-4">
            <!-- Banner Ringkasan 3-Panel (Tanpa Biaya & Demografi) -->
            <div class="bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-stretch gap-6 group border border-white/10">
                <!-- Panel 1: Total Pemeriksaan -->
                <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-200 mb-1">Total Pemeriksaan Lab</p>
                    <h3 class="text-4xl lg:text-5xl font-black tracking-tight drop-shadow-sm">
                        {{ number_format($this->summary['total_pemeriksaan']) }}
                    </h3>
                    <p class="text-xs text-emerald-100/80 mt-2 flex items-center gap-1.5 font-medium">
                        <span class="icon-[solar--test-tube-minimalistic-bold-duotone] text-sm"></span>
                        Tindakan Terverifikasi
                    </p>
                </div>

                <div class="hidden lg:block w-px bg-white/15 my-2"></div>

                <!-- Panel 2: Pasien Unik -->
                <div class="relative z-10 text-white flex-1 flex flex-col justify-center">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-200 mb-1">Pasien Dilayani</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl lg:text-4xl font-black">{{ number_format($this->summary['unique_patients']) }}</span>
                        <span class="text-xs font-semibold text-emerald-100">Total Pengunjung</span>
                    </div>
                    <p class="text-xs text-emerald-100/80 mt-2 flex items-center gap-1.5 font-medium">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-sm"></span>
                        Pasien Terdaftar & Menjalani Pemeriksaan
                    </p>
                </div>

                <div class="hidden lg:block w-px bg-white/15 my-2"></div>

                <!-- Panel 3: Rasio Pelayanan -->
                <div class="relative z-10 text-white flex-1 flex flex-col justify-center">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-200 mb-1">Instalasi Pelayanan</p>
                    <div class="flex flex-wrap items-center gap-3 mt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm text-xs font-bold text-sky-200 border border-white/10">
                            <span class="icon-[solar--user-hand-up-bold-duotone] text-sm"></span>
                            Ralan: {{ number_format($this->summary['status']['ralan']) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm text-xs font-bold text-violet-200 border border-white/10">
                            <span class="icon-[solar--bed-bold-duotone] text-sm"></span>
                            Ranap: {{ number_format($this->summary['status']['ranap']) }}
                        </span>
                    </div>
                    <p class="text-xs text-emerald-100/80 mt-2 flex items-center gap-1.5 font-medium">
                        <span class="icon-[solar--hospital-bold-duotone] text-sm"></span>
                        Distribusi Unit Pelayanan SIMRS
                    </p>
                </div>

                <!-- Elemen Dekorasi Latar Belakang -->
                <span class="icon-[solar--test-tube-minimalistic-bold-duotone] text-8xl opacity-10 absolute -right-6 -bottom-6 rotate-12 text-white pointer-events-none"></span>
            </div>

            <!-- Baris 4-Kartu KPI Rinci -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-sky-500 transition-all">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Rawat Jalan</span>
                    <h4 class="text-2xl font-black text-sky-600 dark:text-sky-400">
                        {{ number_format($this->summary['status']['ralan']) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-1">Poli & IGD</span>
                </div>
                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-violet-500 transition-all">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Rawat Inap</span>
                    <h4 class="text-2xl font-black text-violet-600 dark:text-violet-400">
                        {{ number_format($this->summary['status']['ranap']) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-1">Bangsal & Perawatan</span>
                </div>
                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-500 transition-all">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Item Pemeriksaan</span>
                    <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ number_format(count($this->summary['top_pemeriksaan'])) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-1">Jenis Tindakan</span>
                </div>
                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-amber-500 transition-all">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Dokter Pengirim</span>
                    <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ number_format(count($this->summary['top_dokter'])) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-1">DPJP / Perujuk Aktif</span>
                </div>
            </div>
        </div>

        <!-- Area Konten Utama -->
        <div class="space-y-8">
            @if ($mainView === 'chart')
                <div class="space-y-6">
                    <!-- Grafik Tren Utama -->
                    <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-white flex items-center gap-2">
                                <span class="icon-[solar--graph-bold-duotone] text-xl text-emerald-600"></span>
                                Tren Pemeriksaan Laboratorium
                            </h4>
                            <span class="text-xs font-bold text-gray-400">
                                Total Pemeriksaan per Periode Waktu
                            </span>
                        </div>
                        <div class="h-96" wire:ignore wire:key="chart-lab-trend">
                            <x-chart chartId="chartLabTrend" chartType="line"
                                :labels="$this->summary['charts']['trend']['labels']"
                                :datasets="$this->summary['charts']['trend']['datasets']" />
                        </div>
                    </div>

                    <!-- Distribusi Status & Penjamin -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Status Kunjungan -->
                        <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                Proporsi Status Pelayanan (Ralan vs Ranap)
                            </h4>
                            <div class="h-72 flex items-center justify-center" wire:ignore wire:key="chart-lab-status">
                                <x-chart chartId="chartLabStatus" chartType="doughnut"
                                    :labels="$this->summary['charts']['status']['labels']"
                                    :datasets="$this->summary['charts']['status']['datasets']" />
                            </div>
                        </div>

                        <!-- Sebaran Penjamin / Cara Bayar -->
                        <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                Sebaran Cara Bayar / Penjamin Pasien
                            </h4>
                            <div class="h-72" wire:ignore wire:key="chart-lab-penjamin">
                                <x-chart chartId="chartLabPenjamin" chartType="bar" barType="y"
                                    :labels="$this->summary['charts']['top_penjamin']['labels']"
                                    :datasets="$this->summary['charts']['top_penjamin']['datasets']" />
                            </div>
                        </div>
                    </div>

                    <!-- 10 Pemeriksaan Laboratorium Terbanyak -->
                    <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-white mb-6 flex items-center gap-2">
                            <span class="icon-[solar--ranking-bold-duotone] text-xl text-emerald-600"></span>
                            10 Pemeriksaan Laboratorium Terbanyak
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-lab-top">
                            <x-chart chartId="chartLabTop" chartType="bar" barType="y"
                                :labels="$this->summary['charts']['top_pemeriksaan']['labels']"
                                :datasets="$this->summary['charts']['top_pemeriksaan']['datasets']" />
                        </div>
                    </div>
                </div>
            @else
                <!-- Tampilan Dalam Angka -->
                <div class="space-y-8">
                    <x-recap.in-figures title="Status Pelayanan">
                        <x-box title="Rawat Jalan" :value="number_format($this->summary['status']['ralan'])"
                            icon="icon-[solar--user-hand-up-bold-duotone]" />
                        <x-box title="Rawat Inap" :value="number_format($this->summary['status']['ranap'])"
                            icon="icon-[solar--bed-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Kategori Laboratorium">
                        @foreach ($this->summary['kategori'] as $item)
                            <x-box :title="'Kategori ' . ($item->kategori ?: '-')" :value="number_format($item->total)"
                                icon="icon-[solar--test-tube-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="10 Pemeriksaan Terbanyak">
                        @foreach ($this->summary['top_pemeriksaan'] as $item)
                            <x-box :title="$item->nm_perawatan ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--checklist-minimalistic-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Dokter Pengirim Terbanyak">
                        @foreach ($this->summary['top_dokter'] as $item)
                            <x-box :title="$item->nama_dokter ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--stethoscope-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Penjamin / Cara Bayar">
                        @foreach ($this->summary['top_penjamin'] as $item)
                            <x-box :title="$item->png_jawab ?: '-'" :value="number_format($item->total)"
                                icon="icon-[solar--wallet-money-bold-duotone]" />
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
                        } catch (e) { console.error(`Chart init error [${chartId}]:`, e); }
                    }, 50);
                }
            }));

            Livewire.on('refresh-lab-charts', (eventData) => {
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
                            const mappings = [
                                { name: 'chartLabTrend', prop: 'trend' },
                                { name: 'chartLabStatus', prop: 'status' },
                                { name: 'chartLabTop', prop: 'top_pemeriksaan' },
                                { name: 'chartLabPenjamin', prop: 'top_penjamin' }
                            ];
                            mappings.forEach(m => {
                                if (cleanCharts[m.prop]) {
                                    window.dispatchEvent(new CustomEvent(`refreshChartData-${m.name}`, { detail: cleanCharts[m.prop] }));
                                }
                            });
                        }, 150);
                    });
                } catch (e) { console.error('Error refreshing lab charts:', e); }
            });
        </script>
    @endscript
</x-content>
