<div x-show="loaded" x-init="window.addEventListener('livewire:navigated', () => setTimeout(() => loaded = false, 500));
window.addEventListener('livewire:navigate', () => loaded = true);"
    class="fixed top-0 left-0 flex items-center justify-center w-screen h-screen backdrop-blur z-999999 " x-transition
    x-transition.opacity x-transition.scale.origin.left.right x-transition.duration.500ms>
    <div class="w-16 h-16 border-4 border-solid rounded-full animate-spin border-primary border-t-transparent"></div>
</div>
