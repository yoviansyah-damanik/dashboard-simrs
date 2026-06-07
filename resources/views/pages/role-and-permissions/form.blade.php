<div class="p-6 space-y-4 sm:p-8 max-h-[75vh] overflow-y-auto">
    <x-form.input type="text" wire:model.lazy="name" :error="$errors->first('name')" block label="Nama Peran" />

    <div>
        <p class="{{ $labelClass ?? 'text-sm font-bold text-gray-700 dark:text-gray-300 mb-2' }}">Hak Akses</p>

        <div class="space-y-3">
            @foreach ($groupedPermissions as $group => $groupPermissions)
                <div class="bg-gray-50 dark:bg-meta-4 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-black text-gray-700 dark:text-white uppercase tracking-widest">
                            {{ str_replace(['_', '-'], ' ', $group) }}
                        </p>
                        <label class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase cursor-pointer">
                            <input type="checkbox"
                                x-on:change="$wire.togglePermissionGroup('{{ $group }}', $event.target.checked)"
                                @checked(count(array_intersect($groupPermissions, $permissions)) === count($groupPermissions))>
                            Pilih Semua
                        </label>
                    </div>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($groupPermissions as $permission)
                            <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 cursor-pointer">
                                <input type="checkbox" wire:model="permissions" value="{{ $permission }}">
                                {{ $permission }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @error('permissions')
            <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
        @enderror
    </div>

    <x-button base="!mt-7" color="primary" block wire:click="save">
        {{ $roleId ? 'Simpan Perubahan' : 'Tambah Peran' }}
    </x-button>
</div>
