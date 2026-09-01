<x-content>
    <x-breadcrumb title="RL 3.14 - Pelayanan Khusus" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.14']]" />

    <x-sirs.report-header title="RL 3.14 - Pelayanan Khusus" :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.stat-cards :stats="['Hemodialisa' => $data['hemodialisa'], 'Operasi' => $data['operasi']]" />
</x-content>
