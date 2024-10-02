<div x-data='{
        id: $id("{{ $attributes->whereStartsWith('wire:model')->first() }}"),
        uploading: false,
        progress: 0
    }'
    x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false"
    x-on:livewire-upload-cancel="uploading = false" x-on:livewire-upload-error="uploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress">
    @if ($label)
        <label :for="id" class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <div class="relative">
        <input :id="id" class="{{ $baseClass }}" {{ $attributes->whereStartsWith('wire:model') }}
            {{ $attributes->merge(['placeholder' => $placeholder]) }} type="file" placeholder=""
            wire:loading.attr='disabled' @required($required) @disabled($loading) />
    </div>
    <div x-show="uploading">
        <progress max="100" x-bind:value="progress"></progress>
    </div>
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
