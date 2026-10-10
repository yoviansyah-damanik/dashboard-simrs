<x-content>
    <x-breadcrumb title="Rawat Jalan" :items="[['title' => 'Rawat Jalan'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Control Toolbar: Switcher & Filter Periode -->
        <div class="p-3 sm:p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                <!-- Sisi Kiri: Switcher Tampilan (Tabel / Grafik) -->
                <div class="flex flex-wrap items-center gap-3">
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
                                @foreach ($this->months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 outline-none">
                                @foreach ($this->years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-emerald-500 outline-none">
                            @foreach ($this->years as $y)
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

        <!-- KPI Area: Summary Banner & 4 Cards -->
        <div class="space-y-4">
            <!-- 3-Panel Summary Banner -->
            <div class="bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 dark:from-emerald-950 dark:via-slate-900 dark:to-slate-950 p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-stretch gap-6 group border border-emerald-500/20">
                <!-- Background Ambient Glow -->
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Panel 1: Total Volume -->
                <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-1.5">Total Kunjungan</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl sm:text-6xl font-black leading-none tracking-tight">{{ number_format($this->overallStats['total']) }}</h3>
                        <span class="text-xs font-bold text-emerald-200">Jiwa</span>
                    </div>
                    <div class="flex items-center gap-5 mt-5">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-emerald-200/80 uppercase tracking-wider">Laki-laki</span>
                            <span class="text-xl font-black text-cyan-300">{{ number_format($this->overallStats['gender']['laki']) }}</span>
                        </div>
                        <div class="w-px h-7 bg-white/20"></div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-emerald-200/80 uppercase tracking-wider">Perempuan</span>
                            <span class="text-xl font-black text-rose-300">{{ number_format($this->overallStats['gender']['perempuan']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Age Demographics Grid -->
                <div class="relative z-10 text-white flex-1 border-t lg:border-t-0 lg:border-x border-white/10 pt-4 lg:pt-0 lg:px-8 hidden sm:block">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-3 text-center">Rincian Kelompok Usia & Gender</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach($this->overallStats['age_groups'] as $age)
                            <div class="flex flex-col p-2.5 bg-white/10 dark:bg-white/5 rounded-xl border border-white/10 backdrop-blur-sm hover:bg-white/15 transition-all">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-emerald-100 uppercase tracking-tight truncate w-3/4">{{ $age->kelompok }}</span>
                                    <span class="text-[11px] font-black bg-white/20 px-1.5 py-0.5 rounded">{{ number_format($age->total) }}</span>
                                </div>
                                <div class="flex items-center gap-2 pt-1 border-t border-white/10">
                                    <div class="flex-1 flex items-center gap-1 justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        <span class="text-[11px] font-bold text-cyan-200">{{ number_format($age->laki) }}</span>
                                    </div>
                                    <div class="flex-1 flex items-center gap-1 justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        <span class="text-[11px] font-bold text-rose-200">{{ number_format($age->perempuan) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Panel 3: Efficiency Metric -->
                <div class="relative z-10 text-white min-w-[180px] flex flex-col justify-center items-center text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-2">Penyelesaian</p>
                    <h3 class="text-4xl sm:text-5xl font-black leading-none">{{ number_format($this->overallStats['completion_rate'], 1) }}%</h3>
                    <div class="mt-3.5 px-3.5 py-1 bg-white/10 rounded-full border border-white/10">
                        <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider">Total Rate Periode</span>
                    </div>
                    <span class="icon-[solar--chart-square-bold-duotone] text-6xl opacity-10 absolute -right-3 -bottom-3 rotate-12 pointer-events-none"></span>
                </div>
            </div>

            <!-- 4-Card KPI Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                <!-- 1. Pasien Baru -->
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between group hover:border-emerald-500/40 transition-all">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Pasien Baru</span>
                        <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($this->recapData->sum('pasien_baru')) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--user-plus-bold-duotone] text-xl"></span>
                    </div>
                </div>

                <!-- 2. Pasien Lama -->
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between group hover:border-slate-400 transition-all">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Pasien Lama</span>
                        <h4 class="text-2xl font-black text-slate-800 dark:text-slate-200 mt-1">{{ number_format($this->recapData->sum('pasien_lama')) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--user-bold-duotone] text-xl"></span>
                    </div>
                </div>

                <!-- 3. Sudah Periksa -->
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between group hover:border-teal-500/40 transition-all">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Sudah Periksa</span>
                        <h4 class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-1">{{ number_format($this->recapData->sum('sudah_periksa')) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--check-circle-bold-duotone] text-xl"></span>
                    </div>
                </div>

                <!-- 4. Belum Periksa -->
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between group hover:border-amber-500/40 transition-all">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Belum Periksa</span>
                        <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($this->recapData->sum('belum_periksa')) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <span class="icon-[solar--clock-circle-bold-duotone] text-xl"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area: Chart / List -->
        <div class="space-y-6">
            @if ($mainView === 'chart')
                <div class="space-y-6">
                    <!-- Trend & Poly Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-slate-900/80 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-5 flex items-center gap-2">
                                <span class="icon-[solar--graph-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                Tren Kunjungan Harian
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-out-trend">
                                <x-chart chartId="chartOutTrend" chartType="line" :labels="$this->overallStats['charts']['trend']['labels']" :datasets="$this->overallStats['charts']['trend']['datasets']" />
                            </div>
                        </div>

                        <div class="bg-white dark:bg-slate-900/80 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-5 flex items-center gap-2">
                                <span class="icon-[solar--hospital-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                10 Poliklinik Terbanyak
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-out-poly">
                                <x-chart chartId="chartOutPoly" chartType="bar" barType="x" :labels="$this->overallStats['charts']['poly_distribution']['labels']"
                                    :datasets="$this->overallStats['charts']['poly_distribution']['datasets']" />
                            </div>
                        </div>
                    </div>

                    <!-- Demographics Section -->
                    <div class="bg-white dark:bg-slate-900/80 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Analisis Demografi Pasien</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 rounded-xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 text-center">
                                    Sebaran Jenis Kelamin
                                </h4>
                                <div class="h-64 flex items-center justify-center" wire:ignore wire:key="chart-out-gender">
                                    <x-chart chartId="chartOutGender" chartType="doughnut" :labels="$this->patientDemographics['charts']['gender']['labels']"
                                        :datasets="$this->patientDemographics['charts']['gender']['datasets']" />
                                </div>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 text-center">
                                    Top 5 Penjamin
                                </h4>
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
                <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[700px]">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                                    <th class="px-5 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Poliklinik</th>
                                    <th class="px-5 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dokter</th>
                                    <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">Total</th>
                                    <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center text-slate-500 dark:text-slate-400">Status Pasien</th>
                                    <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center text-slate-500 dark:text-slate-400">Pemeriksaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                @forelse($this->recapData as $item)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-200 uppercase tracking-tight">{{ $item->nm_poli }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-xs sm:text-sm text-slate-600 dark:text-slate-400">{{ $item->nm_dokter }}</div>
                                        </td>
                                        <td class="px-4 py-3.5 text-center bg-emerald-50/30 dark:bg-emerald-950/10">
                                            <span class="inline-flex items-center justify-center min-w-[2.25rem] px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-xs font-bold font-mono">
                                                {{ number_format($item->total_reg) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-700 dark:text-blue-400 text-xs font-semibold">
                                                    <span>Baru:</span>
                                                    <span class="font-bold font-mono">{{ number_format($item->pasien_baru) }}</span>
                                                </div>
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                                    <span>Lama:</span>
                                                    <span class="font-bold font-mono">{{ number_format($item->pasien_lama) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                                                    <span>Sudah:</span>
                                                    <span class="font-bold font-mono">{{ number_format($item->sudah_periksa) }}</span>
                                                </div>
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-semibold">
                                                    <span>Belum:</span>
                                                    <span class="font-bold font-mono">{{ number_format($item->belum_periksa) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center gap-2 text-slate-400">
                                                <span class="icon-[solar--document-add-bold-duotone] text-5xl"></span>
                                                <p class="text-xs font-medium">Tidak ada data kunjungan untuk periode ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($this->recapData->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800 font-bold text-xs sm:text-sm">
                                        <td class="px-5 py-3.5 text-slate-900 dark:text-white uppercase font-black" colspan="2">TOTAL</td>
                                        <td class="px-4 py-3.5 text-center font-black font-mono text-emerald-700 dark:text-emerald-400 bg-emerald-500/10">
                                            {{ number_format($this->recapData->sum('total_reg')) }}
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-center gap-3 text-xs">
                                                <span class="text-blue-600 font-bold font-mono">B: {{ number_format($this->recapData->sum('pasien_baru')) }}</span>
                                                <span class="text-slate-500 font-bold font-mono">L: {{ number_format($this->recapData->sum('pasien_lama')) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-center gap-3 text-xs">
                                                <span class="text-emerald-600 font-bold font-mono">S: {{ number_format($this->recapData->sum('sudah_periksa')) }}</span>
                                                <span class="text-amber-600 font-bold font-mono">B: {{ number_format($this->recapData->sum('belum_periksa')) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
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
                    _resizeObserver: null,
                    _currentData: null,

                    init() {
                        this._currentData = {
                            labels: JSON.parse(JSON.stringify(initialLabels || [])),
                            datasets: JSON.parse(JSON.stringify(initialDatasets || []))
                        };

                        this._resizeObserver = new ResizeObserver((entries) => {
                            for (let entry of entries) {
                                if (entry.contentRect.width > 0 && entry.contentRect.height > 0) {
                                    if (!this.chart || this.chart.width === 0 || this.chart.height === 0) {
                                        this.initChart(this._currentData);
                                    } else {
                                        try { this.chart.resize(); } catch (e) {}
                                    }
                                }
                            }
                        });

                        if (this.$refs.chartContainer) {
                            this._resizeObserver.observe(this.$refs.chartContainer);
                        }

                        this.initChart(this._currentData);

                        this._updateHandler = (event) => {
                            const payload = JSON.parse(JSON.stringify(event.detail));
                            if (!payload || !payload.labels) return;
                            this._currentData = payload;

                            const canvas = this.$refs.chartContainer ? this.$refs.chartContainer.querySelector('canvas') : null;
                            const existingChart = canvas ? Chart.getChart(canvas) : null;

                            if (existingChart && document.body.contains(canvas) && canvas.offsetWidth > 0 && canvas.offsetHeight > 0) {
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
                        if (this._resizeObserver) {
                            this._resizeObserver.disconnect();
                            this._resizeObserver = null;
                        }
                        window.removeEventListener(`refreshChartData-${chartId}`, this._updateHandler);
                        if (this.chart) {
                            try { this.chart.destroy(); } catch (e) {}
                            this.chart = null;
                        }
                    },
                    initChart(data) {
                        if (!data || !data.labels) return;
                        this._currentData = data;
                        if (this._initTimeout) clearTimeout(this._initTimeout);
                        this._initTimeout = setTimeout(() => {
                            if (!this.$refs.chartContainer) return;
                            if (this.$refs.chartContainer.offsetWidth === 0 && this.$refs.chartContainer.offsetHeight === 0) {
                                return;
                            }
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
                            const existing = Chart.getChart(canvas);
                            if (existing) {
                                try { existing.destroy(); } catch (e) {}
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
                                        animation: { duration: 400 }
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