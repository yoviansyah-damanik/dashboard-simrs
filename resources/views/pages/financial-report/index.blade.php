<x-content>
    <x-breadcrumb title="Laporan Keuangan" :items="[['title' => 'Laporan Keuangan']]" />

    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">
        Rekapitulasi pendapatan registrasi dari pasien dengan status pelayanan selain "Batal".
    </p>

    {{-- Filter Periode --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <select wire:model.live="period"
                class="appearance-none pl-10 pr-12 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary focus:ring-0 cursor-pointer outline-none transition-all shadow-sm">
                <option value="today">Hari Ini</option>
                <option value="last_7_days">7 Hari Lalu</option>
                <option value="last_30_days">30 Hari Lalu</option>
                <option value="this_week">Minggu Ini</option>
                <option value="this_month">Bulan Ini</option>
                <option value="this_year">Tahun Ini</option>
                <option value="monthly">Pilih Bulan</option>
                <option value="yearly">Pilih Tahun</option>
                <option value="custom">Custom</option>
            </select>
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                <span class="icon-[solar--calendar-minimalistic-bold] text-lg"></span>
            </div>
            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                <span class="icon-[solar--alt-arrow-down-bold-duotone] text-lg"></span>
            </div>
        </div>

        @if ($period === 'monthly')
            <div class="flex items-center gap-2">
                <select wire:model.live="selectedMonth"
                    class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                    @foreach ($this->months as $index => $name)
                        <option value="{{ $index }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedYear"
                    class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                    @foreach ($this->years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        @elseif($period === 'yearly')
            <select wire:model.live="selectedYear"
                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                @foreach ($this->years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        @elseif($period === 'custom')
            <div
                class="flex items-center gap-2 px-4 py-1 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark shadow-sm">
                <input type="date" wire:model.live="startDate"
                    class="bg-transparent border-none focus:ring-0 text-sm font-bold cursor-pointer text-gray-700 dark:text-white" />
                <span class="text-gray-300 font-bold">/</span>
                <input type="date" wire:model.live="endDate"
                    class="bg-transparent border-none focus:ring-0 text-sm font-bold cursor-pointer text-gray-700 dark:text-white" />
            </div>
        @endif
    </div>

    {{-- Ringkasan Utama --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div
            class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="icon-[solar--wallet-money-bold-duotone] text-3xl"></span>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pendapatan Registrasi</p>
                <p class="text-2xl font-black text-gray-800 dark:text-white">
                    Rp {{ number_format($this->summary['total_pendapatan'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="icon-[solar--users-group-rounded-bold-duotone] text-3xl"></span>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pasien (Bukan Batal)</p>
                <p class="text-2xl font-black text-gray-800 dark:text-white">
                    {{ number_format($this->summary['total_pasien'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Rincian per Jenis Kunjungan --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        @php
            $visitTypes = [
                [
                    'key' => 'rawat_jalan',
                    'label' => 'Rawat Jalan',
                    'icon' => 'icon-[solar--stethoscope-bold-duotone]',
                    'color' => 'text-cyan-600 bg-cyan-500/10',
                ],
                [
                    'key' => 'rawat_inap',
                    'label' => 'Rawat Inap',
                    'icon' => 'icon-[solar--bed-bold-duotone]',
                    'color' => 'text-violet-600 bg-violet-500/10',
                ],
                [
                    'key' => 'igd',
                    'label' => 'IGD',
                    'icon' => 'icon-[solar--health-bold-duotone]',
                    'color' => 'text-rose-600 bg-rose-500/10',
                ],
            ];
        @endphp

        @foreach ($visitTypes as $visit)
            <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center {{ $visit['color'] }}">
                        <span class="{{ $visit['icon'] }} text-xl"></span>
                    </div>
                    <p class="text-xs font-black text-gray-700 dark:text-white uppercase tracking-widest">
                        {{ $visit['label'] }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pendapatan Registrasi</p>
                    <p class="text-lg font-black text-gray-800 dark:text-white">
                        Rp {{ number_format($this->summary[$visit['key']]['total_pendapatan'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="icon-[solar--user-bold-duotone] text-base"></span>
                    <span class="font-bold">{{ number_format($this->summary[$visit['key']]['jumlah_pasien'], 0, ',', '.') }}</span>
                    <span>pasien</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Status Pembayaran --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div
            class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm p-6 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="icon-[solar--check-circle-bold-duotone] text-2xl"></span>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sudah Bayar</p>
                <p class="text-xl font-black text-gray-800 dark:text-white">
                    {{ number_format($this->summary['status_bayar']['sudah_bayar'], 0, ',', '.') }} pasien
                </p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm p-6 flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600 shrink-0">
                <span class="icon-[solar--clock-circle-bold-duotone] text-2xl"></span>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum Bayar</p>
                <p class="text-xl font-black text-gray-800 dark:text-white">
                    {{ number_format($this->summary['status_bayar']['belum_bayar'], 0, ',', '.') }} pasien
                </p>
            </div>
        </div>
    </div>
</x-content>
