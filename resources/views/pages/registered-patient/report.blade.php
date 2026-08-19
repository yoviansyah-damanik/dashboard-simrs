<x-content>
    <x-breadcrumb title="Laporan Kunjungan dan Pengunjung" :items="[['title' => 'Pendaftaran'], ['title' => 'Laporan Kunjungan dan Pengunjung']]" />

    {{-- Filter Periode --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <select wire:model.live="period"
                    class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
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
            @elseif ($period === 'yearly')
                <select wire:model.live="selectedYear"
                    class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                    @foreach ($this->years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            @elseif ($period === 'custom')
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

        <x-button color="default" icon="i-ph-printer" onclick="window.print()">Cetak</x-button>
    </div>

    @php $s = $this->summary; @endphp

    {{-- Ringkasan Cepat --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @php
            $stats = [
                ['label' => 'Total Pengunjung', 'value' => $s['total_pengunjung'], 'icon' => 'icon-[solar--users-group-rounded-bold-duotone]', 'color' => 'text-primary bg-primary/10'],
                ['label' => 'Total Kunjungan', 'value' => $s['total_kunjungan'], 'icon' => 'icon-[solar--clipboard-list-bold-duotone]', 'color' => 'text-cyan-600 bg-cyan-500/10'],
                ['label' => 'Pasien Baru', 'value' => $s['pengunjung_baru'], 'icon' => 'icon-[solar--user-plus-bold-duotone]', 'color' => 'text-emerald-600 bg-emerald-500/10'],
                ['label' => 'Pasien Lama', 'value' => $s['pengunjung_lama'], 'icon' => 'icon-[solar--user-bold-duotone]', 'color' => 'text-gray-600 bg-gray-500/10'],
                ['label' => 'Dirujuk', 'value' => $s['rujukan'], 'icon' => 'icon-[solar--map-arrow-right-bold-duotone]', 'color' => 'text-amber-600 bg-amber-500/10'],
                ['label' => 'Rawat Inap', 'value' => $s['rawat_inap'], 'icon' => 'icon-[solar--bed-bold-duotone]', 'color' => 'text-violet-600 bg-violet-500/10'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div
                class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $stat['color'] }}">
                    <span class="{{ $stat['icon'] }} text-xl"></span>
                </div>
                <div>
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                    <p class="text-xl font-black text-gray-800 dark:text-white">{{ number_format($stat['value'], 0, ',', '.') }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tabel Laporan --}}
    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        {{-- Judul Formal --}}
        <div class="px-6 pt-6 pb-4 border-b border-stroke dark:border-strokedark text-center">
            <p class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-widest">
                Laporan Kunjungan dan Pengunjung Pendaftaran
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                &ndash;
                {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="bg-gray-50 dark:bg-meta-4 text-sm font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                        <th class="px-5 py-3 text-left w-1/3">Poliklinik</th>
                        <th class="px-4 py-3 text-right">Pengunjung</th>
                        <th class="px-4 py-3 text-right">Kunjungan</th>
                        <th class="px-4 py-3 text-right">Baru</th>
                        <th class="px-4 py-3 text-right">Lama</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($s['by_poliklinik'] as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300 font-bold text-sm">{{ $item->nm_poli }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($item->pengunjung, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($item->kunjungan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600">{{ number_format($item->baru, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ number_format($item->lama, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="icon-[solar--document-add-bold-duotone] text-6xl text-gray-200"></span>
                                    <p class="text-gray-400 italic text-sm">Tidak ada data kunjungan untuk periode ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    {{-- ===== TOTAL ===== --}}
                    <tr class="bg-primary/10 dark:bg-primary/20 font-black text-base border-t-2 border-primary/30">
                        <td class="px-5 py-4 text-primary uppercase tracking-widest text-sm">Total Keseluruhan</td>
                        <td class="px-4 py-4 text-right text-primary text-base">{{ number_format($s['total_pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-primary text-base">{{ number_format($s['total_kunjungan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-emerald-600 text-base">{{ number_format($s['pengunjung_baru'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-gray-500 text-base">{{ number_format($s['pengunjung_lama'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="px-6 py-3 text-sm text-gray-400 italic border-t border-stroke dark:border-strokedark">
            *) Ditampilkan {{ count($s['by_poliklinik']) }} poliklinik dengan kunjungan terbanyak pada periode ini.
        </p>
    </div>

    <style>
        @media print {

            nav,
            aside,
            header,
            footer {
                display: none !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
</x-content>
