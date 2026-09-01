<x-content>
    <x-breadcrumb title="RL 3.5 - Kunjungan Rawat Jalan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.5']]" />

    <x-sirs.report-header title="RL 3.5 - Kunjungan Rawat Jalan per Poliklinik" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Poliklinik</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Baru (L)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Baru (P)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Lama (L)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Lama (P)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($data as $poli => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $poli }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['baru_l']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['baru_p']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['lama_l']) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($row['lama_p']) }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-content>
