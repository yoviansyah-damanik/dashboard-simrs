<div x-data='{ id: $id("{{ $attributes->whereStartsWith('wire:model')->first() }}")}'>
    @if ($label)
        <label :for="id" class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <select :id="id" class="{{ $baseClass }}" {{ $attributes->whereStartsWith('wire:model') }}
        {{ $attributes }} wire:loading.attr='disabled' @required($required) @disabled($loading)>
        @foreach ($items as $item)
            @if (!is_array($item))
                <option value="{{ $item }}">{{ $item }}</option>
            @else
                <option value="{{ $item['value'] }}">{{ $item['title'] }}</option>
            @endif
        @endforeach
    </select>
    @if ($error)
        <div class="{{ $errorClass }}">
            {{ $error }}
        </div>
    @else
        @if ($info)
            <div class="mt-1 text-sm text-gray-700 ms-4">
                {{ $info }}
            </div>
        @endif
    @enderror
</div>
