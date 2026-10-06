<x-content>
    <x-breadcrumb title="Laporan Pasien Rawat Inap" :items="[['title' => 'Rawat Inap', 'href' => route('inpatient')], ['title' => 'Laporan Pasien']]" />

    <x-export-loading wire:target="exportCsv, exportPdf, exportExcel" />

    {{-- Filter Periode & Ekspor --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <select wire:model.live="period"
                    class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
                    <option value="monthly">Pilih Bulan</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="today">Hari Ini</option>
                    <option value="last_7_days">7 Hari Lalu</option>
                    <option value="last_30_days">30 Hari Lalu</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_year">Tahun Ini</option>
                    <option value="yearly">Pilih Tahun</option>
                    <option value="custom">Rentang Tanggal (Custom)</option>
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
                        class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm cursor-pointer">
                        @foreach ($months as $index => $name)
                            <option value="{{ $index }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedYear"
                        class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm cursor-pointer">
                        @foreach ($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif ($period === 'yearly')
                <select wire:model.live="selectedYear"
                    class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm cursor-pointer">
                    @foreach ($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            @elseif ($period === 'custom')
                <div
                    class="flex items-center gap-2 px-4 py-1.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark shadow-sm">
                    <input type="date" wire:model.live="startDate"
                        class="bg-transparent border-none focus:ring-0 text-sm font-bold cursor-pointer text-gray-700 dark:text-white" />
                    <span class="text-gray-300 font-bold">s/d</span>
                    <input type="date" wire:model.live="endDate"
                        class="bg-transparent border-none focus:ring-0 text-sm font-bold cursor-pointer text-gray-700 dark:text-white" />
                </div>
            @endif
        </div>

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

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Pasien --}}
        <div
            class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-primary bg-primary/10">
                <span class="icon-[solar--bed-bold-duotone] text-2xl"></span>
            </div>
            <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Pasien</p>
                <p class="text-2xl font-black text-gray-800 dark:text-white">
                    {{ number_format($summary['total_pasien'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Masih Dirawat --}}
        <div
            class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-amber-600 bg-amber-500/10">
                <span class="icon-[solar--clock-circle-bold-duotone] text-2xl"></span>
            </div>
            <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Masih Dirawat</p>
                <p class="text-2xl font-black text-amber-600">
                    {{ number_format($summary['masih_dirawat'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Sudah Pulang / Keluar --}}
        <div
            class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-emerald-600 bg-emerald-500/10">
                <span class="icon-[solar--check-circle-bold-duotone] text-2xl"></span>
            </div>
            <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Sudah Pulang</p>
                <p class="text-2xl font-black text-emerald-600">
                    {{ number_format($summary['sudah_pulang'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Penjamin Terpilih --}}
        <div
            class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-cyan-600 bg-cyan-500/10">
                <span class="icon-[solar--shield-check-bold-duotone] text-2xl"></span>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Penjamin</p>
                @php
                    $activePayType = collect($payTypes)->firstWhere('value', $payType);
                @endphp
                <p class="text-base font-bold text-gray-800 dark:text-white truncate" title="{{ $activePayType['title'] ?? 'Semua' }}">
                    {{ $activePayType['title'] ?? 'Semua' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-5 space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Pencarian --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                    Pencarian Pasien
                </label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.400ms="search"
                        placeholder="No. Rawat, No. RM, atau Nama..."
                        class="w-full pl-10 pr-9 py-2.5 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-sm font-medium dark:text-white placeholder:text-gray-400 focus:border-primary focus:bg-white dark:focus:bg-boxdark focus:ring-0 outline-none transition-all shadow-sm" />
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <span class="icon-[solar--magnifer-bold-duotone] text-lg"></span>
                    </div>
                    @if ($search)
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <span class="icon-[solar--close-circle-bold] text-base"></span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Penjamin --}}
            <div>
                <x-form.select label="Penjamin" labelClass="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400" block :items="$payTypes" wire:model.live="payType" />
            </div>

            {{-- Status Pulang --}}
            <div>
                <x-form.select label="Status Pulang" labelClass="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400" block :items="$statusPulangOptions" wire:model.live="statusPulang" />
            </div>

            {{-- Bangsal --}}
            <div>
                <x-form.select label="Bangsal" labelClass="block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400" block :items="$wards" wire:model.live="ward" />
            </div>
        </div>

        {{-- Footer Filter Bar: Limit, Toggle Grafik & Reset --}}
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-stroke dark:border-strokedark">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tampilkan:</span>
                <select wire:model.live="limit"
                    class="px-3 py-1.5 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-lg text-xs font-bold text-gray-700 dark:text-gray-200 focus:border-primary outline-none cursor-pointer shadow-sm">
                    @foreach ($limits as $l)
                        <option value="{{ $l }}">{{ $l }} baris per halaman</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="toggleCharts"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $showCharts ? 'text-primary bg-primary/10 hover:bg-primary/20 dark:text-primary dark:bg-primary/10' : 'text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300' }}">
                    <span class="{{ $showCharts ? 'icon-[solar--eye-closed-bold-duotone]' : 'icon-[solar--chart-bold-duotone]' }} text-sm"></span>
                    <span>{{ $showCharts ? 'Sembunyikan Grafik' : 'Tampilkan Grafik' }}</span>
                </button>
                @if ($search || $payType !== 'BPJ' || $statusPulang !== 'semua' || $ward !== 'semua')
                    <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 transition-all cursor-pointer">
                        <span class="icon-[solar--restart-bold-duotone] text-sm"></span>
                        Reset Semua Filter
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Seksi Grafik & Visualisasi Tren Rawat Inap --}}
    @if ($showCharts)
        <div wire:key="inpatient-charts-{{ md5($startDate . $endDate . $payType . $statusPulang . $ward . $search) }}"
            x-data="inpatientReportCharts(@js($this->chartPayload))"
            class="space-y-4 no-print transition-all duration-300">

            {{-- Baris 1: Tren Pasien Masuk Harian (8 col) & Komposisi Penjamin (4 col) --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                {{-- Tren Pasien Masuk Rawat Inap --}}
                <div class="lg:col-span-8 p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-stroke/70 dark:border-strokedark/70 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <span class="icon-[solar--graph-up-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Tren Pasien Masuk Rawat Inap</h3>
                                <p class="text-[11px] text-gray-400 font-medium">Fluktuasi harian tanggal masuk pasien & komparasi gender</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-primary/10 text-primary dark:text-primary border border-primary/20">
                            {{ count($this->chartPayload['trend']['labels']) }} Titik Waktu
                        </span>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="chartInpatientTrend"></canvas>
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
                                <p class="text-[11px] text-gray-400 font-medium">Distribusi cara bayar & asuransi pasien ranap</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[280px] w-full flex items-center justify-center">
                        <canvas id="chartInpatientPayer"></canvas>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Top 8 Bangsal (6 col) & Sebaran Kelompok Umur (6 col) --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                {{-- Top 8 Bangsal / Ruangan --}}
                <div class="p-4 sm:p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between transition hover:shadow-md">
                    <div class="flex items-center justify-between pb-3 border-b border-stroke/70 dark:border-strokedark/70 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--bed-bold-duotone] text-lg"></span>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Top 8 Bangsal / Ruangan</h3>
                                <p class="text-[11px] text-gray-400 font-medium">Bangsal dengan volume pasien masuk tertinggi</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[270px] w-full">
                        <canvas id="chartInpatientWard"></canvas>
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
                        <canvas id="chartInpatientAge"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabel Laporan --}}
    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        {{-- Judul Formal Laporan --}}
        <div class="px-6 pt-6 pb-4 border-b border-stroke dark:border-strokedark text-center">
            <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-widest">
                Laporan Pasien Rawat Inap
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">
                Periode Tanggal Masuk:
                <span class="font-bold text-gray-700 dark:text-gray-200">
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                </span>
                s/d
                <span class="font-bold text-gray-700 dark:text-gray-200">
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                </span>
                &bull; Penjamin:
                <span class="font-bold text-primary">
                    {{ $activePayType['title'] ?? 'Semua' }}
                </span>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4 text-xs font-black text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-stroke dark:border-strokedark">
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-left whitespace-nowrap">No. Rawat</th>
                        <th class="px-4 py-3.5 text-left whitespace-nowrap">No. RM</th>
                        <th class="px-4 py-3.5 text-left min-w-[200px]">Nama Pasien</th>
                        <th class="px-4 py-3.5 text-left min-w-[180px]">Bangsal</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tgl Masuk</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tgl Keluar</th>
                        <th class="px-4 py-3.5 text-left min-w-[150px]">Penjamin</th>
                        <th class="px-4 py-3.5 text-left min-w-[240px]">DPJP Ranap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($patients as $index => $patient)
                        @php
                            $isMasihDirawat = ($patient->tgl_keluar == '0000-00-00' || empty($patient->tgl_keluar));
                            $rowNumber = ($patients instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                ? ($patients->firstItem() + $index)
                                : ($index + 1);
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/40 transition-colors">
                            <td class="px-4 py-3.5 text-center font-bold text-gray-500 text-xs">
                                {{ $rowNumber }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs font-bold text-primary whitespace-nowrap">
                                {{ $patient->no_rawat }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ $patient->no_rkm_medis }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-800 dark:text-white">
                                {{ $patient->nm_pasien }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300 text-xs font-medium">
                                {{ $patient->nm_bangsal }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($patient->tgl_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if ($isMasihDirawat)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Masih Dirawat
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($patient->tgl_keluar)->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">
                                <span class="inline-block px-2 py-0.5 rounded bg-gray-100 dark:bg-meta-4 font-medium">
                                    {{ $patient->png_jawab ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-700 dark:text-gray-300">
                                @if (!empty($patient->dpjp_ranap))
                                    <div class="flex items-center gap-1.5">
                                        <span class="icon-[solar--stethoscope-bold-duotone] text-primary text-sm shrink-0"></span>
                                        <span class="font-medium">{{ $patient->dpjp_ranap }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <span class="icon-[solar--document-text-bold-duotone] text-5xl text-gray-300 dark:text-gray-600"></span>
                                    <p class="text-gray-500 dark:text-gray-400 font-bold text-sm">Tidak ada data pasien rawat inap yang sesuai.</p>
                                    <p class="text-gray-400 text-xs">Coba ubah filter periode, bangsal, atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($patients instanceof \Illuminate\Pagination\LengthAwarePaginator && $patients->hasPages())
            <div class="p-4 border-t border-stroke dark:border-strokedark">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

    <style>
        @media print {
            nav,
            aside,
            header,
            footer,
            .breadcrumb,
            button {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>

    @script
        <script>
            Alpine.data('inpatientReportCharts', (chartPayload) => ({
                trendChart: null,
                payerChart: null,
                wardChart: null,
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
                    if (this.wardChart) { try { this.wardChart.destroy(); } catch(e){} this.wardChart = null; }
                    if (this.ageChart) { try { this.ageChart.destroy(); } catch(e){} this.ageChart = null; }
                },
                renderAll(data) {
                    if (!data) return;
                    this.destroyAll();
                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.04)';
                    const textColor = isDark ? '#94A3B8' : '#64748B';

                    // 1. Line Chart: Tren Pasien Masuk Rawat Inap
                    const trendCanvas = document.getElementById('chartInpatientTrend');
                    if (trendCanvas && data.trend) {
                        const ctx = trendCanvas.getContext('2d');
                        if (ctx) {
                            const gradPrimary = ctx.createLinearGradient(0, 0, 0, 260);
                            gradPrimary.addColorStop(0, 'rgba(59, 130, 246, 0.28)');
                            gradPrimary.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                            this.trendChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.trend.labels || [],
                                    datasets: [
                                        {
                                            label: 'Total Pasien',
                                            data: data.trend.total || [],
                                            borderColor: '#3B82F6',
                                            backgroundColor: gradPrimary,
                                            fill: true,
                                            tension: 0.35,
                                            borderWidth: 2.5,
                                            pointRadius: (data.trend.labels || []).length > 25 ? 1 : 3,
                                            pointHoverRadius: 5,
                                            pointBackgroundColor: '#3B82F6',
                                        },
                                        {
                                            label: 'Laki-laki',
                                            data: data.trend.pria || [],
                                            borderColor: '#06B6D4',
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
                    const payerCanvas = document.getElementById('chartInpatientPayer');
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

                    // 3. Horizontal Bar Chart: Top 8 Bangsal
                    const wardCanvas = document.getElementById('chartInpatientWard');
                    if (wardCanvas && data.ward) {
                        const ctx = wardCanvas.getContext('2d');
                        if (ctx) {
                            this.wardChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: data.ward.labels || [],
                                    datasets: [{
                                        label: 'Total Pasien',
                                        data: data.ward.total || [],
                                        backgroundColor: isDark ? 'rgba(245, 158, 11, 0.75)' : 'rgba(245, 158, 11, 0.85)',
                                        borderColor: '#F59E0B',
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
                    const ageCanvas = document.getElementById('chartInpatientAge');
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
