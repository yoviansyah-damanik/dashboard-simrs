<x-content>
    <x-breadcrumb title="RL 1.2 - Indikator Pelayanan Rumah Sakit" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 1.2']]" />

    <x-sirs.report-header title="RL 1.2 - Indikator Pelayanan Rumah Sakit"
        subtitle="Rekap indikator pelayanan rawat inap tahunan seluruh RS" :profil="$profil" bulan="" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach ([
        'bor' => ['BOR', '%', '60-85%'],
        'alos' => ['ALOS', ' hari', '6-9 hari'],
        'bto' => ['BTO', '', '2-4x'],
        'toi' => ['TOI', ' hari', '1-3 hari'],
        'ndr' => ['NDR', '‰', '<25‰'],
        'gdr' => ['GDR', '‰', '<45‰'],
    ] as $key => [$label, $suffix, $ideal])
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 text-center">
                <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">{{ $label }}</p>
                <h4 class="text-2xl font-black text-primary">{{ number_format($data[$key], 2) }}{{ $suffix }}</h4>
                <p class="text-sm text-gray-400 mt-1">Ideal: {{ $ideal }}</p>
            </div>
        @endforeach
    </div>
</x-content>
