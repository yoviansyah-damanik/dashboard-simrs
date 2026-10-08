<x-content>
    <x-breadcrumb title="Gizi" :items="[['title' => 'Layanan Penunjang Medis'], ['title' => 'Gizi'], ['title' => 'Rekap Permintaan Diet']]" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">
                        Rekapitulasi permintaan dan distribusi diet pasien rawat inap rumah sakit.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
                    <a href="{{ route('ancillary.summary') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white dark:bg-boxdark dark:text-gray-300 border border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4/40 transition-all shadow-xs"
                        title="Buka Ringkasan Layanan Penunjang">
                        <span class="icon-[solar--widget-2-bold-duotone] text-sm text-emerald-600"></span>
                        <span>Ringkasan Penunjang</span>
                    </a>
                    <a href="{{ route('ancillary.yearly-matrix', ['activeTab' => 'gizi']) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white dark:bg-boxdark dark:text-gray-300 border border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4/40 transition-all shadow-xs"
                        title="Buka Matriks Indikator Tahunan">
                        <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-sm text-amber-600"></span>
                        <span>Matriks Indikator</span>
                    </a>
                    <a href="{{ route('nutrition') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-amber-700 bg-amber-500/10 hover:bg-amber-500/20 dark:text-amber-300 dark:bg-amber-900/30 border border-amber-500/20 transition-all">
                        <span class="icon-[solar--cup-first-bold-duotone] text-base"></span>
                        Daftar Permintaan Diet
                        <span class="icon-[solar--arrow-right-line-duotone] text-sm"></span>
                    </a>
                </div>
            </div>

            <!-- Sub-Header Controls & Filter Periode -->
            <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stroke dark:border-strokedark">
                <div class="flex items-center gap-4">
                    <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl border border-stroke dark:border-strokedark">
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-amber-600 dark:text-amber-400' : 'text-gray-500' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            Grafik
                        </button>
                        <button wire:click="$set('mainView', 'figures')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-black uppercase tracking-widest transition-all {{ $mainView === 'figures' ? 'bg-white dark:bg-boxdark shadow-sm text-amber-600 dark:text-amber-400' : 'text-gray-500' }}">
                            <span class="icon-[solar--document-text-bold-duotone] text-lg"></span>
                            Dalam Angka
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-amber-500 focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
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
                                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-amber-500 outline-none shadow-sm">
                                @foreach ($this->months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-amber-500 outline-none shadow-sm">
                                @foreach ($this->years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-amber-500 outline-none shadow-sm">
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

        <!-- Banner & Kartu KPI Utama -->
        <div class="space-y-4">
            <!-- 3-Panel Hero Banner -->
            <div class="bg-gradient-to-br from-amber-600 via-orange-600 to-amber-900 p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-stretch gap-6 group border border-white/10">
                <!-- Panel 1: Total Permintaan -->
                <div class="relative z-10 text-white min-w-[220px] flex flex-col justify-center">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-200 mb-1">Total Porsi Diet</p>
                    <h3 class="text-4xl lg:text-5xl font-black tracking-tight drop-shadow-sm">
                        {{ number_format($this->summary['summary']['total_porsi']) }}
                    </h3>
                    <p class="text-xs text-amber-100/90 mt-2 flex items-center gap-1.5 font-medium">
                        <span class="icon-[solar--cup-first-bold-duotone] text-sm"></span>
                        Porsi Makanan Terdistribusi
                    </p>
                </div>

                <div class="hidden lg:block w-px bg-white/20 my-2"></div>

                <!-- Panel 2: Pasien Unik -->
                <div class="relative z-10 text-white flex-1 flex flex-col justify-center">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-200 mb-1">Pasien Terlayani</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl lg:text-4xl font-black">{{ number_format($this->summary['summary']['total_pasien']) }}</span>
                        <span class="text-xs font-semibold text-amber-100">Pasien Unik (Rawat Inap)</span>
                    </div>
                    <p class="text-xs text-amber-100/90 mt-2 flex items-center gap-1.5 font-medium">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-sm"></span>
                        Menerima Pelayanan Gizi & Diet
                    </p>
                </div>

                <div class="hidden lg:block w-px bg-white/20 my-2"></div>

                <!-- Panel 3: Komposisi Waktu -->
                <div class="relative z-10 text-white flex-1 flex flex-col justify-center">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-200 mb-2">Komposisi Waktu Makan</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm text-xs font-bold text-amber-100 border border-white/10">
                            Pagi: {{ number_format($this->summary['summary']['pagi']) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm text-xs font-bold text-orange-200 border border-white/10">
                            Siang: {{ number_format($this->summary['summary']['siang']) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm text-xs font-bold text-indigo-200 border border-white/10">
                            Sore: {{ number_format($this->summary['summary']['sore']) }}
                        </span>
                    </div>
                </div>

                <span class="icon-[solar--cup-first-bold-duotone] text-9xl opacity-10 absolute -right-6 -bottom-6 rotate-12 pointer-events-none"></span>
            </div>

            <!-- 4-Card KPI Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-amber-500 transition-all">
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Makan Pagi
                    </span>
                    <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ number_format($this->summary['summary']['pagi']) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-0.5">Porsi Terdistribusi</span>
                </div>

                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-orange-500 transition-all">
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Makan Siang
                    </span>
                    <h4 class="text-2xl font-black text-orange-600 dark:text-orange-400">
                        {{ number_format($this->summary['summary']['siang']) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-0.5">Porsi Terdistribusi</span>
                </div>

                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-indigo-500 transition-all">
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Makan Sore / Malam
                    </span>
                    <h4 class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                        {{ number_format($this->summary['summary']['sore']) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-0.5">Porsi Terdistribusi</span>
                </div>

                <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-500 transition-all">
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Variasi Diet
                    </span>
                    <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ number_format($this->summary['summary']['total_jenis_diet']) }}
                    </h4>
                    <span class="text-[10px] text-gray-400 font-semibold mt-0.5">Jenis Diet Digunakan</span>
                </div>
            </div>
        </div>

        <!-- Konten Utama: Grafik vs Dalam Angka -->
        <div class="space-y-8">
            @if ($mainView === 'chart')
                <div class="space-y-6">
                    <!-- Row 1: Tren & Distribusi Waktu Makan -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Line Chart: Tren Permintaan Diet -->
                        <div class="lg:col-span-8 bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                                <span class="icon-[solar--graph-bold-duotone] text-lg text-amber-500"></span>
                                Tren Jumlah Permintaan Diet (Waktu ke Waktu)
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-diet-trend">
                                <x-chart chartId="chartDietTrend" chartType="line"
                                    :labels="$this->summary['charts']['trend']['labels']"
                                    :datasets="$this->summary['charts']['trend']['datasets']" />
                            </div>
                        </div>

                        <!-- Doughnut Chart: Distribusi Waktu Makan -->
                        <div class="lg:col-span-4 bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center flex items-center justify-center gap-2">
                                <span class="icon-[solar--pie-chart-2-bold-duotone] text-lg text-orange-500"></span>
                                Distribusi Waktu Makan
                            </h4>
                            <div class="h-80 flex items-center justify-center" wire:ignore wire:key="chart-diet-waktu">
                                <x-chart chartId="chartDietWaktu" chartType="doughnut"
                                    :labels="$this->summary['charts']['waktu']['labels']"
                                    :datasets="$this->summary['charts']['waktu']['datasets']" />
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Top Jenis Diet & Sebaran Bangsal -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Bar Chart: 10 Jenis Diet Terbanyak -->
                        <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center flex items-center justify-center gap-2">
                                <span class="icon-[solar--cup-first-bold-duotone] text-lg text-emerald-500"></span>
                                10 Jenis Diet Paling Banyak Diminta
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-diet-top-jenis">
                                <x-chart chartId="chartDietTopJenis" chartType="bar" barType="y"
                                    :labels="$this->summary['charts']['top_diet']['labels']"
                                    :datasets="$this->summary['charts']['top_diet']['datasets']" />
                            </div>
                        </div>

                        <!-- Bar Chart: Sebaran Permintaan per Bangsal -->
                        <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 text-center flex items-center justify-center gap-2">
                                <span class="icon-[solar--hospital-bold-duotone] text-lg text-cyan-500"></span>
                                10 Bangsal / Ruangan Penerima Diet Terbanyak
                            </h4>
                            <div class="h-80" wire:ignore wire:key="chart-diet-bangsal">
                                <x-chart chartId="chartDietBangsal" chartType="bar" barType="y"
                                    :labels="$this->summary['charts']['bangsal']['labels']"
                                    :datasets="$this->summary['charts']['bangsal']['datasets']" />
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Rincian Tabel Data Rekapitulasi -->
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        <!-- Tabel Rekap per Jenis Diet -->
                        <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-stroke dark:border-strokedark">
                                <h4 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                    <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-emerald-500 text-lg"></span>
                                    Rincian per Jenis Diet
                                </h4>
                                <span class="text-xs font-bold text-gray-400">{{ count($this->summary['tables']['per_diet']) }} Jenis Diet</span>
                            </div>

                            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-gray-50 dark:bg-meta-4/40 sticky top-0 text-[11px] font-black uppercase tracking-wider text-gray-400">
                                        <tr>
                                            <th class="py-2.5 px-3">Nama Diet</th>
                                            <th class="py-2.5 px-2 text-center text-amber-600">Pagi</th>
                                            <th class="py-2.5 px-2 text-center text-orange-600">Siang</th>
                                            <th class="py-2.5 px-2 text-center text-indigo-600">Sore</th>
                                            <th class="py-2.5 px-3 text-right">Total</th>
                                            <th class="py-2.5 px-2 text-right">Rasio</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                                        @forelse ($this->summary['tables']['per_diet'] as $row)
                                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 transition-colors">
                                                <td class="py-2.5 px-3">
                                                    <span class="font-bold text-gray-900 dark:text-white">{{ $row->nama_diet }}</span>
                                                    <span class="text-[10px] text-gray-400 block font-mono">{{ $row->kd_diet }}</span>
                                                </td>
                                                <td class="py-2.5 px-2 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row->pagi) }}</td>
                                                <td class="py-2.5 px-2 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row->siang) }}</td>
                                                <td class="py-2.5 px-2 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row->sore) }}</td>
                                                <td class="py-2.5 px-3 text-right font-black text-amber-600 dark:text-amber-400">{{ number_format($row->total) }}</td>
                                                <td class="py-2.5 px-2 text-right text-[11px] text-gray-500 font-bold">{{ $row->persen }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-gray-400">Belum ada data permintaan diet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tabel Rekap per Bangsal / Ruangan -->
                        <div class="bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-stroke dark:border-strokedark">
                                <h4 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                    <span class="icon-[solar--hospital-bold-duotone] text-cyan-500 text-lg"></span>
                                    Rincian per Bangsal / Ruangan
                                </h4>
                                <span class="text-xs font-bold text-gray-400">{{ count($this->summary['tables']['per_bangsal']) }} Bangsal</span>
                            </div>

                            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-gray-50 dark:bg-meta-4/40 sticky top-0 text-[11px] font-black uppercase tracking-wider text-gray-400">
                                        <tr>
                                            <th class="py-2.5 px-3">Nama Bangsal</th>
                                            <th class="py-2.5 px-2 text-center text-amber-600">Pagi</th>
                                            <th class="py-2.5 px-2 text-center text-orange-600">Siang</th>
                                            <th class="py-2.5 px-2 text-center text-indigo-600">Sore</th>
                                            <th class="py-2.5 px-3 text-right">Total</th>
                                            <th class="py-2.5 px-2 text-right">Rasio</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                                        @forelse ($this->summary['tables']['per_bangsal'] as $row)
                                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 transition-colors">
                                                <td class="py-2.5 px-3 font-bold text-gray-900 dark:text-white line-clamp-1">
                                                    {{ $row->nama_bangsal }}
                                                </td>
                                                <td class="py-2.5 px-2 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row->pagi) }}</td>
                                                <td class="py-2.5 px-2 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row->siang) }}</td>
                                                <td class="py-2.5 px-2 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row->sore) }}</td>
                                                <td class="py-2.5 px-3 text-right font-black text-cyan-600 dark:text-cyan-400">{{ number_format($row->total) }}</td>
                                                <td class="py-2.5 px-2 text-right text-[11px] text-gray-500 font-bold">{{ $row->persen }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-gray-400">Belum ada data permintaan diet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- DALAM ANGKA VIEW -->
                <div class="space-y-8">
                    <x-recap.in-figures title="Distribusi Waktu Makan">
                        <x-box title="Makan Pagi" :value="number_format($this->summary['summary']['pagi'])"
                            icon="icon-[solar--sun-2-bold-duotone]" />
                        <x-box title="Makan Siang" :value="number_format($this->summary['summary']['siang'])"
                            icon="icon-[solar--sun-bold-duotone]" />
                        <x-box title="Makan Sore / Malam" :value="number_format($this->summary['summary']['sore'])"
                            icon="icon-[solar--moon-stars-bold-duotone]" />
                        <x-box title="Total Porsi" :value="number_format($this->summary['summary']['total_porsi'])"
                            icon="icon-[solar--cup-first-bold-duotone]" />
                    </x-recap.in-figures>

                    <x-recap.in-figures title="10 Jenis Diet Paling Banyak Diminta">
                        @foreach ($this->summary['tables']['per_diet']->take(10) as $item)
                            <x-box :title="$item->nama_diet" :value="number_format($item->total)"
                                icon="icon-[solar--cup-first-bold-duotone]" />
                        @endforeach
                    </x-recap.in-figures>

                    <x-recap.in-figures title="Sebaran Bangsal Penerima Diet Terbanyak">
                        @foreach ($this->summary['tables']['per_bangsal']->take(10) as $item)
                            <x-box :title="$item->nama_bangsal" :value="number_format($item->total)"
                                icon="icon-[solar--hospital-bold-duotone]" />
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

            handleRefresh('refresh-diet-charts', [
                { name: 'chartDietTrend', prop: 'trend' },
                { name: 'chartDietWaktu', prop: 'waktu' },
                { name: 'chartDietTopJenis', prop: 'top_diet' },
                { name: 'chartDietBangsal', prop: 'bangsal' }
            ]);
        </script>
    @endscript
</x-content>
