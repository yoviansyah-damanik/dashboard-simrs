<div x-data='{
    id: $id("{{ $attributes->whereStartsWith('wire:model')->first() }}"),
    content: "",
    get result() {
        $wire.{{ $attributes->whereStartsWith('wire:model')->first() }} = this.content
        return content
    }
}'
    x-on:clear-textarea.window = "content = ''"
    x-on:set-{{ $attributes->whereStartsWith('wire:model')->first() }}-textarea-value.window = "content = $event.detail[0]">
    @if ($label)
        <label :for="id" class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <div class="relative" wire:ignore>
        <input type="hidden" x-text="result" :id="id" />
        <trix-editor class="{{ $baseClass }}" x-model="content" :input="id" wire:loading.attr='disabled'
            @required($required) @disabled($loading)></trix-editor>
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
