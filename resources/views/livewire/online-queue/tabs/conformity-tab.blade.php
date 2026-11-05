<div
    class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60 space-y-4">
    {{-- Header & Sub-filter Kesesuaian --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                Audit Kesesuaian Antrean JKN terhadap SEP & Surat Kontrol / Rujukan
            </h2>
            <p class="text-xs text-slate-500">
                Memastikan nomor referensi booking Mobile JKN sesuai dengan SKDP (pasien kontrol) atau nomor rujukan
                FKTP/RS
            </p>
        </div>

        {{-- Filter Status Sesuai / Tidak --}}
        <div
            class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            <button wire:click="setFilter('')"
                class="px-2.5 py-1 text-xs font-bold rounded-lg transition {{ $filterStatus === '' ? 'bg-white shadow text-slate-900 dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">
                Semua ({{ number_format($summary['conformity']['total']) }})
            </button>
            <button wire:click="setFilter('sesuai')"
                class="px-2.5 py-1 text-xs font-bold rounded-lg transition {{ $filterStatus === 'sesuai' ? 'bg-emerald-500 text-white shadow' : 'text-slate-500 hover:text-emerald-600 dark:text-slate-400' }}">
                Sesuai ({{ number_format($summary['conformity']['sesuai']) }})
            </button>
            <button wire:click="setFilter('tidak')"
                class="px-2.5 py-1 text-xs font-bold rounded-lg transition {{ $filterStatus === 'tidak' ? 'bg-rose-500 text-white shadow' : 'text-slate-500 hover:text-rose-600 dark:text-slate-400' }}">
                Tidak Sesuai ({{ number_format($summary['conformity']['tidak']) }})
            </button>
        </div>
    </div>

    {{-- Ringkasan Indikator Kesesuaian --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-2">
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60">
            <span class="text-[10px] uppercase font-bold text-slate-400">Total Booking JKN</span>
            <div class="text-lg font-black text-slate-900 dark:text-white">
                {{ number_format($summary['conformity']['total']) }}</div>
        </div>
        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
            <span class="text-[10px] uppercase font-bold text-emerald-800 dark:text-emerald-300">Tingkat
                Kesesuaian</span>
            <div class="text-lg font-black text-emerald-600 dark:text-emerald-400">
                {{ $summary['conformity']['pct_sesuai'] }}%</div>
        </div>
        <div class="p-3 rounded-xl bg-sky-500/10 border border-sky-500/20">
            <span class="text-[10px] uppercase font-bold text-sky-800 dark:text-sky-300">Validitas SEP Terbit</span>
            <div class="text-lg font-black text-sky-600 dark:text-sky-400">
                {{ $summary['conformity']['pct_valid_sep'] }}%</div>
        </div>
        <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20">
            <span class="text-[10px] uppercase font-bold text-rose-800 dark:text-rose-300">Perlu Penyesuaian</span>
            <div class="text-lg font-black text-rose-600 dark:text-rose-400">
                {{ number_format($summary['conformity']['tidak']) }}</div>
        </div>
    </div>

    {{-- Tabel Audit Kesesuaian --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                    <th class="py-3 px-3">No. Booking</th>
                    <th class="py-3 px-3">Tgl Registrasi</th>
                    <th class="py-3 px-3">Poliklinik</th>
                    <th class="py-3 px-3">Jenis Kunjungan</th>
                    <th class="py-3 px-3">No. Referensi Antrean</th>
                    <th class="py-3 px-3">No. SEP</th>
                    <th class="py-3 px-3">No. SKDP / Rujukan SEP</th>
                    <th class="py-3 px-3 text-center">Hasil Audit</th>
                </tr>
            </thead>
            <tbody
                class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
                @forelse ($conformityRows as $row)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                        <td class="py-2.5 px-3 font-mono text-[11px] font-bold text-slate-900 dark:text-white">
                            {{ $row['no_rawat'] }}</td>
                        <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">{{ $row['tgl_registrasi'] }}</td>
                        <td class="py-2.5 px-3 font-semibold">{{ $row['nm_poli'] }}</td>
                        <td class="py-2.5 px-3">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains(strtolower($row['jenis_kunjungan']), 'kontrol') ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300' : 'bg-blue-500/15 text-blue-700 dark:text-blue-300' }}">
                                {{ $row['jenis_kunjungan'] }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 font-mono text-[11px] text-slate-800 dark:text-slate-200 font-semibold">
                            {{ $row['no_referensi'] }}</td>
                        <td class="py-2.5 px-3 font-mono text-[11px] text-slate-600 dark:text-slate-300">
                            {{ $row['no_sep'] }}</td>
                        <td class="py-2.5 px-3 font-mono text-[11px]">
                            @if ($row['noskdp'] !== '-')
                                <div class="text-amber-700 dark:text-amber-400 font-semibold">SKDP:
                                    {{ $row['noskdp'] }}</div>
                            @endif
                            @if ($row['no_rujukan'] !== '-')
                                <div class="text-sky-700 dark:text-sky-400">Ruj: {{ $row['no_rujukan'] }}</div>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @if ($row['is_sesuai'])
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    SESUAI
                                </span>
                            @else
                                <div class="inline-flex flex-col items-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-700 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        TIDAK SESUAI
                                    </span>
                                    @foreach ($row['alasan'] as $als)
                                        <span
                                            class="text-[9px] text-rose-600 font-semibold mt-0.5">{{ $als }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">
                            Tidak ada data antrean JKN yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginasi Kesesuaian --}}
    <div
        class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500">
        <div>
            Menampilkan halaman <span class="font-bold text-slate-800 dark:text-white">{{ $page }}</span> dari
            <span class="font-bold text-slate-800 dark:text-white">{{ $lastPage }}</span> (Total
            {{ number_format($totalConformity) }} data)
        </div>
        <div class="flex items-center gap-1.5">
            <button wire:click="prevPage" @disabled($page <= 1)
                class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-40 cursor-pointer">
                Sebelumnya
            </button>
            <button wire:click="nextPage" @disabled($page >= $lastPage)
                class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-40 cursor-pointer">
                Berikutnya
            </button>
        </div>
    </div>
</div>
