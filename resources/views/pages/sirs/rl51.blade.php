<x-content>
    <x-breadcrumb title="RL 5.1 - Morbiditas Rawat Jalan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 5.1']]" />

    <x-sirs.report-header title="RL 5.1 - Morbiditas Pasien Rawat Jalan" subtitle="Berdasarkan kelompok umur dan jenis kelamin"
        :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.age-morbidity-matrix :data="$data['data']" :labels="$data['labels']" />
</x-content>
