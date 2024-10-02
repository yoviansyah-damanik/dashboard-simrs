@if ($href)
    <a type="button" href="{{ $href }}" class="{{ $baseClass }}" {{ $attributes }}
        @disabled($loading) wire:loading.attr='disabled' wire:navigate>
        @if ($icon)
            <div @class([
                'flex items-center gap-3',
                'justify-center' => $slot->isEmpty(),
            ])>
                <span class="{{ $iconClass }}"></span>
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button class="{{ $baseClass }}" {{ $attributes }} @disabled($loading) wire:loading.attr='disabled'>
        @if ($icon)
            <div @class([
                'flex items-center gap-3',
                'justify-center' => $slot->isEmpty(),
            ])>
                <span class="{{ $iconClass }}"></span>
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
