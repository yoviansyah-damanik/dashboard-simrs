<x-content>
    <x-breadcrumb title="RL 3.17 - Farmasi Pengadaan Obat" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.17']]" />

    <x-sirs.report-header title="RL 3.17 - Farmasi Pengadaan Obat/BHP Medis"
        subtitle="Top 50 barang berdasarkan jumlah pengadaan, rekapitulasi tahunan" :profil="$profil" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Nama Barang</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Satuan</th>
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Jumlah Pengadaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($data as $i => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 text-center">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $row['nama_brng'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $row['kode_sat'] }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($row['jumlah_pengadaan']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-400">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-content>
