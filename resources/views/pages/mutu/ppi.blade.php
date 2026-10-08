<x-content>
    <x-breadcrumb title="Surveilans PPI & HAIs" :items="[['title' => 'Mutu & Akreditasi'], ['title' => 'Surveilans PPI & HAIs']]" />

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="icon-[solar--virus-bold-duotone] text-teal-600 dark:text-teal-400 text-3xl"></span>
                <span>Surveilans PPI & Infeksi Nosokomial (HAIs)</span>
            </h1>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                Laju Kejadian ISK, Phlebitis, VAP, IDO per 1000 Hari Pemakaian Alat Medis & Audit Kepatuhan Bundles Pencegahan Infeksi
            </p>
        </div>

        <div class="flex items-center gap-2 no-print">
            <x-button color="default" icon="i-ph-file-pdf" wire:click="exportPdf" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf">Cetak PDF Data</span>
                <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
                    <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
                    <span>Menyiapkan PDF...</span>
                </span>
            </x-button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-4 mb-6 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--calendar-bold-duotone] text-teal-600 text-base"></span>
                    <span>Tahun:</span>
                </span>
                <select wire:model.live="selectedYear"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-teal-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5 pl-1">
                    <span class="icon-[solar--clock-circle-bold-duotone] text-teal-600 text-base"></span>
                    <span>Periode:</span>
                </span>
                <select wire:model.live="selectedMonth"
                    class="py-2 pl-3 pr-8 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-800 dark:text-white focus:border-teal-500 outline-none shadow-sm cursor-pointer">
                    @foreach ($months as $mKey => $mLabel)
                        <option value="{{ $mKey }}">{{ $mLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="text-xs font-bold text-teal-600 dark:text-teal-400 flex items-center gap-2">
            <span class="icon-[solar--shield-check-bold] text-base"></span>
            <span>Rata-rata Kepatuhan Bundle PPI: {{ $summary['kepatuhan_bundle_rata'] }}%</span>
        </div>
    </div>

    {{-- 4 Kartu Utama HAIs (ISK, PLEB, VAP, IDO) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($summary['indicators'] as $key => $hais)
            <div class="p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-700 dark:text-gray-300">
                            {{ strtoupper($key) }}
                        </span>
                        @if ($hais['is_safe'])
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Aman (Dalam Standar)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                Melebihi Batas
                            </span>
                        @endif
                    </div>

                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">
                        {{ $hais['nama'] }}
                    </h4>

                    <div class="flex items-baseline gap-1 my-2">
                        <span class="text-3xl font-black {{ $hais['is_safe'] ? 'text-teal-600 dark:text-teal-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $hais['rate'] }}
                        </span>
                        <span class="text-xs font-semibold text-gray-400">{{ $hais['satuan'] }}</span>
                    </div>

                    <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-1 border-t border-stroke dark:border-strokedark pt-2 mt-3">
                        <div class="flex justify-between">
                            <span>Kasus Infeksi:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $hais['kasus'] }} kasus</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Hari Rawat Alat:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ number_format($hais['hari_alat']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Standar Maksimal:</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $hais['standar'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-stroke dark:border-strokedark">
                    <div class="flex justify-between text-[11px] mb-1">
                        <span class="text-gray-500 font-semibold">Audit Bundle:</span>
                        <span class="font-bold text-teal-600 dark:text-teal-400">{{ $hais['bundle_kepatuhan'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-teal-500 h-full rounded-full" style="width: {{ $hais['bundle_kepatuhan'] }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Grafik Tren HAIs --}}
    <div class="mb-6 p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm no-print">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
            <span class="icon-[solar--graph-new-bold-duotone] text-teal-600 text-xl"></span>
            <span>Tren Bulanan Laju Infeksi Nosokomial (HAIs) Tahun {{ $selectedYear }}</span>
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
            Fluktuasi angka kejadian ISK, Phlebitis, dan IDO sepanjang tahun.
        </p>
        <div class="relative w-full h-[260px]">
            <canvas id="haisTrendChart"></canvas>
        </div>
    </div>

    {{-- Tabel Pasien Dalam Pemantauan Surveilans --}}
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-stroke dark:border-strokedark flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="icon-[solar--user-check-bold-duotone] text-teal-600 text-lg"></span>
                <span>Pasien Rawat Inap Dalam Pemantauan Alat & Surveilans PPI</span>
            </h3>
            <span class="text-xs text-gray-500">{{ count($patientList) }} Pasien Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-meta-4 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-stroke dark:border-strokedark">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">No. Rawat</th>
                        <th class="py-3 px-4">Nama Pasien</th>
                        <th class="py-3 px-4">Kamar / Bangsal</th>
                        <th class="py-3 px-4">Alat Terpasang</th>
                        <th class="py-3 px-4">Temuan Infeksi</th>
                        <th class="py-3 px-4">Antibiotik</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse ($patientList as $p)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-meta-4/30 transition-colors">
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $p['tanggal'] }}</td>
                            <td class="py-3 px-4 font-mono text-[11px]">{{ $p['no_rawat'] }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">{{ $p['pasien'] }}</td>
                            <td class="py-3 px-4">{{ $p['kamar'] }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">{{ $p['alat'] }}</td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $p['temuan_infeksi'] }}</td>
                            <td class="py-3 px-4 italic text-gray-500">{{ $p['antibiotik'] }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $p['status'] === 'Aman' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                                    {{ $p['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                Tidak ada data surveilans pasien untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script Chart --}}
    @script
        <script>
            let haisChartInstance = null;

            function initHaisChart(data) {
                const ctx = document.getElementById('haisTrendChart');
                if (!ctx) return;

                if (haisChartInstance) haisChartInstance.destroy();

                haisChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Phlebitis (‰)',
                                data: data.pleb,
                                backgroundColor: 'rgba(20, 184, 166, 0.7)',
                                borderRadius: 6,
                            },
                            {
                                label: 'ISK (‰)',
                                data: data.isk,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderRadius: 6,
                            },
                            {
                                label: 'IDO (%)',
                                data: data.ido,
                                backgroundColor: 'rgba(244, 63, 94, 0.7)',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            const initialHais = @json($trendChartData);
            setTimeout(() => { initHaisChart(initialHais); }, 100);

            Livewire.on('refresh-hais-chart', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                if (data) initHaisChart(data);
            });
        </script>
    @endscript
</x-content>
