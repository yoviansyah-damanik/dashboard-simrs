<div class="space-y-6">
    {{-- HEADER & BREADCRUMB --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                <span class="icon-[solar--chart-line-up-bold] size-4"></span>
                <span>Evaluasi Pendaftaran Rawat Jalan &bull; Terintegrasi API Terapi</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Konsistensi & Kepatuhan Antrean Online
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Monitoring pemanfaatan antrean daring (Mobile JKN) vs Onsite (Loket Admisi & APM Mandiri)
            </p>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <button wire:click="refreshData" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 active:scale-95 transition disabled:opacity-60 cursor-pointer">
                <span wire:loading.remove wire:target="refreshData" class="icon-[solar--refresh-bold] size-4 text-emerald-600"></span>
                <span wire:loading wire:target="refreshData" class="icon-[solar--spinner-bold] size-4 animate-spin text-emerald-600"></span>
                <span>Segarkan API Terapi</span>
            </button>

            <button wire:click="exportPdf" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-sm shadow-emerald-500/20 active:scale-95 transition disabled:opacity-60 cursor-pointer">
                <span wire:loading.remove wire:target="exportPdf" class="icon-[solar--printer-bold] size-4"></span>
                <span wire:loading wire:target="exportPdf" class="icon-[solar--spinner-bold] size-4 animate-spin"></span>
                <span>Cetak Laporan PDF</span>
            </button>
        </div>
    </div>

    {{-- FILTER PANEL & THRESHOLD CONTROLLER --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12 items-end">
            {{-- Filter Tahun --}}
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                    Tahun
                </label>
                <select wire:model.live="selectedYear"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bulan --}}
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                    Bulan Pelayanan
                </label>
                <select wire:model.live="selectedMonth"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @foreach ($monthNames as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Poliklinik --}}
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                    Unit Poliklinik
                </label>
                <select wire:model.live="selectedPolyclinic"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Semua Poliklinik Rawat Jalan</option>
                    @foreach ($polyclinics as $kd => $nm)
                        <option value="{{ $kd }}">{{ $nm }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Target Threshold Dinamis (Slider + Input) --}}
            <div class="lg:col-span-4 bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-transparent p-3 rounded-xl border border-emerald-500/20">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                        <span class="icon-[solar--target-bold] size-4 text-emerald-600 dark:text-emerald-400"></span>
                        <span>Threshold Target JKN</span>
                    </label>
                    <div class="flex items-center gap-1">
                        <input type="number" wire:model.live.debounce.300ms="threshold" min="10" max="100" step="1"
                            class="w-14 text-center rounded-lg border border-emerald-500/40 bg-white px-1.5 py-0.5 text-xs font-black text-emerald-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-slate-800 dark:text-emerald-300">
                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400">%</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="range" wire:model.live.debounce.150ms="threshold" min="20" max="90" step="1"
                        class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-600 dark:bg-slate-700">
                </div>
                <div class="flex justify-between text-[10px] text-slate-400 mt-1 font-semibold">
                    <span class="cursor-pointer hover:text-emerald-600" wire:click="$set('threshold', 50)">50%</span>
                    <span class="cursor-pointer hover:text-emerald-600 font-bold text-emerald-700 dark:text-emerald-400" wire:click="$set('threshold', 60)">60% (Standar)</span>
                    <span class="cursor-pointer hover:text-emerald-600" wire:click="$set('threshold', 70)">70%</span>
                    <span class="cursor-pointer hover:text-emerald-600" wire:click="$set('threshold', 80)">80%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU KPI RINGKASAN METRIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        {{-- KPI 1: Capaian Kepatuhan JKN vs Threshold --}}
        <div class="sm:col-span-2 rounded-2xl border p-4.5 transition-all shadow-sm relative overflow-hidden {{ $summary['is_patuh'] ? 'border-emerald-500/30 bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent dark:from-emerald-950/20' : 'border-rose-500/30 bg-gradient-to-br from-rose-500/10 via-rose-500/5 to-transparent dark:from-rose-950/20' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $summary['is_patuh'] ? 'text-emerald-800 dark:text-emerald-300' : 'text-rose-800 dark:text-rose-300' }}">
                    Kepatuhan Antrean JKN (Kode B)
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black {{ $summary['is_patuh'] ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/20 text-rose-700 dark:text-rose-300' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $summary['is_patuh'] ? 'bg-emerald-500' : 'bg-rose-500 animate-ping' }}"></span>
                    {{ $summary['is_patuh'] ? 'TERCAPAI' : 'DI BAWAH TARGET' }}
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight {{ $summary['is_patuh'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $summary['pct_jkn'] }}%
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    / Target: {{ number_format($threshold, 1) }}%
                </span>
            </div>
            <div class="mt-2 text-xs font-semibold flex items-center justify-between text-slate-600 dark:text-slate-400">
                <span>{{ number_format($summary['total_jkn']) }} antrean JKN terlayani</span>
                <span class="font-bold {{ $summary['gap_threshold'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $summary['gap_threshold'] >= 0 ? '+' : '' }}{{ $summary['gap_threshold'] }}% vs target
                </span>
            </div>
        </div>

        {{-- KPI 2: Total Pasien Ralan (Denominator) --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4.5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Total Kunjungan Poli
            </span>
            <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                {{ number_format($summary['total_ralan']) }}
            </div>
            <div class="mt-1 text-[11px] text-slate-500 font-semibold">
                Denominator (Non-IGD & Aktif)
            </div>
        </div>

        {{-- KPI 3: Onsite Loket (Kode A) --}}
        <div class="rounded-2xl border border-sky-500/20 bg-sky-500/5 p-4.5 shadow-sm dark:bg-sky-950/10 dark:border-sky-800/40">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-800 dark:text-sky-300">
                    Onsite Loket (Kode A)
                </span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-sky-500/15 text-sky-700 dark:text-sky-300">
                    {{ $summary['pct_loket'] }}%
                </span>
            </div>
            <div class="mt-2 text-2xl font-black text-sky-700 dark:text-sky-300">
                {{ number_format($summary['total_loket']) }}
            </div>
            <div class="mt-1 text-[11px] text-slate-500">
                Admisi Konvensional
            </div>
        </div>

        {{-- KPI 4: Onsite APM (Kode D) --}}
        <div class="rounded-2xl border border-teal-500/20 bg-teal-500/5 p-4.5 shadow-sm dark:bg-teal-950/10 dark:border-teal-800/40">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-800 dark:text-teal-300">
                    Onsite APM (Kode D)
                </span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-teal-500/15 text-teal-700 dark:text-teal-300">
                    {{ $summary['pct_apm'] }}%
                </span>
            </div>
            <div class="mt-2 text-2xl font-black text-teal-700 dark:text-teal-300">
                {{ number_format($summary['total_apm']) }}
            </div>
            <div class="mt-1 text-[11px] text-slate-500">
                Anjungan Mandiri
            </div>
        </div>

        {{-- KPI 5: Total Onsite (A+D) --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4.5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Total Onsite (A+D)
                </span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ $summary['pct_onsite'] }}%
                </span>
            </div>
            <div class="mt-2 text-2xl font-black text-slate-800 dark:text-slate-200">
                {{ number_format($summary['total_onsite']) }}
            </div>
            <div class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                Mandiri (JKN+APM): {{ $summary['pct_mandiri'] }}%
            </div>
        </div>
    </div>

    {{-- CATATAN PENJELASAN METRIK & SUMBER DATA --}}
    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-slate-100/80 border border-slate-200 dark:bg-slate-800/40 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-400">
        <span class="icon-[solar--info-circle-bold] size-4 text-emerald-600 dark:text-emerald-400 shrink-0"></span>
        <span>
            <strong>Basis Pelayanan Pasien BPJS Terintegrasi:</strong> Data bersumber langsung dari <strong>API BPJS Antrean Terapi (/api/v1/bpjs/antrean/report)</strong>. Menyajikan pemanfaatan antrean Mobile JKN (Kode B), Onsite Loket (A), dan APM (D) beserta validitas penerbitan SEP rawat jalan.
        </span>
    </div>

    {{-- NAVIGASI TAB MODUL --}}
    <div class="flex border-b border-slate-200/80 dark:border-slate-800 overflow-x-auto no-scrollbar gap-2">
        <button wire:click="setTab('ringkasan')"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'ringkasan' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}">
            <span class="icon-[solar--chart-bold] size-4"></span>
            <span>Grafik & Tren Kepatuhan</span>
        </button>

        @if ($selectedMonth === '')
            <button wire:click="setTab('tahunan')"
                class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'tahunan' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}">
                <span class="icon-[solar--calendar-date-bold] size-4"></span>
                <span>Laporan Tahunan & Monthly Growth (MoM)</span>
            </button>
        @endif

        <button wire:click="setTab('poliklinik')"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'poliklinik' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}">
            <span class="icon-[solar--buildings-2-bold] size-4"></span>
            <span>Breakdown Poliklinik & Dokter</span>
        </button>

        <button wire:click="setTab('kesesuaian')"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'kesesuaian' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white' }}">
            <span class="icon-[solar--shield-check-bold] size-4"></span>
            <span>Audit Kesesuaian JKN vs SEP / SKDP</span>
            @if (($summary['conformity']['tidak'] ?? 0) > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500 text-white font-bold">
                    {{ $summary['conformity']['tidak'] }}
                </span>
            @endif
        </button>
    </div>

    {{-- KONTEN TAB BERBASIS LAZY LOADING COMPONENT --}}
    <div>
        @if ($activeTab === 'ringkasan')
            <livewire:online-queue.tabs.overview-tab
                :year="$selectedYear"
                :month="$selectedMonth !== '' ? (int) $selectedMonth : null"
                :kd-poli="$selectedPolyclinic"
                :threshold="$threshold"
                :key="'tab-ringkasan-'.$selectedYear.'-'.$selectedMonth.'-'.$selectedPolyclinic.'-'.$threshold"
                lazy />
        @elseif ($activeTab === 'tahunan')
            <livewire:online-queue.tabs.annual-report-tab
                :year="$selectedYear"
                :kd-poli="$selectedPolyclinic"
                :threshold="$threshold"
                :key="'tab-tahunan-'.$selectedYear.'-'.$selectedPolyclinic.'-'.$threshold"
                lazy />
        @elseif ($activeTab === 'poliklinik')
            <livewire:online-queue.tabs.polyclinic-doctor-tab
                :year="$selectedYear"
                :month="$selectedMonth !== '' ? (int) $selectedMonth : null"
                :kd-poli="$selectedPolyclinic"
                :threshold="$threshold"
                :key="'tab-poliklinik-'.$selectedYear.'-'.$selectedMonth.'-'.$selectedPolyclinic.'-'.$threshold"
                lazy />
        @elseif ($activeTab === 'kesesuaian')
            <livewire:online-queue.tabs.conformity-tab
                :year="$selectedYear"
                :month="$selectedMonth !== '' ? (int) $selectedMonth : null"
                :kd-poli="$selectedPolyclinic"
                :filter-status="$conformityFilter"
                :key="'tab-kesesuaian-'.$selectedYear.'-'.$selectedMonth.'-'.$selectedPolyclinic.'-'.$conformityFilter"
                lazy />
        @endif
    </div>
</div>
