@props(['label', 'value', 'icon'])

<div class="flex items-start gap-3">
    <div class="mt-0.5 text-slate-400">
        <i class="{{ $icon }}"></i>
    </div>
    <div>
        <p class="text-[10px] uppercase font-bold text-slate-400 leading-none mb-1">{{ $label }}</p>
        <p class="text-sm font-semibold text-slate-700">{{ $value ?: '-' }}</p>
    </div>
</div>
