<x-content>
    <x-breadcrumb title="RL 4.2 - 10 Besar Penyakit Rawat Inap" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 4.2']]" />

    <x-sirs.report-header title="RL 4.2 - 10 Besar Penyakit Rawat Inap" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.top-disease-table :data="$data['data']" title="Kode Grup (3 digit)" />
</x-content>
