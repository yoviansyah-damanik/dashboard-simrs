<div class="space-y-6">
    {{-- GRAFIK BARIS 1: COMBO TREN & DONUT KOMPOSISI --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Grafik Utama: Combo Bar & Target Line --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60"
            x-data="{
                chart: null,
                initChart() {
                    const ctx = document.getElementById('chart-tren-kepatuhan')?.getContext('2d');
                    if (!ctx) return;
                    if (this.chart) this.chart.destroy();
            
                    const isDark = document.documentElement.classList.contains('dark');
                    const textColor = isDark ? '#94A3B8' : '#64748B';
                    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
            
                    @if (!empty($dailyTrend)) const labels = @js(array_column($dailyTrend, 'day')).map(d => 'Tgl ' + d);
                        const jknData = @js(array_column($dailyTrend, 'jkn'));
                        const loketData = @js(array_column($dailyTrend, 'loket'));
                        const apmData = @js(array_column($dailyTrend, 'apm'));
                        const pctJknData = @js(array_column($dailyTrend, 'pct_jkn'));
                    @else
                        const labels = @js(array_column($monthlyReport, 'month_name'));
                        const jknData = @js(array_column($monthlyReport, 'jkn'));
                        const loketData = @js(array_column($monthlyReport, 'loket'));
                        const apmData = @js(array_column($monthlyReport, 'apm'));
                        const pctJknData = @js(array_column($monthlyReport, 'pct_jkn')); @endif
            
                    const targetLine = Array(labels.length).fill({{ $threshold }});
            
                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Target Threshold (' + {{ $threshold }} + '%)',
                                    data: targetLine,
                                    borderColor: 'rgba(239, 68, 68, 0.85)',
                                    borderWidth: 2,
                                    borderDash: [6, 4],
                                    pointRadius: 0,
                                    yAxisID: 'y1',
                                    order: 1
                                },
                                {
                                    type: 'line',
                                    label: '% Capaian JKN',
                                    data: pctJknData,
                                    borderColor: 'rgba(16, 185, 129, 1)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                                    tension: 0.3,
                                    yAxisID: 'y1',
                                    order: 2
                                },
                                {
                                    type: 'bar',
                                    label: 'JKN (Kode B)',
                                    data: jknData,
                                    backgroundColor: 'rgba(139, 92, 246, 0.85)',
                                    borderRadius: 6,
                                    yAxisID: 'y',
                                    order: 3
                                },
                                {
                                    type: 'bar',
                                    label: 'Loket (Kode A)',
                                    data: loketData,
                                    backgroundColor: 'rgba(2, 132, 199, 0.85)',
                                    borderRadius: 6,
                                    yAxisID: 'y',
                                    order: 4
                                },
                                {
                                    type: 'bar',
                                    label: 'APM (Kode D)',
                                    data: apmData,
                                    backgroundColor: 'rgba(13, 148, 136, 0.85)',
                                    borderRadius: 6,
                                    yAxisID: 'y',
                                    order: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: gridColor },
                                    ticks: { color: textColor, font: { size: 10 } },
                                    title: { display: true, text: 'Volume Pasien', color: textColor, font: { size: 10 } }
                                },
                                y1: {
                                    position: 'right',
                                    min: 0,
                                    max: 100,
                                    grid: { display: false },
                                    ticks: { color: textColor, callback: val => val + '%', font: { size: 10 } },
                                    title: { display: true, text: 'Persentase JKN (%)', color: textColor, font: { size: 10 } }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    labels: { boxWidth: 10, color: textColor, font: { size: 10, weight: 'bold' } }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(item) {
                                            if (item.dataset.yAxisID === 'y1') {
                                                return item.dataset.label + ': ' + item.raw + '%';
                                            }
                                            return item.dataset.label + ': ' + Number(item.raw).toLocaleString('id-ID') + ' pasien';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }" x-init="initChart();">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ !empty($dailyTrend) ? 'Tren Harian Pendaftaran Rawat Jalan' : 'Tren Bulanan Pendaftaran Rawat Jalan (' . $year . ')' }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Evaluasi volume kanal pendaftaran dan perbandingan persentase JKN terhadap target baku BPJS
                    </p>
                </div>
            </div>
            <div class="h-72 w-full">
                <canvas id="chart-tren-kepatuhan"></canvas>
            </div>
        </div>

        {{-- Donut Komposisi Pendaftaran --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60 flex flex-col justify-between"
            x-data="{
                chart: null,
                initChart() {
                    const ctx = document.getElementById('chart-donut-komposisi')?.getContext('2d');
                    if (!ctx) return;
                    if (this.chart) this.chart.destroy();
            
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Mobile JKN (B)', 'Loket Admisi (A)', 'APM Mandiri (D)'],
                            datasets: [{
                                data: [{{ $summary['total_jkn'] }}, {{ $summary['total_loket'] }}, {{ $summary['total_apm'] }}],
                                backgroundColor: [
                                    'rgba(139, 92, 246, 0.9)',
                                    'rgba(2, 132, 199, 0.9)',
                                    'rgba(13, 148, 136, 0.9)'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                }
            }" x-init="initChart();">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                    Komposisi Jalur Registrasi
                </h2>
                <p class="text-xs text-slate-500">
                    Proporsi pemanfaatan antrean daring vs loket
                </p>
            </div>

            <div class="relative my-4 flex items-center justify-center h-48">
                <canvas id="chart-donut-komposisi"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xs uppercase font-bold text-slate-400">Pemanfaatan JKN</span>
                    <span
                        class="text-2xl font-black {{ $summary['is_patuh'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $summary['pct_jkn'] }}%
                    </span>
                    <span class="text-[10px] font-semibold text-slate-400">Target:
                        {{ number_format($threshold, 1) }}%</span>
                </div>
            </div>

            <div class="space-y-2 text-xs">
                <div
                    class="flex items-center justify-between p-2 rounded-xl bg-violet-500/10 text-violet-800 dark:text-violet-300 font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-violet-500"></span>
                        <span>Mobile JKN (B)</span>
                    </div>
                    <span class="font-bold">{{ number_format($summary['total_jkn']) }}
                        ({{ $summary['pct_jkn'] }}%)</span>
                </div>
                <div
                    class="flex items-center justify-between p-2 rounded-xl bg-sky-500/10 text-sky-800 dark:text-sky-300 font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-sky-500"></span>
                        <span>Loket Admisi (A)</span>
                    </div>
                    <span class="font-bold">{{ number_format($summary['total_loket']) }}
                        ({{ $summary['pct_loket'] }}%)</span>
                </div>
                <div
                    class="flex items-center justify-between p-2 rounded-xl bg-teal-500/10 text-teal-800 dark:text-teal-300 font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-teal-500"></span>
                        <span>APM Mandiri (D)</span>
                    </div>
                    <span class="font-bold">{{ number_format($summary['total_apm']) }}
                        ({{ $summary['pct_apm'] }}%)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK BARIS 2: TOP 10 POLIKLINIK (STACKED BAR) --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/60"
        x-data="{
            chart: null,
            initChart() {
                const ctx = document.getElementById('chart-stacked-poli')?.getContext('2d');
                if (!ctx) return;
                if (this.chart) this.chart.destroy();
        
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#94A3B8' : '#64748B';
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
        
                @php
$topPoli = array_slice($polyclinicRows, 0, 10); @endphp
                const labels = @js(array_column($topPoli, 'nm_poli'));
                const jknData = @js(array_column($topPoli, 'jkn'));
                const loketData = @js(array_column($topPoli, 'loket'));
                const apmData = @js(array_column($topPoli, 'apm'));
        
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Mobile JKN (B)',
                                data: jknData,
                                backgroundColor: 'rgba(139, 92, 246, 0.85)',
                                borderRadius: 4
                            },
                            {
                                label: 'Loket Admisi (A)',
                                data: loketData,
                                backgroundColor: 'rgba(2, 132, 199, 0.85)',
                                borderRadius: 4
                            },
                            {
                                label: 'APM Mandiri (D)',
                                data: apmData,
                                backgroundColor: 'rgba(13, 148, 136, 0.85)',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true, grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                grid: { color: gridColor },
                                ticks: { color: textColor, font: { size: 10 } },
                                title: { display: true, text: 'Total Pendaftaran', color: textColor, font: { size: 10 } }
                            }
                        },
                        plugins: {
                            legend: { position: 'top', align: 'end', labels: { boxWidth: 10, color: textColor } }
                        }
                    }
                });
            }
        }" x-init="initChart();">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                    Top 10 Poliklinik Berdasarkan Volume & Kanal Pendaftaran
                </h2>
                <p class="text-xs text-slate-500">
                    Distribusi jalur registrasi pasien di poliklinik dengan kunjungan tertinggi
                </p>
            </div>
        </div>
        <div class="h-80 w-full">
            <canvas id="chart-stacked-poli"></canvas>
        </div>
    </div>
</div>
