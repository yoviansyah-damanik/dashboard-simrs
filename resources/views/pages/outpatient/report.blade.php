<x-content>
    <x-breadcrumb title="Laporan Pasien Rawat Jalan" :items="[['title' => 'Rawat Jalan', 'href' => route('outpatient')], ['title' => 'Laporan Pasien']]" />

    <x-export-loading wire:target="exportCsv, exportPdf, exportExcel" />

    {{-- Filter Periode & Aksi Ekspor --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Mode Periode: Bulanan / Tahunan / Rentang Kustom --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 shrink-0">
                <button type="button" wire:click="setPeriod('monthly')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $period === 'monthly' ? 'bg-emerald-600 text-white font-bold shadow-sm ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
                    <span class="icon-[solar--calendar-minimalistic-bold] text-base {{ $period === 'monthly' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}"></span>
                    <span>Periode Bulanan</span>
                </button>
                <button type="button" wire:click="setPeriod('yearly')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $period === 'yearly' ? 'bg-emerald-600 text-white font-bold shadow-sm ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
                    <span class="icon-[solar--calendar-bold-duotone] text-base {{ $period === 'yearly' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}"></span>
                    <span>Periode Tahunan</span>
                </button>
                <button type="button" wire:click="setPeriod('custom')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $period === 'custom' ? 'bg-emerald-600 text-white font-bold shadow-sm ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
                    <span class="icon-[solar--calendar-search-bold-duotone] text-base {{ $period === 'custom' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}"></span>
                    <span>Rentang Kustom</span>
                </button>
            </div>

            {{-- Dropdown Preset Cepat --}}
            <div class="relative">
                <select wire:model.live="period"
                    class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                    <option value="today">Hari Ini</option>
                    <option value="last_7_days">7 Hari Lalu</option>
                    <option value="last_30_days">30 Hari Lalu</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="this_year">Tahun Ini</option>
                    <option value="monthly">Pilih Bulan & Tahun</option>
                    <option value="yearly">Pilih Tahun</option>
                    <option value="custom">Rentang Tanggal (Custom)</option>
                </select>
                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-sm"></span>
                </div>
                <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                </div>
            </div>

            {{-- Selector Bulan & Tahun / Input Tanggal --}}
            @if ($period === 'monthly')
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <select wire:model.live="selectedMonth"
                            class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                            @foreach ($this->months as $index => $name)
                                <option value="{{ $index }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--calendar-date-bold-duotone] text-sm"></span>
                        </div>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                        </div>
                    </div>

                    <div class="relative">
                        <select wire:model.live="selectedYear"
                            class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                            @foreach ($this->years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--history-bold-duotone] text-sm"></span>
                        </div>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                        </div>
                    </div>
                </div>
            @elseif ($period === 'yearly')
                <div class="relative">
                    <select wire:model.live="selectedYear"
                        class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                        @foreach ($this->years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--history-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            @elseif ($period === 'custom')
                <div class="flex items-center gap-2 px-3 py-1 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-sm">
                    <input type="date" wire:model.live="startDate"
                        class="bg-transparent border-none focus:ring-0 text-xs font-bold cursor-pointer text-gray-700 dark:text-white outline-none" />
                    <span class="text-gray-300 font-bold text-xs">s/d</span>
                    <input type="date" wire:model.live="endDate"
                        class="bg-transparent border-none focus:ring-0 text-xs font-bold cursor-pointer text-gray-700 dark:text-white outline-none" />
                </div>
            @endif

            {{-- Indikator Loading --}}
            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memuat data...</span>
            </div>
        </div>

        {{-- Tombol Cetak & Ekspor Dokumen --}}
        <div class="flex items-center gap-2">
            <x-button color="default" icon="i-ph-printer" onclick="window.print()">
                Cetak
            </x-button>
            <x-button color="green" icon="i-ph-file-xls" wire:click="exportExcel">
                Excel
            </x-button>
            <x-button color="primary" icon="i-ph-file-csv" wire:click="exportCsv">
                CSV
            </x-button>
            <x-button color="red" icon="i-ph-file-pdf" wire:click="exportPdf">
                PDF
            </x-button>
        </div>
    </div>

    {{-- Ringkasan Metrik Utama Pasien Rawat Jalan --}}
    @php
        $summaryData = $this->summary;
    @endphp
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Pasien --}}
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4.5 flex flex-col justify-between transition hover:shadow-md hover:border-emerald-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pasien Ralan</p>
                    <p class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mt-1">
                        {{ number_format($summaryData['total_pasien'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-emerald-600 bg-emerald-500/10">
                    <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-2xl"></span>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-stroke/60 dark:border-strokedark/60 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ number_format($summaryData['total_sudah'], 0, ',', '.') }} Sudah
                </span>
                <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    {{ number_format($summaryData['total_belum'], 0, ',', '.') }} Belum
                </span>
            </div>
        </div>

        {{-- Demografi Gender (L / P) --}}
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4.5 flex flex-col justify-between transition hover:shadow-md hover:border-blue-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Demografi Gender</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight">
                            {{ number_format($summaryData['total_pria'], 0, ',', '.') }} L
                        </span>
                        <span class="text-xs text-gray-400 font-bold">vs</span>
                        <span class="text-2xl font-black text-pink-600 dark:text-pink-400 tracking-tight">
                            {{ number_format($summaryData['total_wanita'], 0, ',', '.') }} P
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-blue-600 bg-blue-500/10">
                    <span class="icon-[solar--user-bold-duotone] text-2xl"></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-stroke/60 dark:border-strokedark/60">
                <div class="flex justify-between text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-1.5">
                    <span class="text-blue-600">{{ $summaryData['rasio_pria'] }}% Laki-laki</span>
                    <span class="text-pink-600">{{ $summaryData['rasio_wanita'] }}% Perempuan</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden flex">
                    <div class="bg-blue-500 h-full" style="width: {{ $summaryData['rasio_pria'] }}%"></div>
                    <div class="bg-pink-500 h-full" style="width: {{ $summaryData['rasio_wanita'] }}%"></div>
                </div>
            </div>
        </div>

        {{-- Penjamin Pasien (BPJS, UMUM, DINAS) --}}
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4.5 flex flex-col justify-between transition hover:shadow-md hover:border-cyan-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penjamin Pasien</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                            {{ number_format($summaryData['total_bpjs'], 0, ',', '.') }}
                        </span>
                        <span class="text-xs font-bold text-gray-400">BPJS</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-cyan-600 bg-cyan-500/10">
                    <span class="icon-[solar--shield-check-bold-duotone] text-2xl"></span>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-stroke/60 dark:border-strokedark/60 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 text-sky-600 dark:text-sky-400 font-bold">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    {{ number_format($summaryData['total_umum'], 0, ',', '.') }} Umum
                </span>
                <span class="text-gray-300 dark:text-gray-600">&bull;</span>
                <span class="inline-flex items-center gap-1.5 text-purple-600 dark:text-purple-400 font-bold">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    {{ number_format($summaryData['total_dinas'], 0, ',', '.') }} Dinas
                </span>
            </div>
        </div>

        {{-- Kunjungan Pasien (Baru vs Lama) --}}
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4.5 flex flex-col justify-between transition hover:shadow-md hover:border-indigo-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kunjungan Pasien</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-cyan-600 dark:text-cyan-400 tracking-tight">
                            {{ number_format($summaryData['total_baru'], 0, ',', '.') }} Baru
                        </span>
                        <span class="text-xs text-gray-400 font-bold">/</span>
                        <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                            {{ number_format($summaryData['total_lama'], 0, ',', '.') }} Lama
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-indigo-600 bg-indigo-500/10">
                    <span class="icon-[solar--user-plus-bold-duotone] text-2xl"></span>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-stroke/60 dark:border-strokedark/60 text-xs font-medium">
                <span class="text-gray-500 dark:text-gray-400">Rasio Pasien Baru:</span>
                <span class="font-bold text-cyan-600 dark:text-cyan-400 px-2 py-0.5 rounded-md bg-cyan-50 dark:bg-cyan-500/10">
                    {{ $summaryData['total_pasien'] > 0 ? round(($summaryData['total_baru'] / $summaryData['total_pasien']) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    {{-- Filter & Parameter Panel --}}
    <div class="p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm space-y-4 no-print">
        <div class="flex items-center justify-between pb-3 border-b border-stroke/70 dark:border-strokedark/70">
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                <span class="icon-[solar--filter-bold-duotone] text-emerald-600 dark:text-emerald-400 text-base"></span>
                <span>Filter & Parameter Laporan</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="toggleCharts"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $showCharts ? 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400' : 'text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300' }}">
                    <span class="{{ $showCharts ? 'icon-[solar--eye-closed-bold-duotone]' : 'icon-[solar--chart-bold-duotone]' }} text-sm"></span>
                    <span>{{ $showCharts ? 'Sembunyikan Grafik' : 'Tampilkan Grafik' }}</span>
                </button>
                @if ($poly !== 'semua' || $payType !== 'semua' || $doctor !== 'semua' || $gender !== 'semua' || $sttsDaftar !== 'semua')
                    <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20 transition-all cursor-pointer">
                        <span class="icon-[solar--restart-bold-duotone] text-sm"></span>
                        Reset Filter
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            {{-- Poliklinik / Unit --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                    Poliklinik / Unit
                </label>
                <div class="relative">
                    <select wire:model.live="poly"
                        class="w-full appearance-none pl-9 pr-8 py-2 bg-gray-50 dark:bg-meta-4/60 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer transition">
                        <option value="semua">Semua Poliklinik</option>
                        @foreach ($this->polyclinics as $p)
                            <option value="{{ $p->kd_poli }}">{{ $p->nm_poli }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--hospital-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            {{-- Penjamin / Jenis Bayar --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                    Jenis Bayar / Penjamin
                </label>
                <div class="relative">
                    <select wire:model.live="payType"
                        class="w-full appearance-none pl-9 pr-8 py-2 bg-gray-50 dark:bg-meta-4/60 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer transition">
                        <option value="semua">Semua Penjamin</option>
                        <option value="BPJS">BPJS (Semua)</option>
                        <option value="UMUM">UMUM / Mandiri</option>
                        <option value="DINAS">DINAS (TNI / POLRI)</option>
                        <optgroup label="Asuransi / Perusahaan">
                            @foreach ($this->payTypes as $pj)
                                <option value="{{ $pj->kd_pj }}">{{ $pj->png_jawab }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--wallet-money-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            {{-- Dokter Pemeriksa --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                    Dokter Pemeriksa
                </label>
                <div class="relative">
                    <select wire:model.live="doctor"
                        class="w-full appearance-none pl-9 pr-8 py-2 bg-gray-50 dark:bg-meta-4/60 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer transition">
                        <option value="semua">Semua Dokter</option>
                        @foreach ($this->doctors as $doc)
                            <option value="{{ $doc->kd_dokter }}">{{ $doc->nm_dokter }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--user-hand-up-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                    Jenis Kelamin
                </label>
                <div class="relative">
                    <select wire:model.live="gender"
                        class="w-full appearance-none pl-9 pr-8 py-2 bg-gray-50 dark:bg-meta-4/60 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer transition">
                        <option value="semua">Semua Gender</option>
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--users-group-rounded-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            {{-- Status Kunjungan --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                    Status Kunjungan
                </label>
                <div class="relative">
                    <select wire:model.live="sttsDaftar"
                        class="w-full appearance-none pl-9 pr-8 py-2 bg-gray-50 dark:bg-meta-4/60 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer transition">
                        <option value="semua">Semua Status</option>
                        <option value="Baru">Pasien Baru</option>
                        <option value="Lama">Pasien Lama</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--user-check-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Filter Panel --}}
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-stroke/70 dark:border-strokedark/70 text-xs text-gray-400 font-medium">
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-600 dark:text-gray-300">Rekapitulasi:</span>
                <span>Data diakumulasi secara otomatis sesuai parameter filter aktif</span>
            </div>

            <div>
                Periode data: <span class="font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</span> s/d <span class="font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Seksi Grafik & Visualisasi Tren --}}
    @if ($showCharts)
        <div wire:key="outpatient-charts-{{ md5($startDate . $endDate . $poly . $payType . $doctor . $gender . $sttsDaftar) }}"
            x-data="outpatientReportCharts(@js($this->chartPayload))"
            class="space-y-4 no-print transition-all duration-300">

            {{-- Baris 1: Tren Kunjungan Harian (8 col) & Komposisi Penjamin (4 col) --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                {{-- Tren Kunjungan Pasien Rawat Jalan --}}
                <div class="lg:col-span-8 p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-stroke/70 dark:border-strokedark/70 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--graph-up-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Tren Kunjungan Rawat Jalan</h3>
                                <p class="text-[11px] text-gray-400 font-medium">Fluktuasi harian total pasien & perbandingan gender</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-500/20">
                            {{ count($this->chartPayload['trend']['labels']) }} Titik Waktu
                        </span>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="chartOutpatientTrend"></canvas>
                    </div>
                </div>

                {{-- Proporsi Penjamin / Jenis Bayar --}}
                <div class="lg:col-span-4 p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between transition hover:shadow-md">
                    <div class="flex items-center justify-between pb-3 border-b border-stroke/70 dark:border-strokedark/70 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--pie-chart-2-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Proporsi Penjamin</h3>
                                <p class="text-[11px] text-gray-400 font-medium">Distribusi cara bayar & asuransi</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[280px] w-full flex items-center justify-center">
                        <canvas id="chartOutpatientPayer"></canvas>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Top Poliklinik (6 col) & Sebaran Kelompok Umur (6 col) --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                {{-- Top 8 Poliklinik / Unit --}}
                <div class="p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between transition hover:shadow-md">
                    <div class="flex items-center justify-between pb-3 border-b border-stroke/70 dark:border-strokedark/70 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--hospital-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Top 8 Poliklinik / Unit</h3>
                                <p class="text-[11px] text-gray-400 font-medium">Unit dengan volume kunjungan tertinggi</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[270px] w-full">
                        <canvas id="chartOutpatientPoly"></canvas>
                    </div>
                </div>

                {{-- Sebaran Kelompok Umur SIRS & Gender --}}
                <div class="p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between transition hover:shadow-md">
                    <div class="flex items-center justify-between pb-3 border-b border-stroke/70 dark:border-strokedark/70 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Sebaran Kelompok Umur (SIRS)</h3>
                                <p class="text-[11px] text-gray-400 font-medium">Perbandingan Laki-laki vs Perempuan per kategori usia</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[270px] w-full">
                        <canvas id="chartOutpatientAge"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Segmented Tab Navigation --}}
    <div class="p-1.5 bg-gray-100 dark:bg-meta-4/60 rounded-2xl border border-stroke/50 dark:border-strokedark/50 inline-flex flex-wrap gap-1.5 no-print">
        <button type="button" wire:click="switchTab('rekap_poli')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs transition-all duration-200 cursor-pointer {{ $activeTab === 'rekap_poli' ? 'bg-white dark:bg-boxdark text-emerald-700 dark:text-emerald-400 font-black shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-semibold hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
            <span class="icon-[solar--hospital-bold-duotone] text-base {{ $activeTab === 'rekap_poli' ? 'text-emerald-600 dark:text-emerald-400' : '' }}"></span>
            <span>Rekap per Poliklinik / Unit</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $activeTab === 'rekap_poli' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-gray-200 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                {{ count($this->polyBreakdown) }}
            </span>
        </button>

        <button type="button" wire:click="switchTab('rekap_bayar')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs transition-all duration-200 cursor-pointer {{ $activeTab === 'rekap_bayar' ? 'bg-white dark:bg-boxdark text-emerald-700 dark:text-emerald-400 font-black shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-semibold hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
            <span class="icon-[solar--wallet-money-bold-duotone] text-base {{ $activeTab === 'rekap_bayar' ? 'text-emerald-600 dark:text-emerald-400' : '' }}"></span>
            <span>Rekap Jenis Bayar / Penjamin</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $activeTab === 'rekap_bayar' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-gray-200 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                {{ count($this->payTypeBreakdown) }}
            </span>
        </button>

        <button type="button" wire:click="switchTab('rekap_gender')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs transition-all duration-200 cursor-pointer {{ $activeTab === 'rekap_gender' ? 'bg-white dark:bg-boxdark text-emerald-700 dark:text-emerald-400 font-black shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-semibold hover:bg-gray-200/50 dark:hover:bg-meta-4' }}">
            <span class="icon-[solar--users-group-rounded-bold-duotone] text-base {{ $activeTab === 'rekap_gender' ? 'text-emerald-600 dark:text-emerald-400' : '' }}"></span>
            <span>Demografi & Kelompok Umur</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $activeTab === 'rekap_gender' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-gray-200 dark:bg-meta-4 text-gray-600 dark:text-gray-300' }}">
                {{ count($this->ageGroupBreakdown) }}
            </span>
        </button>
    </div>

    {{-- TAB 1: REKAP POLIKLINIK / UNIT --}}
    @if ($activeTab === 'rekap_poli')
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-stroke dark:border-strokedark flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="icon-[solar--hospital-bold-duotone] text-emerald-600 dark:text-emerald-400 text-lg"></span>
                        Rekapitulasi Kunjungan Pasien per Poliklinik / Unit
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Periode: <span class="font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</span> s/d <span class="font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-meta-4/60 text-xs font-black text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-stroke dark:border-strokedark">
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left min-w-[200px]">Poliklinik / Unit</th>
                            <th class="px-4 py-3.5 text-center min-w-[130px]">Total Pasien</th>
                            <th class="px-4 py-3.5 text-center min-w-[120px]">Proporsi (%)</th>
                            <th class="px-4 py-3.5 text-center min-w-[130px]">Gender (L / P)</th>
                            <th class="px-4 py-3.5 text-center min-w-[130px]">Kunjungan (Baru / Lama)</th>
                            <th class="px-4 py-3.5 text-center min-w-[180px]">Penjamin (BPJS / Umum / Dinas)</th>
                            <th class="px-4 py-3.5 text-center min-w-[130px]">Status (Sudah / Belum)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @php
                            $sumTotal = 0;
                            $sumPria = 0;
                            $sumWanita = 0;
                            $sumBaru = 0;
                            $sumLama = 0;
                            $sumBpjs = 0;
                            $sumUmum = 0;
                            $sumDinas = 0;
                            $sumSudah = 0;
                            $sumBelum = 0;
                        @endphp
                        @forelse ($this->polyBreakdown as $idx => $item)
                            @php
                                $sumTotal += $item['total'];
                                $sumPria += $item['pria'];
                                $sumWanita += $item['wanita'];
                                $sumBaru += $item['baru'];
                                $sumLama += $item['lama'];
                                $sumBpjs += $item['bpjs'];
                                $sumUmum += $item['umum'];
                                $sumDinas += $item['dinas'];
                                $sumSudah += $item['sudah'];
                                $sumBelum += $item['belum'];
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/40 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $item['nm_poli'] }}
                                    </div>
                                    <div class="text-[11px] font-mono text-gray-400">
                                        {{ $item['kd_poli'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                        {{ number_format($item['total'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="w-full max-w-[120px] mx-auto">
                                        <div class="flex justify-between text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            <span>{{ $item['percent'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $item['percent'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-2 text-xs font-bold">
                                        <span class="text-blue-600 dark:text-blue-400">{{ number_format($item['pria'], 0, ',', '.') }} L</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-pink-600 dark:text-pink-400">{{ number_format($item['wanita'], 0, ',', '.') }} P</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-2 text-xs font-bold">
                                        <span class="text-cyan-600 dark:text-cyan-400">{{ number_format($item['baru'], 0, ',', '.') }} Baru</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($item['lama'], 0, ',', '.') }} Lama</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-1.5 text-xs font-semibold">
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 text-[11px] font-bold">
                                            {{ $item['bpjs'] }} BPJS
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400 text-[11px] font-bold">
                                            {{ $item['umum'] }} Um
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 text-[11px] font-bold">
                                            {{ $item['dinas'] }} Din
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-2 text-xs font-bold">
                                        <span class="text-emerald-600 dark:text-emerald-400">{{ $item['sudah'] }}</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-amber-600 dark:text-amber-400">{{ $item['belum'] }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data kunjungan poliklinik untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($this->polyBreakdown) > 0)
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-meta-4/80 font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider border-t-2 border-stroke dark:border-strokedark">
                                <td colspan="2" class="px-4 py-4 text-center">TOTAL KESELURUHAN</td>
                                <td class="px-4 py-4 text-center font-black text-emerald-700 dark:text-emerald-400 text-sm">
                                    {{ number_format($sumTotal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">100%</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-blue-600 dark:text-blue-400">{{ number_format($sumPria, 0, ',', '.') }} L</span> / 
                                    <span class="text-pink-600 dark:text-pink-400">{{ number_format($sumWanita, 0, ',', '.') }} P</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-cyan-600 dark:text-cyan-400">{{ number_format($sumBaru, 0, ',', '.') }} Baru</span> / 
                                    <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($sumLama, 0, ',', '.') }} Lama</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    {{ $sumBpjs }} BPJS &bull; {{ $sumUmum }} Um &bull; {{ $sumDinas }} Din
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-emerald-600 dark:text-emerald-400">{{ $sumSudah }}</span> / 
                                    <span class="text-amber-600 dark:text-amber-400">{{ $sumBelum }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 2: REKAP JENIS BAYAR / PENJAMIN --}}
    @if ($activeTab === 'rekap_bayar')
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-stroke dark:border-strokedark flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="icon-[solar--wallet-money-bold-duotone] text-emerald-600 dark:text-emerald-400 text-lg"></span>
                        Rekapitulasi Kunjungan Pasien per Jenis Bayar / Penjamin
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Periode: <span class="font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</span> s/d <span class="font-bold text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-meta-4/60 text-xs font-black text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-stroke dark:border-strokedark">
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left min-w-[240px]">Penjamin / Asuransi</th>
                            <th class="px-4 py-3.5 text-center min-w-[130px]">Total Pasien</th>
                            <th class="px-4 py-3.5 text-center min-w-[140px]">Proporsi (%)</th>
                            <th class="px-4 py-3.5 text-center min-w-[140px]">Gender (L / P)</th>
                            <th class="px-4 py-3.5 text-center min-w-[140px]">Kunjungan (Baru / Lama)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @php
                            $sumPayTotal = 0;
                            $sumPayPria = 0;
                            $sumPayWanita = 0;
                            $sumPayBaru = 0;
                            $sumPayLama = 0;
                        @endphp
                        @forelse ($this->payTypeBreakdown as $idx => $pay)
                            @php
                                $sumPayTotal += $pay['total'];
                                $sumPayPria += $pay['pria'];
                                $sumPayWanita += $pay['wanita'];
                                $sumPayBaru += $pay['baru'];
                                $sumPayLama += $pay['lama'];
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/40 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $pay['png_jawab'] }}
                                    </div>
                                    <div class="text-[11px] font-mono text-gray-400">
                                        Kode: {{ $pay['kd_pj'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                        {{ number_format($pay['total'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="w-full max-w-[120px] mx-auto">
                                        <div class="flex justify-between text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            <span>{{ $pay['percent'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $pay['percent'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-2 text-xs font-bold">
                                        <span class="text-blue-600 dark:text-blue-400">{{ number_format($pay['pria'], 0, ',', '.') }} L</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-pink-600 dark:text-pink-400">{{ number_format($pay['wanita'], 0, ',', '.') }} P</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-2 text-xs font-bold">
                                        <span class="text-cyan-600 dark:text-cyan-400">{{ number_format($pay['baru'], 0, ',', '.') }} Baru</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($pay['lama'], 0, ',', '.') }} Lama</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data jenis bayar untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($this->payTypeBreakdown) > 0)
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-meta-4/80 font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider border-t-2 border-stroke dark:border-strokedark">
                                <td colspan="2" class="px-4 py-4 text-center">TOTAL KESELURUHAN</td>
                                <td class="px-4 py-4 text-center font-black text-emerald-700 dark:text-emerald-400 text-sm">
                                    {{ number_format($sumPayTotal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">100%</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-blue-600 dark:text-blue-400">{{ number_format($sumPayPria, 0, ',', '.') }} L</span> / 
                                    <span class="text-pink-600 dark:text-pink-400">{{ number_format($sumPayWanita, 0, ',', '.') }} P</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-cyan-600 dark:text-cyan-400">{{ number_format($sumPayBaru, 0, ',', '.') }} Baru</span> / 
                                    <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($sumPayLama, 0, ',', '.') }} Lama</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 3: DEMOGRAFI & KELOMPOK UMUR --}}
    @if ($activeTab === 'rekap_gender')
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-stroke dark:border-strokedark flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="icon-[solar--users-group-rounded-bold-duotone] text-emerald-600 dark:text-emerald-400 text-lg"></span>
                        Rekapitulasi Demografi & Kelompok Umur (SIRS Standar)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Klasifikasi kelompok umur berdasarkan tabel master SIRS Kemkes / database SIMRS.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-meta-4/60 text-xs font-black text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-stroke dark:border-strokedark">
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left min-w-[220px]">Kelompok Umur</th>
                            <th class="px-4 py-3.5 text-center min-w-[130px]">Total Pasien</th>
                            <th class="px-4 py-3.5 text-center min-w-[140px]">Distribusi (%)</th>
                            <th class="px-4 py-3.5 text-center min-w-[140px]">Laki-laki (L)</th>
                            <th class="px-4 py-3.5 text-center min-w-[140px]">Perempuan (P)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @php
                            $sumAgeTotal = 0;
                            $sumAgePria = 0;
                            $sumAgeWanita = 0;
                        @endphp
                        @forelse ($this->ageGroupBreakdown as $idx => $age)
                            @php
                                $sumAgeTotal += $age['total'];
                                $sumAgePria += $age['pria'];
                                $sumAgeWanita += $age['wanita'];
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/40 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $age['nama'] }}
                                    </div>
                                    <div class="text-[11px] font-mono text-gray-400">
                                        Kode: {{ $age['kode'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                        {{ number_format($age['total'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="w-full max-w-[120px] mx-auto">
                                        <div class="flex justify-between text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">
                                            <span>{{ $age['percent'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $age['percent'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($age['pria'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-pink-600 dark:text-pink-400">
                                    {{ number_format($age['wanita'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data demografi kelompok umur untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($this->ageGroupBreakdown) > 0)
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-meta-4/80 font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider border-t-2 border-stroke dark:border-strokedark">
                                <td colspan="2" class="px-4 py-4 text-center">TOTAL KESELURUHAN</td>
                                <td class="px-4 py-4 text-center font-black text-emerald-700 dark:text-emerald-400 text-sm">
                                    {{ number_format($sumAgeTotal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">100%</td>
                                <td class="px-4 py-4 text-center text-blue-600 dark:text-blue-400 font-black">
                                    {{ number_format($sumAgePria, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center text-pink-600 dark:text-pink-400 font-black">
                                    {{ number_format($sumAgeWanita, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif

    @script
        <script>
            Alpine.data('outpatientReportCharts', (chartPayload) => ({
                trendChart: null,
                payerChart: null,
                polyChart: null,
                ageChart: null,
                init() {
                    this.$nextTick(() => {
                        this.renderAll(chartPayload);
                    });
                },
                destroy() {
                    this.destroyAll();
                },
                destroyAll() {
                    if (this.trendChart) { try { this.trendChart.destroy(); } catch(e){} this.trendChart = null; }
                    if (this.payerChart) { try { this.payerChart.destroy(); } catch(e){} this.payerChart = null; }
                    if (this.polyChart) { try { this.polyChart.destroy(); } catch(e){} this.polyChart = null; }
                    if (this.ageChart) { try { this.ageChart.destroy(); } catch(e){} this.ageChart = null; }
                },
                renderAll(data) {
                    if (!data) return;
                    this.destroyAll();
                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.04)';
                    const textColor = isDark ? '#94A3B8' : '#64748B';

                    // 1. Line Chart: Tren Kunjungan Harian
                    const trendCanvas = document.getElementById('chartOutpatientTrend');
                    if (trendCanvas && data.trend) {
                        const ctx = trendCanvas.getContext('2d');
                        if (ctx) {
                            const gradEmerald = ctx.createLinearGradient(0, 0, 0, 260);
                            gradEmerald.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
                            gradEmerald.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                            this.trendChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.trend.labels || [],
                                    datasets: [
                                        {
                                            label: 'Total Pasien',
                                            data: data.trend.total || [],
                                            borderColor: '#10B981',
                                            backgroundColor: gradEmerald,
                                            fill: true,
                                            tension: 0.35,
                                            borderWidth: 2.5,
                                            pointRadius: (data.trend.labels || []).length > 25 ? 1 : 3,
                                            pointHoverRadius: 5,
                                            pointBackgroundColor: '#10B981',
                                        },
                                        {
                                            label: 'Laki-laki',
                                            data: data.trend.pria || [],
                                            borderColor: '#3B82F6',
                                            backgroundColor: 'transparent',
                                            borderWidth: 1.8,
                                            tension: 0.35,
                                            pointRadius: 0,
                                            pointHoverRadius: 4,
                                        },
                                        {
                                            label: 'Perempuan',
                                            data: data.trend.wanita || [],
                                            borderColor: '#EC4899',
                                            backgroundColor: 'transparent',
                                            borderWidth: 1.8,
                                            tension: 0.35,
                                            pointRadius: 0,
                                            pointHoverRadius: 4,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 600, easing: 'easeOutQuart' },
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 12,
                                                font: { size: 11, weight: '600' }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
                                            titleColor: isDark ? '#F1F5F9' : '#0F172A',
                                            bodyColor: isDark ? '#CBD5E1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                            boxPadding: 4,
                                            usePointStyle: true,
                                            callbacks: {
                                                label: function(context) {
                                                    const val = context.raw || 0;
                                                    return ` ${context.dataset.label}: ${val.toLocaleString('id-ID')} pasien`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: { color: textColor, font: { size: 10 } }
                                        },
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: textColor, font: { size: 10, weight: 'bold' } }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // 2. Donut Chart: Proporsi Penjamin
                    const payerCanvas = document.getElementById('chartOutpatientPayer');
                    if (payerCanvas && data.payer) {
                        const ctx = payerCanvas.getContext('2d');
                        if (ctx) {
                            const palette = ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#64748B'];
                            this.payerChart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: data.payer.labels || [],
                                    datasets: [{
                                        data: data.payer.totals || [],
                                        backgroundColor: palette.slice(0, (data.payer.labels || []).length),
                                        borderWidth: 2,
                                        borderColor: isDark ? '#1E293B' : '#FFFFFF',
                                        hoverOffset: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '66%',
                                    animation: { duration: 600, easing: 'easeOutQuart' },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'bottom',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 8,
                                                font: { size: 10, weight: '600' }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
                                            titleColor: isDark ? '#F1F5F9' : '#0F172A',
                                            bodyColor: isDark ? '#CBD5E1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                            callbacks: {
                                                label: function(context) {
                                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                    const val = context.raw || 0;
                                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                                    return ` ${context.label}: ${val.toLocaleString('id-ID')} (${pct}%)`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // 3. Horizontal Bar Chart: Top 8 Poliklinik
                    const polyCanvas = document.getElementById('chartOutpatientPoly');
                    if (polyCanvas && data.poly) {
                        const ctx = polyCanvas.getContext('2d');
                        if (ctx) {
                            this.polyChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: data.poly.labels || [],
                                    datasets: [{
                                        label: 'Total Pasien',
                                        data: data.poly.total || [],
                                        backgroundColor: isDark ? 'rgba(6, 182, 212, 0.75)' : 'rgba(14, 165, 233, 0.85)',
                                        borderColor: '#06B6D4',
                                        borderWidth: 1,
                                        borderRadius: 6,
                                        maxBarThickness: 22,
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 600, easing: 'easeOutQuart' },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
                                            titleColor: isDark ? '#F1F5F9' : '#0F172A',
                                            bodyColor: isDark ? '#CBD5E1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                            callbacks: {
                                                label: function(context) {
                                                    return ` Total: ${(context.raw || 0).toLocaleString('id-ID')} pasien`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: { color: textColor, font: { size: 10 } }
                                        },
                                        y: {
                                            grid: { display: false },
                                            ticks: { color: textColor, font: { size: 10, weight: 'bold' } }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // 4. Grouped Bar Chart: Kelompok Umur & Gender (SIRS)
                    const ageCanvas = document.getElementById('chartOutpatientAge');
                    if (ageCanvas && data.age) {
                        const ctx = ageCanvas.getContext('2d');
                        if (ctx) {
                            this.ageChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: data.age.labels || [],
                                    datasets: [
                                        {
                                            label: 'Laki-laki',
                                            data: data.age.pria || [],
                                            backgroundColor: 'rgba(59, 130, 246, 0.85)',
                                            borderColor: '#3B82F6',
                                            borderWidth: 1,
                                            borderRadius: 5,
                                            maxBarThickness: 16,
                                        },
                                        {
                                            label: 'Perempuan',
                                            data: data.age.wanita || [],
                                            backgroundColor: 'rgba(236, 72, 153, 0.85)',
                                            borderColor: '#EC4899',
                                            borderWidth: 1,
                                            borderRadius: 5,
                                            maxBarThickness: 16,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 600, easing: 'easeOutQuart' },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 12,
                                                font: { size: 10, weight: '600' }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
                                            titleColor: isDark ? '#F1F5F9' : '#0F172A',
                                            bodyColor: isDark ? '#CBD5E1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 10,
                                            callbacks: {
                                                label: function(context) {
                                                    return ` ${context.dataset.label}: ${(context.raw || 0).toLocaleString('id-ID')} pasien`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: gridColor },
                                            ticks: { color: textColor, font: { size: 10 } }
                                        },
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: textColor, font: { size: 10, weight: 'bold' } }
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
            }));
        </script>
    @endscript
</x-content>
