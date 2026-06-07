<div class="p-6 space-y-4 sm:p-8 text-center">
    <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-600">
        <span class="icon-[solar--trash-bin-trash-bold-duotone] text-3xl"></span>
    </div>
    <p class="text-sm text-gray-700 dark:text-gray-300">
        Apakah Anda yakin ingin menghapus pengguna <span class="font-black">{{ $name }}</span>?
        Tindakan ini tidak dapat dibatalkan.
    </p>
    <div class="flex items-center justify-center gap-3">
        <x-button color="primary-transparent" x-on:click="$dispatch('toggle-delete-user-modal')">Batal</x-button>
        <x-button color="red" wire:click="delete">Hapus</x-button>
    </div>
</div>
