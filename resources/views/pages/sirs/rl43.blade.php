<x-content>
    <x-breadcrumb title="RL 4.3 - 10 Besar Kematian Rawat Inap" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 4.3']]" />

    <x-sirs.report-header title="RL 4.3 - 10 Besar Kematian Rawat Inap" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Kode Grup</th>
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Nama Penyakit</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Meninggal (L)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Meninggal (P)</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @php $no = 1; @endphp
                    @forelse ($data as $kode => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 text-center">{{ $no++ }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold">{{ $kode }}</td>
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ Str::limit($row['nama'], 40) }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ number_format($row['mati_l']) }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ number_format($row['mati_p']) }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($row['mati_total']) }}</td>
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
