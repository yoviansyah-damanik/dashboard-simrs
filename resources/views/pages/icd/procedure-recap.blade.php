<x-content>
    <x-breadcrumb title="Rekapitulasi Data Tindakan" :items="[
        ['title' => 'Laporan'],
        ['title' => 'Rekapitulasi Data Tindakan']
    ]" />

    <div class="space-y-6 animate-in fade-in duration-500">
        <!-- 1. Header Command Card -->
        <div class="relative p-6 sm:p-8 rounded-[2.5rem] bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-950 text-white overflow-hidden shadow-2xl border border-white/10">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-black uppercase tracking-widest">
                        <span class="icon-[solar--magic-stick-3-bold-duotone] text-sm"></span>
                        Klasifikasi Prosedur & ICD-9-CM
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white">
                        Rekap Data Tindakan & Prosedur Medis
                    </h2>
                    <p class="text-sm text-slate-300 max-w-2xl font-medium">
                        Pemetaan 10 besar pola tindakan medis, agregasi per bab ICD-9-CM, serta statistik prosedur pasien Rawat Jalan (Poli & IGD) dan Rawat Inap secara terintegrasi.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center flex-wrap gap-3 shrink-0">
                    <button type="button" wire:click="exportPdf" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-cyan-600 hover:bg-cyan-500 text-white shadow-lg shadow-cyan-900/30 transition-all cursor-pointer">
                        <span wire:loading.remove wire:target="exportPdf" class="icon-[solar--file-download-bold-duotone] text-base"></span>
                        <span wire:loading wire:target="exportPdf" class="icon-[solar--spinner-line-duotone] animate-spin text-base"></span>
                        <span>Cetak PDF Data</span>
                    </button>
                    <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-bold text-xs bg-white/10 hover:bg-white/15 text-slate-200 border border-white/10 transition-all cursor-pointer">
                        <span class="icon-[solar--refresh-circle-bold-duotone] text-base"></span>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Panel Filter & Parameter Laporan -->
        <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-stroke/70 dark:border-strokedark/70">
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <span class="icon-[solar--tuning-4-bold-duotone] text-lg text-cyan-600 dark:text-cyan-400"></span>
                    <span>Parameter Filter Laporan</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-bold">Mode Periode:</span>
                    <div class="inline-flex p-0.5 rounded-lg bg-gray-100 dark:bg-meta-4 text-xs font-bold">
                        <button type="button" wire:click="setPeriodType('month')"
                            class="px-2.5 py-1 rounded-md transition-all {{ $periodType === 'month' ? 'bg-white dark:bg-boxdark text-cyan-600 shadow-2xs' : 'text-gray-500' }}">
                            Bulan/Tahun
                        </button>
                        <button type="button" wire:click="setPeriodType('range')"
                            class="px-2.5 py-1 rounded-md transition-all {{ $periodType === 'range' ? 'bg-white dark:bg-boxdark text-cyan-600 shadow-2xs' : 'text-gray-500' }}">
                            Rentang Tanggal
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Periode: Bulan/Tahun atau Rentang Tanggal -->
                @if ($periodType === 'month')
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tahun</label>
                        <select wire:model.live="year" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                            @foreach ($this->availableYears as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Bulan</label>
                        <select wire:model.live="month" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                            <option value="">Semua Bulan (Setahun)</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tgl Mulai</label>
                        <input type="date" wire:model.live="startDate" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tgl Akhir</label>
                        <input type="date" wire:model.live="endDate" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                    </div>
                @endif

                <!-- Status Rawat / Pelayanan -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Status Rawat</label>
                    <select wire:model.live="serviceStatus" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                        <option value="all">Semua Layanan (Poli, IGD & Ranap)</option>
                        <option value="Poli">Rawat Jalan Poliklinik (Poli)</option>
                        <option value="IGD">Gawat Darurat (IGD)</option>
                        <option value="Ralan">Rawat Jalan Total (Poli + IGD)</option>
                        <option value="Ranap">Rawat Inap (Ranap)</option>
                    </select>
                </div>

                <!-- Kategori Bab ICD-9 -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Kategori Bab ICD-9</label>
                    <select wire:model.live="category" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                        <option value="all">Semua Kategori Bab</option>
                        @foreach ($this->procedureCategories as $key => $cat)
                            <option value="{{ $key }}">{{ $cat['short_label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Prioritas Tindakan -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Prioritas Prosedur</label>
                    <select wire:model.live="priority" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                        <option value="all">Semua Prioritas</option>
                        <option value="1">Prosedur Utama (Primer)</option>
                        <option value="2">Prosedur Sekunder</option>
                    </select>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Jenis Kelamin</label>
                    <select wire:model.live="gender" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 py-2 px-3">
                        <option value="all">Semua Gender (L & P)</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Pencarian Teks Kode ICD-9 atau Deskripsi -->
            <div class="pt-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <span class="icon-[solar--magnifer-linear] text-base"></span>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan Kode ICD-9 (misal: 23.70, 99.21, 74.1) atau Nama Prosedur..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-cyan-500 focus:ring-cyan-500 transition">
                    @if ($search)
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <span class="icon-[solar--close-circle-bold] text-base"></span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Executive KPI Metric Cards -->
        @php $summary = $this->summary; @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Card 1: Total Tindakan -->
            <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Total Tindakan</span>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                            <span class="icon-[solar--magic-stick-3-bold-duotone] text-lg"></span>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-800 dark:text-white">
                        {{ number_format($summary['total_tindakan'] ?? 0) }}
                    </div>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                    <span>Prosedur Terinput</span>
                </div>
            </div>

            <!-- Card 2: Prosedur Unik -->
            <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Prosedur Unik</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <span class="icon-[solar--hashtag-bold-duotone] text-lg"></span>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-800 dark:text-white">
                        {{ number_format($summary['total_prosedur_unik'] ?? 0) }}
                    </div>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Kode ICD-9 Terpakai</span>
                </div>
            </div>

            <!-- Card 3: Pasien Unik -->
            <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Pasien Unik</span>
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <span class="icon-[solar--users-group-rounded-bold-duotone] text-lg"></span>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-800 dark:text-white">
                        {{ number_format($summary['total_pasien_unik'] ?? 0) }}
                    </div>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-2 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span>Individu Pasien</span>
                </div>
            </div>

            <!-- Card 4: Poliklinik (Poli) -->
            <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-extrabold">Poli (Ralan)</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--stethoscope-bold-duotone] text-lg"></span>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ number_format($summary['poli'] ?? 0) }}
                    </div>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-2 flex items-center justify-between">
                    <span>Poliklinik Rawat Jalan</span>
                </div>
            </div>

            <!-- Card 5: Gawat Darurat (IGD) -->
            <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 font-extrabold">Gawat Darurat (IGD)</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <span class="icon-[solar--danger-triangle-bold-duotone] text-lg"></span>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ number_format($summary['igd'] ?? 0) }}
                    </div>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-2 flex items-center justify-between">
                    <span>Instalasi Kegawatdaruratan</span>
                </div>
            </div>

            <!-- Card 6: Rawat Inap (Ranap) -->
            <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400 font-extrabold">Rawat Inap (Ranap)</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-purple-600 dark:text-purple-400">
                        {{ number_format($summary['ranap'] ?? 0) }}
                    </div>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-2 flex items-center justify-between">
                    <span>Bangsal & Ruang Rawat</span>
                </div>
            </div>
        </div>

        <!-- 4. Visualisasi Top 10 Tindakan Terbanyak & Ranking Morbiditas -->
        @php $top = $this->topProcedures; @endphp
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Grafik Batang Top 10 (Span 2) -->
            <div class="lg:col-span-2 bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between"
                 x-data="procedureBarChart('chartTopProcedures', @js($top))"
                 @procedure-chart-updated.window="updateChart($event.detail.top)">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-5 gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                <span class="icon-[solar--graph-new-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">10 Besar Tindakan Terbanyak</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Distribusi Kasus Prosedur Medis ICD-9-CM</p>
                            </div>
                        </div>

                        <!-- Mode Switcher: Bertumpuk vs Berdampingan vs Total Tindakan -->
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-meta-4/60 p-1 rounded-xl self-start sm:self-auto">
                            <button type="button" 
                                @click="setMode('stacked')" 
                                :class="chartMode === 'stacked' ? 'bg-white dark:bg-boxdark text-cyan-600 dark:text-cyan-400 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer"
                                title="Akumulasi Laki-laki + Perempuan">
                                Bertumpuk
                            </button>
                            <button type="button" 
                                @click="setMode('grouped')" 
                                :class="chartMode === 'grouped' ? 'bg-white dark:bg-boxdark text-cyan-600 dark:text-cyan-400 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer"
                                title="Komparasi Laki-laki dan Perempuan Berdampingan">
                                Berdampingan
                            </button>
                            <button type="button" 
                                @click="setMode('total')" 
                                :class="chartMode === 'total' ? 'bg-white dark:bg-boxdark text-cyan-600 dark:text-cyan-400 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer"
                                title="Total Kasus Keseluruhan">
                                Total Kasus
                            </button>
                        </div>
                    </div>

                    <!-- Chart Canvas Container -->
                    <div class="h-[320px] w-full relative">
                        <div wire:ignore class="w-full h-full">
                            <canvas id="chartTopProcedures" class="w-full h-full"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Card Top Prosedur Unggulan (Span 1) -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <span class="icon-[solar--ranking-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Top Prosedur</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Proporsi Utama vs Sekunder</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        @forelse (array_slice($top['items'] ?? [], 0, 5) as $item)
                            <div class="p-3 rounded-2xl bg-gray-50/80 dark:bg-meta-4/40 border border-stroke/60 dark:border-strokedark/60 hover:border-cyan-500/40 transition">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="w-5 h-5 rounded-md bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-black text-[10px] flex items-center justify-center shrink-0">
                                            {{ $item['rank'] }}
                                        </span>
                                        <span class="font-mono text-xs font-bold text-gray-800 dark:text-white truncate">
                                            {{ $item['code'] }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-black text-cyan-600 dark:text-cyan-400 shrink-0">
                                        {{ number_format($item['total']) }} <span class="text-[10px] font-normal text-gray-400">tindakan</span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium truncate mb-2">
                                    {{ $item['name'] }}
                                </p>
                                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                        Utama: {{ $item['utama'] }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                        Sekunder: {{ $item['sekunder'] }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                        Poli: {{ $item['poli'] }} | IGD: {{ $item['igd'] }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-xs">
                                Belum ada data tindakan pada periode ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-stroke/70 dark:border-strokedark/70 text-[10px] text-gray-400 font-semibold text-center">
                    Top 5 tindakan medis dengan frekuensi tertinggi
                </div>
            </div>
        </div>

        <!-- 5. Tabel Rekapitulasi Data Tindakan (ICD-9-CM) -->
        <div class="bg-white dark:bg-boxdark rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-stroke/70 dark:border-strokedark/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                        <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">
                            Tabel Rekapitulasi Prosedur Medis
                        </h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                            Rincian Per Kode ICD-9-CM & Distribusi Layanan
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400 font-bold">Baris per halaman:</span>
                    <select wire:model.live="perPage" class="text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white py-1.5 px-3">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Konten -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stroke/80 dark:border-strokedark/80 bg-gray-50/75 dark:bg-meta-4/30 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-4 px-4 text-center w-14">No</th>
                            <th class="py-4 px-4 w-28">Kode ICD-9</th>
                            <th class="py-4 px-4">Deskripsi Prosedur Medis</th>
                            <th class="py-4 px-3 text-center">Gender (L/P)</th>
                            <th class="py-4 px-4 text-center">Layanan (Poli / IGD / Ranap)</th>
                            <th class="py-4 px-3 text-center">Prioritas</th>
                            <th class="py-4 px-4 text-right">Total Tindakan</th>
                            <th class="py-4 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 text-xs">
                        @forelse ($procedures as $idx => $row)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/20 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-gray-400">
                                    {{ $procedures->firstItem() + $idx }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-black text-cyan-600 dark:text-cyan-400 px-2 py-0.5 rounded-lg bg-cyan-500/10">
                                        {{ $row->kode }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-800 dark:text-white">
                                        {{ $row->deskripsi_panjang }}
                                    </div>
                                    @if (!empty($row->deskripsi_pendek) && $row->deskripsi_pendek !== $row->deskripsi_panjang)
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $row->deskripsi_pendek }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <div class="inline-flex items-center gap-1.5 font-bold">
                                        <span class="text-blue-500 font-mono" title="Laki-laki">{{ $row->total_pria }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">/</span>
                                        <span class="text-pink-500 font-mono" title="Perempuan">{{ $row->total_wanita }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1 font-bold text-[11px]">
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400" title="Poliklinik">
                                            Poli: {{ $row->total_poli }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400" title="Gawat Darurat">
                                            IGD: {{ $row->total_igd }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-purple-500/10 text-purple-600 dark:text-purple-400" title="Rawat Inap">
                                            Ranap: {{ $row->total_ranap }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <div class="inline-flex items-center gap-1 font-bold text-[10px]">
                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-meta-4 text-blue-600 dark:text-blue-400" title="Utama">
                                            U: {{ $row->total_utama }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300" title="Sekunder">
                                            S: {{ $row->total_sekunder }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="font-black text-gray-800 dark:text-white text-sm">
                                        {{ number_format($row->total_tindakan) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button" wire:click="openDetail('{{ $row->kode }}', '{{ addslashes($row->deskripsi_panjang) }}')"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-50 dark:bg-cyan-500/10 hover:bg-cyan-100 text-cyan-600 dark:text-cyan-400 text-xs font-bold transition flex items-center justify-center gap-1 mx-auto cursor-pointer"
                                        title="Lihat Pasien">
                                        <span class="icon-[solar--eye-bold] text-sm"></span>
                                        <span>Rincian</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-gray-400 text-xs">
                                    Tidak ada data tindakan yang sesuai dengan kriteria filter saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if ($procedures->hasPages())
                <div class="p-6 border-t border-stroke/70 dark:border-strokedark/70">
                    {{ $procedures->links() }}
                </div>
            @endif
        </div>

        <!-- 6. Modal Drilldown Kasus Pasien (Class modifier !mt-0 sesuai standar) -->
        @if ($showDetailModal)
            <div class="fixed inset-0 z-[99999] flex items-start justify-center p-4 sm:p-6 overflow-y-auto bg-black/60 backdrop-blur-xs transition !mt-0"
                 x-data="{}"
                 @keydown.escape.window="$wire.closeDetail()">
                <div class="relative w-full max-w-4xl bg-white dark:bg-boxdark rounded-[2.5rem] shadow-2xl border border-stroke dark:border-strokedark my-8 overflow-hidden !mt-0">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-6 sm:p-7 border-b border-stroke/80 dark:border-strokedark/80 bg-gray-50/50 dark:bg-meta-4/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                <span class="icon-[solar--user-id-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-cyan-600 dark:text-cyan-400 px-2 py-0.5 rounded-lg bg-cyan-500/10 text-xs">
                                        {{ $selectedCode }}
                                    </span>
                                    <h4 class="text-base font-black text-gray-800 dark:text-white">
                                        Rincian Pasien Tindakan Medis
                                    </h4>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5 font-medium truncate max-w-xl">
                                    {{ $selectedDescription }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeDetail"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-meta-4 transition cursor-pointer">
                            <span class="icon-[solar--close-circle-bold] text-2xl"></span>
                        </button>
                    </div>

                    <!-- Modal Body: Tabel Kunjungan Pasien -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-stroke/70 dark:border-strokedark/70 text-[10px] font-black uppercase text-gray-400 tracking-wider">
                                        <th class="py-2.5 px-3">No. Rawat</th>
                                        <th class="py-2.5 px-3">No. RM</th>
                                        <th class="py-2.5 px-3">Nama Pasien</th>
                                        <th class="py-2.5 px-2 text-center">JK / Umur</th>
                                        <th class="py-2.5 px-3">Tgl Rawat</th>
                                        <th class="py-2.5 px-3">Layanan / Unit</th>
                                        <th class="py-2.5 px-3">DPJP / Dokter</th>
                                        <th class="py-2.5 px-2 text-center">Prioritas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stroke/50 dark:divide-strokedark/50">
                                    @forelse ($drilldownPatients as $p)
                                        <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/20">
                                            <td class="py-2.5 px-3 font-mono font-bold text-gray-600 dark:text-gray-300">
                                                {{ $p['no_rawat'] }}
                                            </td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-cyan-600 dark:text-cyan-400">
                                                {{ $p['no_rkm_medis'] }}
                                            </td>
                                            <td class="py-2.5 px-3 font-bold text-gray-800 dark:text-white">
                                                {{ $p['nm_pasien'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-center font-bold">
                                                <span class="{{ $p['jk'] === 'L' ? 'text-blue-500' : 'text-pink-500' }}">{{ $p['jk'] }}</span>
                                                <span class="text-gray-400 text-[10px]">({{ $p['umur'] }} th)</span>
                                            </td>
                                            <td class="py-2.5 px-3 text-gray-600 dark:text-gray-400">
                                                {{ Carbon\Carbon::parse($p['tgl_registrasi'])->format('d/m/Y') }}
                                            </td>
                                            <td class="py-2.5 px-3">
                                                @if ($p['status_lanjut'] === 'Ranap')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                                        Ranap: {{ $p['unit'] }}
                                                    </span>
                                                @elseif ($p['kd_poli'] === 'IGDK')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                                        IGD
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                                        Poli: {{ $p['unit'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3 text-gray-600 dark:text-gray-400 truncate max-w-xs">
                                                {{ $p['dokter'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p['prioritas'] === 'Utama' ? 'bg-blue-500/10 text-blue-600' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $p['prioritas'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-6 text-gray-400 text-xs">
                                                Tidak ditemukan riwayat pasien untuk tindakan ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 sm:p-5 border-t border-stroke/70 dark:border-strokedark/70 bg-gray-50/50 dark:bg-meta-4/20 flex items-center justify-between text-xs font-semibold text-gray-400">
                        <span>Menampilkan maks. 50 data kunjungan pasien terbaru</span>
                        <button type="button" wire:click="closeDetail"
                            class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-meta-4 hover:bg-gray-300 text-gray-700 dark:text-gray-200 font-bold transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Script Chart.js dengan Alpine.js untuk Top 10 Tindakan Terbanyak -->
    @script
        <script>
            Alpine.data('procedureBarChart', (chartId, initialData) => ({
                chart: null,
                currentData: null,
                chartMode: 'stacked', // 'stacked' | 'grouped' | 'total'

                init() {
                    this.currentData = initialData;
                    this.$nextTick(() => {
                        this.renderChart(this.currentData);
                    });
                },

                destroy() {
                    this.destroyExistingChart();
                },

                setMode(mode) {
                    if (this.chartMode === mode) return;
                    this.chartMode = mode;
                    this.renderChart(this.currentData || initialData);
                },

                destroyExistingChart() {
                    const canvas = document.getElementById(chartId);
                    if (canvas && typeof Chart !== 'undefined' && typeof Chart.getChart === 'function') {
                        const existingChart = Chart.getChart(canvas);
                        if (existingChart) {
                            try { existingChart.destroy(); } catch (e) {}
                        }
                    }
                    if (this.chart) {
                        try { this.chart.destroy(); } catch (e) {}
                        this.chart = null;
                    }
                },

                updateChart(data) {
                    if (!data) return;
                    this.currentData = data;
                    this.renderChart(data);
                },

                formatLabels(data) {
                    if (!data) return [];
                    if (Array.isArray(data.items) && data.items.length > 0) {
                        return data.items.map(it => {
                            const name = it.short_name || it.name || '';
                            const shortName = name.length > 20 ? name.substring(0, 18) + '…' : name;
                            return [it.code, shortName];
                        });
                    }
                    return (data.labels || []).map(lbl => {
                        const match = typeof lbl === 'string' ? lbl.match(/^([^\s(]+)\s*\((.*)\)$/) : null;
                        return match ? [match[1], match[2]] : lbl;
                    });
                },

                renderChart(data) {
                    data = data || this.currentData;
                    if (!data) return;
                    const canvas = document.getElementById(chartId);
                    if (!canvas) return;

                    this.destroyExistingChart();

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.04)';
                    const textColor = isDark ? '#94A3B8' : '#64748B';

                    const isStacked = this.chartMode === 'stacked';
                    const isTotal = this.chartMode === 'total';
                    const formattedLabels = this.formatLabels(data);

                    let datasets = [];

                    if (isTotal) {
                        const totalData = (data.items && data.items.length > 0)
                            ? data.items.map(i => Number(i.total || 0))
                            : (data.data_total || []).map(Number);

                        datasets = [
                            {
                                label: 'Total Tindakan',
                                data: totalData,
                                backgroundColor: '#06B6D4', // Cyan
                                borderRadius: 6,
                                barPercentage: 0.65,
                                categoryPercentage: 0.8,
                                maxBarThickness: 34,
                            }
                        ];
                    } else {
                        datasets = [
                            {
                                label: 'Laki-laki',
                                data: [...(data.data_pria || [])].map(Number),
                                backgroundColor: '#3B82F6', // Blue
                                borderRadius: isStacked ? 0 : 6,
                                barPercentage: isStacked ? 0.65 : 0.8,
                                categoryPercentage: 0.8,
                                maxBarThickness: isStacked ? 38 : 22,
                            },
                            {
                                label: 'Perempuan',
                                data: [...(data.data_wanita || [])].map(Number),
                                backgroundColor: '#EC4899', // Pink
                                borderRadius: isStacked ? 6 : 6,
                                barPercentage: isStacked ? 0.65 : 0.8,
                                categoryPercentage: 0.8,
                                maxBarThickness: isStacked ? 38 : 22,
                            }
                        ];
                    }

                    try {
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: formattedLabels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        display: !isTotal,
                                        position: 'top',
                                        align: 'end',
                                        labels: {
                                            boxWidth: 10,
                                            boxHeight: 10,
                                            usePointStyle: true,
                                            color: textColor,
                                            font: { size: 11, weight: 'bold' }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? '#1E293B' : '#0F172A',
                                        padding: 12,
                                        cornerRadius: 10,
                                        titleFont: { size: 12, weight: 'bold' },
                                        bodyFont: { size: 11 },
                                        callbacks: {
                                            title: (items) => {
                                                if (!items || !items.length) return '';
                                                const idx = items[0].dataIndex;
                                                if (data.items && data.items[idx]) {
                                                    return `${data.items[idx].code} - ${data.items[idx].name}`;
                                                }
                                                const lbl = items[0].label;
                                                return Array.isArray(lbl) ? lbl.join(' - ') : lbl;
                                            },
                                            afterBody: (items) => {
                                                if (!items || !items.length) return '';
                                                const idx = items[0].dataIndex;
                                                const it = (data.items && data.items[idx]) ? data.items[idx] : null;
                                                const total = it ? it.total : (Number(data.data_pria?.[idx] || 0) + Number(data.data_wanita?.[idx] || 0));
                                                const pria = it ? it.pria : Number(data.data_pria?.[idx] || 0);
                                                const wanita = it ? it.wanita : Number(data.data_wanita?.[idx] || 0);

                                                const lines = [
                                                    `Total Tindakan: ${total}`,
                                                    `Laki-laki: ${pria} | Perempuan: ${wanita}`
                                                ];
                                                if (it && (it.utama !== undefined && it.sekunder !== undefined)) {
                                                    lines.push(`Utama: ${it.utama} | Sekunder: ${it.sekunder}`);
                                                }
                                                if (it && (it.poli !== undefined && it.igd !== undefined && it.ranap !== undefined)) {
                                                    lines.push(`Poli: ${it.poli} | IGD: ${it.igd} | Ranap: ${it.ranap}`);
                                                }
                                                return lines.join('\n');
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: isStacked,
                                        grid: { display: false },
                                        ticks: {
                                            color: textColor,
                                            font: { size: 10, weight: '600' },
                                            maxRotation: 0,
                                            minRotation: 0,
                                        }
                                    },
                                    y: {
                                        stacked: isStacked,
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: {
                                            color: textColor,
                                            font: { size: 10 },
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        console.error('Error inisialisasi Chart.js untuk tindakan ICD-9:', e);
                    }
                }
            }));
        </script>
    @endscript
</x-content>
