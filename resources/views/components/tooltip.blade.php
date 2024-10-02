<div class="{{ $base }}" x-data="{ id: $id('tooltip') }">
    {{ $slot }}
    <div class="{{ $tooltipClass }}">
        {{ $title }}
    </div>
</div>
