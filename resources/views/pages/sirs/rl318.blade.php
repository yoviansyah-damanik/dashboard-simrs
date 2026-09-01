<x-content>
    <x-breadcrumb title="RL 3.18 - Farmasi Resep" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.18']]" />

    <x-sirs.report-header title="RL 3.18 - Farmasi Resep Obat" subtitle="Rekapitulasi tahunan" :profil="$profil"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <x-sirs.stat-cards :stats="['Resep Rawat Jalan' => $data['ralan'], 'Resep Rawat Inap' => $data['ranap'], 'Total Resep' => $data['total']]" />
</x-content>
