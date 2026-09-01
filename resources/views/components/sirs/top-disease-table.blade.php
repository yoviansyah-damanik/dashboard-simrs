@props(['data', 'title' => 'Kode ICD-10'])

<div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-meta-4">
                    <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">{{ $title }}</th>
                    <th class="px-4 py-3 text-left font-black text-gray-500 uppercase">Nama Penyakit</th>
                    <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Laki-laki</th>
                    <th class="px-4 py-3 text-center font-black text-gray-500 uppercase">Perempuan</th>
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
                        <td class="px-4 py-3 text-center">{{ number_format($row['l']) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($row['p']) }}</td>
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
