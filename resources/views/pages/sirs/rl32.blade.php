<x-content>
    <x-breadcrumb title="RL 3.2 - Rekap Rawat Inap" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 3.2']]" />

    <x-sirs.report-header title="RL 3.2 - Rekapitulasi Kegiatan Pelayanan Rawat Inap" :profil="$profil"
        :bulan="$namaBulan" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :bulan="$bulan" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="text-sm divide-y divide-stroke dark:divide-strokedark" style="min-width: 1600px;">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left font-black text-gray-500 uppercase min-w-[160px]">Jenis Pelayanan</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Awal Bln</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Masuk</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Keluar Hidup</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Meninggal &lt;48j</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Meninggal &gt;=48j</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Akhir Bln</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Lama Dirawat</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Hari Perawatan</th>
                        <th class="px-3 py-3 text-center font-black text-gray-500 uppercase">Tempat Tidur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($data as $key => $row)
                        @php
                            $matiKurang48 = $row['pasien_laki_mati_kurang48'] + $row['pasien_perempuan_mati_kurang48'];
                            $matiLebih48 = $row['pasien_laki_mati_lebih48'] + $row['pasien_perempuan_mati_lebih48'];
                        @endphp
                        <tr
                            class="{{ $key === 99 ? 'bg-primary/10 font-black' : 'hover:bg-gray-50 dark:hover:bg-meta-4' }}">
                            <td class="px-3 py-3 text-center">{{ $key === 99 ? '' : $key }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['pasien_awal']) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['pasien_masuk']) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['pasien_keluar_hidup']) }}</td>
                            <td class="px-3 py-3 text-center text-red-600">{{ number_format($matiKurang48) }}</td>
                            <td class="px-3 py-3 text-center text-red-600">{{ number_format($matiLebih48) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['pasien_akhir']) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['jumlah_lama_dirawat']) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['jumlah_hari_perawatan']) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($row['tempat_tidur']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
