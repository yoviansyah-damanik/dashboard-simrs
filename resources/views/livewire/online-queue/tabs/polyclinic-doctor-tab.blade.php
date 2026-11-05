<div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>Distribusi Kanal Pendaftaran & Pertumbuhan MoM per Poliklinik & Dokter</span>
            </h2>
            <p class="text-xs text-slate-500">
                Evaluasi adopsi antrean online serta tren pertumbuhan Month-over-Month (MoM %) dibandingkan {{ $polyclinicRows[0]['prev_period_label'] ?? 'bulan sebelumnya' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                <span class="icon-[solar--chart-square-bold] size-3.5"></span>
                <span>Basis MoM: {{ $polyclinicRows[0]['prev_period_label'] ?? '-' }}</span>
            </span>
            <span class="text-xs font-semibold text-slate-400">
                {{ count($polyclinicRows) }} unit pelayanan
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                    <th class="py-3 px-3">No</th>
                    <th class="py-3 px-3">Poliklinik</th>
                    <th class="py-3 px-3">Dokter Penanggung Jawab</th>
                    <th class="py-3 px-3 text-right">Total Pasien</th>
                    <th class="py-3 px-3 text-center">MoM Total</th>
                    <th class="py-3 px-3 text-right text-violet-700 dark:text-violet-400">JKN (Kode B)</th>
                    <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">% JKN</th>
                    <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">MoM JKN</th>
                    <th class="py-3 px-3 text-center text-violet-700 dark:text-violet-400">&Delta; % JKN</th>
                    <th class="py-3 px-3 text-right text-sky-700 dark:text-sky-400">Loket (A)</th>
                    <th class="py-3 px-3 text-right text-teal-700 dark:text-teal-400">APM (D)</th>
                    <th class="py-3 px-3 text-right">Onsite Total</th>
                    <th class="py-3 px-3 text-center">% Onsite</th>
                    <th class="py-3 px-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
                @forelse ($polyclinicRows as $i => $row)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                        <td class="py-2.5 px-3 text-slate-400">{{ $i + 1 }}</td>
                        <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white">{{ $row['nm_poli'] }}</td>
                        <td class="py-2.5 px-3 text-slate-600 dark:text-slate-300">{{ $row['nm_dokter'] }}</td>
                        <td class="py-2.5 px-3 text-right font-semibold">{{ number_format($row['total']) }}</td>
                        
                        {{-- MoM Total --}}
                        <td class="py-2.5 px-3 text-center">
                            @if ($row['growth_total'] !== null)
                                <span class="inline-flex items-center gap-0.5 font-bold {{ $row['growth_total'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    @if ($row['growth_total'] > 0)
                                        <span class="icon-[solar--arrow-right-up-bold] size-3"></span>+{{ $row['growth_total'] }}%
                                    @elseif ($row['growth_total'] < 0)
                                        <span class="icon-[solar--arrow-right-down-bold] size-3"></span>{{ $row['growth_total'] }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        {{-- JKN Count & Pct --}}
                        <td class="py-2.5 px-3 text-right font-bold text-violet-700 dark:text-violet-400">
                            {{ number_format($row['jkn']) }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-bold {{ $row['is_patuh'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $row['pct_jkn'] }}%
                        </td>

                        {{-- MoM JKN --}}
                        <td class="py-2.5 px-3 text-center">
                            @if ($row['growth_jkn'] !== null)
                                <span class="inline-flex items-center gap-0.5 font-bold {{ $row['growth_jkn'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    @if ($row['growth_jkn'] > 0)
                                        <span class="icon-[solar--arrow-right-up-bold] size-3"></span>+{{ $row['growth_jkn'] }}%
                                    @elseif ($row['growth_jkn'] < 0)
                                        <span class="icon-[solar--arrow-right-down-bold] size-3"></span>{{ $row['growth_jkn'] }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        {{-- Delta Poin % JKN --}}
                        <td class="py-2.5 px-3 text-center">
                            @if ($row['delta_pct_jkn'] !== null)
                                <span class="font-bold text-[11px] {{ $row['delta_pct_jkn'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $row['delta_pct_jkn'] > 0 ? '+' : '' }}{{ $row['delta_pct_jkn'] }}%
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="py-2.5 px-3 text-right text-sky-700 dark:text-sky-400">
                            {{ number_format($row['loket']) }}
                        </td>
                        <td class="py-2.5 px-3 text-right text-teal-700 dark:text-teal-400">
                            {{ number_format($row['apm']) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-semibold">{{ number_format($row['onsite']) }}</td>
                        <td class="py-2.5 px-3 text-center text-slate-400">{{ $row['pct_onsite'] }}%</td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black {{ $row['is_patuh'] ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                                {{ $row['is_patuh'] ? 'PATUH' : 'BELUM' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="py-8 text-center text-slate-400">
                            Tidak ada data untuk poliklinik pada periode terpilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
