<x-content>
    <x-breadcrumb title="Rawat Inap" :items="[['title' => 'Rawat Inap'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Control Toolbar: Main Tab Switcher, Sub-controls & Filter Periode -->
        <div class="p-3 sm:p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md space-y-3">
            <!-- Row 1: Subtitle & Main Tab Switcher -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Rekapitulasi Rawat Inap</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-0.5">Monitoring sensus pasien aktif, rekap indikator, dan snapshot tempat tidur secara real-time.</p>
                </div>

                <!-- Main Tabs Switcher -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl shrink-0 self-start lg:self-auto">
                    <button wire:click="switchTab('current_patients')"
                        class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainTab === 'current_patients' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-base"></span>
                        <span>Pasien Dirawat</span>
                    </button>
                    <button wire:click="switchTab('recap')"
                        class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainTab === 'recap' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                        <span class="icon-[solar--folder-with-files-bold-duotone] text-base"></span>
                        <span>Rekapitulasi</span>
                    </button>
                    <button wire:click="switchTab('snapshot')"
                        class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainTab === 'snapshot' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                        <span class="icon-[solar--screencast-2-bold-duotone] text-base"></span>
                        <span>Snapshot Bed</span>
                    </button>
                </div>
            </div>

            <!-- Row 2: Contextual Controls & Filters (Divided by Active Tab) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <!-- Sisi Kiri: Switcher View atau Search Bar -->
                <div class="flex flex-wrap items-center gap-3">
                    @if ($mainTab === 'current_patients')
                        <div class="relative w-full sm:w-80">
                            <input type="text" wire:model.live.debounce.300ms="searchPatient"
                                placeholder="Cari Nama Pasien / No. RM..."
                                class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-sm">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                                <span class="icon-[solar--magnifer-bold-duotone] text-base"></span>
                            </div>
                        </div>
                    @endif

                    @if ($mainTab === 'recap')
                        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl shrink-0">
                            <button wire:click="$set('mainView', 'list')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainView === 'list' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                                <span class="icon-[solar--list-bold-duotone] text-base"></span>
                                <span>Tabel</span>
                            </button>
                            <button wire:click="$set('mainView', 'chart')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                                <span class="icon-[solar--chart-bold-duotone] text-base"></span>
                                <span>Grafik</span>
                            </button>
                        </div>
                    @endif

                    @if ($mainTab === 'snapshot')
                        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl shrink-0">
                            <button wire:click="$set('snapshotView', 'list')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $snapshotView === 'list' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                                <span class="icon-[solar--widget-bold-duotone] text-base"></span>
                                <span>Kartu</span>
                            </button>
                            <button wire:click="$set('snapshotView', 'chart')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $snapshotView === 'chart' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                                <span class="icon-[solar--chart-bold-duotone] text-base"></span>
                                <span>Grafik</span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Sisi Kanan: Pilihan Periode Waktu (Hanya untuk Rekapitulasi) -->
                @if ($mainTab === 'recap')
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
                @endif
            </div>
        </div>

        @if ($mainTab === 'current_patients')
            <!-- ========================================== -->
            <!-- TAB 1: PASIEN DIRAWAT (REAL-TIME CENSUS)   -->
            <!-- ========================================== -->
            <div wire:key="tab-current-patients" class="space-y-6">
                <!-- Sensus Banner Card -->
                <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 backdrop-blur-md">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shrink-0">
                            <span class="icon-[solar--users-group-two-rounded-bold-duotone]"></span>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">Pasien Dirawat Saat Ini</h3>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Sensus real-time kamar, bangsal, dan lama hari rawat inap.</p>
                        </div>
                    </div>
                    <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2">
                        <span class="text-3xl sm:text-4xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($currentPatients->count()) }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Pasien Aktif</span>
                    </div>
                </div>

                <!-- Tabel Pasien Dirawat -->
                <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden backdrop-blur-md">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="px-5 py-3.5">Pasien</th>
                                    <th class="px-4 py-3.5 text-center">No. RM</th>
                                    <th class="px-4 py-3.5 text-center">Kamar / Bangsal</th>
                                    <th class="px-4 py-3.5 text-center">Kelas</th>
                                    <th class="px-4 py-3.5 text-center">Penjamin</th>
                                    <th class="px-4 py-3.5 text-center">Tgl Masuk</th>
                                    <th class="px-5 py-3.5 text-center">Lama Rawat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @forelse($currentPatients as $p)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                                        <td class="px-5 py-3.5">
                                            <div class="flex flex-col">
                                                <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $p->nm_pasien }}</span>
                                                <span class="text-[11px] font-mono text-slate-400 uppercase tracking-tight">{{ $p->no_rawat }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="px-2 py-0.5 rounded-md font-mono text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $p->no_rkm_medis }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">{{ $p->nm_bangsal }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">{{ $p->kelas }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ $p->png_jawab }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="flex flex-col">
                                                <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($p->tgl_masuk)->format('d/m/Y') }}</span>
                                                <span class="text-[11px] text-slate-400 font-mono">{{ $p->jam_masuk }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $p->lama_inap > 5 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' }}">
                                                {{ $p->lama_inap }} Hari
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <span class="icon-[solar--folder-error-bold-duotone] text-4xl text-slate-300 dark:text-slate-600"></span>
                                                <p class="text-sm font-semibold">Tidak ada pasien yang ditemukan dalam daftar rawat inap saat ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($currentPatients->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-slate-50 dark:bg-slate-800/80 border-t-2 border-slate-200 dark:border-slate-700 font-bold text-xs sm:text-sm">
                                        <td class="px-5 py-3.5 text-slate-800 dark:text-white" colspan="5">
                                            TOTAL / RATA-RATA
                                        </td>
                                        <td class="px-4 py-3.5 text-center text-slate-700 dark:text-slate-300 font-mono">
                                            {{ number_format($currentPatients->count()) }} Pasien
                                        </td>
                                        <td class="px-5 py-3.5 text-center text-emerald-600 dark:text-emerald-400 font-mono font-black">
                                            {{ number_format($currentPatients->avg('lama_inap'), 1) }} Hari
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

        @elseif($mainTab === 'recap')
            <!-- ========================================== -->
            <!-- TAB 2: REKAPITULASI (STATISTIK & INDIKATOR) -->
            <!-- ========================================== -->
            <div wire:key="tab-recap" class="space-y-6">
                <!-- Row 1: Primary KPIs Banner & Trends -->
                <div class="grid grid-cols-1 gap-6 {{ $mainView === 'chart' ? 'lg:grid-cols-3' : '' }}">
                    <!-- Gradient Banner -->
                    <div class="bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 dark:from-emerald-950 dark:via-slate-900 dark:to-slate-950 p-6 rounded-3xl shadow-xl relative overflow-hidden group border border-emerald-500/20 {{ $mainView === 'list' ? 'grid grid-cols-1 md:grid-cols-12 gap-6' : 'flex flex-col justify-between min-h-[300px]' }}">
                        <!-- Background Ambient Glow -->
                        <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>

                        @if ($mainView === 'list')
                            <!-- Panel 1: Total Pasien & Gender -->
                            <div class="relative z-10 text-white flex flex-col justify-center md:col-span-3">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-1.5">Total Pasien</p>
                                <div class="flex items-baseline gap-2">
                                    <h3 class="text-5xl sm:text-6xl font-black leading-none tracking-tight">{{ number_format($recapData->sum('total_pasien')) }}</h3>
                                    <span class="text-xs font-bold text-emerald-200">Jiwa</span>
                                </div>
                                <div class="flex items-center gap-5 mt-5">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-bold text-emerald-200/80 uppercase tracking-wider">Laki-laki</span>
                                        <span class="text-xl font-black text-cyan-300">{{ number_format($recapData->sum('total_laki')) }}</span>
                                    </div>
                                    <div class="w-px h-7 bg-white/20"></div>
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-bold text-emerald-200/80 uppercase tracking-wider">Perempuan</span>
                                        <span class="text-xl font-black text-pink-300">{{ number_format($recapData->sum('total_perempuan')) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 2: Age Group Breakdown -->
                            <div class="relative z-10 text-white md:col-span-6 border-y md:border-y-0 md:border-x border-white/10 py-4 md:py-0 md:px-6 flex flex-col justify-center">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-3 text-center">Kelompok Usia & Gender</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    @foreach ($demographics['age'] as $age)
                                        <div class="p-2.5 bg-white/10 dark:bg-black/20 rounded-xl border border-white/10 shadow-sm backdrop-blur-sm hover:bg-white/15 transition-all">
                                            <div class="flex items-center justify-between gap-1 mb-1.5">
                                                <span class="text-[11px] font-bold text-emerald-100 truncate">{{ str_replace('Pasien ', '', $age->kelompok_umur) }}</span>
                                                <span class="text-xs font-black bg-white/20 px-1.5 py-0.5 rounded-md">{{ number_format($age->total) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between text-[11px] font-bold pt-1.5 border-t border-white/10">
                                                <span class="text-cyan-300">L: {{ number_format($age->laki) }}</span>
                                                <span class="text-pink-300">P: {{ number_format($age->perempuan) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Panel 3: Total Hari Perawatan (HP) -->
                            <div class="relative z-10 text-white flex flex-col justify-center items-center text-center md:col-span-3">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-1.5">Hari Perawatan</p>
                                <h3 class="text-4xl sm:text-5xl font-black leading-none font-mono tracking-tight text-white">{{ number_format($recapData->sum('total_hp')) }}</h3>
                                <div class="mt-3 px-3.5 py-1 bg-white/15 rounded-full border border-white/10">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100">Total HP Periode</span>
                                </div>
                            </div>
                        @else
                            <!-- Compact View for Chart Mode -->
                            <div class="relative z-10 text-white">
                                <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">Total Pasien (Periode)</p>
                                <h3 class="text-5xl font-black mt-2 font-mono">{{ number_format($recapData->sum('total_pasien')) }}</h3>
                                <div class="flex items-center gap-6 mt-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-cyan-300"></span>
                                        <span class="text-xs font-bold">L: {{ number_format($recapData->sum('total_laki')) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-pink-300"></span>
                                        <span class="text-xs font-bold">P: {{ number_format($recapData->sum('total_perempuan')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-auto pt-6 border-t border-white/10 flex items-center justify-between text-white relative z-10">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-emerald-200 uppercase tracking-widest">Total Hari Perawatan</span>
                                    <span class="text-2xl font-black font-mono">{{ number_format($recapData->sum('total_hp')) }} HP</span>
                                </div>
                                <span class="icon-[solar--users-group-rounded-bold] text-4xl opacity-40"></span>
                            </div>
                        @endif
                    </div>

                    <!-- Trend Admission/Discharge - ONLY IN CHART MODE -->
                    @if ($mainView === 'chart')
                        <div class="lg:col-span-2 bg-white dark:bg-slate-900/80 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                    <span class="icon-[solar--graph-up-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Tren Pasien Masuk & Keluar
                                </h4>
                                <div class="flex items-center gap-4 text-xs font-bold">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                        <span class="text-slate-600 dark:text-slate-300">Masuk</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-slate-600 dark:text-slate-300">Keluar</span>
                                    </div>
                                </div>
                            </div>
                            <div class="h-[220px]" wire:ignore wire:key="chart-recap-trend">
                                <x-chart chartId="chartTrendInOut" chartType="line" :labels="$overall['charts']['trend']['labels']" :datasets="$overall['charts']['trend']['datasets']" />
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Row 2: Summary Stats (Volume Pasien & Indikator RS) -->
                <div class="grid grid-cols-1 lg:grid-cols-7 gap-4">
                    <!-- Group 1: Volume Pasien (3 Cards) -->
                    <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Admissions Period -->
                        <div class="bg-white dark:bg-slate-900/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between group hover:border-indigo-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Masuk (Periode)</span>
                            <div class="flex items-center justify-between mt-2">
                                <h4 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-mono">{{ number_format($snapshotStats['admissions']) }}</h4>
                                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                                    <span class="icon-[solar--user-plus-bold-duotone]"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Discharges Period -->
                        <div class="bg-white dark:bg-slate-900/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between group hover:border-emerald-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Keluar (Periode)</span>
                            <div class="flex items-center justify-between mt-2">
                                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($snapshotStats['discharges']) }}</h4>
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                                    <span class="icon-[solar--user-check-bold-duotone]"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Dinas / TNI Patients -->
                        <div class="bg-white dark:bg-slate-900/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between group hover:border-rose-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pasien Dinas</span>
                            <div class="flex items-center justify-between mt-2">
                                <h4 class="text-2xl font-black text-rose-600 dark:text-rose-400 font-mono">{{ number_format($snapshotStats['tni_patients']) }}</h4>
                                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg">
                                    <span class="icon-[solar--medal-ribbon-star-bold-duotone]"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Performance Indicators (5 Cards: BOR, ALOS, BTO, TOI, GDR) -->
                    <div class="lg:col-span-4 grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div class="bg-white dark:bg-slate-900/80 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center group hover:border-emerald-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">BOR</span>
                            <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">{{ number_format($overall['bor'], 1) }}%</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-tight">Tingkat Hunian</p>
                        </div>
                        <div class="bg-white dark:bg-slate-900/80 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center group hover:border-indigo-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">ALOS</span>
                            <h4 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-1">{{ number_format($overall['alos'], 1) }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-tight">Rata-rata Rawat</p>
                        </div>
                        <div class="bg-white dark:bg-slate-900/80 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center group hover:border-amber-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">BTO</span>
                            <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">{{ number_format($overall['bto'], 1) }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-tight">Perputaran TT</p>
                        </div>
                        <div class="bg-white dark:bg-slate-900/80 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center group hover:border-sky-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">TOI</span>
                            <h4 class="text-2xl font-black text-sky-600 dark:text-sky-400 font-mono mt-1">{{ number_format($overall['toi'], 1) }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-tight">Hari Kosong TT</p>
                        </div>
                        <div class="col-span-2 sm:col-span-1 bg-white dark:bg-slate-900/80 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center group hover:border-rose-500/40 transition-all backdrop-blur-md">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">GDR</span>
                            <h4 class="text-2xl font-black text-rose-600 dark:text-rose-400 font-mono mt-1">{{ number_format($overall['gdr'], 1) }}‰</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-tight">Angka Kematian</p>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Ward Breakdown (Tabel vs Grafik) -->
                <div class="space-y-4">
                    @if ($mainView === 'list')
                        <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden backdrop-blur-md">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse min-w-[1200px]">
                                    <thead>
                                        <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                            <th class="px-4 py-3.5">Bangsal</th>
                                            <th class="px-3 py-3.5 text-center">Kelas</th>
                                            <th class="px-3 py-3.5 text-center bg-slate-100/50 dark:bg-slate-800/80">TT</th>
                                            <th class="px-3 py-3.5 text-center text-indigo-600 dark:text-indigo-400">Terisi</th>
                                            <th class="px-3 py-3.5 text-center bg-emerald-500/5">Total</th>
                                            <th class="px-3 py-3.5 text-center text-cyan-600 dark:text-cyan-400">L</th>
                                            <th class="px-3 py-3.5 text-center text-pink-600 dark:text-pink-400">P</th>
                                            <th class="px-3 py-3.5 text-center text-emerald-600 dark:text-emerald-400">Pulang</th>
                                            <th class="px-3 py-3.5 text-center text-amber-600 dark:text-amber-400">Rujuk</th>
                                            <th class="px-3 py-3.5 text-center">APS</th>
                                            <th class="px-3 py-3.5 text-center text-rose-600 dark:text-rose-400">Mati</th>
                                            <th class="px-3 py-3.5 text-center">HP</th>
                                            <th class="px-3 py-3.5 text-center bg-purple-500/5">ALOS</th>
                                            <th class="px-3 py-3.5 text-center bg-amber-500/5">BTO</th>
                                            <th class="px-3 py-3.5 text-center bg-emerald-500/5">BOR (%)</th>
                                            <th class="px-3 py-3.5 text-center bg-sky-500/5">TOI</th>
                                            <th class="px-3 py-3.5 text-center bg-rose-500/5">GDR (‰)</th>
                                            <th class="px-3 py-3.5 text-center bg-red-500/5">NDR (‰)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                        @forelse($overall['ward_groups'] as $group)
                                            {{-- Baris grup: total gabungan per bangsal (semua kelas) --}}
                                            <tr class="bg-slate-50/70 dark:bg-slate-800/50 text-xs sm:text-sm font-bold">
                                                <td class="px-4 py-3 text-slate-800 dark:text-white" colspan="2">
                                                    {{ $group['nm_bangsal'] }}
                                                    <span class="ml-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">(Total / Rata-rata)</span>
                                                </td>
                                                <td class="px-3 py-3 text-center font-mono bg-slate-100 dark:bg-slate-800/80">{{ number_format($group['kapasitas']) }}</td>
                                                <td class="px-3 py-3 text-center text-indigo-600 dark:text-indigo-400 font-mono">{{ number_format($group['terisi']) }}</td>
                                                <td class="px-3 py-3 text-center bg-emerald-500/5 font-mono text-slate-800 dark:text-white">{{ number_format($group['total_pasien']) }}</td>
                                                <td class="px-3 py-3 text-center text-cyan-600 dark:text-cyan-400 font-mono">{{ number_format($group['total_laki']) }}</td>
                                                <td class="px-3 py-3 text-center text-pink-600 dark:text-pink-400 font-mono">{{ number_format($group['total_perempuan']) }}</td>
                                                <td class="px-3 py-3 text-center text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($group['jumlah_pulang']) }}</td>
                                                <td class="px-3 py-3 text-center text-amber-600 dark:text-amber-400 font-mono">{{ number_format($group['jumlah_dirujuk']) }}</td>
                                                <td class="px-3 py-3 text-center text-slate-600 dark:text-slate-300 font-mono">{{ number_format($group['jumlah_aps']) }}</td>
                                                <td class="px-3 py-3 text-center text-rose-600 dark:text-rose-400 font-mono">{{ number_format($group['jumlah_meninggal']) }}</td>
                                                <td class="px-3 py-3 text-center font-mono text-slate-600 dark:text-slate-300">{{ number_format($group['total_hp']) }}</td>
                                                <td class="px-3 py-3 text-center font-mono bg-purple-500/5 text-purple-600 dark:text-purple-400">{{ number_format($group['alos'], 1) }}</td>
                                                <td class="px-3 py-3 text-center font-mono bg-amber-500/5 text-amber-600 dark:text-amber-400">{{ number_format($group['bto'], 1) }}</td>
                                                <td class="px-3 py-3 text-center font-mono bg-emerald-500/5 {{ \App\Services\HospitalIndicatorService::isWithinRange('bor', $group['bor']) ? 'text-emerald-600 dark:text-emerald-400' : ($group['bor'] > \App\Services\HospitalIndicatorService::RANGES['bor']['max'] ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                                    {{ number_format($group['bor'], 1) }}%
                                                </td>
                                                <td class="px-3 py-3 text-center font-mono bg-sky-500/5 text-sky-600 dark:text-sky-400">{{ number_format($group['toi'], 1) }}</td>
                                                <td class="px-3 py-3 text-center font-mono bg-rose-500/5 text-rose-600 dark:text-rose-400">{{ number_format($group['gdr'], 1) }}</td>
                                                <td class="px-3 py-3 text-center font-mono bg-red-500/5 text-red-600 dark:text-red-400">{{ number_format($group['ndr'], 1) }}</td>
                                            </tr>

                                            {{-- Baris detail: rincian per kelas di dalam bangsal ini --}}
                                            @foreach ($group['rows'] as $item)
                                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors text-xs">
                                                    <td class="px-4 py-3 pl-8 text-slate-500 dark:text-slate-400">{{ $item['nm_bangsal'] }}</td>
                                                    <td class="px-3 py-3 text-center">
                                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $item['kelas'] }}</span>
                                                    </td>
                                                    <td class="px-3 py-3 text-center font-mono text-slate-700 dark:text-slate-300">{{ number_format($item['kapasitas']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-indigo-600 dark:text-indigo-400 font-semibold">{{ number_format($item['terisi']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-slate-700 dark:text-slate-200 font-semibold">{{ number_format($item['total_pasien']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-cyan-600 dark:text-cyan-400">{{ number_format($item['total_laki']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-pink-600 dark:text-pink-400">{{ number_format($item['total_perempuan']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($item['jumlah_pulang']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-amber-600 dark:text-amber-400">{{ number_format($item['jumlah_dirujuk']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-slate-500">{{ number_format($item['jumlah_aps']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-rose-600 dark:text-rose-400">{{ number_format($item['jumlah_meninggal']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-slate-400">{{ number_format($item['total_hp']) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-purple-600 dark:text-purple-400">{{ number_format($item['alos'], 1) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-amber-600 dark:text-amber-400">{{ number_format($item['bto'], 1) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono {{ \App\Services\HospitalIndicatorService::isWithinRange('bor', $item['bor']) ? 'text-emerald-600 dark:text-emerald-400' : ($item['bor'] > \App\Services\HospitalIndicatorService::RANGES['bor']['max'] ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                                        {{ number_format($item['bor'], 1) }}%
                                                    </td>
                                                    <td class="px-3 py-3 text-center font-mono text-sky-600 dark:text-sky-400">{{ number_format($item['toi'], 1) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-rose-600 dark:text-rose-400">{{ number_format($item['gdr'], 1) }}</td>
                                                    <td class="px-3 py-3 text-center font-mono text-red-600 dark:text-red-400">{{ number_format($item['ndr'], 1) }}</td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="18" class="px-4 py-12 text-center text-slate-400">
                                                    Tidak ada data rekapitulasi untuk periode ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($recapData->isNotEmpty())
                                        <tfoot>
                                            <tr class="bg-slate-100 dark:bg-slate-800 border-t-2 border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-bold">
                                                <td class="px-4 py-3.5 text-slate-800 dark:text-white" colspan="2">TOTAL / RATA-RATA</td>
                                                <td class="px-3 py-3.5 text-center font-mono bg-slate-200/50 dark:bg-slate-700/50">{{ number_format($recapData->sum('kapasitas')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-indigo-600 dark:text-indigo-400">{{ number_format($recapData->sum('terisi')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-slate-800 dark:text-white">{{ number_format($recapData->sum('total_pasien')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-cyan-600 dark:text-cyan-400">{{ number_format($recapData->sum('total_laki')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-pink-600 dark:text-pink-400">{{ number_format($recapData->sum('total_perempuan')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($recapData->sum('jumlah_pulang')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-amber-600 dark:text-amber-400">{{ number_format($recapData->sum('jumlah_dirujuk')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-slate-600 dark:text-slate-300">{{ number_format($recapData->sum('jumlah_aps')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-rose-600 dark:text-rose-400">{{ number_format($recapData->sum('jumlah_meninggal')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-slate-600 dark:text-slate-300">{{ number_format($recapData->sum('total_hp')) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-purple-600 dark:text-purple-400">{{ number_format($overall['alos'], 1) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-amber-600 dark:text-amber-400">{{ number_format($overall['bto'], 1) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($overall['bor'], 1) }}%</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-sky-600 dark:text-sky-400">{{ number_format($overall['toi'], 1) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-rose-600 dark:text-rose-400">{{ number_format($overall['gdr'], 1) }}</td>
                                                <td class="px-3 py-3.5 text-center font-mono text-red-600 dark:text-red-400">{{ number_format($overall['ndr'], 1) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @else
                        <!-- Grafik Cards Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="icon-[solar--user-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Perbandingan Total Pasien per Bangsal
                                </h4>
                                <div class="h-80" wire:ignore wire:key="chart-recap-wards">
                                    <x-chart chartId="mainChartWards" chartType="bar" barType="x" :labels="$overall['charts']['wards_patients']['labels']" :datasets="$overall['charts']['wards_patients']['datasets']" />
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="icon-[solar--chart-square-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Rata-rata BOR per Bangsal (%)
                                </h4>
                                <div class="h-80" wire:ignore wire:key="chart-recap-bor">
                                    <x-chart chartId="mainChartBOR" chartType="bar" barType="x" :labels="$overall['charts']['wards_bor']['labels']" :datasets="$overall['charts']['wards_bor']['datasets']" />
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="icon-[solar--clock-circle-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Rata-rata Lama Hari (ALOS) per Bangsal
                                </h4>
                                <div class="h-80" wire:ignore wire:key="chart-recap-alos">
                                    <x-chart chartId="mainChartALOS" chartType="bar" barType="x" :labels="$overall['charts']['wards_alos']['labels']" :datasets="$overall['charts']['wards_alos']['datasets']" />
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="icon-[solar--refresh-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Angka Perputaran Tempat Tidur (BTO) per Bangsal
                                </h4>
                                <div class="h-80" wire:ignore wire:key="chart-recap-bto">
                                    <x-chart chartId="mainChartBTO" chartType="bar" barType="x" :labels="$overall['charts']['wards_bto']['labels']" :datasets="$overall['charts']['wards_bto']['datasets']" />
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="icon-[solar--danger-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Angka Kematian Kasar (GDR) per Bangsal
                                </h4>
                                <div class="h-80" wire:ignore wire:key="chart-recap-gdr">
                                    <x-chart chartId="mainChartGDR" chartType="bar" barType="x" :labels="$overall['charts']['wards_gdr']['labels']" :datasets="$overall['charts']['wards_gdr']['datasets']" />
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="icon-[solar--hourglass-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                    Interval Perputaran Tempat Tidur (TOI) per Bangsal
                                </h4>
                                <div class="h-80" wire:ignore wire:key="chart-recap-toi">
                                    <x-chart chartId="mainChartTOI" chartType="bar" barType="x" :labels="$overall['charts']['wards_toi']['labels']" :datasets="$overall['charts']['wards_toi']['datasets']" />
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Row 4: Demographic & Discharge Analysis (Chart Mode Only) -->
                @if ($mainView === 'chart')
                    <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 backdrop-blur-md">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                                <span class="icon-[solar--chart-bold-duotone]"></span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Analisis Demografi & Kepulangan</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50/50 dark:bg-slate-800/40 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 text-center">Jenis Kelamin</h4>
                                <div class="h-44 flex items-center justify-center" wire:ignore wire:key="chart-main-gender">
                                    <x-chart chartId="chartGender" chartType="doughnut" :labels="$demographics['charts']['gender']['labels']" :datasets="$demographics['charts']['gender']['datasets']" />
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50/50 dark:bg-slate-800/40 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 text-center">Status Kepulangan</h4>
                                <div class="h-44 flex items-center justify-center" wire:ignore wire:key="chart-main-discharge">
                                    <x-chart chartId="chartDischarge" chartType="pie" :labels="$demographics['charts']['discharge']['labels']" :datasets="$demographics['charts']['discharge']['datasets']" />
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50/50 dark:bg-slate-800/40 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 text-center">Kelompok Umur</h4>
                                <div class="h-56" wire:ignore wire:key="chart-main-age">
                                    <x-chart chartId="chartAge" chartType="bar" barType="x" :labels="$demographics['charts']['age']['labels']" :datasets="$demographics['charts']['age']['datasets']" />
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50/50 dark:bg-slate-800/40 rounded-xl border border-slate-200/60 dark:border-slate-700/60 flex flex-col">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 text-center">Top Jenis Bayar</h4>
                                <div class="h-56" wire:ignore wire:key="chart-main-ins">
                                    <x-chart chartId="chartInsurance" chartType="bar" barType="y" :labels="$demographics['charts']['insurance']['labels']" :datasets="$demographics['charts']['insurance']['datasets']" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        @elseif($mainTab === 'snapshot')
            <!-- ========================================== -->
            <!-- TAB 3: SNAPSHOT BED (REAL-TIME OKUPANSI)   -->
            <!-- ========================================== -->
            <div wire:key="tab-snapshot" class="space-y-6">
                <!-- Snapshot Header Banner -->
                <div class="bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 dark:from-emerald-950 dark:via-slate-900 dark:to-slate-950 rounded-3xl shadow-xl border border-emerald-500/20 overflow-hidden relative group">
                    <span class="absolute right-0 bottom-0 p-6 opacity-10 pointer-events-none">
                        <span class="icon-[solar--bed-bold] text-[160px] text-white"></span>
                    </span>

                    <div class="p-6 sm:p-8 relative z-10 flex flex-col lg:flex-row gap-6 lg:gap-8">
                        <!-- Sisi Kiri: Ringkasan Total Bed -->
                        <div class="flex-shrink-0 flex flex-col justify-center lg:border-r lg:border-white/10 lg:pr-8">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200 mb-2">Kapasitas Bed Keseluruhan</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-5xl sm:text-6xl font-black text-white font-mono leading-none tracking-tight">{{ number_format($snapshotStats['occupied']) }}</h3>
                                <span class="text-xl sm:text-2xl font-bold text-emerald-200/60 font-mono">/ {{ number_format($snapshotStats['total_bed']) }}</span>
                            </div>
                            <div class="mt-4">
                                <span class="px-4 py-1.5 bg-white/15 backdrop-blur-md rounded-xl text-xs font-bold text-white uppercase tracking-wider border border-white/10 inline-flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                    {{ number_format($snapshotStats['available']) }} Bed Kosong Saat Ini
                                </span>
                            </div>
                        </div>

                        <!-- Sisi Kanan: Rekapitulasi Per Kelas -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">Okupansi Per Kelas</p>
                                <button type="button" x-data @click="$dispatch('open-modal', 'class-modal')"
                                    class="text-xs font-bold text-emerald-200 hover:text-white underline underline-offset-4 transition-colors">
                                    Lihat Detail &rarr;
                                </button>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach ($realtimeClassStats as $stat)
                                    @php
                                        $occ = $stat->kapasitas > 0 ? ($stat->terisi / $stat->kapasitas) * 100 : 0;
                                    @endphp
                                    <div class="bg-white/10 dark:bg-black/20 backdrop-blur-sm p-3.5 rounded-xl border border-white/10 flex flex-col group hover:bg-white/15 transition-all">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-bold text-emerald-100 uppercase tracking-wider truncate">{{ $stat->kelas }}</span>
                                            <span class="text-xs font-black text-white bg-emerald-600/40 px-2 py-0.5 rounded-full font-mono">{{ number_format($occ, 0) }}%</span>
                                        </div>
                                        <div class="flex items-baseline gap-1 mt-0.5">
                                            <span class="text-xl font-black text-white font-mono">{{ number_format($stat->terisi) }}</span>
                                            <span class="text-xs font-bold text-emerald-200/60 font-mono">/ {{ number_format($stat->kapasitas) }}</span>
                                        </div>
                                        <!-- Mini Progress Bar -->
                                        <div class="mt-3 w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-emerald-300 h-full rounded-full transition-all duration-700"
                                                style="width: {{ $occ }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Snapshot Views: List / Cards vs Charts -->
                @if ($snapshotView === 'list')
                    <div class="space-y-6">
                        @foreach ($recapData->groupBy('kelas') as $kelas => $bangsals)
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 px-1">
                                    <div class="h-6 w-1.5 bg-emerald-500 rounded-full"></div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white uppercase tracking-wider">
                                        KATEGORI: {{ $kelas }}
                                    </h3>
                                    <div class="flex items-center gap-2 px-3 py-1 bg-slate-100 dark:bg-slate-800/80 rounded-full text-xs font-bold">
                                        <span class="text-slate-500 dark:text-slate-400">Total: {{ number_format($bangsals->sum('kapasitas')) }}</span>
                                        <span class="text-slate-300 dark:text-slate-600">|</span>
                                        <span class="text-emerald-600 dark:text-emerald-400">Terisi: {{ number_format($bangsals->sum('terisi')) }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
                                    @foreach ($bangsals as $b)
                                        @php
                                            $occPercent = $b->kapasitas > 0 ? ($b->terisi / $b->kapasitas) * 100 : 0;
                                            $statusBadge = match (true) {
                                                $occPercent >= 90 => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
                                                $occPercent >= 70 => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
                                                $occPercent >= 40 => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
                                                default => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20',
                                            };
                                            $barGradient = match (true) {
                                                $occPercent >= 90 => 'from-rose-500 to-red-600',
                                                $occPercent >= 70 => 'from-amber-400 to-orange-500',
                                                $occPercent >= 40 => 'from-emerald-400 to-teal-500',
                                                default => 'from-sky-400 to-blue-500',
                                            };
                                        @endphp
                                        <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col group hover:border-emerald-500/40 transition-all hover:shadow-md backdrop-blur-md">
                                            <div class="p-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $b->nm_bangsal }}</h4>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold font-mono shrink-0 {{ $statusBadge }} {{ $occPercent >= 90 ? 'animate-pulse' : '' }}">
                                                    {{ number_format($occPercent, 0) }}%
                                                </span>
                                            </div>
                                            <div class="p-4 flex-1 space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terisi</span>
                                                        <span class="text-xl font-black text-slate-800 dark:text-white font-mono">{{ number_format($b->terisi) }}</span>
                                                    </div>
                                                    <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center text-base">
                                                        <span class="icon-[solar--bed-bold-duotone]"></span>
                                                    </div>
                                                    <div class="flex flex-col text-right">
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kapasitas</span>
                                                        <span class="text-xl font-black text-slate-400 font-mono">{{ number_format($b->kapasitas) }}</span>
                                                    </div>
                                                </div>

                                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden shadow-inner">
                                                    <div class="h-full rounded-full bg-gradient-to-r {{ $barGradient }} transition-all duration-700"
                                                        style="width: {{ $occPercent }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                <span class="icon-[solar--pie-chart-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                Okupansi per Kategori Kelas
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-snap-class">
                                <x-chart chartId="chartSnapshotClass" chartType="bar" barType="x" :labels="$snapshotCharts['class_occupancy']['labels']" :datasets="$snapshotCharts['class_occupancy']['datasets']" />
                            </div>
                        </div>

                        <div class="bg-white dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                <span class="icon-[solar--graph-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                                Okupansi Real-time per Bangsal (%)
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-snap-ward">
                                <x-chart chartId="chartSnapshotWard" chartType="bar" barType="x" :labels="$snapshotCharts['ward_occupancy']['labels']" :datasets="$snapshotCharts['ward_occupancy']['datasets']" />
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Modal Detail Okupansi Per Kelas -->
        <x-modal name="class-modal" size="2xl" modalTitle="Okupansi Per Kelas">
            <div class="p-4 max-h-[60vh] overflow-y-auto space-y-2.5">
                @foreach ($realtimeClassStats as $stat)
                    @php
                        $occ = $stat->kapasitas > 0 ? ($stat->terisi / $stat->kapasitas) * 100 : 0;
                        $barColor = match (true) {
                            $occ > 90 => 'bg-rose-500',
                            $occ > 70 => 'bg-amber-500',
                            $occ > 40 => 'bg-emerald-500',
                            default => 'bg-sky-500',
                        };
                        $badgeColor = match (true) {
                            $occ > 90 => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
                            $occ > 70 => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
                            $occ > 40 => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
                            default => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20',
                        };
                    @endphp
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-4">
                        <div class="w-12 h-12 flex items-center justify-center rounded-xl font-black font-mono text-xs shrink-0 {{ $badgeColor }}">
                            {{ number_format($occ, 0) }}%
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">{{ $stat->kelas }}</span>
                                <span class="font-semibold text-slate-500 dark:text-slate-400 font-mono">
                                    <strong class="text-slate-800 dark:text-white">{{ number_format($stat->terisi) }}</strong> / {{ number_format($stat->kapasitas) }} Bed
                                </span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                <div class="{{ $barColor }} h-full rounded-full transition-all duration-700"
                                    style="width: {{ $occ }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-modal>

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
                                        datasets: (data.datasets || []).map(ds => ({
                                            ...ds
                                        }))
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            },
                                            x: {
                                                beginAtZero: true
                                            },
                                        },
                                        indexAxis: barType,
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        animation: {
                                            duration: 500
                                        }
                                    }
                                });
                            } catch (e) {
                                console.error(`Chart init error [${chartId}]:`, e);
                            }
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
                            } else if (eventData && typeof eventData === 'object' && !Array.isArray(eventData)) {
                                payload = eventData.charts || Object.values(eventData).find(v => v && v.charts)?.charts;
                            }

                            if (!payload) return;

                            const cleanCharts = JSON.parse(JSON.stringify(payload));

                            Alpine.nextTick(() => {
                                setTimeout(() => {
                                    chartMappings.forEach(mapping => {
                                        const chartData = cleanCharts[mapping.prop];
                                        if (chartData) {
                                            window.dispatchEvent(new CustomEvent(
                                                `refreshChartData-${mapping.name}`, {
                                                    detail: chartData
                                                }));
                                        }
                                    });
                                }, 150);
                            });
                        } catch (e) {
                            console.error(`Error refreshing ${eventName}:`, e);
                        }
                    });
                };

                handleRefresh('refresh-all-charts', [
                    { name: 'chartGender', prop: 'gender' },
                    { name: 'chartAge', prop: 'age' },
                    { name: 'chartInsurance', prop: 'insurance' },
                    { name: 'chartDischarge', prop: 'discharge' }
                ]);

                handleRefresh('refresh-main-charts', [
                    { name: 'mainChartWards', prop: 'wards_patients' },
                    { name: 'mainChartBOR', prop: 'wards_bor' },
                    { name: 'mainChartALOS', prop: 'wards_alos' },
                    { name: 'mainChartBTO', prop: 'wards_bto' },
                    { name: 'mainChartGDR', prop: 'wards_gdr' },
                    { name: 'mainChartTOI', prop: 'wards_toi' },
                    { name: 'chartTrendInOut', prop: 'trend' }
                ]);

                handleRefresh('refresh-snapshot-charts', [
                    { name: 'chartSnapshotClass', prop: 'class_occupancy' },
                    { name: 'chartSnapshotWard', prop: 'ward_occupancy' }
                ]);
            </script>
        @endscript
    </div>
</x-content>
