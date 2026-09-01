<x-content>
    <x-breadcrumb title="RL 3.8 - Laboratorium" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.8']]" />

    <x-sirs.report-header title="RL 3.8 - Pemeriksaan Laboratorium" :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.ralan-ranap-table :data="$data" />
</x-content>
