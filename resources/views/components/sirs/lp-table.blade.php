@props(['rows'])

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
                @foreach ($rows as $label => $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">{{ $label }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($row['l']) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($row['p']) }}</td>
                        <td class="px-4 py-3 text-center font-black">{{ number_format($row['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
