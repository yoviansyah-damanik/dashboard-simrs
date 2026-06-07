<x-content>
    <x-breadcrumb title="Hak Akses" :items="[['title' => 'Hak Akses']]" />

    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <x-form.input block class="flex-1" type="search" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nama Peran" />

        <x-button color="primary" icon="i-ph-shield-plus"
            x-on:click="$dispatch('toggle-role-form-modal')"
            wire:click="$dispatch('setRoleForm', { id: null })">
            Tambah Peran
        </x-button>
    </div>
    <div class="grid grid-flow-col grid-rows-1 gap-3">
        <x-form.select block label="Perpage" :items="$limits" wire:model.live='limit' />
    </div>

    <x-table :columns="['#', 'Nama Peran', 'Jumlah Hak Akses', 'Jumlah Pengguna', 'Aksi']">
        <x-slot name="body">
            @forelse ($roles as $role)
                <x-table.tr>
                    <x-table.td centered>
                        {{ $roles->perPage() * ($roles->currentPage() - 1) + $loop->iteration }}
                    </x-table.td>
                    <x-table.td>
                        {{ $role['name'] }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $role['permissions_count'] }}
                    </x-table.td>
                    <x-table.td centered>
                        {{ $role['users_count'] }}
                    </x-table.td>
                    <x-table.td centered>
                        <div class="flex items-center justify-center gap-1.5">
                            <x-tooltip title="Ubah Hak Akses">
                                <x-button size="sm" color="primary" icon="i-ph-pencil-simple"
                                    x-on:click="$dispatch('toggle-role-form-modal')"
                                    wire:click="$dispatch('setRoleForm', { id: {{ $role['id'] }} })">
                                </x-button>
                            </x-tooltip>
                            <x-tooltip title="Hapus">
                                <x-button size="sm" color="red" icon="i-ph-trash"
                                    x-on:click="$dispatch('toggle-delete-role-modal')"
                                    wire:click="$dispatch('setRoleToDelete', { id: {{ $role['id'] }} })">
                                </x-button>
                            </x-tooltip>
                        </div>
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.tr>
                    <x-table.td centered colspan="5">
                        <x-no-data />
                    </x-table.td>
                </x-table.tr>
            @endforelse
        </x-slot>

        <x-slot name="paginate">
            {{ $roles->links(data: ['scrollTo' => 'table']) }}
        </x-slot>
    </x-table>

    <div wire:ignore>
        <x-modal name="role-form-modal" size="3xl" modalTitle="Formulir Peran & Hak Akses">
            <livewire:role-and-permissions.form />
        </x-modal>

        <x-modal name="delete-role-modal" size="lg" modalTitle="Hapus Peran">
            <livewire:role-and-permissions.delete />
        </x-modal>
    </div>
</x-content>
