<x-content>
    <x-breadcrumb title="Jadwal Operasi" :items="[['title' => 'Jadwal Operasi']]" />

    <div class="flex flex-col gap-3 lg:flex-row">
        <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
            placeholder="Cari berdasarkan No Rawat atau Nama Pasien" />
        <div class="flex flex-1 gap-3">
            <x-form.input block type="date" wire:model.live='startDate' />
            <x-form.input block type="date" wire:model.live='endDate' />
        </div>
    </div>

    <div class="grid grid-flow-col grid-rows-4 gap-3 sm:grid-rows-2 lg:grid-rows-1">
        <x-form.select label="Perpage" block :items="$limits" wire:model.live='limit' />
        <x-form.select label="Status" block :items="$statuses" wire:model.live='status' />
        <x-form.select label="Ruang Operasi" block :items="$rooms" wire:model.live='room' />
        <x-form.select label="Dokter" block :items="$doctors" wire:model.live='doctor' />
    </div>

    @if ($schedules->count() > 0)
        <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-meta-4">
                            <th class="px-5 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b border-stroke dark:border-strokedark">
                                Jadwal</th>
                            <th class="px-5 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b border-stroke dark:border-strokedark">
                                Pasien</th>
                            <th class="px-5 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b border-stroke dark:border-strokedark">
                                Tindakan</th>
                            <th class="px-5 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b border-stroke dark:border-strokedark">
                                Dokter</th>
                            <th class="px-5 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b border-stroke dark:border-strokedark">
                                Ruang</th>
                            <th class="px-5 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b border-stroke dark:border-strokedark text-center">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke dark:divide-strokedark">
                        @foreach ($schedules as $schedule)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-meta-4/20 transition-all">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($schedule['tanggal'])->translatedFormat('d M Y') }}</div>
                                    <div class="text-sm text-gray-400">
                                        {{ \Carbon\Carbon::parse($schedule['jam_mulai'])->format('H:i') }}
                                        @if ($schedule['jam_selesai'])
                                            &ndash; {{ \Carbon\Carbon::parse($schedule['jam_selesai'])->format('H:i') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-700 dark:text-gray-300">{{ $schedule['nama_pasien'] ?? '-' }}</div>
                                    <div class="text-sm text-gray-400">{{ $schedule['no_rekam_medis'] ?? $schedule['no_rawat'] }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $schedule['nama_paket'] ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $schedule['nama_dokter'] ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $schedule['nama_ruang'] ?? '-' }}</td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $statusColor = match ($schedule['status']) {
                                            'Menunggu' => 'bg-amber-500/10 text-amber-600',
                                            'Proses Operasi' => 'bg-primary/10 text-primary',
                                            'Selesai' => 'bg-emerald-500/10 text-emerald-600',
                                            default => 'bg-gray-500/10 text-gray-500',
                                        };
                                    @endphp
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-bold {{ $statusColor }}">
                                        {{ $schedule['status'] ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <x-no-data />
    @endif

    <x-pagination>
        {{ $schedules->links() }}
    </x-pagination>
</x-content>
