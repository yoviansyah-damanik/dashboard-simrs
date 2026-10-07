{{-- Tombol Logout Proporsional untuk Dropdown Header --}}
<div>
    <button type="button" wire:click="logout" wire:loading.attr="disabled"
        class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-500/10 hover:bg-rose-600 hover:text-white dark:bg-rose-500/15 dark:hover:bg-rose-600 dark:hover:text-white active:scale-[0.98] transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:pointer-events-none group"
        title="Log Out">
        <span class="icon-[solar--logout-2-bold-duotone] text-sm group-hover:-translate-x-0.5 transition-transform duration-200"></span>
        <span wire:loading.remove wire:target="logout">Log Out</span>
        <span wire:loading wire:target="logout" class="flex items-center gap-1.5">
            <span class="icon-[solar--refresh-bold-duotone] animate-spin text-xs"></span>
            <span>Memproses...</span>
        </span>
    </button>
</div>
