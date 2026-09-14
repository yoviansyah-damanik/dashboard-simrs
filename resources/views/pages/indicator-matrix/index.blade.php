<x-content>
    <x-breadcrumb title="Matriks Indikator Tahunan" :items="[['title' => 'Matriks Indikator Tahunan']]" />

    <x-sirs.report-header title="Matriks Indikator Tahunan" subtitle="BOR, ALOS, BTO, TOI, NDR, GDR per bulan"
        :profil="$profil" bulan="" :tahun="$tahun" />

    <x-sirs.period-filter :tahun="$tahun" :showBulan="false" />

    @php
        $indicators = [
            'bor' => ['label' => 'BOR', 'suffix' => '%', 'decimals' => 1, 'withRange' => true],
            'alos' => ['label' => 'ALOS (Hari)', 'suffix' => '', 'decimals' => 1, 'withRange' => false],
            'bto' => ['label' => 'BTO (Kali)', 'suffix' => '', 'decimals' => 1, 'withRange' => false],
            'toi' => ['label' => 'TOI (Hari)', 'suffix' => '', 'decimals' => 1, 'withRange' => false],
            'ndr' => ['label' => 'NDR (‰)', 'suffix' => '', 'decimals' => 1, 'withRange' => true],
            'gdr' => ['label' => 'GDR (‰)', 'suffix' => '', 'decimals' => 1, 'withRange' => true],
        ];
    @endphp

    <div
        class="overflow-hidden bg-white border shadow-sm dark:bg-boxdark rounded-3xl border-stroke dark:border-strokedark">
        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-stroke dark:divide-strokedark">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th
                            class="sticky left-0 z-10 px-4 py-3 font-black tracking-widest text-left text-gray-500 uppercase bg-gray-50 dark:bg-meta-4">
                            Indikator</th>
                        @for ($bulan = 1; $bulan <= 12; $bulan++)
                            <th
                                class="px-3 py-3 font-black tracking-widest text-center text-gray-500 uppercase border-l border-stroke dark:border-strokedark">
                                {{ Str::limit(ucfirst(strtolower(\App\Helpers\SirsHelper::getMonthName($bulan))), 3, '') }}
                            </th>
                        @endfor
                        <th
                            class="px-4 py-3 font-black tracking-widest text-center uppercase border-l-2 text-primary border-primary/30 bg-primary/5">
                            Tahun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($indicators as $key => $meta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/30">
                            <td
                                class="sticky left-0 z-10 px-4 py-3 font-bold text-gray-700 bg-white dark:text-gray-300 dark:bg-boxdark">
                                {!! $meta['label'] !!}</td>
                            @for ($bulan = 1; $bulan <= 12; $bulan++)
                                @php $nilai = $matrix[$bulan][$key]; @endphp
                                <td
                                    class="px-3 py-3 text-center border-l border-stroke dark:border-strokedark {{ $meta['withRange'] && !\App\Services\HospitalIndicatorService::isWithinRange($key, $nilai) ? 'text-red-500 font-black' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ number_format($nilai, $meta['decimals']) }}{{ $meta['suffix'] }}
                                </td>
                            @endfor
                            <td
                                class="px-4 py-3 font-black text-center border-l-2 text-primary border-primary/30 bg-primary/5">
                                {{ number_format($matrix['tahun'][$key], $meta['decimals']) }}{{ $meta['suffix'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="px-6 py-3 text-sm italic text-gray-400 border-t border-stroke dark:border-strokedark">
            Nilai berwarna merah berada di luar rentang ideal standar Depkes (BOR 60-85%, NDR &lt;25‰, GDR &lt;45‰).
        </p>
    </div>
</x-content>
