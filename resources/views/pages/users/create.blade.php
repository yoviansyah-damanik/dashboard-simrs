<div class="p-6 space-y-3 sm:p-8 sm:space-y-4">
    <x-form.input type="text" wire:model.lazy="username" :error="$errors->first('username')" block
        label="Nama Pengguna" />
    <x-form.input type="text" wire:model.lazy="name" :error="$errors->first('name')" block label="Nama Lengkap" />
    <x-form.input type="email" wire:model.lazy="email" :error="$errors->first('email')" block label="Email" />
    <x-form.input type="text" wire:model.lazy="as" :error="$errors->first('as')" block label="Jabatan" />
    <x-form.select :items="$roles" wire:model.lazy="role" :error="$errors->first('role')" block label="Peran" />
    <x-form.input type="password" wire:model.lazy="password" :error="$errors->first('password')" block
        :label="$userId ? 'Kata Sandi Baru (kosongkan jika tidak diubah)' : 'Kata Sandi'" />

    <x-button base="!mt-7" color="primary" block wire:click="save">
        {{ $userId ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
    </x-button>
</div>
