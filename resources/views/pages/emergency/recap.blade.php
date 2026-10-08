<x-content>
    <x-breadcrumb title="Gawat Darurat" :items="[['title' => 'Gawat Darurat'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Control Toolbar: Switcher & Filter Periode -->
        <div class="p-3 sm:p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                <!-- Sisi Kiri: Switcher Tampilan (Tabel / Grafik) -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl shrink-0">
                        <button wire:click="$set('mainView', 'list')"
                            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainView === 'list' ? 'bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                            <span class="icon-[solar--list-bold-duotone] text-base"></span>
                            <span>Tabel</span>
                        </button>
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-base"></span>
                            <span>Grafik</span>
                        </button>
                    </div>
                </div>

                <!-- Sisi Kanan: Pilihan Periode Waktu -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 cursor-pointer outline-none transition-all">
                            <option value="today">Hari Ini</option>
                            <option value="last_7_days">7 Hari Terakhir</option>
                            <option value="last_30_days">30 Hari Terakhir</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="this_year">Tahun Ini</option>
                            <option value="monthly">Pilih Bulan</option>
                            <option value="yearly">Pilih Tahun</option>
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-rose-600 dark:text-rose-400">
                            <span class="icon-[solar--calendar-minimalistic-bold] text-base"></span>
                        </div>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <span class="icon-[solar--alt-arrow-down-bold] text-xs"></span>
                        </div>
                    </div>

                    @if ($period === 'monthly')
                        <div class="flex items-center gap-2">
                            <select wire:model.live="selectedMonth"
                                class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-rose-500 outline-none">
                                @foreach ($this->months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-rose-500 outline-none">
                                @foreach ($this->years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 focus:border-rose-500 outline-none">
                            @foreach ($this->years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI Area: Summary Banner & 4 Cards -->
        <div class="space-y-4">
            <!-- 3-Panel Summary Banner -->
            <div class="bg-gradient-to-br from-rose-600 via-rose-700 to-slate-900 dark:from-rose-950 dark:via-slate-900 dark:to-slate-950 p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-stretch gap-6 group border border-rose-500/20">
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-rose-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Panel 1: Total Volume -->
                <div class="relative z-10 text-white min-w-[200px] flex flex-col justify-center">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-200 mb-1.5">Total Kunjungan IGD</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl sm:text-6xl font-black leading-none tracking-tight">{{ number_format($this->overallStats['total']) }}</h3>
                        <span class="text-xs font-bold text-rose-200">Jiwa</span>
                    </div>
                    <div class="flex items-center gap-5 mt-5">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-rose-200/80 uppercase tracking-wider">Laki-laki</span>
                            <span class="text-xl font-black text-cyan-300">{{ number_format($this->overallStats['gender']['laki']) }}</span>
                        </div>
                        <div class="h-8 w-px bg-white/20"></div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-rose-200/80 uppercase tracking-wider">Perempuan</span>
                            <span class="text-xl font-black text-pink-300">{{ number_format($this->overallStats['gender']['perempuan']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Transfer Ranap vs Ralan -->
                <div class="relative z-10 flex-1 flex flex-col justify-center lg:border-x lg:border-white/10 lg:px-6">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-200 mb-3">Tindak Lanjut Pasien IGD</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                            <span class="text-[11px] font-medium text-purple-200 block">Masuk Rawat Inap</span>
                            <span class="text-2xl font-black text-white">{{ number_format($this->overallStats['ranap']) }}</span>
                            <span class="text-[11px] text-purple-200/80 block mt-0.5">{{ $this->overallStats['rasio_ranap'] }}% dirawat</span>
                        </div>
                        <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                            <span class="text-[11px] font-medium text-sky-200 block">Rawat Jalan / Pulang</span>
                            <span class="text-2xl font-black text-white">{{ number_format($this->overallStats['ralan']) }}</span>
                            <span class="text-[11px] text-sky-200/80 block mt-0.5">{{ 100 - $this->overallStats['rasio_ranap'] }}% ralan</span>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Demografi Umur -->
                <div class="relative z-10 min-w-[220px] flex flex-col justify-center">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-200 mb-3">Kelompok Umur (SIRS)</p>
                    <div class="space-y-1.5">
                        @foreach ($this->overallStats['age_groups']->take(4) as $ag)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-rose-100/90 font-medium">{{ $ag->kelompok }}</span>
                                <span class="font-bold text-white">{{ number_format($ag->total) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 4 Mini Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <span class="icon-[solar--shield-check-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pasien BPJS</span>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($this->overallStats['insurance']['bpjs']) }}</h4>
                    </div>
                </div>
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="p-2.5 bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 rounded-xl">
                        <span class="icon-[solar--wallet-money-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pasien Mandiri</span>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($this->overallStats['insurance']['umum']) }}</h4>
                    </div>
                </div>
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-xl">
                        <span class="icon-[solar--medal-star-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Dinas / Lainnya</span>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($this->overallStats['insurance']['lain']) }}</h4>
                    </div>
                </div>
                <div class="p-4 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-xl">
                        <span class="icon-[solar--user-plus-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Rasio Pasien Baru</span>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white">{{ round($this->overallStats['new_ratio'], 1) }}%</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mode Tampilan: TABEL -->
        @if ($mainView === 'list')
            <div class="p-5 sm:p-6 bg-white dark:bg-slate-900/80 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="icon-[solar--stethoscope-bold-duotone] text-rose-600 dark:text-rose-400 text-lg"></span>
                        Rekapitulasi Pelayanan Dokter IGD
                    </h4>
                    <span class="text-xs text-slate-500 font-medium">{{ $recapData->count() }} Dokter Terdata</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700/80">
                                <th class="py-3 px-3">Nama Dokter</th>
                                <th class="py-3 px-3 text-center">Total Kunjungan</th>
                                <th class="py-3 px-3 text-center">Masuk Ranap</th>
                                <th class="py-3 px-3 text-center">Rawat Jalan</th>
                                <th class="py-3 px-3 text-center">Pasien Baru</th>
                                <th class="py-3 px-3 text-center">Pasien Lama</th>
                                <th class="py-3 px-3 text-center">Dirujuk</th>
                                <th class="py-3 px-3 text-center">Meninggal</th>
                                <th class="py-3 px-3 text-center">Sudah Diperiksa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            @forelse ($recapData as $row)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $row->nm_dokter }}</td>
                                    <td class="py-2.5 px-3 text-center font-black text-rose-600 dark:text-rose-400">{{ number_format($row->total_reg) }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-purple-600 dark:text-purple-400">{{ number_format($row->masuk_ranap) }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-sky-600 dark:text-sky-400">{{ number_format($row->rawat_jalan) }}</td>
                                    <td class="py-2.5 px-3 text-center text-slate-600 dark:text-slate-400">{{ number_format($row->pasien_baru) }}</td>
                                    <td class="py-2.5 px-3 text-center text-slate-600 dark:text-slate-400">{{ number_format($row->pasien_lama) }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-amber-600 dark:text-amber-400">{{ number_format($row->dirujuk) }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-rose-600 dark:text-rose-400">{{ number_format($row->meninggal) }}</td>
                                    <td class="py-2.5 px-3 text-center text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($row->sudah_periksa) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-500">Tidak ada data pelayanan IGD pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Mode Tampilan: GRAFIK -->
        @if ($mainView === 'chart')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Trend Chart -->
                <div class="lg:col-span-2 p-5 bg-white dark:bg-slate-900/80 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Tren Kunjungan Harian IGD
                    </h4>
                    <div class="h-64 relative flex items-center justify-center">
                        <p class="text-xs text-slate-400">Gunakan tab Tabel untuk rincian data angka atau periksa laporan berkala.</p>
                    </div>
                </div>

                <!-- Donut Tindak Lanjut -->
                <div class="p-5 bg-white dark:bg-slate-900/80 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-center">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 text-center">
                        Proporsi Tindak Lanjut
                    </h4>
                    <div class="space-y-3">
                        <div class="p-3 bg-purple-50 dark:bg-purple-950/30 rounded-xl flex justify-between items-center">
                            <span class="text-xs font-bold text-purple-700 dark:text-purple-300">Masuk Ranap</span>
                            <span class="text-sm font-black text-purple-800 dark:text-purple-200">{{ number_format($this->overallStats['ranap']) }} ({{ $this->overallStats['rasio_ranap'] }}%)</span>
                        </div>
                        <div class="p-3 bg-sky-50 dark:bg-sky-950/30 rounded-xl flex justify-between items-center">
                            <span class="text-xs font-bold text-sky-700 dark:text-sky-300">Rawat Jalan / Pulang</span>
                            <span class="text-sm font-black text-sky-800 dark:text-sky-200">{{ number_format($this->overallStats['ralan']) }} ({{ 100 - $this->overallStats['rasio_ranap'] }}%)</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-content>
