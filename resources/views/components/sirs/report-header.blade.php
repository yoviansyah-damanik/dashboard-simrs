@props([
    'title' => '',
    'subtitle' => '',
    'profil' => [],
    'bulan' => '',
    'tahun' => '',
])

<div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
    <div class="px-6 pt-6 pb-4 border-b border-stroke dark:border-strokedark text-center">
        <p class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-widest">{{ $title }}</p>
        @if ($subtitle)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
        @endif
        @if ($bulan || $tahun)
            <p class="text-sm font-bold text-primary mt-2">
                Periode: {{ $bulan ? "$bulan $tahun" : "Tahun $tahun" }}
            </p>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 px-6 py-4 bg-gray-50 dark:bg-meta-4/30 text-sm">
        <div>
            <span class="font-bold text-gray-500 dark:text-gray-400">Nama RS:</span>
            <span class="text-gray-800 dark:text-white ml-1">{{ $profil['nama_instansi'] ?? '-' }}</span>
        </div>
        <div>
            <span class="font-bold text-gray-500 dark:text-gray-400">Alamat:</span>
            <span class="text-gray-800 dark:text-white ml-1">{{ $profil['alamat_instansi'] ?? '-' }}</span>
        </div>
        <div>
            <span class="font-bold text-gray-500 dark:text-gray-400">Kab/Kota:</span>
            <span class="text-gray-800 dark:text-white ml-1">{{ $profil['kabupaten'] ?? '-' }}</span>
        </div>
        <div>
            <span class="font-bold text-gray-500 dark:text-gray-400">Provinsi:</span>
            <span class="text-gray-800 dark:text-white ml-1">{{ $profil['propinsi'] ?? '-' }}</span>
        </div>
    </div>
</div>
