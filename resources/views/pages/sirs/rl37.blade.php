<x-content>
    <x-breadcrumb title="RL 3.7 - Neonatal/Bayi/Balita" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.7']]" />

    <x-sirs.report-header title="RL 3.7 - Pelayanan Neonatal/Bayi/Balita" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Hidup (L)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Hidup (P)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Meninggal (L)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Meninggal (P)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach (['neonatal' => 'Neonatal (0-28 hari)', 'bayi' => 'Bayi (29 hari - 1 tahun)', 'balita' => 'Balita (1-5 tahun)'] as $key => $label)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $label }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($data[$key]['hidup_l']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($data[$key]['hidup_p']) }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ number_format($data[$key]['mati_l']) }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ number_format($data[$key]['mati_p']) }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($data[$key]['total']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
