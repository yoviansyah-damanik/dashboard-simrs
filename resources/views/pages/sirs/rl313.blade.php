<x-content>
    <x-breadcrumb title="RL 3.13 - Rehabilitasi Medik" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.13']]" />

    <x-sirs.report-header title="RL 3.13 - Rehabilitasi Medik" subtitle="Rekapitulasi tahunan" :profil="$profil"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <x-sirs.lp-table :rows="['Kunjungan Rehabilitasi Medik' => $data]" />
</x-content>
