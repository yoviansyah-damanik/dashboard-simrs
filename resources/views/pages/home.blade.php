<x-content>
    <x-breadcrumb title="Beranda" :items="[['title' => 'Beranda']]" />

    <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Section 1: Welcome Hero -->
        <div class="relative p-10 rounded-[3.5rem] bg-slate-900 text-white overflow-hidden shadow-2xl shadow-slate-900/20 group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-indigo-900/30"></div>
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] -mr-48 -mt-48 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-indigo-500/10 rounded-full blur-[100px] -ml-24 -mb-24"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-500/10 backdrop-blur-md rounded-2xl border border-emerald-500/20 text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        SIMRS Command Center
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-5xl md:text-6xl font-black tracking-tighter leading-[0.9] text-transparent bg-clip-text bg-gradient-to-r from-white via-white to-emerald-200">
                            Selamat Datang, <br/>
                            <span class="text-emerald-400">{{ auth()->user()->name }}</span>
                        </h1>
                    </div>
                    <p class="max-w-md text-slate-400 text-base font-medium leading-relaxed">
                        Pantau ekosistem digital rumah sakit Anda dengan presisi tinggi melalui dashboard analitik real-time terintegrasi.
                    </p>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="p-6 bg-white/5 backdrop-blur-2xl rounded-[2.5rem] border border-white/10 shadow-2xl group-hover:border-emerald-500/30 transition-colors duration-700">
                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <div class="text-4xl font-black text-white leading-none mb-1">{{ now()->format('d') }}</div>
                                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 opacity-80">{{ now()->format('M Y') }}</div>
                            </div>
                            <div class="w-px h-12 bg-white/10"></div>
                            <div class="text-center">
                                <div class="text-4xl font-black text-white leading-none mb-1">{{ now()->format('H:i') }}</div>
                                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Main KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Outpatient KPI -->
            <a href="{{ route('outpatient.recap') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-2xl hover:border-primary/30 transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full group-hover:bg-primary/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-primary/5">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-3xl"></span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->outpatientToday) }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pasien Rawat Jalan</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[8px] font-bold uppercase">Hari Ini</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Inpatient KPI -->
            <a href="{{ route('inpatient.recap') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-2xl hover:border-indigo-500/30 transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full group-hover:bg-indigo-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-indigo-100">
                        <span class="icon-[solar--hospital-bold-duotone] text-3xl"></span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->inpatientActive) }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pasien Rawat Inap</span>
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[8px] font-bold uppercase">Sedang Inap</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Bed Occupancy KPI -->
            <a href="{{ route('room') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-2xl hover:border-emerald-500/30 transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:bg-emerald-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-emerald-100">
                        <span class="icon-[solar--bed-bold-duotone] text-3xl"></span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->roomStats['available']) }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kamar Tersedia</span>
                            <span class="text-[10px] font-bold text-gray-300">/ {{ $this->roomStats['total'] }} Bed</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Polyclinic KPI -->
            <a href="{{ route('polyclinic') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-2xl hover:border-amber-500/30 transition-all duration-500 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:bg-amber-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-amber-100">
                        <span class="icon-[solar--stethoscope-bold-duotone] text-3xl"></span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->polyclinicCount) }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit Poliklinik</span>
                            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-600 text-[8px] font-bold uppercase">Aktif</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Section 3: Trends & Operational Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Inpatient Trend -->
            <div class="bg-white dark:bg-boxdark p-8 rounded-[3rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col h-[400px]">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tighter leading-none">Tren Rawat Inap</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">15 Hari Terakhir</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-[9px] font-black uppercase text-gray-500">Masuk</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span class="text-[9px] font-black uppercase text-gray-500">Keluar</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 relative" x-data="chartComponent('chartHomeInTrend', 'line', {{ json_encode($this->inpatientTrend) }})">
                    <canvas id="chartHomeInTrend"></canvas>
                </div>
            </div>

            <!-- Outpatient Trend -->
            <div class="bg-white dark:bg-boxdark p-8 rounded-[3rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col h-[400px]">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tighter leading-none">Tren Rawat Jalan</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">15 Hari Terakhir</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <span class="text-[9px] font-black uppercase text-gray-500">Kunjungan</span>
                    </div>
                </div>
                <div class="flex-1 relative" x-data="chartComponent('chartHomeOutTrend', 'line', {{ json_encode($this->outpatientTrend) }})">
                    <canvas id="chartHomeOutTrend"></canvas>
                </div>
            </div>
        </div>

        <!-- Section 4: Clinical Indicators & Shortcuts -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Indikator Pelayanan -->
            <div class="xl:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Indicator Header (Left sidebar for this row) -->
                <div class="md:col-span-1 bg-slate-900 p-8 rounded-[3rem] text-white overflow-hidden relative group flex flex-col justify-between">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>
                    <div class="relative z-10">
                        <h4 class="text-xl font-black uppercase tracking-tighter mb-1">Indikator</h4>
                        <h4 class="text-xl font-black uppercase tracking-tighter mb-4">Pelayanan</h4>
                        <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Bulan Ini</p>
                    </div>
                    <div class="relative z-10 mt-8 p-4 bg-white/5 rounded-2xl border border-white/10 text-center">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Bed</div>
                        <div class="text-2xl font-black text-white leading-none">{{ $this->clinicalIndicators['beds'] }}</div>
                    </div>
                </div>

                <!-- Indicator Metrics (3 columns) -->
                <div class="md:col-span-3 grid grid-cols-2 gap-6">
                    <!-- BOR -->
                    <div class="bg-white dark:bg-boxdark p-6 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">BOR</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase leading-tight">Bed Occupancy Ratio</div>
                        </div>
                        <div class="mt-4">
                            <div class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ $this->clinicalIndicators['bor'] }}<span class="text-lg ml-1 opacity-40">%</span></div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-3 overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ min($this->clinicalIndicators['bor'], 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ALOS -->
                    <div class="bg-white dark:bg-boxdark p-6 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">ALOS</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase leading-tight">Avg Length of Stay</div>
                        </div>
                        <div class="mt-4">
                            <div class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ $this->clinicalIndicators['alos'] }}<span class="text-lg ml-1 opacity-40 text-gray-400">Hari</span></div>
                        </div>
                    </div>

                    <!-- TOI -->
                    <div class="bg-white dark:bg-boxdark p-6 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">TOI</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase leading-tight">Turn Over Interval</div>
                        </div>
                        <div class="mt-4">
                            <div class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ $this->clinicalIndicators['toi'] }}<span class="text-lg ml-1 opacity-40 text-gray-400">Hari</span></div>
                        </div>
                    </div>

                    <!-- BTO -->
                    <div class="bg-white dark:bg-boxdark p-6 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">BTO</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase leading-tight">Bed Turn Over</div>
                        </div>
                        <div class="mt-4">
                            <div class="text-4xl font-black text-gray-800 dark:text-white tracking-tighter">{{ $this->clinicalIndicators['bto'] }}<span class="text-lg ml-1 opacity-40 text-gray-400">Kali</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Akses Cepat (Quick Shortcuts) -->
            <div class="xl:col-span-1 bg-white dark:bg-boxdark p-8 rounded-[3rem] border border-stroke dark:border-strokedark shadow-sm">
                <h4 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tighter mb-6">Akses Cepat</h4>
                <div class="space-y-4">
                    <a href="{{ route('outpatient.recap') }}" wire:navigate class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-meta-4 hover:bg-primary/10 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-boxdark flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-500">
                                <span class="icon-[solar--clipboard-list-bold-duotone] text-2xl text-primary"></span>
                            </div>
                            <span class="text-sm font-black uppercase tracking-tight text-gray-700 dark:text-gray-300">Rawat Jalan</span>
                        </div>
                        <span class="icon-[solar--alt-arrow-right-bold] text-primary opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></span>
                    </a>
                    
                    <a href="{{ route('inpatient.recap') }}" wire:navigate class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-meta-4 hover:bg-indigo-500/10 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-boxdark flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-500">
                                <span class="icon-[solar--hospital-bold-duotone] text-2xl text-indigo-500"></span>
                            </div>
                            <span class="text-sm font-black uppercase tracking-tight text-gray-700 dark:text-gray-300">Rawat Inap</span>
                        </div>
                        <span class="icon-[solar--alt-arrow-right-bold] text-indigo-500 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></span>
                    </a>

                    <a href="{{ route('room') }}" wire:navigate class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-meta-4 hover:bg-emerald-500/10 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-boxdark flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-500">
                                <span class="icon-[solar--bed-bold-duotone] text-2xl text-emerald-500"></span>
                            </div>
                            <span class="text-sm font-black uppercase tracking-tight text-gray-700 dark:text-gray-300">Status Kamar</span>
                        </div>
                        <span class="icon-[solar--alt-arrow-right-bold] text-emerald-500 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            Alpine.data('chartComponent', (chartId, chartType, initialData, barType = 'x') => ({
                chart: null,
                init() {
                    this.renderChart(initialData);
                    window.addEventListener(`refreshChartData-${chartId}`, (e) => {
                        this.renderChart(e.detail);
                    });
                },
                renderChart(data) {
                    const canvas = document.getElementById(chartId);
                    if (!canvas) return;
                    setTimeout(() => {
                        if (this.chart) {
                            this.chart.destroy();
                            this.chart = null;
                        }
                        const ctx = canvas.getContext('2d');
                        if (!ctx) return;
                        this.chart = new Chart(ctx, {
                            type: chartType,
                            data: {
                                labels: [...(data.labels || [])],
                                datasets: (data.datasets || []).map(ds => ({ ...ds }))
                            },
                            options: {
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        grid: { color: 'rgba(0,0,0,0.03)' }
                                    },
                                    x: { 
                                        grid: { display: false }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                },
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: { duration: 800 }
                            }
                        });
                    }, 50);
                }
            }));
        </script>
    @endscript
</x-content>
