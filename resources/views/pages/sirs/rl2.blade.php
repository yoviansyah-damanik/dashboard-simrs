<x-content>
    <x-breadcrumb title="RL 2 - Ketenagaan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 2']]" />

    <x-sirs.report-header title="RL 2 - Ketenagaan" subtitle="Rekap pegawai aktif per jenis tenaga dan status kepegawaian"
        :profil="$profil" bulan="" :tahun="now()->year" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Jenis Tenaga</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">PNS/Tetap</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">Non-PNS</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($data as $key => $row)
                        <tr
                            class="{{ $key === 99 ? 'bg-primary/10 font-black' : 'hover:bg-gray-50 dark:hover:bg-meta-4' }}">
                            <td class="px-4 py-3 text-center">{{ $key === 99 ? '' : $key }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['pns']) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['non_pns']) }}</td>
                            <td class="px-4 py-3 text-right font-black">{{ number_format($row['total']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
