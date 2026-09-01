<x-content>
    <x-breadcrumb title="RL 3.16 - Keluarga Berencana" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.16']]" />

    <x-sirs.report-header title="RL 3.16 - Pelayanan Keluarga Berencana" subtitle="Rekapitulasi tahunan" :profil="$profil"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <x-sirs.lp-table :rows="['Peserta KB' => $data]" />
</x-content>
