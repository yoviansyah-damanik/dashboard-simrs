@props([
    'title',
    'isActive' => false,
    'total' => 0,
    'available' => 0,
    'filled' => 0,
])

@php
    $percentage = $total > 0 ? round(($filled / $total) * 100, 0) : 0;
    $meta = \App\Helpers\StatusHelper::getRoomClassMeta($title);
    $iconClass = $meta['icon'];
    $iconColor = $meta['iconColor'];
    $glowGradient = $meta['glowGradient'];
@endphp

<div @class([
    'relative w-full rounded-2xl p-4 sm:p-5 transition-all duration-300 cursor-pointer overflow-hidden border select-none group',
    'bg-white dark:bg-slate-900 shadow-sm hover:shadow-xl hover:-translate-y-0.5',
    'border-emerald-500 ring-2 ring-emerald-500/30 dark:ring-emerald-400/30 shadow-emerald-500/10' => $isActive,
    'border-slate-200/80 dark:border-slate-800/80 hover:border-emerald-500/40' => !$isActive,
]) wire:click="setShow('{{ $title }}')">

    {{-- Ambient Glow / Accent Background Saat Aktif --}}
    @if ($isActive)
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/15 dark:bg-emerald-500/20 rounded-full blur-2xl pointer-events-none"></div>
    @endif

    {{-- Header Kartu Kelas --}}
    <div class="flex items-start justify-between gap-3 mb-3.5">
        <div class="flex items-center gap-3 min-w-0">
            <div @class([
                'w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center shrink-0 transition-all duration-300',
                'bg-gradient-to-br text-white shadow-lg ' . $glowGradient => $isActive,
                $iconColor => !$isActive,
            ])>
                <span class="{{ $iconClass }} text-xl sm:text-2xl"></span>
            </div>
            <div class="min-w-0">
                <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight truncate">
                    {{ $title }}
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-semibold">
                    Kapasitas: <span class="font-bold text-slate-700 dark:text-slate-200">{{ number_format($total) }} Bed</span>
                </p>
            </div>
        </div>

        @if ($isActive)
            <span class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Dipilih</span>
            </span>
        @endif
    </div>

    {{-- Progress Bar Okupansi Bed --}}
    <div class="mb-3.5">
        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
            <span>Okupansi</span>
            <span class="font-bold font-mono text-slate-700 dark:text-slate-300">{{ $percentage }}%</span>
        </div>
        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden flex">
            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width: {{ $percentage }}%"></div>
        </div>
    </div>

    {{-- Mini Grid Status (Tersedia & Terisi) --}}
    <div class="grid grid-cols-2 gap-2 pt-1">
        <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 text-center">
            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Tersedia</span>
            <span class="text-base sm:text-xl font-black font-mono text-emerald-600 dark:text-emerald-400 leading-tight">
                {{ number_format($available) }}
            </span>
        </div>
        <div class="p-2 sm:p-2.5 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20 text-center">
            <span class="text-[10px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider block">Terisi</span>
            <span class="text-base sm:text-xl font-black font-mono text-indigo-600 dark:text-indigo-400 leading-tight">
                {{ number_format($filled) }}
            </span>
        </div>
    </div>

    {{-- Aksi Buka Modal Hint --}}
    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
        <span>Buka detail kamar</span>
        <span class="icon-[solar--maximize-square-minimalistic-bold-duotone] text-sm group-hover:scale-110 transition-transform"></span>
    </div>
</div>

