<x-content>
    <x-breadcrumb title="Akun" :items="[['title' => 'Akun']]" />

    <div class="flex flex-col items-start max-w-screen-xl gap-4 mx-auto lg:flex-row">
        <div class="w-full p-6 space-y-3 bg-white rounded-lg shadow lg:max-w-96 sm:p-8 sm:space-y-4">
            @foreach ($types as $item)
                <x-button wire:click="$set('type','{{ $item['value'] }}')" block :color="$item['value'] == $type ? 'primary' : 'primary-transparent'" :loading="$item['value'] == $type">
                    {{ $item['title'] }}
                </x-button>
            @endforeach
        </div>
        @if ($type == 'account')
            <div class="flex-1 w-full p-6 mx-auto space-y-3 bg-white rounded-lg shadow sm:p-8 sm:space-y-4">
                <div class="text-lg font-semibold text-primary-500">Informasi Akun</div>
                <x-form.input :loading="true" :error="$errors->first('username')" type="text" wire:model.lazy="username" block
                    label="Nama Pengguna" />
                <x-form.input type="text" :error="$errors->first('name')" wire:model.lazy="name" block label="Nama Lengkap" />
                <x-form.input type="email" :error="$errors->first('email')" wire:model.lazy="email" block label="Email" />
                <x-button base="!mt-7" color="primary" block wire:click="saveAccount">Simpan</x-button>
            </div>
        @endif
        @if ($type == 'password')
            <div class="flex-1 w-full p-6 mx-auto space-y-3 bg-white rounded-lg shadow sm:p-8 sm:space-y-4">
                <div class="text-lg font-semibold text-primary-500">Kata Sandi</div>
                <x-form.input type="password" wire:model.lazy="currentPassword" :error="$errors->first('currentPassword')" block
                    label="Kata Sandi Saat Ini" />
                <x-form.input type="password" wire:model.lazy="newPassword" :error="$errors->first('newPassword')" block
                    label="Kata Sandi Baru" />
                <x-form.input type="password" wire:model.lazy="rePassword" :error="$errors->first('rePassword')" block
                    label="Ulangi Kata Sandi Baru" />
                <x-button base="!mt-7" color="primary" block wire:click="savePassword">Simpan</x-button>
            </div>
        @endif
    </div>
</x-content>
