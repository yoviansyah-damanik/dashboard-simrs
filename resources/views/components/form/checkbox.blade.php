<div x-data="{
    id: $id('{{ $attributes->whereStartsWith('wire:model')->first() }}'),
}" class="{{ $wrapClass }}">
    <input type="checkbox" class="{{ $baseClass }}" :id="id"
        {{ $attributes->whereStartsWith('wire:model') }} wire:loading.attr='disabled' @required($required)
        @disabled($loading) />
    <label :for="id" class="{{ $labelClass }}">
        {{ $label }} </label>
    @if ($error)
        <div class="{{ $errorClass }}">
            {{ $error }}
        </div>
    @enderror
</div>
