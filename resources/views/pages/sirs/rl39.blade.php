<x-content>
    <x-breadcrumb title="RL 3.9 - Radiologi" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.9']]" />

    <x-sirs.report-header title="RL 3.9 - Pemeriksaan Radiologi" :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.ralan-ranap-table :data="$data" />
</x-content>
