<x-content>
    <x-breadcrumb title="SIRS Online" :items="[['title' => 'SIRS Online']]" />

    <p class="text-sm text-gray-500 dark:text-gray-400 -mt-3">Sistem Informasi Rumah Sakit — Laporan RL (Rekam Laporan) 1-5
        untuk pelaporan ke Kementerian Kesehatan.</p>

    @foreach ([
        'dasar' => ['title' => 'RL 1 & RL 2 - Data Dasar dan Ketenagaan', 'color' => 'text-indigo-600 bg-indigo-500/10', 'hover' => 'hover:border-indigo-400'],
        'bulanan' => ['title' => 'RL 3 - Laporan Bulanan', 'color' => 'text-primary bg-primary/10', 'hover' => 'hover:border-primary'],
        'tahunan' => ['title' => 'RL 3 - Laporan Tahunan', 'color' => 'text-amber-600 bg-amber-500/10', 'hover' => 'hover:border-amber-400'],
        'penyakit' => ['title' => 'RL 4-5 - Laporan Penyakit', 'color' => 'text-emerald-600 bg-emerald-500/10', 'hover' => 'hover:border-emerald-400'],
    ] as $group => $meta)
        <div class="space-y-3">
            <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-widest">{{ $meta['title'] }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach ($formulir[$group] as $item)
                    <a wire:navigate href="{{ route($item['route']) }}"
                        class="block p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark {{ $meta['hover'] }} hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between">
                            <span class="text-sm font-black px-2 py-0.5 rounded {{ $meta['color'] }}">{{ $item['kode'] }}</span>
                            <span class="icon-[solar--alt-arrow-right-bold-duotone] text-lg text-gray-300 group-hover:text-primary transition-colors"></span>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white mt-2">{{ $item['judul'] }}</h4>
                        <p class="text-sm text-gray-400 mt-1">{{ $item['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</x-content>
