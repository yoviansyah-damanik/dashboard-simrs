<div x-data='{ id: $id("{{ $attributes->whereStartsWith('wire:model')->first() }}") }'>
    @if ($label)
        <label class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <div :id="id">
        @foreach ($items as $idx => $item)
            <label class="{{ $wrapClass }}"
                for="{{ $attributes->whereStartsWith('wire:model')->first() . '-item-' . $idx + 1 }}">
                <input id="{{ $attributes->whereStartsWith('wire:model')->first() . '-item-' . $idx + 1 }}"
                    {{ $attributes->whereStartsWith('wire:model') }}
                    name="{{ $attributes->whereStartsWith('wire:model')->first() }}" type="radio"
                    class="{{ $baseClass }}" wire:loading.attr='disabled' @required($required)
                    @disabled($loading) value="{{ $item['value'] }}" />
                {{ $item['label'] }}
            </label>
        @endforeach
        @if ($error)
            <div class="{{ $errorClass }}">
                {{ $error }}
            </div>
        @enderror
</div>
</div>
