<div 
    x-data="chartComponent(@js($chartId), @js($chartType), @js($barType ?? 'x'), @js($labels ?? []), @js($datasets ?? []))"
    x-ref="chartContainer"
    wire:ignore
    class="w-full h-full"
>
    <canvas class="w-full h-full" id="{{ $chartId }}"></canvas>
</div>
