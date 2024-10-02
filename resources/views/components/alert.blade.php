<div :id="id" x-data="{ id: $id('alert'), open: $wire.showAlert.length ? $wire.entangle('showAlert').live : true }" x-show="open" x-transition.scale x-transition.duration.500ms
    class="{{ $baseClass }}" role="alert">
    <div class="flex items-start gap-3">
        <span class="{{ $iconClass }}"></span>
        <div class="flex-1 text-base">
            {{ $slot }}
        </div>
        @if ($closeButton)
            <button x-on:click="open = !open" class="text-base text-inherit">
                <span class="i-ph-x-bold size-4"></span>
            </button>
        @endif
    </div>
</div>
