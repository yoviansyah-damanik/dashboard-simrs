<x-content>
    <x-breadcrumb title="Laporan Data Pasien" :items="[['title' => 'Laporan Data Pasien']]" />

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
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $stats = [
                ['label' => 'Total Pengunjung', 'value' => $s['total_pengunjung'], 'icon' => 'icon-[solar--users-group-rounded-bold-duotone]', 'color' => 'text-primary bg-primary/10'],
                ['label' => 'Total Kunjungan',  'value' => $s['total_kunjungan'],  'icon' => 'icon-[solar--clipboard-list-bold-duotone]',       'color' => 'text-cyan-600 bg-cyan-500/10'],
                ['label' => 'Dirujuk',           'value' => $s['rujukan'],          'icon' => 'icon-[solar--map-arrow-right-bold-duotone]',        'color' => 'text-amber-600 bg-amber-500/10'],
                ['label' => 'Rawat Inap',        'value' => $s['rawat_inap'],       'icon' => 'icon-[solar--bed-bold-duotone]',                    'color' => 'text-violet-600 bg-violet-500/10'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $stat['color'] }}">
                    <span class="{{ $stat['icon'] }} text-xl"></span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
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
                Laporan Pendataan / Pencatatan Data Pasien Berobat
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                &ndash;
                {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4 text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                        <th class="px-5 py-3 text-left w-1/3">Kelompok Pasien</th>
                        <th class="px-4 py-3 text-right">Pengunjung</th>
                        <th class="px-4 py-3 text-right">Kunjungan</th>
                        <th class="px-4 py-3 text-right">Rujukan</th>
                        <th class="px-4 py-3 text-right">Rawat Inap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">

                    {{-- ===== ANGKATAN DARAT ===== --}}
                    <tr class="bg-primary/5 dark:bg-primary/10">
                        <td colspan="5" class="px-5 py-2 text-xs font-black text-primary uppercase tracking-widest">
                            Angkatan Darat
                        </td>
                    </tr>
                    @php
                        $adRows = [
                            ['label' => 'Militer (MIL)',             'p' => $s['angkatan_darat']['pengunjung']['mil'], 'k' => $s['angkatan_darat']['kunjungan']['mil']],
                            ['label' => 'PNS TNI (PNS)',             'p' => $s['angkatan_darat']['pengunjung']['pns'], 'k' => $s['angkatan_darat']['kunjungan']['pns']],
                            ['label' => 'Keluarga TNI (KEL)',        'p' => $s['angkatan_darat']['pengunjung']['kel'], 'k' => $s['angkatan_darat']['kunjungan']['kel']],
                        ];
                    @endphp
                    @foreach ($adRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-5 py-3 pl-10 text-gray-600 dark:text-gray-400 text-sm">{{ $row['label'] }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($row['p'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($row['k'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">—</td>
                            <td class="px-4 py-3 text-right text-gray-400">—</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-meta-4 font-black">
                        <td class="px-5 py-3 pl-10 text-gray-700 dark:text-white text-sm">Jumlah Angkatan Darat</td>
                        <td class="px-4 py-3 text-right text-gray-800 dark:text-white">{{ number_format($s['angkatan_darat']['pengunjung']['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800 dark:text-white">{{ number_format($s['angkatan_darat']['kunjungan']['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-amber-600">{{ number_format($s['angkatan_darat']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-violet-600">{{ number_format($s['angkatan_darat']['rawat_inap'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== ANGKATAN LAIN ===== --}}
                    <tr class="bg-cyan-500/5 dark:bg-cyan-500/10">
                        <td colspan="5" class="px-5 py-2 text-xs font-black text-cyan-600 uppercase tracking-widest">
                            Angkatan Lain (TNI AU, TNI AL)
                        </td>
                    </tr>
                    @php
                        $alRows = [
                            ['label' => 'Militer (MIL)',             'p' => $s['angkatan_lain']['pengunjung']['mil'], 'k' => $s['angkatan_lain']['kunjungan']['mil']],
                            ['label' => 'PNS TNI (PNS)',             'p' => $s['angkatan_lain']['pengunjung']['pns'], 'k' => $s['angkatan_lain']['kunjungan']['pns']],
                            ['label' => 'Keluarga TNI (KEL)',        'p' => $s['angkatan_lain']['pengunjung']['kel'], 'k' => $s['angkatan_lain']['kunjungan']['kel']],
                        ];
                    @endphp
                    @foreach ($alRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-5 py-3 pl-10 text-gray-600 dark:text-gray-400 text-sm">{{ $row['label'] }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($row['p'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($row['k'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">—</td>
                            <td class="px-4 py-3 text-right text-gray-400">—</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-meta-4 font-black">
                        <td class="px-5 py-3 pl-10 text-gray-700 dark:text-white text-sm">Jumlah Angkatan Lain</td>
                        <td class="px-4 py-3 text-right text-gray-800 dark:text-white">{{ number_format($s['angkatan_lain']['pengunjung']['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800 dark:text-white">{{ number_format($s['angkatan_lain']['kunjungan']['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-amber-600">{{ number_format($s['angkatan_lain']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-violet-600">{{ number_format($s['angkatan_lain']['rawat_inap'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== PURNAWIRAWAN ===== --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="px-5 py-3 font-bold text-gray-700 dark:text-white">Purnawirawan (PURN)</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($s['purnawirawan']['pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($s['purnawirawan']['kunjungan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-amber-600">{{ number_format($s['purnawirawan']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-violet-600">{{ number_format($s['purnawirawan']['rawat_inap'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== PASIEN UMUM ===== --}}
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="px-5 py-3 font-bold text-gray-700 dark:text-white">Pasien Umum</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($s['umum']['pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">{{ number_format($s['umum']['kunjungan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-amber-600">{{ number_format($s['umum']['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-violet-600">{{ number_format($s['umum']['rawat_inap'], 0, ',', '.') }}</td>
                    </tr>

                    {{-- ===== TOTAL ===== --}}
                    <tr class="bg-primary/10 dark:bg-primary/20 font-black text-base border-t-2 border-primary/30">
                        <td class="px-5 py-4 text-primary uppercase tracking-widest text-xs">Total Keseluruhan</td>
                        <td class="px-4 py-4 text-right text-primary text-base">{{ number_format($s['total_pengunjung'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-primary text-base">{{ number_format($s['total_kunjungan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-amber-600 text-base">{{ number_format($s['rujukan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-violet-600 text-base">{{ number_format($s['rawat_inap'], 0, ',', '.') }}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media print {
            nav, aside, header, footer { display: none !important; }
            body { background: white !important; }
        }
    </style>
</x-content>
