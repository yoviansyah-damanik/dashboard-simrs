<x-content>
    <x-breadcrumb title="RL 3.6 - Pelayanan Kebidanan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.6']]" />

    <x-sirs.report-header title="RL 3.6 - Pelayanan Kebidanan (Persalinan)" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.lp-table :rows="[
        'Persalinan Normal' => $data['normal'],
        'Sectio Caesarea' => $data['sectio'],
        'Persalinan Buatan' => $data['buatan'],
        'Lainnya' => $data['lainnya'],
    ]" />
</x-content>
