@props(['data', 'labels', 'kematian' => null])

<div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="text-sm divide-y divide-stroke dark:divide-strokedark" style="min-width: 2200px;">
            <thead>
                <tr class="bg-gray-50 dark:bg-meta-4">
                    <th rowspan="2" class="px-3 py-3 text-center font-black text-gray-500 uppercase sticky left-0 bg-gray-50 dark:bg-meta-4 z-10">No</th>
                    <th rowspan="2" class="px-3 py-3 text-center font-black text-gray-500 uppercase sticky left-10 bg-gray-50 dark:bg-meta-4 z-10">Kode</th>
                    <th rowspan="2" class="px-4 py-3 text-left font-black text-gray-500 uppercase sticky left-28 bg-gray-50 dark:bg-meta-4 z-10 min-w-[180px]">
                        Nama Penyakit</th>
                    @foreach ($labels as $label)
                        <th colspan="2" class="px-2 py-2 text-center font-black text-gray-500 uppercase border-l border-stroke dark:border-strokedark">
                            {{ $label }}</th>
                    @endforeach
                    <th colspan="2" class="px-3 py-2 text-center font-black text-gray-500 uppercase border-l border-stroke dark:border-strokedark">Total</th>
                    <th rowspan="2" class="px-3 py-3 text-center font-black text-gray-500 uppercase">Jml</th>
                    @if ($kematian !== null)
                        <th colspan="3" class="px-3 py-2 text-center font-black text-red-500 uppercase border-l border-stroke dark:border-strokedark">Meninggal</th>
                    @endif
                </tr>
                <tr class="bg-gray-50 dark:bg-meta-4">
                    @for ($i = 0; $i < count($labels); $i++)
                        <th class="px-2 py-2 text-center font-black text-gray-400">L</th>
                        <th class="px-2 py-2 text-center font-black text-gray-400">P</th>
                    @endfor
                    <th class="px-3 py-2 text-center font-black text-gray-400 border-l border-stroke dark:border-strokedark">L</th>
                    <th class="px-3 py-2 text-center font-black text-gray-400">P</th>
                    @if ($kematian !== null)
                        <th class="px-3 py-2 text-center font-black text-red-400 border-l border-stroke dark:border-strokedark">L</th>
                        <th class="px-3 py-2 text-center font-black text-red-400">P</th>
                        <th class="px-3 py-2 text-center font-black text-red-400">Jml</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-stroke dark:divide-strokedark">
                @php $no = 1; @endphp
                @forelse ($data as $kode => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="px-3 py-3 text-center sticky left-0 bg-white dark:bg-boxdark z-10">{{ $no++ }}</td>
                        <td class="px-3 py-3 text-center font-mono font-bold sticky left-10 bg-white dark:bg-boxdark z-10">{{ $kode }}</td>
                        <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300 sticky left-28 bg-white dark:bg-boxdark z-10">
                            {{ Str::limit($item['nama'], 30) }}</td>
                        @foreach ($labels as $label)
                            @php $d = $item['detail'][$label]; @endphp
                            <td class="px-2 py-3 text-center border-l border-stroke dark:border-strokedark">{{ $d['l'] ?: '' }}</td>
                            <td class="px-2 py-3 text-center">{{ $d['p'] ?: '' }}</td>
                        @endforeach
                        <td class="px-3 py-3 text-center font-black border-l border-stroke dark:border-strokedark">{{ $item['total_l'] }}</td>
                        <td class="px-3 py-3 text-center font-black">{{ $item['total_p'] }}</td>
                        <td class="px-3 py-3 text-center font-black">{{ $item['total'] }}</td>
                        @if ($kematian !== null)
                            @php $mati = $kematian[$kode] ?? ['l' => 0, 'p' => 0, 'total' => 0]; @endphp
                            <td class="px-3 py-3 text-center text-red-600 border-l border-stroke dark:border-strokedark">{{ $mati['l'] ?: '' }}</td>
                            <td class="px-3 py-3 text-center text-red-600">{{ $mati['p'] ?: '' }}</td>
                            <td class="px-3 py-3 text-center text-red-600 font-black">{{ $mati['total'] ?: '' }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 3 + count($labels) * 2 + 3 + ($kematian !== null ? 3 : 0) }}" class="px-4 py-10 text-center text-gray-400">
                            Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="px-6 py-3 text-sm text-gray-400 italic border-t border-stroke dark:border-strokedark">
        Total {{ count($data) }} kode penyakit
    </p>
</div>
