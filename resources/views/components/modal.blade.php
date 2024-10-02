<div x-data="{
    showModal: false,
    closeModal() {
        this.showModal = false;
        $wire.dispatch('clearModal');
        document.body.classList.remove('overlay');
    }
}"
    x-on:toggle-{{ $name }}.window="showModal = !showModal; document.body.classList.toggle('overlay');"
    id="{{ $name }}" data-modal="{{ $name }}" role="modal" {{ $attributes }}>
    <div class="{{ $backdropClass }}" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" x-show="showModal">
        <div class="{{ $modalClass }}" x-on:click.outside="closeModal">
            <x-modal.header :title="$modalTitle" />
            {{ $slot }}
        </div>
    </div>
</div>
