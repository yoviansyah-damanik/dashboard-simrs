<x-content>
    <x-breadcrumb title="RL 3.15 - Kesehatan Jiwa" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.15']]" />

    <x-sirs.report-header title="RL 3.15 - Pelayanan Kesehatan Jiwa" subtitle="Rekapitulasi tahunan" :profil="$profil"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <x-sirs.lp-table :rows="['Rawat Jalan' => $data['ralan'], 'Rawat Inap' => $data['ranap']]" />
</x-content>
