<x-content>
    <x-breadcrumb title="Matriks Indikator Tahunan" :items="[['title' => 'Matriks Indikator Tahunan']]" />

    <x-sirs.report-header title="Matriks Indikator Tahunan"
        :subtitle="'BOR, ALOS, BTO, TOI, NDR, GDR per bulan tahun ' . $tahun"
        :profil="$profil"
        bulan=""
        :tahun="$tahun" />

    {{-- Filter Evaluasi Tahunan --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Filter Tahun Saja --}}
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 select-none pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-emerald-600 dark:text-emerald-400 text-base"></span>
                    <span>Tahun Evaluasi:</span>
                </span>
                <div class="relative">
                    <select wire:model.live="tahun"
                        class="appearance-none pl-9 pr-8 py-2 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-emerald-500 outline-none shadow-sm cursor-pointer hover:border-gray-400 dark:hover:border-strokedark transition">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--history-bold-duotone] text-sm"></span>
                    </div>
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="icon-[solar--alt-arrow-down-bold-duotone] text-xs"></span>
                    </div>
                </div>
            </div>

            <div wire:loading.flex class="flex items-center gap-2 text-xs font-bold text-emerald-600">
                <span class="icon-[solar--refresh-bold-duotone] animate-spin text-base"></span>
                <span>Memuat data...</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-button color="default" icon="i-ph-printer" onclick="window.print()">Cetak</x-button>
        </div>
    </div>

    @php
        $indicators = [
            'bor' => ['label' => 'BOR', 'suffix' => '%', 'decimals' => 1, 'withRange' => true],
            'alos' => ['label' => 'ALOS (Hari)', 'suffix' => '', 'decimals' => 1, 'withRange' => false],
            'bto' => ['label' => 'BTO (Kali)', 'suffix' => '', 'decimals' => 1, 'withRange' => true],
            'toi' => ['label' => 'TOI (Hari)', 'suffix' => '', 'decimals' => 1, 'withRange' => false],
            'ndr' => ['label' => 'NDR (‰)', 'suffix' => '', 'decimals' => 1, 'withRange' => true],
            'gdr' => ['label' => 'GDR (‰)', 'suffix' => '', 'decimals' => 1, 'withRange' => true],
        ];

        $tabThemes = [
            'bor' => [
                'active' => 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-500/30',
                'dot' => '#10B981',
            ],
            'alos' => [
                'active' => 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-500/30',
                'dot' => '#3B82F6',
            ],
            'bto' => [
                'active' => 'bg-amber-600 text-white shadow-sm ring-2 ring-amber-500/30',
                'dot' => '#F59E0B',
            ],
            'toi' => [
                'active' => 'bg-purple-600 text-white shadow-sm ring-2 ring-purple-500/30',
                'dot' => '#8B5CF6',
            ],
            'ndr' => [
                'active' => 'bg-pink-600 text-white shadow-sm ring-2 ring-pink-500/30',
                'dot' => '#EC4899',
            ],
            'gdr' => [
                'active' => 'bg-rose-600 text-white shadow-sm ring-2 ring-rose-500/30',
                'dot' => '#EF4444',
            ],
        ];
    @endphp

    {{-- Interactive Chart Section --}}
    <div x-data="indicatorMatrixChart(@js($chartData))" wire:key="indicator-matrix-chart-{{ $tahun }}-{{ $standard }}"
        class="p-5 mb-6 bg-white border shadow-sm sm:p-6 dark:bg-boxdark rounded-3xl border-stroke dark:border-strokedark">

        {{-- Header & Chart Controls --}}
        <div class="flex flex-col gap-4 pb-5 border-b sm:flex-row sm:items-center sm:justify-between border-stroke/70 dark:border-strokedark/70">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <span class="icon-[solar--chart-square-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 sm:text-lg dark:text-white">
                            Grafik Tren Indikator Pelayanan (Tahun {{ $tahun }})
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Tren bulanan indikator efisiensi rawat inap tahun {{ $tahun }} dan perbandingan terhadap <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $standard === 'barber_johnson' ? 'Standar Barber-Johnson' : 'Standar Depkes RI' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Standard Selector (Depkes RI vs Barber-Johnson) --}}
                <div class="inline-flex items-center gap-1 p-1 bg-gray-100 rounded-xl dark:bg-meta-4/60 shrink-0 border border-stroke/50 dark:border-strokedark/50">
                    <span class="pl-2 pr-1 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 shrink-0 select-none">
                        <span class="icon-[solar--scale-bold-duotone] text-sm text-primary-500"></span>
                        <span>Standar:</span>
                    </span>
                    <button type="button"
                        wire:click="setStandard('depkes')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $standard === 'depkes' ? 'bg-emerald-600 text-white shadow-sm font-bold ring-1 ring-emerald-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-200/60 dark:hover:bg-meta-4 font-medium' }}">
                        <span class="w-2 h-2 rounded-full transition-all duration-200 shrink-0 {{ $standard === 'depkes' ? 'bg-white ring-2 ring-white/40' : 'bg-gray-400' }}"></span>
                        <span>1. Depkes RI</span>
                        @if ($standard === 'depkes')
                            <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded font-semibold text-white ml-0.5">Aktif</span>
                        @endif
                    </button>
                    <button type="button"
                        wire:click="setStandard('barber_johnson')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer {{ $standard === 'barber_johnson' ? 'bg-blue-600 text-white shadow-sm font-bold ring-1 ring-blue-500/20' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-200/60 dark:hover:bg-meta-4 font-medium' }}">
                        <span class="w-2 h-2 rounded-full transition-all duration-200 shrink-0 {{ $standard === 'barber_johnson' ? 'bg-white ring-2 ring-white/40' : 'bg-gray-400' }}"></span>
                        <span>2. Barber-Johnson</span>
                        @if ($standard === 'barber_johnson')
                            <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded font-semibold text-white ml-0.5">Aktif</span>
                        @endif
                    </button>
                </div>

                {{-- Chart View Switcher (Line vs Bar) --}}
                <div class="inline-flex items-center p-1 bg-gray-100 rounded-xl dark:bg-meta-4/60 shrink-0 border border-stroke/50 dark:border-strokedark/50">
                    <button type="button" @click="setChartType('line')"
                        :class="chartType === 'line' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                        <span class="icon-[solar--graph-new-bold-duotone] text-sm text-emerald-600 dark:text-emerald-400"></span>
                        <span>Garis</span>
                    </button>
                    <button type="button" @click="setChartType('bar')"
                        :class="chartType === 'bar' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                        <span class="icon-[solar--chart-2-bold-duotone] text-sm text-emerald-600 dark:text-emerald-400"></span>
                        <span>Batang</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Indicator Tabs Selector --}}
        <div class="pt-4 pb-2">
            <div class="flex items-center gap-2 pb-2 overflow-x-auto scrollbar-none">
                @foreach ($chartData['indicators'] as $key => $ind)
                    @php
                        $activeClass = $tabThemes[$key]['active'] ?? 'bg-primary-500 text-white shadow-sm';
                        $dotColor = $ind['color'];
                    @endphp
                    <button type="button" @click="setIndicator('{{ $key }}')"
                        :class="activeIndicator === '{{ $key }}'
                            ? '{{ $activeClass }}'
                            : 'bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-meta-4 border border-stroke/50 dark:border-strokedark/50'"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 shrink-0">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0"
                            :class="activeIndicator === '{{ $key }}' ? 'bg-white' : ''"
                            :style="activeIndicator === '{{ $key }}' ? '' : 'background-color: {{ $dotColor }}'"></span>
                        <span>{{ $ind['shortName'] }}</span>
                        <span class="text-[10px] font-normal"
                            :class="activeIndicator === '{{ $key }}' ? 'text-white/80' : 'text-gray-400 dark:text-gray-400'"
                            x-text="'(' + ((chartData.indicators['{{ $key }}'] && chartData.indicators['{{ $key }}'].standards && chartData.indicators['{{ $key }}'].standards[activeStandard]) ? chartData.indicators['{{ $key }}'].standards[activeStandard].targetLabel : '{{ $ind['targetLabel'] }}') + ')'">
                        </span>
                    </button>
                @endforeach
                <button type="button" @click="setIndicator('all')"
                    :class="activeIndicator === 'all'
                        ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow-sm ring-2 ring-gray-500/30'
                        : 'bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-meta-4 border border-stroke/50 dark:border-strokedark/50'"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 shrink-0">
                    <span class="icon-[solar--layers-bold-duotone] text-sm"></span>
                    <span>Semua Indikator</span>
                </button>
            </div>
        </div>

        {{-- Active Indicator Stat Badges (Shown when viewing a single indicator) --}}
        <template x-if="activeIndicator !== 'all'">
            <div class="grid grid-cols-2 gap-3 my-4 sm:grid-cols-4">
                {{-- Angka Tahunan --}}
                <div class="p-4 bg-gray-50/80 dark:bg-meta-4/30 rounded-2xl border border-stroke/60 dark:border-strokedark/60">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Capaian Tahunan</p>
                    <p class="mt-1 text-xl font-black text-gray-800 sm:text-2xl dark:text-white"
                        x-text="currentIndicator.yearly + currentIndicator.unit"></p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="currentIndicator.description"></p>
                </div>

                {{-- Rata-rata Bulanan --}}
                <div class="p-4 bg-gray-50/80 dark:bg-meta-4/30 rounded-2xl border border-stroke/60 dark:border-strokedark/60">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rata-rata Bulanan</p>
                    <p class="mt-1 text-xl font-black text-emerald-600 dark:text-emerald-400 sm:text-2xl"
                        x-text="currentIndicator.avg + currentIndicator.unit"></p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">12 Bulan Periode {{ $tahun }}</p>
                </div>

                {{-- Puncak & Titik Terendah --}}
                <div class="p-4 bg-gray-50/80 dark:bg-meta-4/30 rounded-2xl border border-stroke/60 dark:border-strokedark/60">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fluktuasi (Max / Min)</p>
                    <div class="mt-1 flex items-center justify-between text-xs font-bold text-gray-800 dark:text-white">
                        <span class="text-emerald-600 dark:text-emerald-400 font-black">▲ <span x-text="currentIndicator.max.value"></span></span>
                        <span class="text-[11px] text-gray-400 uppercase font-medium" x-text="'(' + currentIndicator.max.month + ')'"></span>
                    </div>
                    <div class="mt-0.5 flex items-center justify-between text-xs font-bold text-gray-800 dark:text-white">
                        <span class="text-rose-600 dark:text-rose-400 font-black">▼ <span x-text="currentIndicator.min.value"></span></span>
                        <span class="text-[11px] text-gray-400 uppercase font-medium" x-text="'(' + currentIndicator.min.month + ')'"></span>
                    </div>
                </div>

                {{-- Standar Acuan & Kepatuhan --}}
                <div class="p-4 bg-gray-50/80 dark:bg-meta-4/30 rounded-2xl border border-stroke/60 dark:border-strokedark/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Standar Acuan</p>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md uppercase"
                                :class="activeStandard === 'barber_johnson' ? 'bg-primary-500/10 text-primary-600 dark:text-primary-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'"
                                x-text="activeStandard === 'barber_johnson' ? 'Barber-Johnson' : 'Depkes RI'"></span>
                        </div>
                        <p class="mt-1 text-sm font-black text-gray-800 dark:text-white" x-text="currentTargetLabel"></p>
                    </div>
                    <div class="mt-2">
                        <span x-show="currentIsIdeal"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Sesuai Standar
                        </span>
                        <span x-show="!currentIsIdeal"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Perlu Evaluasi
                        </span>
                    </div>
                </div>
            </div>
        </template>

        {{-- Canvas Chart Container --}}
        <div class="relative w-full min-h-[320px] h-80 sm:h-96 mt-2" x-ref="chartContainer" wire:ignore>
            <canvas class="w-full h-full"></canvas>
        </div>

        {{-- Chart Footer Note --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-4 pt-3.5 border-t border-stroke/60 dark:border-strokedark/60 text-xs text-gray-400">
            <div class="flex items-center gap-2">
                <span class="icon-[solar--info-circle-bold-duotone] text-base text-emerald-600 dark:text-emerald-400"></span>
                <span>Garis putus-putus pada grafik menandai ambang batas ideal menurut <strong class="text-gray-600 dark:text-gray-300" x-text="currentStandardName"></strong>.</span>
            </div>
            <div class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                Pilihan Standar: 1. Depkes RI &bull; 2. Barber-Johnson
            </div>
        </div>
    </div>

    {{-- Side-by-Side Comparison: Standar Depkes RI vs Barber-Johnson --}}
    <div class="overflow-hidden bg-white border shadow-sm dark:bg-boxdark rounded-3xl border-stroke dark:border-strokedark mb-6">
        <div class="p-6 border-b border-stroke dark:border-strokedark flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-primary-500/10 text-primary-600 dark:text-primary-400">
                    <span class="icon-[solar--scale-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 sm:text-lg dark:text-white">
                        Komparasi 2 Standar Pelayanan: Depkes RI vs Barber-Johnson
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Perbandingan batas nilai ideal dan status evaluasi capaian rumah sakit periode tahun {{ $tahun }}
                    </p>
                </div>
            </div>

            {{-- Quick toggle indicator for the active view --}}
            <div class="flex items-center gap-2 text-xs">
                <span class="text-gray-400 font-medium">Standar Aktif:</span>
                <span class="px-2.5 py-1 rounded-lg font-black uppercase text-[11px] {{ $standard === 'barber_johnson' ? 'bg-primary-500/10 text-primary-600 dark:text-primary-400 ring-1 ring-primary-500/30' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 ring-1 ring-emerald-500/30' }}">
                    {{ $standard === 'barber_johnson' ? '2. Barber-Johnson' : '1. Depkes RI' }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-stroke dark:divide-strokedark">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4/40 text-xs font-black tracking-wider uppercase text-gray-500">
                        <th class="px-5 py-3.5 text-left">Indikator</th>
                        <th class="px-4 py-3.5 text-center">Capaian RS (Tahun {{ $tahun }})</th>
                        <th class="px-4 py-3.5 text-left bg-emerald-500/5 border-l border-stroke dark:border-strokedark">
                            <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>1. Standar Depkes RI</span>
                            </div>
                        </th>
                        <th class="px-4 py-3.5 text-left bg-primary-500/5 border-l border-stroke dark:border-strokedark">
                            <div class="flex items-center gap-1.5 text-primary-700 dark:text-primary-400">
                                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                                <span>2. Standar Barber-Johnson</span>
                            </div>
                        </th>
                        <th class="px-5 py-3.5 text-left border-l border-stroke dark:border-strokedark">Keterangan / Daerah Efisiensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark text-xs">
                    @foreach ($standardsComparison as $k => $item)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-meta-4/20 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-gray-800 dark:text-white">
                                <div class="font-black text-sm">{{ $item['short'] }}</div>
                                <div class="text-[11px] text-gray-400 font-normal">{{ $item['name'] }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="text-base font-black text-gray-800 dark:text-white">
                                    {{ number_format($item['yearlyVal'], 1) }}{{ $item['unit'] }}
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    Rata-rata: {{ number_format($item['avgVal'], 1) }}{{ $item['unit'] }}/bln
                                </div>
                            </td>
                            {{-- Kolom Depkes RI --}}
                            <td class="px-4 py-3.5 border-l border-stroke dark:border-strokedark bg-emerald-500/5 whitespace-nowrap">
                                <div class="font-bold text-gray-800 dark:text-white text-xs">
                                    {{ $item['depkes']['label'] }}
                                </div>
                                <div class="mt-1">
                                    @if ($item['depkes']['isIdeal'])
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                            ✓ Sesuai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400">
                                            ✕ Evaluasi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            {{-- Kolom Barber-Johnson --}}
                            <td class="px-4 py-3.5 border-l border-stroke dark:border-strokedark bg-primary-500/5 whitespace-nowrap">
                                <div class="font-bold text-gray-800 dark:text-white text-xs">
                                    {{ $item['barber_johnson']['label'] }}
                                </div>
                                <div class="mt-1">
                                    @if ($item['barber_johnson']['isIdeal'])
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                            ✓ Sesuai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400">
                                            ✕ Evaluasi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            {{-- Kolom Catatan / Daerah Efisiensi --}}
                            <td class="px-5 py-3.5 border-l border-stroke dark:border-strokedark text-gray-500 dark:text-gray-400 leading-relaxed text-[11px]">
                                {{ $item['note'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Data Matrix Table --}}
    <div
        class="overflow-hidden bg-white border shadow-sm dark:bg-boxdark rounded-3xl border-stroke dark:border-strokedark">
        <div class="px-6 py-4 border-b border-stroke dark:border-strokedark flex items-center justify-between">
            <div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white">Tabel Matriks Indikator Bulanan</h4>
                <p class="text-xs text-gray-400 mt-0.5">Rincian angka indikator per bulan tahun {{ $tahun }} beserta total tahunan</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-stroke dark:divide-strokedark">
                <thead>
                    <tr class="bg-gray-50 dark:bg-meta-4">
                        <th
                            class="sticky left-0 z-10 px-4 py-3 font-black tracking-widest text-left text-gray-500 uppercase bg-gray-50 dark:bg-meta-4">
                            Indikator</th>
                        @for ($b = 1; $b <= 12; $b++)
                            <th
                                class="px-3 py-3 font-black tracking-widest text-center uppercase border-l border-stroke dark:border-strokedark text-gray-500">
                                {{ Str::limit(ucfirst(strtolower(\App\Helpers\SirsHelper::getMonthName($b))), 3, '') }}
                            </th>
                        @endfor
                        <th
                            class="px-4 py-3 font-black tracking-widest text-center uppercase border-l-2 text-primary-500 border-primary-500/30 bg-primary-500/5">
                            Tahun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ($indicators as $key => $meta)
                        @php
                            $withRange = ($key === 'alos' && $standard === 'barber_johnson') ? true : $meta['withRange'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/30">
                            <td
                                class="sticky left-0 z-10 px-4 py-3 font-bold text-gray-700 bg-white dark:text-gray-300 dark:bg-boxdark">
                                {!! $meta['label'] !!}</td>
                            @for ($b = 1; $b <= 12; $b++)
                                @php
                                    $nilai = $matrix[$b][$key];
                                    $isOutOfRange = $withRange && !\App\Services\HospitalIndicatorService::isWithinRange($key, $nilai, true, $standard);
                                @endphp
                                <td
                                    class="px-3 py-3 text-center border-l border-stroke dark:border-strokedark {{ $isOutOfRange ? 'text-red-500 font-black' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ number_format($nilai, $meta['decimals']) }}{{ $meta['suffix'] }}
                                </td>
                            @endfor
                            @php
                                $nilaiTahun = $matrix['tahun'][$key];
                                $isYearlyOutOfRange = $withRange && !\App\Services\HospitalIndicatorService::isWithinRange($key, $nilaiTahun, false, $standard);
                            @endphp
                            <td
                                class="px-4 py-3 font-black text-center border-l-2 text-primary-500 border-primary-500/30 bg-primary-500/5 {{ $isYearlyOutOfRange ? 'text-red-500' : '' }}">
                                {{ number_format($nilaiTahun, $meta['decimals']) }}{{ $meta['suffix'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3.5 text-xs text-gray-500 dark:text-gray-400 border-t border-stroke dark:border-strokedark flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <span class="font-bold">Standar Evaluasi Tabel:</span>
                <span class="font-black text-primary-600 dark:text-primary-400">{{ $standard === 'barber_johnson' ? '2. Barber-Johnson (BOR 75-85%, ALOS 3-12 hari, BTO ≥2.5/bln atau ≥30/thn, TOI 1-3 hari)' : '1. Depkes RI (BOR 60-85%, ALOS 6-9 hari, BTO 2-4 kali/bulan atau 40-50/tahun, TOI 1-3 hari, NDR <25‰, GDR <45‰)' }}</span>.
                Nilai berwarna merah berada di luar rentang ideal.
            </div>
            <div class="italic text-gray-400 shrink-0">
                Ubah tombol standar di atas grafik untuk mengganti standar acuan evaluasi.
            </div>
        </div>
    </div>

    @script
        <script>
            Alpine.data('indicatorMatrixChart', (rawChartData) => {
                // Simpan chartInstance di closure variable (bukan di this) agar tidak diproxy oleh Livewire/Alpine
                let chartInstance = null;
                const cleanChartData = JSON.parse(JSON.stringify(rawChartData));

                return {
                    activeIndicator: 'bor',
                    chartType: 'line',
                    activeStandard: cleanChartData.activeStandard || 'depkes',
                    chartData: cleanChartData,

                    get currentIndicator() {
                        return this.chartData.indicators[this.activeIndicator] || {};
                    },

                    get currentStandardMeta() {
                        const ind = this.currentIndicator;
                        if (!ind) return {};
                        if (ind.standards && ind.standards[this.activeStandard]) {
                            return ind.standards[this.activeStandard];
                        }
                        return { min: ind.minTarget, max: ind.maxTarget, targetLabel: ind.targetLabel, isIdeal: ind.isIdeal };
                    },

                    get currentTargetLabel() {
                        return this.currentStandardMeta.targetLabel || this.currentIndicator.targetLabel || '-';
                    },

                    get currentStandardName() {
                        return this.activeStandard === 'barber_johnson' ? 'Standar Barber-Johnson' : 'Standar Depkes RI';
                    },

                    get currentIsIdeal() {
                        return this.currentStandardMeta.isIdeal ?? this.currentIndicator.isIdeal ?? true;
                    },

                    setStandard(std) {
                        if (this.activeStandard === std) return;
                        this.activeStandard = std;
                        this.updateDatasets();
                    },

                    init() {
                        this.$nextTick(() => {
                            this.buildChart();
                        });
                    },

                    destroy() {
                        if (chartInstance) {
                            try {
                                chartInstance.destroy();
                            } catch (e) {}
                            chartInstance = null;
                        }
                    },

                    setIndicator(key) {
                        if (this.activeIndicator === key) return;
                        this.activeIndicator = key;
                        this.updateDatasets();
                    },

                    setChartType(type) {
                        if (this.chartType === type) return;
                        this.chartType = type;
                        this.buildChart();
                    },

                    isDark() {
                        return document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
                    },

                    getDatasets() {
                        const isDark = this.isDark();
                        let datasets = [];

                        if (this.activeIndicator === 'all') {
                            const keys = ['bor', 'alos', 'bto', 'toi', 'ndr', 'gdr'];
                            datasets = keys.map(k => {
                                const ind = this.chartData.indicators[k];
                                return {
                                    label: ind.shortName + ' (' + ind.unit.trim() + ')',
                                    data: [...ind.data],
                                    borderColor: ind.color,
                                    backgroundColor: ind.bgColor,
                                    borderWidth: 2.5,
                                    tension: 0.35,
                                    pointBackgroundColor: ind.color,
                                    pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    fill: false,
                                };
                            });
                        } else {
                            const ind = this.chartData.indicators[this.activeIndicator];
                            if (!ind) return datasets;

                            const stdMeta = (ind.standards && ind.standards[this.activeStandard])
                                ? ind.standards[this.activeStandard]
                                : { min: ind.minTarget, max: ind.maxTarget, targetLabel: ind.targetLabel };

                            datasets.push({
                                label: ind.name,
                                data: [...ind.data],
                                borderColor: ind.color,
                                backgroundColor: ind.bgColor,
                                borderWidth: 3,
                                tension: 0.35,
                                pointBackgroundColor: ind.color,
                                pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                fill: this.chartType === 'line',
                                borderRadius: this.chartType === 'bar' ? 6 : 0,
                            });

                            if (this.chartType === 'line') {
                                const stdPrefix = this.activeStandard === 'barber_johnson' ? 'BJ' : 'Depkes';
                                if (stdMeta.min !== null && stdMeta.min !== undefined && stdMeta.min > 0) {
                                    datasets.push({
                                        label: 'Batas Bawah ' + stdPrefix + ' (' + stdMeta.min + ind.unit.trim() + ')',
                                        data: Array(12).fill(stdMeta.min),
                                        borderColor: isDark ? 'rgba(52, 211, 153, 0.85)' : '#059669',
                                        borderWidth: 2,
                                        borderDash: [6, 4],
                                        pointRadius: 0,
                                        pointHoverRadius: 0,
                                        fill: false,
                                    });
                                }
                                if (stdMeta.max !== null && stdMeta.max !== undefined && stdMeta.max > 0) {
                                    datasets.push({
                                        label: 'Batas Atas ' + stdPrefix + ' (' + stdMeta.max + ind.unit.trim() + ')',
                                        data: Array(12).fill(stdMeta.max),
                                        borderColor: isDark ? 'rgba(248, 113, 113, 0.85)' : '#e11d48',
                                        borderWidth: 2,
                                        borderDash: [6, 4],
                                        pointRadius: 0,
                                        pointHoverRadius: 0,
                                        fill: false,
                                    });
                                }
                            }
                        }

                        // Pastikan objek plain murni tanpa keterikatan Proxy Livewire
                        return JSON.parse(JSON.stringify(datasets));
                    },

                    updateDatasets() {
                        if (!chartInstance) {
                            this.buildChart();
                            return;
                        }

                        try {
                            chartInstance.data.labels = [...this.chartData.labels];
                            chartInstance.data.datasets = this.getDatasets();
                            if (chartInstance.options && chartInstance.options.scales && chartInstance.options.scales.y) {
                                chartInstance.options.scales.y.suggestedMax = (this.activeIndicator === 'bor') ? 100 : undefined;
                            }
                            chartInstance.update();
                        } catch (e) {
                            console.warn('Update chart in-place gagal, fallback ke buildChart:', e);
                            this.buildChart();
                        }
                    },

                    buildChart() {
                        const container = this.$refs.chartContainer;
                        if (!container) return;

                        // Bersihkan chart lama bila ada
                        if (chartInstance) {
                            try {
                                chartInstance.destroy();
                            } catch (e) {}
                            chartInstance = null;
                        }

                        // Pasang elemen kanvas baru yang terisolasi dari event lama
                        container.innerHTML = '';
                        const canvas = document.createElement('canvas');
                        canvas.className = 'w-full h-full';
                        container.appendChild(canvas);

                        const isDark = this.isDark();
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                        const textColor = isDark ? '#94a3b8' : '#64748b';
                        const datasets = this.getDatasets();
                        const self = this;

                        try {
                            chartInstance = new Chart(canvas, {
                                type: this.chartType,
                                data: {
                                    labels: [...this.chartData.labels],
                                    datasets: datasets
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 400,
                                        easing: 'easeOutQuart'
                                    },
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                color: textColor,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 14,
                                                font: {
                                                    size: 11,
                                                    weight: '600'
                                                }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                            titleColor: isDark ? '#f1f5f9' : '#0f172a',
                                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                            borderWidth: 1,
                                            padding: 12,
                                            boxPadding: 6,
                                            usePointStyle: true,
                                            callbacks: {
                                                title: function(items) {
                                                    if (!items.length) return '';
                                                    const idx = items[0].dataIndex;
                                                    return (self.chartData.fullLabels && self.chartData.fullLabels[idx])
                                                        ? self.chartData.fullLabels[idx] + ' ' + @js($tahun)
                                                        : items[0].label;
                                                },
                                                label: function(context) {
                                                    const ind = self.chartData.indicators[self.activeIndicator];
                                                    const suffix = ind ? ind.unit : '';
                                                    return ' ' + context.dataset.label + ': ' + Number(context.raw).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + suffix;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false,
                                            },
                                            ticks: {
                                                color: textColor,
                                                font: {
                                                    size: 12,
                                                    weight: 'bold'
                                                }
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            suggestedMax: (this.activeIndicator === 'bor') ? 100 : undefined,
                                            grid: {
                                                color: gridColor,
                                            },
                                            ticks: {
                                                color: textColor,
                                                font: {
                                                    size: 11
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Inisialisasi Chart.js gagal:', err);
                        }
                    }
                };
            });
        </script>
    @endscript
</x-content>
