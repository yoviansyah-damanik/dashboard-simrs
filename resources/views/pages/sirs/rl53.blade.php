<x-content>
    <x-breadcrumb title="RL 5.3 - 10 Besar Kunjungan Rawat Jalan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 5.3']]" />

    <x-sirs.report-header title="RL 5.3 - 10 Besar Kunjungan Rawat Jalan" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.top-disease-table :data="$data" title="Kode Grup (3 digit)" />
</x-content>
