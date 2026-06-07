<x-content>
    <x-breadcrumb title="Manajemen Pengguna" :items="[['title' => 'Manajemen Pengguna']]" />

    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <x-form.input block class="flex-1" type="search" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nama Pengguna" />

        <x-button color="primary" icon="i-ph-user-plus"
            x-on:click="$dispatch('toggle-create-user-modal')"
            wire:click="$dispatch('setUserForm', { id: null })">
            Tambah Pengguna
        </x-button>
    </div>
    <div class="grid grid-flow-col grid-rows-1 gap-3">
        <x-form.select block label="Perpage" :items="$limits" wire:model.live='limit' />
        <x-form.select block label="Peran Pengguna" :items="$roles" wire:model.live='role' />
    </div>

    <x-table :columns="['#', 'Nama Pengguna', 'Nama Lengkap', 'Email', 'Peran', 'Status', 'Aksi']">
        <x-slot name="body">
            @forelse ($users as $user)
                <x-table.tr>
                    <x-table.td centered>
                        {{ $users->perPage() * ($users->currentPage() - 1) + $loop->iteration }}
                    </x-table.td>
                    <x-table.td>
                        {{ $user['username'] }}
                    </x-table.td>
                    <x-table.td>
                        {{ $user['name'] }}
                    </x-table.td>
                    <x-table.td>
                        {{ $user['email'] }}
                    </x-table.td>
                    <x-table.td>
                        {{ $user['role'] }}
                    </x-table.td>
                    <x-table.td centered>
                        @if ($user['is_active'])
                            <span
                                class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 text-[10px] font-black rounded-full uppercase tracking-widest">
                                Aktif
                            </span>
                        @else
                            <span
                                class="px-2.5 py-1 bg-rose-500/10 text-rose-600 text-[10px] font-black rounded-full uppercase tracking-widest">
                                Nonaktif
                            </span>
                        @endif
                    </x-table.td>
                    <x-table.td centered>
                        <div class="flex items-center justify-center gap-1.5">
                            <x-tooltip title="Ubah">
                                <x-button size="sm" color="primary" icon="i-ph-pencil-simple"
                                    x-on:click="$dispatch('toggle-create-user-modal')"
                                    wire:click="$dispatch('setUserForm', { id: '{{ $user['id'] }}' })">
                                </x-button>
                            </x-tooltip>
                            <x-tooltip title="Atur Ulang Kata Sandi">
                                <x-button size="sm" color="secondary" icon="i-ph-key"
                                    x-on:click="$dispatch('toggle-reset-password-user-modal')"
                                    wire:click="$dispatch('setUserToResetPassword', { id: '{{ $user['id'] }}' })">
                                </x-button>
                            </x-tooltip>
                            <x-tooltip :title="$user['is_active'] ? 'Nonaktifkan' : 'Aktifkan'">
                                <x-button size="sm" :color="$user['is_active'] ? 'yellow' : 'green'"
                                    :icon="$user['is_active'] ? 'i-ph-lock' : 'i-ph-lock-open'"
                                    x-on:click="$dispatch('toggle-activation-user-modal')"
                                    wire:click="$dispatch('setUserToActivate', { id: '{{ $user['id'] }}' })">
                                </x-button>
                            </x-tooltip>
                            <x-tooltip title="Hapus">
                                <x-button size="sm" color="red" icon="i-ph-trash"
                                    x-on:click="$dispatch('toggle-delete-user-modal')"
                                    wire:click="$dispatch('setUserToDelete', { id: '{{ $user['id'] }}' })">
                                </x-button>
                            </x-tooltip>
                        </div>
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.tr>
                    <x-table.td centered colspan="7">
                        <x-no-data />
                    </x-table.td>
                </x-table.tr>
            @endforelse
        </x-slot>

        <x-slot name="paginate">
            {{ $users->links(data: ['scrollTo' => 'table']) }}
        </x-slot>
    </x-table>

    <div wire:ignore>
        <x-modal name="create-user-modal" size="2xl" modalTitle="Formulir Pengguna">
            <livewire:users.create />
        </x-modal>

        <x-modal name="reset-password-user-modal" size="lg" modalTitle="Atur Ulang Kata Sandi">
            <livewire:users.forgot-password />
        </x-modal>

        <x-modal name="activation-user-modal" size="lg" modalTitle="Status Aktivasi Pengguna">
            <livewire:users.activation-menu />
        </x-modal>

        <x-modal name="delete-user-modal" size="lg" modalTitle="Hapus Pengguna">
            <livewire:users.delete />
        </x-modal>
    </div>
</x-content>
