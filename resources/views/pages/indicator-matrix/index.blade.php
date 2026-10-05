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

    {{-- Interactive Chart Section --}}
    <div x-data="indicatorMatrixChart(@js($chartData))" wire:key="indicator-matrix-chart-{{ $tahun }}"
        class="p-5 mb-6 bg-white border shadow-sm sm:p-6 dark:bg-boxdark rounded-3xl border-stroke dark:border-strokedark">

        {{-- Header & Chart Controls --}}
        <div class="flex flex-col gap-4 pb-5 border-b sm:flex-row sm:items-center sm:justify-between border-stroke/70 dark:border-strokedark/70">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-primary/10 text-primary">
                        <span class="icon-[solar--chart-square-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 sm:text-lg dark:text-white">
                            Grafik Tren Indikator Pelayanan ({{ $tahun }})
                        </h3>
                        <p class="text-xs text-gray-400">
                            Tren bulanan indikator efisiensi rawat inap dan perbandingan terhadap standar ideal Depkes RI
                        </p>
                    </div>
                </div>
            </div>

            {{-- Chart View Switcher (Line vs Bar) --}}
            <div class="inline-flex items-center self-start p-1 bg-gray-100 rounded-xl dark:bg-meta-4/60 shrink-0">
                <button type="button" @click="setChartType('line')"
                    :class="chartType === 'line' ? 'bg-white dark:bg-boxdark text-primary shadow-xs font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                    <span class="icon-[solar--graph-new-bold-duotone] text-sm"></span>
                    <span>Garis</span>
                </button>
                <button type="button" @click="setChartType('bar')"
                    :class="chartType === 'bar' ? 'bg-white dark:bg-boxdark text-primary shadow-xs font-bold' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                    <span class="icon-[solar--chart-2-bold-duotone] text-sm"></span>
                    <span>Batang</span>
                </button>
            </div>
        </div>

        {{-- Indicator Tabs Selector --}}
        <div class="pt-4 pb-2">
            <div class="flex items-center gap-2 pb-2 overflow-x-auto scrollbar-none">
                @foreach ($chartData['indicators'] as $key => $ind)
                    <button type="button" @click="setIndicator('{{ $key }}')"
                        :class="activeIndicator === '{{ $key }}' ? 'bg-primary text-white shadow-sm shadow-primary/30 ring-2 ring-primary/20' : 'bg-gray-100 dark:bg-meta-4/60 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-meta-4'"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 shrink-0">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0"
                            :style="activeIndicator === '{{ $key }}' ? 'background-color: #ffffff' : 'background-color: {{ $ind['color'] }}'"></span>
                        <span>{{ $ind['shortName'] }}</span>
                        <span class="text-[10px] opacity-80 font-normal">({{ $ind['targetLabel'] }})</span>
                    </button>
                @endforeach
                <button type="button" @click="setIndicator('all')"
                    :class="activeIndicator === 'all' ? 'bg-primary text-white shadow-sm shadow-primary/30 ring-2 ring-primary/20' : 'bg-gray-100 dark:bg-meta-4/60 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-meta-4'"
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
                <div class="p-3 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/50 dark:border-strokedark/50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Capaian Tahunan</p>
                    <p class="mt-1 text-lg font-black text-gray-800 sm:text-xl dark:text-white"
                        x-text="currentIndicator.yearly + currentIndicator.unit"></p>
                    <p class="text-[11px] text-gray-400 truncate" x-text="currentIndicator.description"></p>
                </div>

                {{-- Rata-rata Bulanan --}}
                <div class="p-3 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/50 dark:border-strokedark/50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rata-rata Bulanan</p>
                    <p class="mt-1 text-lg font-black text-primary sm:text-xl"
                        x-text="currentIndicator.avg + currentIndicator.unit"></p>
                    <p class="text-[11px] text-gray-400">12 Bulan Periode {{ $tahun }}</p>
                </div>

                {{-- Puncak & Titik Terendah --}}
                <div class="p-3 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/50 dark:border-strokedark/50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Fluktuasi (Max / Min)</p>
                    <p class="mt-1 text-sm font-bold text-gray-800 dark:text-white">
                        <span class="text-emerald-500 font-black">▲ <span x-text="currentIndicator.max.value"></span></span>
                        <span class="text-[11px] text-gray-400" x-text="'(' + currentIndicator.max.month + ')'"></span>
                    </p>
                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                        <span class="text-rose-500 font-black">▼ <span x-text="currentIndicator.min.value"></span></span>
                        <span class="text-[11px] text-gray-400" x-text="'(' + currentIndicator.min.month + ')'"></span>
                    </p>
                </div>

                {{-- Standar Depkes & Kepatuhan --}}
                <div class="p-3 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke/50 dark:border-strokedark/50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Standar Ideal Depkes</p>
                    <p class="mt-1 text-sm font-black text-gray-800 dark:text-white" x-text="currentIndicator.targetLabel"></p>
                    <div class="mt-1">
                        <span x-show="currentIndicator.isIdeal"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase tracking-tight">
                            <span class="icon-[solar--check-circle-bold] text-xs"></span>
                            Sesuai Standar
                        </span>
                        <span x-show="!currentIndicator.isIdeal"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 uppercase tracking-tight">
                            <span class="icon-[solar--danger-triangle-bold] text-xs"></span>
                            Perlu Evaluasi
                        </span>
                    </div>
                </div>
            </div>
        </template>

        {{-- Canvas Chart Container --}}
        <div class="relative w-full h-72 sm:h-84 lg:h-96 mt-2" wire:ignore>
            <canvas x-ref="chartCanvas"></canvas>
        </div>

        {{-- Chart Footer Note --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-4 pt-3.5 border-t border-stroke/60 dark:border-strokedark/60 text-xs text-gray-400">
            <div class="flex items-center gap-2">
                <span class="icon-[solar--info-circle-bold-duotone] text-base text-primary"></span>
                <span>Garis putus-putus pada grafik menandai ambang batas ideal Depkes RI. Arahkan kursor ke titik grafik untuk rincian data.</span>
            </div>
            <div class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                Formula: Standar Depkes RI / Barber-Johnson
            </div>
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

    @script
        <script>
            Alpine.data('indicatorMatrixChart', (chartData) => ({
                activeIndicator: 'bor',
                chartType: 'line',
                chart: null,
                chartData: chartData,

                get currentIndicator() {
                    return this.chartData.indicators[this.activeIndicator] || {};
                },

                init() {
                    this.$nextTick(() => {
                        this.renderChart();
                    });

                    // Update grid & label styling bila dark mode berganti
                    const observer = new MutationObserver(() => {
                        this.renderChart();
                    });
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
                },

                setIndicator(key) {
                    this.activeIndicator = key;
                    this.renderChart();
                },

                setChartType(type) {
                    this.chartType = type;
                    this.renderChart();
                },

                isDark() {
                    return document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
                },

                renderChart() {
                    const canvas = this.$refs.chartCanvas;
                    if (!canvas) return;

                    if (this.chart) {
                        try {
                            this.chart.destroy();
                        } catch (e) {}
                        this.chart = null;
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const isDark = this.isDark();
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                    const textColor = isDark ? '#94a3b8' : '#64748b';

                    let datasets = [];

                    if (this.activeIndicator === 'all') {
                        // Plot seluruh 6 indikator secara multivariat
                        const keys = ['bor', 'alos', 'bto', 'toi', 'ndr', 'gdr'];
                        datasets = keys.map(k => {
                            const ind = this.chartData.indicators[k];
                            return {
                                label: ind.shortName + ' (' + ind.unit.trim() + ')',
                                data: ind.data,
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
                        if (!ind) return;

                        // Dataset utama (realisasi bulanan)
                        let bgFill = ind.bgColor;
                        if (this.chartType === 'line') {
                            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                            gradient.addColorStop(0, ind.color + '4D'); // ~30%
                            gradient.addColorStop(1, ind.color + '00'); // 0%
                            bgFill = gradient;
                        }

                        datasets.push({
                            label: ind.name,
                            data: ind.data,
                            borderColor: ind.color,
                            backgroundColor: bgFill,
                            borderWidth: 3,
                            tension: 0.35,
                            pointBackgroundColor: ind.color,
                            pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            fill: this.chartType === 'line',
                            borderRadius: this.chartType === 'bar' ? 6 : 0,
                        });

                        // Garis referensi batas standar ideal Depkes (pada chart line)
                        if (this.chartType === 'line') {
                            if (ind.minTarget !== null && ind.minTarget > 0) {
                                datasets.push({
                                    label: 'Batas Bawah Ideal (' + ind.minTarget + ind.unit.trim() + ')',
                                    data: Array(12).fill(ind.minTarget),
                                    borderColor: isDark ? 'rgba(52, 211, 153, 0.75)' : 'rgba(16, 185, 129, 0.8)',
                                    borderWidth: 1.5,
                                    borderDash: [6, 4],
                                    pointRadius: 0,
                                    pointHoverRadius: 0,
                                    fill: false,
                                });
                            }
                            if (ind.maxTarget !== null && ind.maxTarget > 0) {
                                datasets.push({
                                    label: 'Batas Atas Ideal (' + ind.maxTarget + ind.unit.trim() + ')',
                                    data: Array(12).fill(ind.maxTarget),
                                    borderColor: isDark ? 'rgba(248, 113, 113, 0.75)' : 'rgba(239, 68, 68, 0.8)',
                                    borderWidth: 1.5,
                                    borderDash: [6, 4],
                                    pointRadius: 0,
                                    pointHoverRadius: 0,
                                    fill: false,
                                });
                            }
                        }
                    }

                    const self = this;
                    this.chart = new Chart(ctx, {
                        type: this.chartType,
                        data: {
                            labels: this.chartData.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 500,
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
                                            return (self.chartData.fullLabels && self.chartData.fullLabels[idx]) ? self.chartData.fullLabels[idx] + ' ' + @js($tahun) : items[0].label;
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
                }
            }));
        </script>
    @endscript
</x-content>
