<div x-data='{
        id: $id("{{ $attributes->whereStartsWith('wire:model')->first() }}"),
        limit: {{ $limit }},
        content: "",
        isMax: false,
        get count() {
            count = this.content.length
            if(count > this.limit){
                this.content = this.content.substring(0, this.limit)
                $wire.{{ $attributes->whereStartsWith('wire:model')->first() }} = this.content
                this.isMax = true
                return this.limit
            }
            this.isMax = false
            return count
        }
    }'
    :data-id="id">
    @if ($label)
        <label :for="id" class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <div class="relative">
        <textarea x-model="content" :id="id" class="{{ $baseClass }}"
            {{ $attributes->whereStartsWith('wire:model') }}
            {{ $attributes->merge(['placeholder' => $placeholder, 'rows' => $rows]) }} placeholder="" rows="4"
            wire:loading.attr='disabled' @required($required) @disabled($loading)></textarea>
        @if ($limit)
            <div class='absolute bottom-0 right-0 p-3 text-sm text-gray-700 pointer-events-none'
                :class="isMax ? 'text-red-700' : ''">
                <span x-text="count"></span>/<span x-text="limit"></span>
            </div>
        @endif
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
