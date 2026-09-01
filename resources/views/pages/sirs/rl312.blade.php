<x-content>
    <x-breadcrumb title="RL 3.12 - Pembedahan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.12']]" />

    <x-sirs.report-header title="RL 3.12 - Pelayanan Pembedahan" :profil="$profil" :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <x-sirs.lp-table :rows="[
        'Operasi Besar' => $data['besar'],
        'Operasi Sedang' => $data['sedang'],
        'Operasi Kecil' => $data['kecil'],
        'Operasi Khusus' => $data['khusus'],
    ]" />
</x-content>
