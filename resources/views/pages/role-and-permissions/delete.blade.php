<div class="p-6 space-y-4 sm:p-8 text-center">
    <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-600">
        <span class="icon-[solar--shield-cross-bold-duotone] text-3xl"></span>
    </div>
    <p class="text-sm text-gray-700 dark:text-gray-300">
        Apakah Anda yakin ingin menghapus peran <span class="font-black">{{ $name }}</span>?
        @if ($usersCount > 0)
            Peran ini masih digunakan oleh <span class="font-black">{{ $usersCount }}</span> pengguna.
        @endif
        Tindakan ini tidak dapat dibatalkan.
    </p>
    <div class="flex items-center justify-center gap-3">
        <x-button color="primary-transparent" x-on:click="$dispatch('toggle-delete-role-modal')">Batal</x-button>
        <x-button color="red" wire:click="delete">Hapus</x-button>
    </div>
</div>
