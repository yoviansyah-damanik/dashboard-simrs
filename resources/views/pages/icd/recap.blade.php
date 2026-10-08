<x-content>
    <x-breadcrumb title="Rekapitulasi Data Penyakit" :items="[
        ['title' => 'Laporan'],
        ['title' => 'Rekapitulasi Data Penyakit']
    ]" />

    <div class="space-y-6 animate-in fade-in duration-500">
        <!-- 1. Header Command Card -->
        <div class="relative p-6 sm:p-8 rounded-[2.5rem] bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white overflow-hidden shadow-2xl border border-white/10">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-widest">
                        <span class="icon-[solar--stethoscope-bold-duotone] text-sm"></span>
                        Surveilans Morbiditas & ICD-10
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white">
                        Rekapitulasi Data Penyakit
                    </h2>
                    <p class="text-sm text-slate-300 max-w-2xl font-medium">
                        Pemetaan 10 & 20 besar pola penyakit, segmentasi kasus baru/lama, serta profil morbiditas pasien Rawat Jalan dan Rawat Inap secara terintegrasi.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center flex-wrap gap-3 shrink-0">
                    <button type="button" wire:click="exportPdf" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-900/30 transition-all cursor-pointer">
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

        <!-- 2. Filter Bar -->
        <div class="p-6 bg-white dark:bg-boxdark rounded-[2.2rem] border border-stroke dark:border-strokedark shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stroke/70 dark:border-strokedark/70">
                <div class="flex items-center gap-2">
                    <span class="icon-[solar--filter-bold-duotone] text-lg text-emerald-600 dark:text-emerald-400"></span>
                    <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Filter Data Penyakit</h4>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-bold">Mode Periode:</span>
                    <div class="inline-flex p-0.5 rounded-lg bg-gray-100 dark:bg-meta-4 text-xs font-bold">
                        <button type="button" wire:click="setPeriodType('month')"
                            class="px-2.5 py-1 rounded-md transition-all {{ $periodType === 'month' ? 'bg-white dark:bg-boxdark text-emerald-600 shadow-2xs' : 'text-gray-500' }}">
                            Bulan/Tahun
                        </button>
                        <button type="button" wire:click="setPeriodType('range')"
                            class="px-2.5 py-1 rounded-md transition-all {{ $periodType === 'range' ? 'bg-white dark:bg-boxdark text-emerald-600 shadow-2xs' : 'text-gray-500' }}">
                            Rentang Tanggal
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                <!-- Periode: Bulan/Tahun atau Rentang -->
                @if ($periodType === 'month')
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tahun</label>
                        <select wire:model.live="year" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                            @foreach ($this->availableYears as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Bulan</label>
                        <select wire:model.live="month" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                            <option value="">Semua Bulan (Setahun)</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tgl Mulai</label>
                        <input type="date" wire:model.live="startDate" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tgl Akhir</label>
                        <input type="date" wire:model.live="endDate" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                    </div>
                @endif

                <!-- Status Pelayanan -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Status Rawat</label>
                    <select wire:model.live="serviceStatus" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                        <option value="all">Semua Layanan</option>
                        <option value="Poli">Poliklinik (Poli)</option>
                        <option value="IGD">Gawat Darurat (IGD)</option>
                        <option value="Ralan">Rawat Jalan (Poli + IGD)</option>
                        <option value="Ranap">Rawat Inap (Ranap)</option>
                    </select>
                </div>

                <!-- Kasus Khusus (ISK & Surveilans) -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Kasus Khusus</label>
                    <select wire:model.live="specialCase" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                        <option value="all">Semua Kasus (Umum)</option>
                        <optgroup label="Infeksi Saluran Kemih">
                            <option value="isk">Infeksi Saluran Kemih (ISK / UTI)</option>
                        </optgroup>
                        <optgroup label="Surveilans & Penyakit Menular">
                            <option value="ispa">ISPA & Pneumonia</option>
                            <option value="tb">Tuberkulosis (TBC)</option>
                            <option value="diare">Diare & Gastroenteritis (GEA)</option>
                            <option value="dbd">Demam Berdarah Dengue (DBD)</option>
                            <option value="tifoid">Demam Tifoid</option>
                        </optgroup>
                        <optgroup label="Penyakit Kronis & Bedah">
                            <option value="hipertensi">Hipertensi</option>
                            <option value="dm">Diabetes Melitus (DM)</option>
                            <option value="dispepsia">Dispepsia & Gastritis</option>
                            <option value="ido">Infeksi Daerah Operasi (IDO)</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Prioritas Diagnosa -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Prioritas</label>
                    <select wire:model.live="priority" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                        <option value="all">Semua Diagnosa</option>
                        <option value="1">Diagnosa Primer (Utama)</option>
                        <option value="2">Diagnosa Sekunder</option>
                    </select>
                </div>

                <!-- Status Kasus -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Status Kasus</label>
                    <select wire:model.live="caseType" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                        <option value="all">Semua Kasus</option>
                        <option value="Baru">Kasus Baru</option>
                        <option value="Lama">Kasus Lama</option>
                    </select>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Jenis Kelamin</label>
                    <select wire:model.live="gender" class="w-full text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3">
                        <option value="all">Semua Gender</option>
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
            </div>

            <!-- Active special case indicator banner -->
            @if ($specialCase !== 'all' && isset($this->specialCases[$specialCase]))
                @php $sc = $this->specialCases[$specialCase]; @endphp
                <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs animate-in fade-in duration-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                            <span class="icon-[solar--shield-warning-bold-duotone] text-base"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-gray-800 dark:text-white">Kasus Khusus: {{ $sc['label'] }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-600 text-white uppercase tracking-wider">{{ $sc['short_label'] }}</span>
                            </div>
                            <span class="text-gray-500 dark:text-gray-400 block text-[11px] mt-0.5 font-medium">{{ $sc['description'] }}</span>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('specialCase', 'all')" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-500/20 bg-white dark:bg-meta-4 px-3 py-1.5 rounded-xl border border-emerald-500/20 shrink-0 self-start sm:self-auto cursor-pointer transition">
                        <span class="icon-[solar--close-circle-linear] text-sm"></span>
                        <span>Semua Kasus</span>
                    </button>
                </div>
            @endif

            <!-- Search input bar -->
            <div class="relative pt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pt-1 pointer-events-none text-gray-400">
                    <span class="icon-[solar--magnifer-linear] text-base"></span>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan Kode ICD-10 (misal: I10, A09) atau Nama Penyakit..."
                    class="w-full text-xs font-semibold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 pl-10 pr-4 py-2.5 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>

        <!-- 3. Key Metric KPI Cards -->
        @php $summary = $this->summary; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Kasus -->
            <div class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--chart-square-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        Total Kasus
                    </span>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                        {{ number_format($summary['total_kasus']) }}
                    </h3>
                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                        <span>Penyakit Unik: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($summary['total_penyakit_unik']) }}</strong></span>
                        <span>Pasien: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($summary['total_pasien_unik']) }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Kasus Baru vs Kasus Lama -->
            <div class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        Klasifikasi Kasus
                    </span>
                </div>
                <div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                            {{ number_format($summary['kasus_baru']) }}
                        </h3>
                        <span class="text-xs font-bold text-gray-400">Kasus Baru</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                        <span>Kasus Lama: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($summary['kasus_lama']) }}</strong></span>
                        <span class="text-blue-600 font-bold">
                            {{ $summary['total_kasus'] > 0 ? round(($summary['kasus_baru'] / $summary['total_kasus']) * 100, 1) : 0 }}% Baru
                        </span>
                    </div>
                </div>
            </div>

            <!-- Diagnosa Primer vs Sekunder -->
            <div class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <span class="icon-[solar--diploma-verified-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        Diagnosa Primer
                    </span>
                </div>
                <div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                            {{ number_format($summary['primer']) }}
                        </h3>
                        <span class="text-xs font-bold text-gray-400">Primer</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                        <span>Sekunder: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($summary['sekunder']) }}</strong></span>
                        <span class="text-amber-600 font-bold">
                            {{ $summary['total_kasus'] > 0 ? round(($summary['primer'] / $summary['total_kasus']) * 100, 1) : 0 }}% Utama
                        </span>
                    </div>
                </div>
            </div>

            <!-- Demografi Gender & Layanan -->
            <div class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="icon-[solar--pie-chart-2-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-purple-500/10 text-purple-600 dark:text-purple-400">
                        Demografi & Instalasi
                    </span>
                </div>
                <div>
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span class="text-blue-600">Laki-laki: {{ number_format($summary['pria']) }}</span>
                        <span class="text-pink-600">Perempuan: {{ number_format($summary['wanita']) }}</span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden mb-2">
                        @php
                            $totalGender = ($summary['pria'] + $summary['wanita']) ?: 1;
                            $pctPria = round(($summary['pria'] / $totalGender) * 100);
                        @endphp
                        <div class="h-full bg-blue-500 transition-all duration-300" style="width: {{ $pctPria }}%"></div>
                    </div>
                    <div class="pt-1.5 border-t border-stroke/50 dark:border-strokedark/50 grid grid-cols-3 gap-1.5 text-center">
                        <div class="bg-emerald-50 dark:bg-emerald-500/10 rounded-lg py-1 px-1">
                            <span class="text-gray-400 block text-[9px] uppercase font-bold">Poli</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">{{ number_format($summary['poli']) }}</span>
                        </div>
                        <div class="bg-rose-50 dark:bg-rose-500/10 rounded-lg py-1 px-1">
                            <span class="text-gray-400 block text-[9px] uppercase font-bold">IGD</span>
                            <span class="font-black text-rose-600 dark:text-rose-400 text-xs">{{ number_format($summary['igd']) }}</span>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-lg py-1 px-1">
                            <span class="text-gray-400 block text-[9px] uppercase font-bold">Ranap</span>
                            <span class="font-black text-blue-600 dark:text-blue-400 text-xs">{{ number_format($summary['ranap']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Visualisasi Top 10 Penyakit Terbanyak (Grafik & Tabel Ranking) -->
        @php $top = $this->topDiseases; @endphp
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Grafik Batang Top 10 (Span 2) -->
            <div class="lg:col-span-2 bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between"
                 x-data="diseaseBarChart('chartTopDiseases', @js($top))"
                 @disease-chart-updated.window="updateChart($event.detail.top)">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-5 gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <span class="icon-[solar--graph-new-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">10 Besar Penyakit Terbanyak</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Distribusi Kasus Morbiditas Pasien</p>
                            </div>
                        </div>

                        <!-- Mode Switcher: Bertumpuk vs Berdampingan vs Total Kasus -->
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-meta-4/60 p-1 rounded-xl self-start sm:self-auto">
                            <button type="button" 
                                @click="setMode('stacked')" 
                                :class="chartMode === 'stacked' ? 'bg-white dark:bg-boxdark text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer"
                                title="Akumulasi Laki-laki + Perempuan">
                                Bertumpuk
                            </button>
                            <button type="button" 
                                @click="setMode('grouped')" 
                                :class="chartMode === 'grouped' ? 'bg-white dark:bg-boxdark text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer"
                                title="Komparasi Laki-laki dan Perempuan Berdampingan">
                                Berdampingan
                            </button>
                            <button type="button" 
                                @click="setMode('total')" 
                                :class="chartMode === 'total' ? 'bg-white dark:bg-boxdark text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer"
                                title="Total Kasus Keseluruhan">
                                Total Kasus
                            </button>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div class="h-[320px] w-full relative">
                        <div wire:ignore class="w-full h-full">
                            <canvas id="chartTopDiseases" class="w-full h-full"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Card Top 5 Unggulan (Span 1) -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <span class="icon-[solar--ranking-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Top Morbiditas</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Proporsi Kasus Baru vs Lama</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse (array_slice($top['items'], 0, 5) as $item)
                            @php
                                $maxTotal = max(array_column($top['items'], 'total') ?: [1]);
                                $barWidth = round(($item['total'] / $maxTotal) * 100);
                            @endphp
                            <div class="p-3 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/50 dark:border-strokedark/50 hover:border-emerald-500/30 transition-all cursor-pointer"
                                wire:click="openDetail('{{ $item['code'] }}', '{{ addslashes($item['name']) }}')">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-black shrink-0 {{ $item['rank'] == 1 ? 'bg-amber-500 text-white' : ($item['rank'] == 2 ? 'bg-slate-400 text-white' : ($item['rank'] == 3 ? 'bg-amber-700 text-white' : 'bg-gray-200 dark:bg-meta-4 text-gray-600')) }}">
                                            {{ $item['rank'] }}
                                        </span>
                                        <div class="min-w-0">
                                            <span class="text-xs font-mono font-black text-emerald-600 dark:text-emerald-400">{{ $item['code'] }}</span>
                                            <span class="text-[11px] font-bold text-gray-800 dark:text-white truncate block" title="{{ $item['name'] }}">
                                                {{ $item['name'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-black text-gray-800 dark:text-white shrink-0 ml-1">
                                        {{ number_format($item['total']) }}
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden mb-1">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $barWidth }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-gray-400 font-medium">
                                    <span>Baru: <strong class="text-emerald-600 font-bold">{{ number_format($item['baru']) }}</strong></span>
                                    <span>Lama: <strong class="text-blue-600 font-bold">{{ number_format($item['lama']) }}</strong></span>
                                    <span>L/P: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($item['pria']) }}/{{ number_format($item['wanita']) }}</strong></span>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-gray-400 font-medium mt-1 pt-1 border-t border-stroke/40 dark:border-strokedark/40">
                                    <span>Poli: <strong class="text-emerald-600 font-bold">{{ number_format($item['poli']) }}</strong></span>
                                    <span>IGD: <strong class="text-rose-600 font-bold">{{ number_format($item['igd']) }}</strong></span>
                                    <span>Ranap: <strong class="text-blue-600 font-bold">{{ number_format($item['ranap']) }}</strong></span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">Belum ada data diagnosa penyakit.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Tabel Morbiditas Lengkap -->
        <div class="bg-white dark:bg-boxdark rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-stroke/70 dark:border-strokedark/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Daftar Rekapitulasi Morbiditas Penyakit</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tabel rincian kasus baru, kasus lama, profil jenis kelamin, dan klasifikasi pelayanan</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400">Tampilkan:</span>
                    <select wire:model.live="perPage" class="text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white py-1.5 px-3">
                        <option value="10">10 Baris</option>
                        <option value="25">25 Baris</option>
                        <option value="50">50 Baris</option>
                        <option value="100">100 Baris</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stroke dark:border-strokedark bg-gray-50/80 dark:bg-meta-4/40 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3 px-4 text-center w-12">No</th>
                            <th class="py-3 px-4 w-28">Kode ICD</th>
                            <th class="py-3 px-4">Nama Diagnosa / Penyakit</th>
                            <th class="py-3 px-3 text-center">Baru (L/P)</th>
                            <th class="py-3 px-3 text-center">Lama (L/P)</th>
                            <th class="py-3 px-3 text-center">Total Gender</th>
                            <th class="py-3 px-3 text-center">Layanan (Poli / IGD / Ranap)</th>
                            <th class="py-3 px-4 text-center">Total Kasus</th>
                            <th class="py-3 px-4 text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 text-xs">
                        @forelse ($diseases as $item)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-meta-4/20 transition-colors">
                                <td class="py-3 px-4 text-center font-bold text-gray-400">
                                    {{ $diseases->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-md font-mono font-black text-xs bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        {{ $item->kd_penyakit }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-gray-800 dark:text-white">
                                    {{ $item->nm_penyakit }}
                                </td>
                                <td class="py-3 px-3 text-center font-semibold text-gray-600 dark:text-gray-300">
                                    <span class="text-blue-600 font-bold">{{ number_format($item->baru_pria) }}</span> / 
                                    <span class="text-pink-600 font-bold">{{ number_format($item->baru_wanita) }}</span>
                                </td>
                                <td class="py-3 px-3 text-center font-semibold text-gray-600 dark:text-gray-300">
                                    <span class="text-blue-600 font-bold">{{ number_format($item->lama_pria) }}</span> / 
                                    <span class="text-pink-600 font-bold">{{ number_format($item->lama_wanita) }}</span>
                                </td>
                                <td class="py-3 px-3 text-center font-semibold text-gray-600 dark:text-gray-300">
                                    <span class="text-blue-600 font-bold">{{ number_format($item->total_pria) }} L</span> / 
                                    <span class="text-pink-600 font-bold">{{ number_format($item->total_wanita) }} P</span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400" title="Poliklinik">
                                            Poli: {{ number_format($item->total_poli) }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400" title="Gawat Darurat">
                                            IGD: {{ number_format($item->total_igd) }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400" title="Rawat Inap">
                                            Ranap: {{ number_format($item->total_ranap) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($item->total_kasus) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" wire:click="openDetail('{{ $item->kd_penyakit }}', '{{ addslashes($item->nm_penyakit) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 transition cursor-pointer"
                                        title="Lihat Rincian Pasien">
                                        <span class="icon-[solar--eye-bold-duotone] text-sm"></span>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-400">
                                    Tidak ada data diagnosa penyakit yang sesuai kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            <div class="p-5 border-t border-stroke/70 dark:border-strokedark/70">
                {{ $diseases->links() }}
            </div>
        </div>

        <!-- 6. Modal Drilldown Kasus Pasien (Wajib menggunakan modifier !mt-0) -->
        @if ($showDetailModal)
            <div class="fixed inset-0 z-50 flex items-start justify-center p-4 sm:p-6 md:p-10 bg-slate-900/60 backdrop-blur-sm overflow-y-auto !mt-0 animate-in fade-in duration-200">
                <div class="relative w-full max-w-5xl bg-white dark:bg-boxdark rounded-[2.5rem] shadow-2xl border border-stroke dark:border-strokedark overflow-hidden !mt-0 my-auto">
                    <!-- Modal Header -->
                    <div class="p-6 sm:p-7 border-b border-stroke/70 dark:border-strokedark/70 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                <span class="icon-[solar--stethoscope-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded font-mono font-black text-xs bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                        {{ $selectedDiseaseCode }}
                                    </span>
                                    <h3 class="text-base sm:text-lg font-black text-gray-800 dark:text-white">
                                        {{ $selectedDiseaseName }}
                                    </h3>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">Daftar kunjungan pasien dengan diagnosa ini (maksimal 50 data terbaru)</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeDetail" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 dark:hover:bg-meta-4/80 flex items-center justify-center text-gray-500 transition cursor-pointer">
                            <span class="icon-[solar--close-circle-bold] text-xl"></span>
                        </button>
                    </div>

                    <!-- Modal Body Table -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        <div class="overflow-x-auto rounded-2xl border border-stroke dark:border-strokedark">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4/50 text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">
                                        <th class="py-2.5 px-3 text-center">No</th>
                                        <th class="py-2.5 px-3">No. RM</th>
                                        <th class="py-2.5 px-3">Nama Pasien</th>
                                        <th class="py-2.5 px-3 text-center">L/P</th>
                                        <th class="py-2.5 px-3 text-center">Umur</th>
                                        <th class="py-2.5 px-3">Tgl Rawat</th>
                                        <th class="py-2.5 px-3">Layanan</th>
                                        <th class="py-2.5 px-3 text-center">Prioritas</th>
                                        <th class="py-2.5 px-3 text-center">Kasus</th>
                                        <th class="py-2.5 px-3">Dokter DPJP</th>
                                        <th class="py-2.5 px-3">Poliklinik/Ruang</th>
                                        <th class="py-2.5 px-3">Penjamin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stroke/50 dark:divide-strokedark/50">
                                    @forelse ($drilldownPatients as $p)
                                        <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/15 transition-colors">
                                            <td class="py-2 px-3 text-center text-gray-400 font-bold">{{ $loop->iteration }}</td>
                                            <td class="py-2 px-3 font-mono font-bold text-gray-800 dark:text-white">{{ $p['no_rkm_medis'] }}</td>
                                            <td class="py-2 px-3 font-bold text-gray-800 dark:text-white">{{ $p['nama'] }}</td>
                                            <td class="py-2 px-3 text-center font-bold {{ $p['jk'] === 'L' ? 'text-blue-600' : 'text-pink-600' }}">{{ $p['jk'] }}</td>
                                            <td class="py-2 px-3 text-center text-gray-600 dark:text-gray-300">{{ $p['umur'] }} th</td>
                                            <td class="py-2 px-3 text-gray-500">{{ $p['tanggal'] }}</td>
                                            <td class="py-2 px-3">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $p['layanan'] === 'Ranap' ? 'bg-blue-500/10 text-blue-600' : ($p['layanan'] === 'IGD' ? 'bg-rose-500/10 text-rose-600' : 'bg-emerald-500/10 text-emerald-600') }}" title="{{ $p['layanan_detail'] ?? $p['layanan'] }}">
                                                    {{ $p['layanan'] }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $p['prioritas'] === 'Primer' ? 'bg-amber-500/10 text-amber-600' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-400' }}">
                                                    {{ $p['prioritas'] }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $p['kasus'] === 'Baru' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-purple-500/10 text-purple-600' }}">
                                                    {{ $p['kasus'] }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 font-medium text-gray-700 dark:text-gray-300 truncate max-w-[140px]" title="{{ $p['dokter'] }}">{{ $p['dokter'] }}</td>
                                            <td class="py-2 px-3 text-gray-500 truncate max-w-[120px]">{{ $p['unit'] }}</td>
                                            <td class="py-2 px-3 text-gray-500 truncate max-w-[100px]">{{ $p['penjamin'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="py-6 text-center text-gray-400">
                                                Tidak ada data rincian pasien ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 sm:p-5 border-t border-stroke/70 dark:border-strokedark/70 bg-gray-50/60 dark:bg-meta-4/20 flex justify-end">
                        <button type="button" wire:click="closeDetail" class="px-5 py-2 rounded-xl text-xs font-bold bg-gray-200 hover:bg-gray-300 dark:bg-meta-4 dark:hover:bg-meta-4/80 text-gray-800 dark:text-white transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @script
        <script>
            Alpine.data('diseaseBarChart', (chartId, initialData) => ({
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
                            const shortName = it.name ? (it.name.length > 20 ? it.name.substring(0, 18) + '…' : it.name) : '';
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
                                label: 'Total Kasus',
                                data: totalData,
                                backgroundColor: '#10B981',
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
                                backgroundColor: '#3B82F6',
                                borderRadius: isStacked ? 0 : 6,
                                barPercentage: isStacked ? 0.65 : 0.8,
                                categoryPercentage: 0.8,
                                maxBarThickness: isStacked ? 38 : 22,
                            },
                            {
                                label: 'Perempuan',
                                data: [...(data.data_wanita || [])].map(Number),
                                backgroundColor: '#EC4899',
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
                                                    `Total Kasus: ${total} Pasien`,
                                                    `Laki-laki: ${pria} | Perempuan: ${wanita}`
                                                ];
                                                if (it && (it.baru !== undefined && it.lama !== undefined)) {
                                                    lines.push(`Kasus Baru: ${it.baru} | Lama: ${it.lama}`);
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
                        console.error('Error inisialisasi Chart.js:', e);
                    }
                }
            }));
        </script>
    @endscript
</x-content>
