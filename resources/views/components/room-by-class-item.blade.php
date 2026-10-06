@props([
    'title',
    'isActive' => false,
    'total' => 0,
    'available' => 0,
    'filled' => 0,
])

@php
    $percentage = $total > 0 ? round(($filled / $total) * 100, 0) : 0;
    $normalized = strtoupper(trim($title));

    // Pemilihan ikon & nuansa tematik sesuai kategori kelas kamar
    if (str_contains($normalized, 'VVIP') || str_contains($normalized, 'VIP')) {
        $iconClass = 'icon-[solar--crown-star-bold-duotone]';
        $iconColor = 'text-amber-500 bg-amber-500/10 dark:bg-amber-500/15 border border-amber-500/20';
        $glowGradient = 'from-amber-500 to-amber-600 shadow-amber-500/25';
    } elseif (str_contains($normalized, 'KELAS 1') || str_contains($normalized, 'KELAS I') || str_contains($normalized, 'KL 1')) {
        $iconClass = 'icon-[solar--medal-star-circle-bold-duotone]';
        $iconColor = 'text-sky-500 bg-sky-500/10 dark:bg-sky-500/15 border border-sky-500/20';
        $glowGradient = 'from-sky-500 to-blue-600 shadow-sky-500/25';
    } elseif (str_contains($normalized, 'KELAS 2') || str_contains($normalized, 'KELAS II') || str_contains($normalized, 'KL 2')) {
        $iconClass = 'icon-[solar--bedside-table-2-bold-duotone]';
        $iconColor = 'text-teal-500 bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/20';
        $glowGradient = 'from-teal-500 to-emerald-600 shadow-teal-500/25';
    } elseif (str_contains($normalized, 'KELAS 3') || str_contains($normalized, 'KELAS III') || str_contains($normalized, 'KL 3')) {
        $iconClass = 'icon-[solar--bed-bold-duotone]';
        $iconColor = 'text-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20';
        $glowGradient = 'from-emerald-500 to-teal-600 shadow-emerald-500/25';
    } elseif (str_contains($normalized, 'ICU') || str_contains($normalized, 'ICCU') || str_contains($normalized, 'NICU') || str_contains($normalized, 'PICU')) {
        $iconClass = 'icon-[solar--heart-pulse-bold-duotone]';
        $iconColor = 'text-rose-500 bg-rose-500/10 dark:bg-rose-500/15 border border-rose-500/20';
        $glowGradient = 'from-rose-500 to-red-600 shadow-rose-500/25';
    } elseif (str_contains($normalized, 'HCU')) {
        $iconClass = 'icon-[solar--pulse-2-bold-duotone]';
        $iconColor = 'text-orange-500 bg-orange-500/10 dark:bg-orange-500/15 border border-orange-500/20';
        $glowGradient = 'from-orange-500 to-amber-600 shadow-orange-500/25';
    } elseif (str_contains($normalized, 'ISOLASI')) {
        $iconClass = 'icon-[solar--shield-cross-bold-duotone]';
        $iconColor = 'text-violet-500 bg-violet-500/10 dark:bg-violet-500/15 border border-violet-500/20';
        $glowGradient = 'from-violet-500 to-purple-600 shadow-violet-500/25';
    } else {
        $iconClass = 'icon-[solar--hospital-bold-duotone]';
        $iconColor = 'text-indigo-500 bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20';
        $glowGradient = 'from-indigo-500 to-blue-600 shadow-indigo-500/25';
    }
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

