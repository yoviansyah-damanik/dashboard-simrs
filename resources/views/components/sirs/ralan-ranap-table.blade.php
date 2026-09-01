@props(['data'])

<div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-meta-4">
                    <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Uraian</th>
                    <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Jumlah Pasien</th>
                    <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Jumlah Pemeriksaan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                    <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">Rawat Jalan</td>
                    <td class="px-4 py-3 text-center">{{ number_format($data['ralan']['pasien']) }}</td>
                    <td class="px-4 py-3 text-center">{{ number_format($data['ralan']['pemeriksaan']) }}</td>
                </tr>
                <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                    <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">Rawat Inap</td>
                    <td class="px-4 py-3 text-center">{{ number_format($data['ranap']['pasien']) }}</td>
                    <td class="px-4 py-3 text-center">{{ number_format($data['ranap']['pemeriksaan']) }}</td>
                </tr>
                <tr class="bg-primary/10 font-black">
                    <td class="px-4 py-3">TOTAL</td>
                    <td class="px-4 py-3 text-center">{{ number_format($data['ralan']['pasien'] + $data['ranap']['pasien']) }}</td>
                    <td class="px-4 py-3 text-center">{{ number_format($data['ralan']['pemeriksaan'] + $data['ranap']['pemeriksaan']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
