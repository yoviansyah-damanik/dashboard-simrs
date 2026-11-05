<div class="space-y-6">
    {{-- BAGIAN 1: REKAPITULASI TREN BULANAN RUMAH SAKIT --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--calendar-date-bold] size-4 text-emerald-600"></span>
                    <span>1. Matriks 12 Bulan & Pertumbuhan MoM Kumulatif Rumah Sakit (Tahun {{ $year }})</span>
                </h2>
                <p class="text-xs text-slate-500">
                    Evaluasi tren volume, pertumbuhan Month-over-Month (MoM %), serta stabilitas kepatuhan pemanfaatan antrean online sepanjang tahun
                </p>
            </div>
            <div class="text-xs text-slate-500 font-semibold">
                Target Kepatuhan: <span class="font-bold text-emerald-600 dark:text-emerald-400">&ge; {{ number_format($threshold, 1) }}%</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        <th class="py-3 px-3">Bulan</th>
                        <th class="py-3 px-3 text-right">Total Pasien</th>
                        <th class="py-3 px-3 text-center">MoM Total</th>
                        <th class="py-3 px-3 text-right text-violet-700 dark:text-violet-400">JKN (Kode B)</th>
                        <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">% JKN</th>
                        <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">MoM JKN</th>
                        <th class="py-3 px-3 text-right text-sky-700 dark:text-sky-400">Loket (A)</th>
                        <th class="py-3 px-3 text-center text-sky-700 dark:text-sky-400">% Loket</th>
                        <th class="py-3 px-3 text-right text-teal-700 dark:text-teal-400">APM (D)</th>
                        <th class="py-3 px-3 text-center text-teal-700 dark:text-teal-400">% APM</th>
                        <th class="py-3 px-3 text-right">Total Onsite</th>
                        <th class="py-3 px-3 text-center">% Onsite</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
                    @php
                        $sumTotal = 0; $sumJkn = 0; $sumLoket = 0; $sumApm = 0;
                    @endphp
                    @foreach ($monthlyReport as $m)
                        @php
                            $sumTotal += $m['total_ralan'];
                            $sumJkn += $m['jkn'];
                            $sumLoket += $m['loket'];
                            $sumApm += $m['apm'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                            <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $m['total_ralan'] > 0 ? ($m['is_patuh'] ? 'bg-emerald-500' : 'bg-rose-500') : 'bg-slate-300' }}"></span>
                                <span>{{ $m['month_name'] }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-right font-semibold">{{ number_format($m['total_ralan']) }}</td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($m['growth_total'] !== null)
                                    <span class="inline-flex items-center gap-0.5 font-bold {{ $m['growth_total'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $m['growth_total'] > 0 ? '+' : '' }}{{ $m['growth_total'] }}%
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-violet-700 dark:text-violet-400">{{ number_format($m['jkn']) }}</td>
                            <td class="py-2.5 px-3 text-center font-bold {{ $m['pct_jkn'] >= $threshold ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $m['pct_jkn'] }}%
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($m['growth_jkn'] !== null)
                                    <span class="inline-flex items-center gap-0.5 font-bold {{ $m['growth_jkn'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $m['growth_jkn'] > 0 ? '+' : '' }}{{ $m['growth_jkn'] }}%
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right text-sky-700 dark:text-sky-400">{{ number_format($m['loket']) }}</td>
                            <td class="py-2.5 px-3 text-center text-slate-400">{{ $m['pct_loket'] }}%</td>
                            <td class="py-2.5 px-3 text-right text-teal-700 dark:text-teal-400">{{ number_format($m['apm']) }}</td>
                            <td class="py-2.5 px-3 text-center text-slate-400">{{ $m['pct_apm'] }}%</td>
                            <td class="py-2.5 px-3 text-right font-semibold">{{ number_format($m['onsite']) }}</td>
                            <td class="py-2.5 px-3 text-center text-slate-400">{{ $m['pct_onsite'] }}%</td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($m['total_ralan'] > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black {{ $m['is_patuh'] ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                                        {{ $m['is_patuh'] ? 'PATUH' : 'BELUM' }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @php
                        $sumOnsite = $sumLoket + $sumApm;
                        $totPctJkn = $sumTotal > 0 ? round(($sumJkn / $sumTotal) * 100, 2) : 0.0;
                        $totPctLoket = $sumTotal > 0 ? round(($sumLoket / $sumTotal) * 100, 2) : 0.0;
                        $totPctApm = $sumTotal > 0 ? round(($sumApm / $sumTotal) * 100, 2) : 0.0;
                        $totPctOnsite = $sumTotal > 0 ? round(($sumOnsite / $sumTotal) * 100, 2) : 0.0;
                        $totPatuh = $totPctJkn >= $threshold;
                    @endphp
                    <tr class="bg-slate-100/70 dark:bg-slate-800/70 font-black text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700">
                        <td class="py-3 px-3">TOTAL KUMULATIF TAHUN {{ $year }}</td>
                        <td class="py-3 px-3 text-right">{{ number_format($sumTotal) }}</td>
                        <td class="py-3 px-3 text-center">-</td>
                        <td class="py-3 px-3 text-right text-violet-700 dark:text-violet-400">{{ number_format($sumJkn) }}</td>
                        <td class="py-3 px-3 text-center {{ $totPatuh ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $totPctJkn }}%
                        </td>
                        <td class="py-3 px-3 text-center">-</td>
                        <td class="py-3 px-3 text-right text-sky-700 dark:text-sky-400">{{ number_format($sumLoket) }}</td>
                        <td class="py-3 px-3 text-center">{{ $totPctLoket }}%</td>
                        <td class="py-3 px-3 text-right text-teal-700 dark:text-teal-400">{{ number_format($sumApm) }}</td>
                        <td class="py-3 px-3 text-center">{{ $totPctApm }}%</td>
                        <td class="py-3 px-3 text-right">{{ number_format($sumOnsite) }}</td>
                        <td class="py-3 px-3 text-center">{{ $totPctOnsite }}%</td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black {{ $totPatuh ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/20 text-rose-700 dark:text-rose-300' }}">
                                {{ $totPatuh ? 'TERCAPAI' : 'DI BAWAH TARGET' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- BAGIAN 2: MATRIKS 12 BULAN & PERTUMBUHAN MOM KUMULATIF PER POLIKLINIK & DOKTER DPJP --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60 space-y-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-200/60 dark:border-slate-800/60 pb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--buildings-2-bold] size-4 text-emerald-600"></span>
                    <span>2. Matriks 12 Bulan & Pertumbuhan MoM Kumulatif per Poliklinik & Dokter DPJP</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Data rincian kinerja bulanan (Januari s.d. Desember) dan pertumbuhan Month-over-Month (MoM %) per Poliklinik & Dokter DPJP (Tahun {{ $year }})
                </p>
            </div>

            {{-- SELECTOR PILIH POLIKLINIK & DOKTER DPJP --}}
            <div class="flex items-center gap-2 min-w-[280px]">
                <label for="selectPolyDoc" class="text-xs font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap">
                    Pilih Dokter DPJP:
                </label>
                <select id="selectPolyDoc" wire:model.live="selectedPolyDocKey"
                    class="w-full text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 text-slate-800 dark:text-slate-100 py-1.5 px-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    @foreach ($annualPolyDoc as $doc)
                        <option value="{{ $doc['key'] }}">
                            {{ $doc['nm_poli'] }} - {{ $doc['nm_dokter'] }} ({{ number_format($doc['total']) }} Pasien)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- KARTU INFO SINGKAT DOKTER TERPILIH --}}
        @if ($selectedItem)
            <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--user-bold] size-5"></span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-white">
                            {{ $selectedItem['nm_dokter'] }}
                        </div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            {{ $selectedItem['nm_poli'] }} &bull; Target Kepatuhan: &ge; {{ number_format($threshold, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div>
                        <span class="text-slate-400 text-[10px] block">Total Setahun</span>
                        <span class="text-slate-900 dark:text-white font-bold">{{ number_format($selectedItem['total']) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">JKN Setahun</span>
                        <span class="text-violet-600 dark:text-violet-400 font-bold">{{ number_format($selectedItem['jkn']) }} ({{ $selectedItem['pct_jkn'] }}%)</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Rata-rata/Bln</span>
                        <span class="text-slate-700 dark:text-slate-300 font-bold">{{ number_format($selectedItem['avg_monthly'], 1) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Status Evaluasi</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black {{ $selectedItem['is_patuh'] ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                            {{ $selectedItem['is_patuh'] ? 'TERCAPAI' : 'DI BAWAH TARGET' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- TABEL 12 BULAN & PERTUMBUHAN MOM DOKTER TERPILIH (IDENTIK DENGAN POIN 1) --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                            <th class="py-3 px-3">Bulan</th>
                            <th class="py-3 px-3 text-right">Total Pasien</th>
                            <th class="py-3 px-3 text-center">MoM Total</th>
                            <th class="py-3 px-3 text-right text-violet-700 dark:text-violet-400">JKN (Kode B)</th>
                            <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">% JKN</th>
                            <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">MoM JKN</th>
                            <th class="py-3 px-3 text-right text-sky-700 dark:text-sky-400">Loket (A)</th>
                            <th class="py-3 px-3 text-center text-sky-700 dark:text-sky-400">% Loket</th>
                            <th class="py-3 px-3 text-right text-teal-700 dark:text-teal-400">APM (D)</th>
                            <th class="py-3 px-3 text-center text-teal-700 dark:text-teal-400">% APM</th>
                            <th class="py-3 px-3 text-right">Total Onsite</th>
                            <th class="py-3 px-3 text-center">% Onsite</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
                        @php
                            $sumDocTotal = 0; $sumDocJkn = 0; $sumDocLoket = 0; $sumDocApm = 0;
                        @endphp
                        @foreach ($selectedItem['monthly'] as $m)
                            @php
                                $sumDocTotal += $m['total'];
                                $sumDocJkn += $m['jkn'];
                                $sumDocLoket += $m['loket'];
                                $sumDocApm += $m['apm'];
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                                <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $m['total'] > 0 ? ($m['is_patuh'] ? 'bg-emerald-500' : 'bg-rose-500') : 'bg-slate-300' }}"></span>
                                    <span>{{ $m['month_name'] }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-semibold">{{ number_format($m['total']) }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if ($m['growth_total'] !== null)
                                        <span class="inline-flex items-center gap-0.5 font-bold {{ $m['growth_total'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ $m['growth_total'] > 0 ? '+' : '' }}{{ $m['growth_total'] }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-violet-700 dark:text-violet-400">{{ number_format($m['jkn']) }}</td>
                                <td class="py-2.5 px-3 text-center font-bold {{ $m['pct_jkn'] >= $threshold ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $m['pct_jkn'] }}%
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if ($m['growth_jkn'] !== null)
                                        <span class="inline-flex items-center gap-0.5 font-bold {{ $m['growth_jkn'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ $m['growth_jkn'] > 0 ? '+' : '' }}{{ $m['growth_jkn'] }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-right text-sky-700 dark:text-sky-400">{{ number_format($m['loket']) }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-400">{{ $m['pct_loket'] }}%</td>
                                <td class="py-2.5 px-3 text-right text-teal-700 dark:text-teal-400">{{ number_format($m['apm']) }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-400">{{ $m['pct_apm'] }}%</td>
                                <td class="py-2.5 px-3 text-right font-semibold">{{ number_format($m['onsite']) }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-400">{{ $m['pct_onsite'] }}%</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if ($m['total'] > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black {{ $m['is_patuh'] ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                                            {{ $m['is_patuh'] ? 'PATUH' : 'BELUM' }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600 text-[10px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @php
                            $sumDocOnsite = $sumDocLoket + $sumDocApm;
                            $totDocPctJkn = $sumDocTotal > 0 ? round(($sumDocJkn / $sumDocTotal) * 100, 2) : 0.0;
                            $totDocPctLoket = $sumDocTotal > 0 ? round(($sumDocLoket / $sumDocTotal) * 100, 2) : 0.0;
                            $totDocPctApm = $sumDocTotal > 0 ? round(($sumDocApm / $sumDocTotal) * 100, 2) : 0.0;
                            $totDocPctOnsite = $sumDocTotal > 0 ? round(($sumDocOnsite / $sumDocTotal) * 100, 2) : 0.0;
                            $totDocPatuh = $totDocPctJkn >= $threshold;
                        @endphp
                        <tr class="bg-slate-100/70 dark:bg-slate-800/70 font-black text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700">
                            <td class="py-3 px-3">TOTAL KUMULATIF TAHUN {{ $year }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($sumDocTotal) }}</td>
                            <td class="py-3 px-3 text-center">-</td>
                            <td class="py-3 px-3 text-right text-violet-700 dark:text-violet-400">{{ number_format($sumDocJkn) }}</td>
                            <td class="py-3 px-3 text-center {{ $totDocPatuh ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $totDocPctJkn }}%
                            </td>
                            <td class="py-3 px-3 text-center">-</td>
                            <td class="py-3 px-3 text-right text-sky-700 dark:text-sky-400">{{ number_format($sumDocLoket) }}</td>
                            <td class="py-3 px-3 text-center">{{ $totDocPctLoket }}%</td>
                            <td class="py-3 px-3 text-right text-teal-700 dark:text-teal-400">{{ number_format($sumDocApm) }}</td>
                            <td class="py-3 px-3 text-center">{{ $totDocPctApm }}%</td>
                            <td class="py-3 px-3 text-right">{{ number_format($sumDocOnsite) }}</td>
                            <td class="py-3 px-3 text-center">{{ $totDocPctOnsite }}%</td>
                            <td class="py-3 px-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black {{ $totDocPatuh ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/20 text-rose-700 dark:text-rose-300' }}">
                                    {{ $totDocPatuh ? 'TERCAPAI' : 'DI BAWAH TARGET' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- TABEL DAFTAR SEMUA POLIKLINIK & DOKTER DPJP (NAVIGASI CEPAT) --}}
        <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800/60 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                    Daftar Rekapitulasi Kumulatif Seluruh Unit Pelayanan (Klik baris untuk melihat matriks bulanan):
                </span>
                <span class="text-[11px] text-slate-400 font-semibold">
                    Total: {{ count($annualPolyDoc) }} dokter DPJP
                </span>
            </div>

            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">Poliklinik</th>
                            <th class="py-2.5 px-3">Dokter Penanggung Jawab</th>
                            <th class="py-2.5 px-3 text-right">Total Setahun</th>
                            <th class="py-2.5 px-3 text-right text-violet-700 dark:text-violet-400">JKN (B)</th>
                            <th class="py-2.5 px-3 text-center text-violet-700 dark:text-violet-400">% JKN</th>
                            <th class="py-2.5 px-3 text-right text-sky-700 dark:text-sky-400">Loket (A)</th>
                            <th class="py-2.5 px-3 text-right text-teal-700 dark:text-teal-400">APM (D)</th>
                            <th class="py-2.5 px-3 text-right">Onsite</th>
                            <th class="py-2.5 px-3 text-center">Rata2/Bln</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
                        @foreach ($annualPolyDoc as $i => $row)
                            @php
                                $isSelected = $selectedPolyDocKey === $row['key'];
                            @endphp
                            <tr wire:click="selectPolyDoc('{{ $row['key'] }}')"
                                class="cursor-pointer transition {{ $isSelected ? 'bg-emerald-500/10 dark:bg-emerald-950/30 font-bold border-l-4 border-emerald-500' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                                <td class="py-2 px-3 text-slate-400">{{ $i + 1 }}</td>
                                <td class="py-2 px-3">{{ $row['nm_poli'] }}</td>
                                <td class="py-2 px-3 {{ $isSelected ? 'text-emerald-700 dark:text-emerald-400' : '' }}">{{ $row['nm_dokter'] }}</td>
                                <td class="py-2 px-3 text-right font-semibold">{{ number_format($row['total']) }}</td>
                                <td class="py-2 px-3 text-right text-violet-700 dark:text-violet-400 font-bold">{{ number_format($row['jkn']) }}</td>
                                <td class="py-2 px-3 text-center font-bold {{ $row['is_patuh'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $row['pct_jkn'] }}%
                                </td>
                                <td class="py-2 px-3 text-right text-sky-700 dark:text-sky-400">{{ number_format($row['loket']) }}</td>
                                <td class="py-2 px-3 text-right text-teal-700 dark:text-teal-400">{{ number_format($row['apm']) }}</td>
                                <td class="py-2 px-3 text-right">{{ number_format($row['onsite']) }}</td>
                                <td class="py-2 px-3 text-center text-slate-600 dark:text-slate-300">{{ number_format($row['avg_monthly'], 1) }}</td>
                                <td class="py-2 px-3 text-center">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black {{ $row['is_patuh'] ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                                        {{ $row['is_patuh'] ? 'PATUH' : 'BELUM' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" wire:click.stop="selectPolyDoc('{{ $row['key'] }}')"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded transition cursor-pointer {{ $isSelected ? 'bg-emerald-600 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <span class="icon-[solar--chart-line-up-bold] size-2.5"></span>
                                        <span>{{ $isSelected ? 'Aktif' : 'Lihat Matriks' }}</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
