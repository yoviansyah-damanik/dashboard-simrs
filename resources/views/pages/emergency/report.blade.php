<x-content>
    <x-breadcrumb title="Laporan Gawat Darurat" :items="[['title' => 'Gawat Darurat'], ['title' => 'Laporan']]" />

    {{-- Filter Bar & Aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Filter Periode --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 select-none pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-rose-600 dark:text-rose-400 text-base"></span>
                    <span>Periode:</span>
                </span>
                <div class="relative">
                    <select wire:model.live="period"
                        class="appearance-none pl-8 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-rose-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                        <option value="today">Hari Ini</option>
                        <option value="last_7_days">7 Hari Terakhir</option>
                        <option value="last_30_days">30 Hari Terakhir</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="monthly">Pilih Bulan</option>
                        <option value="yearly">Pilih Tahun</option>
                    </select>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            @if ($period === 'monthly')
                <div class="flex items-center gap-2">
                    <select wire:model.live="selectedMonth"
                        class="py-2 px-3 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-rose-500 outline-none">
                        @foreach ($this->months as $mNum => $mName)
                            <option value="{{ $mNum }}">{{ $mName }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedYear"
                        class="py-2 px-3 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-rose-500 outline-none">
                        @foreach ($this->years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif ($period === 'yearly')
                <select wire:model.live="selectedYear"
                    class="py-2 px-3 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-rose-500 outline-none">
                    @foreach ($this->years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Filter Status Lanjut (Ranap / Ralan) --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider pl-1">Tindak Lanjut:</span>
                <select wire:model.live="statusLanjut"
                    class="py-2 px-3 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-rose-500 outline-none">
                    <option value="semua">Semua Tindak Lanjut</option>
                    <option value="Ranap">Masuk Rawat Inap (Ranap)</option>
                    <option value="Ralan">Rawat Jalan / Pulang</option>
                </select>
            </div>

            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-rose-600 dark:text-rose-400">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memuat data IGD...</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-button color="default" icon="i-ph-file-xls" wire:click="exportExcel">Ekspor Excel</x-button>
            <x-button color="default" icon="i-ph-printer" onclick="window.print()">Cetak</x-button>
        </div>
    </div>

    {{-- Executive Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
        {{-- Card 1: Total Pasien IGD --}}
        <div class="p-5 bg-gradient-to-br from-rose-600 via-rose-700 to-rose-900 rounded-2xl text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-rose-200">Total Pasien IGD</span>
                <span class="p-2 bg-white/15 rounded-xl text-white backdrop-blur-sm">
                    <span class="icon-[solar--ambulance-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black tracking-tight mb-1">
                {{ number_format($summary['total_pasien'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between text-xs text-rose-100 font-medium">
                <span>Laki: <strong>{{ number_format($summary['total_pria'], 0, ',', '.') }}</strong></span>
                <span>Perempuan: <strong>{{ number_format($summary['total_wanita'], 0, ',', '.') }}</strong></span>
            </div>
        </div>

        {{-- Card 2: Masuk Rawat Inap (Ranap) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dirawat ke Ranap</span>
                <span class="p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                    <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_ranap'], 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
                <span>Transfer ke Ruangan:</span>
                <span class="font-bold text-purple-600 dark:text-purple-400">{{ $summary['rasio_ranap'] }}%</span>
            </div>
        </div>

        {{-- Card 3: Rawat Jalan / Pulang --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rawat Jalan / Pulang</span>
                <span class="p-2 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl">
                    <span class="icon-[solar--user-check-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight mb-1">
                {{ number_format($summary['total_ralan'], 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
                <span>Pulang / Kontrol Poli:</span>
                <span class="font-bold text-sky-600 dark:text-sky-400">{{ $summary['rasio_ralan'] }}%</span>
            </div>
        </div>

        {{-- Card 4: Kasus Khusus (Dirujuk / Meninggal) --}}
        <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dirujuk & Mortalitas</span>
                <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <span class="icon-[solar--danger-triangle-bold-duotone] text-lg"></span>
                </span>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Dirujuk RS Lain:</span>
                    <strong class="text-amber-600 dark:text-amber-400 text-sm">{{ number_format($summary['total_dirujuk'], 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Meninggal di IGD:</span>
                    <strong class="text-rose-600 dark:text-rose-400 text-sm">{{ number_format($summary['total_meninggal'], 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Pulang Paksa (PAPS):</span>
                    <strong class="text-gray-700 dark:text-gray-300 text-sm">{{ number_format($summary['total_pulang_paksa'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="p-5 sm:p-6 mb-6 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
        {{-- Section Header & Tabs --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--document-medicine-bold-duotone] text-xl text-rose-600 dark:text-rose-400"></span>
                    Laporan Pelayanan Instalasi Gawat Darurat
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Periode: {{ Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d. {{ Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                </p>
            </div>

            {{-- Tab Controls --}}
            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 no-print">
                <button type="button" wire:click="setTab('rekap_status')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'rekap_status' ? 'bg-rose-600 text-white shadow-sm font-bold ring-1 ring-rose-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm"></span>
                    <span>Tindak Lanjut</span>
                </button>
                <button type="button" wire:click="setTab('rekap_dokter')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'rekap_dokter' ? 'bg-rose-600 text-white shadow-sm font-bold ring-1 ring-rose-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--stethoscope-bold-duotone] text-sm"></span>
                    <span>Dokter Jaga</span>
                </button>
                <button type="button" wire:click="setTab('rekap_bayar')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'rekap_bayar' ? 'bg-rose-600 text-white shadow-sm font-bold ring-1 ring-rose-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--card-bold-duotone] text-sm"></span>
                    <span>Cara Bayar</span>
                </button>
                <button type="button" wire:click="setTab('data_pasien')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 {{ $activeTab === 'data_pasien' ? 'bg-rose-600 text-white shadow-sm font-bold ring-1 ring-rose-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium' }}">
                    <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-sm"></span>
                    <span>Daftar Pasien</span>
                </button>
            </div>
        </div>

        {{-- TAB 1: REKAP STATUS & TINDAK LANJUT --}}
        @if ($activeTab === 'rekap_status')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Box 1: Status Lanjut (Ranap vs Ralan) --}}
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/50 dark:border-strokedark/50">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Tindak Lanjut Pasien (Status Lanjut)
                    </h4>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-gray-500 dark:text-gray-400 border-b border-stroke dark:border-strokedark font-semibold">
                                <th class="pb-2 text-left">Tindak Lanjut</th>
                                <th class="pb-2 text-center">Jumlah Pasien</th>
                                <th class="pb-2 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                            @php
                                $totLanjut = array_sum($statusBreakdown['by_lanjut']) ?: 1;
                            @endphp
                            @foreach ($statusBreakdown['by_lanjut'] as $lanjutKey => $cnt)
                                <tr class="hover:bg-white/50 dark:hover:bg-boxdark/50">
                                    <td class="py-2.5 text-gray-800 dark:text-gray-200 font-bold">
                                        {{ $lanjutKey === 'Ranap' ? 'Masuk Rawat Inap (Ranap)' : 'Rawat Jalan / Pulang' }}
                                    </td>
                                    <td class="py-2.5 text-center font-bold text-rose-600 dark:text-rose-400">
                                        {{ number_format($cnt, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-gray-700 dark:text-gray-300">
                                        {{ round(($cnt / $totLanjut) * 100, 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Box 2: Status Pelayanan --}}
                <div class="p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/50 dark:border-strokedark/50">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Status Pelayanan Terakhir
                    </h4>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-gray-500 dark:text-gray-400 border-b border-stroke dark:border-strokedark font-semibold">
                                <th class="pb-2 text-left">Status Pelayanan</th>
                                <th class="pb-2 text-center">Jumlah Pasien</th>
                                <th class="pb-2 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                            @php
                                $totStatus = array_sum($statusBreakdown['by_status']) ?: 1;
                            @endphp
                            @foreach ($statusBreakdown['by_status'] as $sttsKey => $cnt)
                                <tr class="hover:bg-white/50 dark:hover:bg-boxdark/50">
                                    <td class="py-2.5 text-gray-800 dark:text-gray-200">
                                        {{ $sttsKey }}
                                    </td>
                                    <td class="py-2.5 text-center font-bold text-gray-800 dark:text-white">
                                        {{ number_format($cnt, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-gray-700 dark:text-gray-300">
                                        {{ round(($cnt / $totStatus) * 100, 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- TAB 2: REKAP DOKTER JAGA --}}
        @if ($activeTab === 'rekap_dokter')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3">Nama Dokter</th>
                            <th class="py-3 px-3 text-center">Total Pasien</th>
                            <th class="py-3 px-3 text-center">Masuk Ranap</th>
                            <th class="py-3 px-3 text-center">Ralan / Pulang</th>
                            <th class="py-3 px-3 text-center">Laki-laki</th>
                            <th class="py-3 px-3 text-center">Perempuan</th>
                            <th class="py-3 px-3 text-center">Dirujuk</th>
                            <th class="py-3 px-3 text-center">Meninggal</th>
                            <th class="py-3 px-3 text-right">Proporsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        @forelse ($doctorBreakdown as $doc)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                <td class="py-2.5 px-3 font-bold text-gray-800 dark:text-gray-200">{{ $doc['nm_dokter'] }}</td>
                                <td class="py-2.5 px-3 text-center font-black text-rose-600 dark:text-rose-400">{{ number_format($doc['total']) }}</td>
                                <td class="py-2.5 px-3 text-center text-purple-600 dark:text-purple-400 font-bold">{{ number_format($doc['ranap']) }}</td>
                                <td class="py-2.5 px-3 text-center text-sky-600 dark:text-sky-400 font-bold">{{ number_format($doc['ralan']) }}</td>
                                <td class="py-2.5 px-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($doc['pria']) }}</td>
                                <td class="py-2.5 px-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($doc['wanita']) }}</td>
                                <td class="py-2.5 px-3 text-center text-amber-600 dark:text-amber-400 font-bold">{{ number_format($doc['dirujuk']) }}</td>
                                <td class="py-2.5 px-3 text-center text-rose-600 dark:text-rose-400 font-bold">{{ number_format($doc['meninggal']) }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-gray-800 dark:text-gray-200">{{ $doc['percent'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-gray-500">Tidak ada data dokter pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 3: REKAP CARA BAYAR --}}
        @if ($activeTab === 'rekap_bayar')
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3">Penanggung Jawab / Cara Bayar</th>
                            <th class="py-3 px-3 text-center">Total Pasien</th>
                            <th class="py-3 px-3 text-center">Masuk Ranap</th>
                            <th class="py-3 px-3 text-center">Rawat Jalan</th>
                            <th class="py-3 px-3 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        @forelse ($payTypeBreakdown as $pay)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                <td class="py-2.5 px-3 font-bold text-gray-800 dark:text-gray-200">{{ $pay['png_jawab'] }}</td>
                                <td class="py-2.5 px-3 text-center font-black text-rose-600 dark:text-rose-400">{{ number_format($pay['total']) }}</td>
                                <td class="py-2.5 px-3 text-center text-purple-600 dark:text-purple-400 font-bold">{{ number_format($pay['ranap']) }}</td>
                                <td class="py-2.5 px-3 text-center text-sky-600 dark:text-sky-400 font-bold">{{ number_format($pay['ralan']) }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-gray-800 dark:text-gray-200">{{ $pay['percent'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data cara bayar pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TAB 4: DATA PASIEN IGD --}}
        @if ($activeTab === 'data_pasien')
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4 no-print">
                <div class="relative w-72">
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari No. Rawat, RM, Nama..."
                        class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-meta-4/30 border border-stroke dark:border-strokedark rounded-xl text-xs font-medium text-gray-800 dark:text-white outline-none focus:border-rose-500 transition" />
                    <span class="icon-[solar--magnifer-bold-duotone] text-base text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Tampilkan:</span>
                    <select wire:model.live="limit"
                        class="py-1.5 px-2 bg-gray-50 dark:bg-meta-4/30 border border-stroke dark:border-strokedark rounded-lg text-xs font-bold text-gray-800 dark:text-white outline-none">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3">No. Rawat</th>
                            <th class="py-3 px-3">No. RM</th>
                            <th class="py-3 px-3">Nama Pasien</th>
                            <th class="py-3 px-2 text-center">L/P</th>
                            <th class="py-3 px-3">Waktu Masuk</th>
                            <th class="py-3 px-3">Dokter Jaga</th>
                            <th class="py-3 px-3">Cara Bayar</th>
                            <th class="py-3 px-3 text-center">Status Pelayanan</th>
                            <th class="py-3 px-3 text-center">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        @forelse ($patients as $p)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                <td class="py-2.5 px-3 font-mono font-bold text-gray-800 dark:text-gray-200">{{ $p->no_rawat }}</td>
                                <td class="py-2.5 px-3 font-mono text-gray-600 dark:text-gray-400">{{ $p->no_rkm_medis }}</td>
                                <td class="py-2.5 px-3 font-bold text-gray-900 dark:text-white">{{ $p->nm_pasien }}</td>
                                <td class="py-2.5 px-2 text-center">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $p->jk === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' }}">
                                        {{ $p->jk }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-gray-600 dark:text-gray-400">
                                    {{ Carbon\Carbon::parse($p->tgl_registrasi)->format('d/m/Y') }} {{ $p->jam_reg }}
                                </td>
                                <td class="py-2.5 px-3 text-gray-800 dark:text-gray-200">{{ $p->nm_dokter }}</td>
                                <td class="py-2.5 px-3 text-gray-700 dark:text-gray-300">{{ $p->png_jawab }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                        {{ $p->stts === 'Sudah' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : '' }}
                                        {{ $p->stts === 'Dirawat' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400' : '' }}
                                        {{ $p->stts === 'Dirujuk' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' : '' }}
                                        {{ $p->stts === 'Meninggal' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400' : '' }}
                                        {{ !in_array($p->stts, ['Sudah', 'Dirawat', 'Dirujuk', 'Meninggal']) ? 'bg-gray-100 text-gray-700 dark:bg-meta-4 dark:text-gray-300' : '' }}
                                    ">
                                        {{ $p->stts }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->status_lanjut === 'Ranap' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' }}">
                                        {{ $p->status_lanjut === 'Ranap' ? 'Rawat Inap' : 'Rawat Jalan' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-500">Tidak ada data pasien IGD pada kriteria ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 no-print">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</x-content>
