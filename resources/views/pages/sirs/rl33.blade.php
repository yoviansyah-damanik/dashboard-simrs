<x-content>
    <x-breadcrumb title="RL 3.3 - Rawat Darurat" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.3']]" />

    <x-sirs.report-header title="RL 3.3 - Pelayanan Rawat Darurat (IGD)" subtitle="Rekapitulasi bulanan" :profil="$profil"
        :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Uraian</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Laki-laki</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Perempuan</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @php
                        $kunjL = $data['kunjungan']['L']->jumlah ?? 0;
                        $kunjP = $data['kunjungan']['P']->jumlah ?? 0;
                        $ditL = $data['diterima']['L']->jumlah ?? 0;
                        $ditP = $data['diterima']['P']->jumlah ?? 0;
                        $rujL = $data['dirujuk']['L']->jumlah ?? 0;
                        $rujP = $data['dirujuk']['P']->jumlah ?? 0;
                    @endphp
                    @foreach ([
        'Total Kunjungan IGD' => [$kunjL, $kunjP],
        'Diterima Rawat Inap' => [$ditL, $ditP],
        'Dirujuk ke RS Lain' => [$rujL, $rujP],
    ] as $label => $pair)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $label }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($pair[0]) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($pair[1]) }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($pair[0] + $pair[1]) }}</td>
                        </tr>
                    @endforeach
                    @foreach ($data['meninggal'] as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">
                                Meninggal ({{ $row->waktu === 'kurang48' ? '<48 jam' : '>=48 jam' }})</td>
                            <td class="px-4 py-3 text-center">{{ $row->jk === 'L' ? $row->jumlah : '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $row->jk === 'P' ? $row->jumlah : '-' }}</td>
                            <td class="px-4 py-3 text-center font-black text-red-600">{{ $row->jumlah }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
