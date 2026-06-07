<div class="p-6 space-y-4 sm:p-8 text-center">
    <div
        class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center
            {{ $isActive ? 'bg-amber-500/10 text-amber-600' : 'bg-emerald-500/10 text-emerald-600' }}">
        <span
            class="text-3xl {{ $isActive ? 'icon-[solar--lock-bold-duotone]' : 'icon-[solar--lock-unlocked-bold-duotone]' }}"></span>
    </div>
    <p class="text-sm text-gray-700 dark:text-gray-300">
        Apakah Anda yakin ingin {{ $isActive ? 'menonaktifkan' : 'mengaktifkan' }} akun
        <span class="font-black">{{ $name }}</span>?
        @if ($isActive)
            Pengguna yang dinonaktifkan tidak akan dapat masuk ke sistem.
        @endif
    </p>
    <div class="flex items-center justify-center gap-3">
        <x-button color="primary-transparent" x-on:click="$dispatch('toggle-activation-user-modal')">Batal</x-button>
        <x-button :color="$isActive ? 'yellow' : 'green'" wire:click="toggle">
            {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
        </x-button>
    </div>
</div>
