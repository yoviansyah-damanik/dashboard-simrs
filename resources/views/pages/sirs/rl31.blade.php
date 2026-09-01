<x-content>
    <x-breadcrumb title="RL 3.1 - Indikator Pelayanan" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.1']]" />

    <x-sirs.report-header title="RL 3.1 - Indikator Pelayanan Rawat Inap"
        subtitle="Sumber Data: RL 3.2 Rekapitulasi Kegiatan Pelayanan Rawat Inap" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    @if (isset($data[99]))
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ([
        'bor' => ['BOR', '%', '60-85%'],
        'alos' => ['ALOS', ' hari', '6-9 hari'],
        'bto' => ['BTO', '', '2-4x'],
        'toi' => ['TOI', ' hari', '1-3 hari'],
        'ndr' => ['NDR', '‰', '<25‰'],
        'gdr' => ['GDR', '‰', '<45‰'],
    ] as $key => [$label, $suffix, $ideal])
                <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-4 text-center">
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">{{ $label }} Rata-rata</p>
                    <h4 class="text-2xl font-black text-primary">{{ number_format($data[99][$key], 2) }}{{ $suffix }}</h4>
                    <p class="text-sm text-gray-400 mt-1">Ideal: {{ $ideal }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Kategori Ruang</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">BOR (%)</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">ALOS (hari)</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">BTO</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">TOI (hari)</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">NDR (‰)</th>
                        <th class="px-4 py-3 text-right font-black text-gray-500 uppercase">GDR (‰)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($data as $key => $row)
                        <tr
                            class="{{ $key === 99 ? 'bg-primary/10 font-black' : 'hover:bg-gray-50 dark:hover:bg-meta-4' }}">
                            <td class="px-4 py-3 text-center">{{ $key === 99 ? '' : $key }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['bor'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['alos'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['bto'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['toi'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['ndr'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['gdr'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
