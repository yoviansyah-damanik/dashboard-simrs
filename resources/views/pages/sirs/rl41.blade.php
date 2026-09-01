<x-content>
    <x-breadcrumb title="RL 4.1 - Morbiditas Rawat Inap" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 4.1']]" />

    <x-sirs.report-header title="RL 4.1 - Morbiditas Pasien Rawat Inap" subtitle="Berdasarkan kelompok umur dan jenis kelamin"
        :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.age-morbidity-matrix :data="$data['data']" :labels="$data['labels']" :kematian="$data['kematian']" />
</x-content>
