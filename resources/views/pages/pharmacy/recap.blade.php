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
                        </div>
                    </div>

                    <!-- Tren & Analisis 10 Obat Paling Sering Digunakan -->
                    <div
                        class="bg-gray-50/50 dark:bg-meta-4/5 p-6 rounded-3xl border border-stroke dark:border-strokedark space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg">
                                    <span class="icon-[solar--ranking-bold-duotone] text-xl"></span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                        Tren 10 Obat Paling Sering Digunakan
                                    </h3>
                                    <p class="text-xs font-semibold text-gray-500 mt-0.5">
                                        Grafik pergerakan konsumsi waktu ke waktu beserta indikator status stok sisa
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('pharmacy.stock') }}" 
                                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-black uppercase tracking-wider text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition-all self-start sm:self-auto">
                                <span class="icon-[solar--box-minimalistic-bold-duotone] text-base"></span>
                                Buka Rekap Stok Farmasi
                                <span class="icon-[solar--arrow-right-line-duotone] text-sm"></span>
                            </a>
                        </div>

                        <!-- Multi-Line Chart: Tren 10 Obat -->
                        <div
                            class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                                    <span class="icon-[solar--graph-new-bold-duotone] text-indigo-500"></span>
                                    Dinamika Tren Pemakaian 10 Obat (Multi-Series)
                                </h4>
                                <span class="text-xs text-gray-400 font-medium hidden sm:inline">
                                    Klik legenda obat untuk menampilkan atau menyembunyikan garis
                                </span>
                            </div>
                            <div class="h-96" wire:ignore wire:key="chart-pharm-top10-trend">
                                <x-chart chartId="chartPharmTop10Trend" chartType="line"
                                    :labels="$this->drugUsage['charts']['trend_top10']['labels']"
                                    :datasets="$this->drugUsage['charts']['trend_top10']['datasets']" />
                            </div>
                        </div>

                        <!-- 2 Col: Akumulasi Bar Chart + Tabel Peringkat dengan Status Stok -->
                        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                            <!-- Akumulasi Bar Chart -->
                            <div
                                class="xl:col-span-5 bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <h4
                                    class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center">
                                    Akumulasi Volume Pemakaian (Top 10)
                                </h4>
                                <div class="h-96" wire:ignore wire:key="chart-pharm-top-obat">
                                    <x-chart chartId="chartPharmTopObat" chartType="bar" barType="y"
                                        :labels="$this->drugUsage['charts']['top_obat']['labels']"
                                        :datasets="$this->drugUsage['charts']['top_obat']['datasets']" />
                                </div>
                            </div>

                            <!-- Tabel Rincian & Status Stok -->
                            <div
                                class="xl:col-span-7 bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-black uppercase tracking-widest text-gray-400">
                                        Status Ketersediaan Stok 10 Obat Teratas
                                    </h4>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Per Depo / Gudang</span>
                                </div>

                                <div class="overflow-x-auto flex-1">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="border-b border-stroke dark:border-strokedark text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                <th class="py-2.5 px-2">#</th>
                                                <th class="py-2.5 px-3">Nama Obat</th>
                                                <th class="py-2.5 px-3 text-right">Terpakai</th>
                                                <th class="py-2.5 px-3 text-right">Sisa Stok</th>
                                                <th class="py-2.5 px-3 text-center">Status Stok</th>
                                                <th class="py-2.5 px-2 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 text-xs font-semibold">
                                            @forelse ($this->drugUsage['top_obat'] as $idx => $obat)
                                                <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 transition-colors {{ $obat->is_akan_habis ? 'bg-amber-50/40 dark:bg-amber-950/20' : ($obat->is_habis ? 'bg-rose-50/40 dark:bg-rose-950/20' : '') }}">
                                                    <td class="py-3 px-2 font-black text-gray-400">
                                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $idx < 3 ? 'bg-primary/10 text-primary font-bold' : 'text-gray-400' }}">
                                                            {{ $idx + 1 }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3 px-3">
                                                        <div class="font-bold text-gray-800 dark:text-white line-clamp-1">
                                                            {{ $obat->nama_brng }}
                                                        </div>
                                                        <div class="text-[11px] text-gray-400 font-mono">
                                                            {{ $obat->kode_brng }} &bull; {{ number_format($obat->pemakaian) }} transaksi
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3 text-right font-black text-indigo-600 dark:text-indigo-400">
                                                        {{ number_format($obat->qty) }}
                                                    </td>
                                                    <td class="py-3 px-3 text-right">
                                                        <span class="font-black {{ $obat->is_habis ? 'text-rose-600 dark:text-rose-400' : ($obat->is_akan_habis ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-300') }}">
                                                            {{ number_format($obat->current_stock) }}
                                                        </span>
                                                        <div class="text-[10px] text-gray-400">
                                                            Min: {{ number_format($obat->min_stock) }}
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3 text-center">
                                                        @if ($obat->is_habis)
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                                Stok Habis
                                                            </span>
                                                        @elseif ($obat->is_akan_habis)
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 animate-pulse">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                                Akan Habis
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                                Aman
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-2 text-center">
                                                        <a href="{{ route('pharmacy.stock', ['search' => $obat->kode_brng]) }}" 
                                                            title="Lihat detail stok obat ini di Rekap Stok"
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 hover:bg-primary hover:text-white dark:bg-meta-4 text-gray-500 transition-colors">
                                                            <span class="icon-[solar--arrow-right-up-linear] text-sm"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-6 text-gray-400">Belum ada data penggunaan obat pada periode ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
                        <div class="col-span-full grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($this->drugUsage['top_obat'] as $idx => $item)
                                <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border {{ $item->is_akan_habis ? 'border-amber-400 bg-amber-50/20' : ($item->is_habis ? 'border-rose-400 bg-rose-50/20' : 'border-stroke dark:border-strokedark') }} flex items-center justify-between gap-4 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl font-black text-xs {{ $idx < 3 ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-meta-4 text-gray-500' }}">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <h5 class="text-sm font-bold text-gray-800 dark:text-white">{{ $item->nama_brng }}</h5>
                                            <p class="text-xs text-gray-400">{{ $item->kode_brng }} &bull; {{ number_format($item->pemakaian) }} transaksi</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ number_format($item->qty) }}</span>
                                        <span class="text-xs text-gray-400 block">Sisa stok: {{ number_format($item->current_stock) }}</span>
                                        @if ($item->is_akan_habis)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-600 uppercase tracking-widest mt-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Akan Habis
                                            </span>
                                        @elseif ($item->is_habis)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-rose-600 uppercase tracking-widest mt-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Stok Habis
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                            const isMultiDataset = (data.datasets || []).length > 1;
                            this.chart = new Chart(ctx, {
                                type: chartType,
                                data: {
                                    labels: [...(data.labels || [])],
                                    datasets: (data.datasets || []).map(ds => ({ ...ds }))
                                },
                                options: {
                                    plugins: {
                                        legend: {
                                            display: chartType === 'doughnut' || isMultiDataset,
                                            position: 'top',
                                            labels: {
                                                boxWidth: 12,
                                                padding: 8,
                                                font: { size: 11, weight: '600' }
                                            }
                                        }
                                    },
                                    scales: chartType === 'doughnut' ? {} : {
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
                { name: 'chartPharmTrend', prop: 'trend' },
                { name: 'chartPharmStatus', prop: 'status' },
                { name: 'chartPharmJenis', prop: 'jenis_resep' },
                { name: 'chartPharmPenyerahan', prop: 'penyerahan' }
            ]);

            handleRefresh('refresh-drug-charts', [
                { name: 'chartPharmDrugTrend', prop: 'trend' },
                { name: 'chartPharmTopObat', prop: 'top_obat' },
                { name: 'chartPharmTop10Trend', prop: 'trend_top10' }
            ]);
        </script>
    @endscript
</x-content>
