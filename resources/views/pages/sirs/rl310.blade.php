<x-content>
    <x-breadcrumb title="RL 3.10 - Rujukan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.10']]" />

    <x-sirs.report-header title="RL 3.10 - Rujukan Masuk dan Keluar" :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.stat-cards :stats="['Rujukan Masuk' => $data['masuk'], 'Rujukan Keluar' => $data['keluar']]" />
</x-content>
