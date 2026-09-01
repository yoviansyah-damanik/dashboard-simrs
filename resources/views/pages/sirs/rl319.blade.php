<x-content>
    <x-breadcrumb title="RL 3.19 - Cara Bayar Pasien" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.19']]" />

    <x-sirs.report-header title="RL 3.19 - Cara Bayar Pasien" subtitle="Rekapitulasi tahunan" :profil="$profil"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Cara Bayar</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Ranap Keluar</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Ranap Lama Dirawat</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Ralan Lab</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Ralan Radiologi</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Ralan Lainnya</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Total Ralan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($data as $kategori => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $kategori }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['ranap_keluar']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['ranap_lama']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['ralan_lab']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['ralan_rad']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['ralan_lain']) }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($row['ralan_total']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
