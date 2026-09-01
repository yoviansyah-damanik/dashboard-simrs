<x-content>
    <x-breadcrumb title="RL 3.4 - Rekapitulasi Pengunjung" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.4']]" />

    <x-sirs.report-header title="RL 3.4 - Rekapitulasi Pengunjung Rumah Sakit" :profil="$profil" :bulan="$namaBulan"
        :tahun="$tahun" />

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
                        $rajalBaruL = collect($data['rajal'])->first(fn($r) => $r->status === 'baru' && $r->jk === 'L')->jumlah ?? 0;
                        $rajalBaruP = collect($data['rajal'])->first(fn($r) => $r->status === 'baru' && $r->jk === 'P')->jumlah ?? 0;
                        $rajalLamaL = collect($data['rajal'])->first(fn($r) => $r->status === 'lama' && $r->jk === 'L')->jumlah ?? 0;
                        $rajalLamaP = collect($data['rajal'])->first(fn($r) => $r->status === 'lama' && $r->jk === 'P')->jumlah ?? 0;
                        $ranapL = collect($data['ranap'])->first(fn($r) => $r->jk === 'L')->jumlah ?? 0;
                        $ranapP = collect($data['ranap'])->first(fn($r) => $r->jk === 'P')->jumlah ?? 0;
                        $igdL = $data['igd']['L']->jumlah ?? 0;
                        $igdP = $data['igd']['P']->jumlah ?? 0;
                    @endphp
                    @foreach ([
        'Rawat Jalan - Kunjungan Baru' => [$rajalBaruL, $rajalBaruP],
        'Rawat Jalan - Kunjungan Lama' => [$rajalLamaL, $rajalLamaP],
        'Rawat Inap' => [$ranapL, $ranapP],
        'Instalasi Gawat Darurat' => [$igdL, $igdP],
    ] as $label => $pair)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $label }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($pair[0]) }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($pair[1]) }}</td>
                            <td class="px-4 py-3 text-center font-black">{{ number_format($pair[0] + $pair[1]) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
