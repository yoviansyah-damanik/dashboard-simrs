<x-content>
    <x-breadcrumb title="RL 1.3 - Fasilitas Tempat Tidur" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 1.3']]" />

    <x-sirs.report-header title="RL 1.3 - Fasilitas Tempat Tidur Rawat Inap"
        subtitle="Kondisi tempat tidur aktif per kelas perawatan saat ini" :profil="$profil" bulan="" :tahun="now()->year" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Kelas Perawatan</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">Jumlah Tempat Tidur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($data['per_kelas'] as $kelas => $jumlah)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $kelas }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($jumlah) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-primary/10 font-black">
                        <td class="px-4 py-3">TOTAL</td>
                        <td class="px-4 py-3 text-right">{{ number_format($data['total']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-content>
