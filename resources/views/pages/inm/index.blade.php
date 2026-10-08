<x-content>
    <x-breadcrumb title="Indikator Nasional Mutu (INM)" :items="[['title' => 'Mutu & Akreditasi'], ['title' => 'Indikator Nasional Mutu (INM)']]" />

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="icon-[solar--shield-check-bold-duotone] text-emerald-600 dark:text-emerald-400 text-3xl"></span>
                <span>Indikator Nasional Mutu (INM)</span>
            </h1>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                Pemantauan 13 Indikator Mutu Pelayanan Rumah Sakit Berdasarkan Permenkes RI No. 30 Tahun 2022 & Standar Akreditasi Kemenkes (STARKES)
            </p>
        </div>

        <div class="flex items-center gap-2 no-print">
            <x-button color="default" icon="i-ph-file-pdf" wire:click="exportPdf" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf">Cetak PDF Data</span>
                <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
                    <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
                    <span>Menyiapkan PDF...</span>
                </span>
            </x-button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Filter Tahun --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-emerald-600 dark:text-emerald-400 text-base"></span>
                    <span>Tahun:</span>
                </span>
                <select wire:model.live="selectedYear"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bulan --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-emerald-600 dark:text-emerald-400 text-base"></span>
                    <span>Periode:</span>
                </span>
                <select wire:model.live="selectedMonth"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($months as $mKey => $mLabel)
                        <option value="{{ $mKey }}">{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Category Filter Pills --}}
            <div class="flex items-center gap-1.5 pl-2 border-l border-stroke dark:border-strokedark">
                <button type="button" wire:click="setCategory('all')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedCategory === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
                    Semua (13)
                </button>
                <button type="button" wire:click="setCategory('pelayanan_klinis')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedCategory === 'pelayanan_klinis' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
                    Pelayanan Klinis
                </button>
                <button type="button" wire:click="setCategory('ppi_keselamatan')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedCategory === 'ppi_keselamatan' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
                    Keselamatan & PPI
                </button>
                <button type="button" wire:click="setCategory('tata_kelola_penunjang')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedCategory === 'tata_kelola_penunjang' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
                    Tata Kelola & Penunjang
                </button>
            </div>

            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 pl-2">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memperbarui metrik...</span>
            </div>
        </div>

        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Standar INM Kemenkes RI</span>
        </div>
    </div>

    {{-- Executive Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Indikator --}}
        <div class="p-5 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl text-white shadow-md relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-wider text-slate-300">Total Indikator Nasional</span>
                <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-2xl text-slate-400"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black">13</span>
                <span class="text-xs font-semibold text-slate-400">Parameter Kemenkes</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Permenkes No. 30 Th 2022</p>
        </div>

        {{-- Indikator Tercapai --}}
        <div class="p-5 bg-gradient-to-br from-emerald-600 to-teal-800 rounded-2xl text-white shadow-md relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-200">Indikator Memenuhi Standar</span>
                <span class="icon-[solar--check-circle-bold-duotone] text-2xl text-emerald-200"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black">{{ $summary['achieved_count'] }}</span>
                <span class="text-xs font-bold text-emerald-200">/ 13 Indikator</span>
            </div>
            <div class="w-full bg-white/20 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-300 h-full rounded-full transition-all duration-500"
                    style="width: {{ round(($summary['achieved_count'] / 13) * 100) }}%"></div>
            </div>
        </div>

        {{-- Indikator Belum Tercapai --}}
        <div class="p-5 bg-gradient-to-br from-rose-600 to-red-800 rounded-2xl text-white shadow-md relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-wider text-rose-200">Perlu Perbaikan / Belum Capai</span>
                <span class="icon-[solar--danger-triangle-bold-duotone] text-2xl text-rose-200"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black">{{ $summary['unachieved_count'] }}</span>
                <span class="text-xs font-bold text-rose-200">Indikator</span>
            </div>
            <p class="text-[11px] text-rose-200/90 mt-2">Fokus perbaikan mutu berkesinambungan</p>
        </div>

        {{-- Rata-rata Skor Mutu --}}
        <div class="p-5 bg-gradient-to-br from-blue-600 to-indigo-800 rounded-2xl text-white shadow-md relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-wider text-blue-200">Indeks Mutu Rumah Sakit</span>
                <span class="icon-[solar--chart-bold-duotone] text-2xl text-blue-200"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black">{{ $summary['average_score'] }}%</span>
                <span class="text-xs font-bold text-blue-200">Skor Rata-rata</span>
            </div>
            <p class="text-[11px] text-blue-200/90 mt-2">Komposit mutu layanan tahun {{ $selectedYear }}</p>
        </div>
    </div>

    {{-- Monthly Trend Chart Section --}}
    <div class="mb-6 p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--graph-new-bold-duotone] text-emerald-600 dark:text-emerald-400 text-xl"></span>
                    <span>Tren Capaian Bulanan vs Standar Kemenkes (Tahun {{ $selectedYear }})</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Pilih indikator di bawah untuk melihat fluktuasi kinerja mutu setiap bulan dibandingkan garis batas target standar.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-500 dark:text-gray-400 whitespace-nowrap">Indikator:</label>
                <select wire:change="setChartIndicator($event.target.value)"
                    class="py-1.5 pl-3 pr-8 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none cursor-pointer">
                    @foreach ($definitions as $defId => $defItem)
                        <option value="{{ $defId }}" @selected($chartIndicator === $defId)>
                            {{ $defItem['code'] }} - {{ $defItem['title'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="relative w-full h-[280px]">
            <canvas id="inmTrendChart"></canvas>
        </div>
    </div>

    {{-- 13 Indicator Grid Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
        @foreach ($filteredIndicators as $id => $item)
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
                {{-- Category Top Bar --}}
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-meta-4 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-strokedark">
                            {{ $item['code'] }}
                        </span>
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                            {{ $item['category_label'] }}
                        </span>
                    </div>

                    @if ($item['is_achieved'])
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            <span class="icon-[solar--check-circle-bold] text-sm"></span>
                            <span>Tercapai</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                            <span class="icon-[solar--close-circle-bold] text-sm"></span>
                            <span>Belum Capai</span>
                        </span>
                    @endif
                </div>

                {{-- Title & Description --}}
                <div class="mb-4">
                    <h4 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        {{ $item['title'] }}
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                        {{ $item['description'] }}
                    </p>
                </div>

                {{-- Metric Row: Rate vs Target --}}
                <div class="p-3.5 bg-gray-50 dark:bg-meta-4/40 rounded-xl mb-4 border border-gray-100 dark:border-strokedark/50">
                    <div class="flex items-baseline justify-between mb-1.5">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Capaian Saat Ini:</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-black {{ $item['is_achieved'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $item['rate'] }}{{ $item['unit'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">
                        <span>Standar Target:</span>
                        <span class="font-black px-2 py-0.5 rounded-md bg-white dark:bg-boxdark border border-stroke dark:border-strokedark text-gray-800 dark:text-white">
                            {{ $item['standard_label'] }}
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    @php
                        $progressPercent = min(100, max(0, $item['rate']));
                    @endphp
                    <div class="w-full bg-gray-200 dark:bg-meta-4 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $item['is_achieved'] ? 'bg-emerald-500' : 'bg-rose-500' }}"
                            style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>

                {{-- Numerator & Denominator Details --}}
                <div class="space-y-1.5 text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-4 border-t border-stroke dark:border-strokedark/40 pt-3">
                    <div class="flex items-center justify-between">
                        <span class="truncate max-w-[200px]" title="{{ $item['numerator_label'] }}">Numerator:</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($item['numerator']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="truncate max-w-[200px]" title="{{ $item['denominator_label'] }}">Denominator:</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($item['denominator']) }}</span>
                    </div>
                    @if (!empty($item['additional_info']))
                        <div class="text-[10px] text-gray-400 italic pt-1">
                            {{ $item['additional_info'] }}
                        </div>
                    @endif
                </div>

                {{-- Action Button --}}
                <button type="button" wire:click="openAuditModal('{{ $id }}')"
                    class="w-full py-2.5 px-3 rounded-xl text-xs font-bold bg-white dark:bg-boxdark hover:bg-emerald-50 dark:hover:bg-emerald-950/30 text-gray-700 dark:text-gray-200 hover:text-emerald-600 dark:hover:text-emerald-400 border border-stroke dark:border-strokedark hover:border-emerald-300 dark:hover:border-emerald-700 transition-all flex items-center justify-center gap-2 no-print shadow-xs">
                    <span class="icon-[solar--document-text-bold-duotone] text-base"></span>
                    <span>Lihat Detail Audit / Log</span>
                </button>
            </div>
        @endforeach
    </div>

    {{-- Detail Audit Modal (Notice !mt-0 to ensure it is flush at the top) --}}
    @if ($activeIndicatorModal && $activeIndicatorInfo)
        <div class="fixed inset-0 z-50 flex items-center !mt-0 justify-center p-3 sm:p-5 md:p-8 bg-slate-950/75 backdrop-blur-md overflow-hidden animate-in fade-in duration-200"
            wire:keydown.escape="closeAuditModal">
            <div class="bg-white dark:bg-boxdark w-full max-w-4xl max-h-[90vh] rounded-3xl border border-stroke dark:border-strokedark shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-200 !mt-0">
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 border-b border-stroke dark:border-strokedark flex items-start justify-between gap-4 bg-gray-50/50 dark:bg-meta-4/20">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                {{ $activeIndicatorInfo['code'] }}
                            </span>
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Standar: {{ $activeIndicatorInfo['standard_label'] }}
                            </span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white">
                            {{ $activeIndicatorInfo['title'] }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $activeIndicatorInfo['description'] }}
                        </p>
                    </div>

                    <button type="button" wire:click="closeAuditModal"
                        class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-meta-4 transition-colors">
                        <span class="icon-[solar--close-circle-bold] text-2xl"></span>
                    </button>
                </div>

                {{-- Modal Content: Formula & Audit Table --}}
                <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1">
                    {{-- Formula Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40">
                            <span class="font-bold text-blue-800 dark:text-blue-300 block mb-1">Pembilang (Numerator):</span>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $activeIndicatorInfo['numerator_label'] }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/40">
                            <span class="font-bold text-purple-800 dark:text-purple-300 block mb-1">Penyebut (Denominator):</span>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $activeIndicatorInfo['denominator_label'] }}</p>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <span class="icon-[solar--list-check-bold-duotone] text-emerald-600 text-base"></span>
                                <span>Sampel Data Operasional & Log Audit Terakhir</span>
                            </h4>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                Menampilkan maks 50 entri audit
                            </span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-stroke dark:border-strokedark">
                            <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                                <thead class="bg-gray-50 dark:bg-meta-4 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-stroke dark:border-strokedark">
                                    <tr>
                                        <th class="py-2.5 px-3">Tanggal</th>
                                        <th class="py-2.5 px-3">No. Rawat / ID</th>
                                        <th class="py-2.5 px-3">Pasien / Subjek</th>
                                        <th class="py-2.5 px-3">Unit Pelayanan</th>
                                        <th class="py-2.5 px-3">Waktu / Catatan</th>
                                        <th class="py-2.5 px-3">Nilai / Hasil</th>
                                        <th class="py-2.5 px-3">Status Kepatuhan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                    @forelse ($indicatorAuditDetails as $row)
                                        <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/30 transition-colors">
                                            <td class="py-2.5 px-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ $row['tanggal'] ?? '-' }}
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-[11px]">
                                                {{ $row['no_rawat'] ?? '-' }}
                                            </td>
                                            <td class="py-2.5 px-3 font-bold text-gray-900 dark:text-white">
                                                {{ $row['subjek'] ?? '-' }}
                                            </td>
                                            <td class="py-2.5 px-3 text-gray-600 dark:text-gray-300">
                                                {{ $row['unit'] ?? '-' }}
                                            </td>
                                            <td class="py-2.5 px-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ $row['waktu_mulai'] ?? '-' }}
                                            </td>
                                            <td class="py-2.5 px-3 font-medium text-gray-800 dark:text-gray-200">
                                                {{ $row['nilai'] ?? '-' }}
                                            </td>
                                            <td class="py-2.5 px-3 whitespace-nowrap">
                                                @if (!empty($row['is_patuh']))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                        <span class="icon-[solar--check-circle-bold]"></span>
                                                        <span>{{ $row['status'] }}</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                                        <span class="icon-[solar--close-circle-bold]"></span>
                                                        <span>{{ $row['status'] }}</span>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-gray-400">
                                                Tidak ada data audit untuk indikator dan periode yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:p-5 border-t border-stroke dark:border-strokedark flex justify-end gap-2 bg-gray-50/50 dark:bg-meta-4/20">
                    <button type="button" wire:click="closeAuditModal"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-boxdark border border-stroke dark:border-strokedark text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-meta-4 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Chart.js Script --}}
    @script
        <script>
            let trendChartInstance = null;

            function initTrendChart(chartData) {
                const ctx = document.getElementById('inmTrendChart');
                if (!ctx) return;

                if (trendChartInstance) {
                    trendChartInstance.destroy();
                }

                trendChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Capaian Mutu (%)',
                                data: chartData.data,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 3,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Standar Target (' + chartData.targets[0] + '%)',
                                data: chartData.targets,
                                borderColor: '#ef4444',
                                borderDash: [6, 6],
                                borderWidth: 2,
                                fill: false,
                                pointRadius: 0,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                ticks: {
                                    callback: function(value) { return value + '%'; }
                                }
                            }
                        }
                    }
                });
            }

            // Initial load from Livewire data
            const initialLabels = @json($trendData['labels'] ?? []);
            const initialData = @json($trendData['data'] ?? []);
            const initialTargets = @json($trendData['targets'] ?? []);

            setTimeout(() => {
                initTrendChart({
                    labels: initialLabels,
                    data: initialData,
                    targets: initialTargets
                });
            }, 100);

            // Listen for chart updates dispatched from Livewire
            Livewire.on('refresh-inm-chart', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                if (data) {
                    initTrendChart(data);
                }
            });
        </script>
    @endscript
</x-content>
