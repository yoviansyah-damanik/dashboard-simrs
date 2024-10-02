<div class="grid grid-cols-[1fr_auto] break-inside-avoid m-0 mb-2">
    <div>
        <div class="w-40 font-semibold">
            {{ $title }}
        </div>
        @if ($slot->isEmpty())
            {{ $value }}
        @else
            {{ $slot }}
        @endif
    </div>
</div>
