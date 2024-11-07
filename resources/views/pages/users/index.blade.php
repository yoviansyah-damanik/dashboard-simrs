<x-content>
    <x-breadcrumb title="Manajemen Pengguna" :items="[['title' => 'Manajemen Pengguna']]" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input block class="flex-1" type="search" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan Nama Pengguna" />
    </div>
    <div class="grid grid-flow-col grid-rows-1 gap-3">
        <x-form.select block label="Perpage" :items="$limits" wire:model.live='limit' />
        <x-form.select block label="Peran Pengguna" :items="$roles" wire:model.live='role' />
    </div>

    <x-table :columns="['#', 'Nama Pengguna', 'Nama Lengkap', 'Email', 'Peran', 'Aksi']">
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
                    <x-table.td>
                        {{-- <x-tooltip title="Lihat Data">
                            <x-button size="sm" color="primary" icon="i-ph-list"
                                x-on:click="$dispatch('toggle-show-user-modal')"
                                wire:click="$dispatch('setuser',{ user: {{ $user['data']['no_rekam_medis'] }} })">
                            </x-button>
                        </x-tooltip> --}}
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.tr>
                    <x-table.td centered colspan="6">
                        <x-no-data />
                    </x-table.td>
                </x-table.tr>
            @endforelse
        </x-slot>

        <x-slot name="paginate">
            {{ $users->links(data: ['scrollTo' => 'table']) }}
        </x-slot>
    </x-table>

    {{-- <div wire:ignore>
        <x-modal name="show-user-modal" size="4xl" modalTitle="Data Pasien">
            <livewire:user.show-data />
        </x-modal>
    </div> --}}
</x-content>
