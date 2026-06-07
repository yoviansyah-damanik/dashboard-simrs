<div class="p-6 space-y-3 sm:p-8 sm:space-y-4">
    <p class="text-sm text-gray-700 dark:text-gray-300">
        Mengatur ulang kata sandi untuk pengguna <span class="font-black">{{ $name }}</span>.
        Pengguna akan menggunakan kata sandi baru ini untuk masuk ke sistem.
    </p>

    <x-form.input type="password" wire:model.lazy="newPassword" :error="$errors->first('newPassword')" block
        label="Kata Sandi Baru" />
    <x-form.input type="password" wire:model.lazy="rePassword" :error="$errors->first('rePassword')" block
        label="Ulangi Kata Sandi Baru" />

    <x-button base="!mt-7" color="primary" block wire:click="resetPassword">Atur Ulang Kata Sandi</x-button>
</div>
