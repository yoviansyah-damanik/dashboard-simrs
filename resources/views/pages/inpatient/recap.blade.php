<x-content>
    <x-breadcrumb title="Rawat Inap" :items="[['title' => 'Rawat Inap'], ['title' => 'Rekap']]" />

    <div class="space-y-6">
        <!-- Tab Navigation -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Monitoring pasien dan rekapitulasi okupansi secara real-time.</p>
                </div>

            <!-- Main Tabs -->
            <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-2xl">
                <button wire:click="switchTab('current_patients')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $mainTab === 'current_patients' ? 'bg-white dark:bg-boxdark shadow-lg text-primary' : 'text-gray-500 hover:text-gray-700' }}">
                    <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-lg"></span>
                    Pasien Dirawat
                </button>
                <button wire:click="switchTab('recap')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $mainTab === 'recap' ? 'bg-white dark:bg-boxdark shadow-lg text-primary' : 'text-gray-500 hover:text-gray-700' }}">
                    <span class="icon-[solar--folder-with-files-bold-duotone] text-lg"></span>
                    Rekapitulasi
                </button>
                <button wire:click="switchTab('snapshot')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $mainTab === 'snapshot' ? 'bg-white dark:bg-boxdark shadow-lg text-primary' : 'text-gray-500 hover:text-gray-700' }}">
                    <span class="icon-[solar--screencast-2-bold-duotone] text-lg"></span>
                    Snapshot Bed
                </button>
            </div>
        </div>

        <!-- Sub-Header for Controls -->
        <div
            class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stroke dark:border-strokedark">
            <div class="flex items-center gap-4">
                @if ($mainTab === 'recap')
                    <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl">
                        <button wire:click="$set('mainView', 'list')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $mainView === 'list' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--list-bold-duotone] text-lg"></span>
                            List
                        </button>
                        <button wire:click="$set('mainView', 'chart')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $mainView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            Grafik
                        </button>
                    </div>
                @endif

                @if ($mainTab === 'current_patients')
                    <div class="relative min-w-[300px]">
                        <input type="text" wire:model.live.debounce.300ms="searchPatient"
                            placeholder="Cari Nama Pasien / No. RM..."
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm focus:border-primary focus:ring-0 outline-none transition-all shadow-sm">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <span class="icon-[solar--magnifer-bold-duotone] text-xl"></span>
                        </div>
                    </div>
                @endif

                @if ($mainTab === 'snapshot')
                    <div class="flex p-1 bg-gray-100 dark:bg-meta-4 rounded-xl">
                        <button wire:click="$set('snapshotView', 'list')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $snapshotView === 'list' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--widget-bold-duotone] text-lg"></span>
                            List
                        </button>
                        <button wire:click="$set('snapshotView', 'chart')"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $snapshotView === 'chart' ? 'bg-white dark:bg-boxdark shadow-sm text-primary' : 'text-gray-500' }}">
                            <span class="icon-[solar--chart-bold-duotone] text-lg"></span>
                            Grafik
                        </button>
                    </div>
                @endif
            </div>

            @if ($mainTab === 'recap')
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <select wire:model.live="period"
                            class="appearance-none pl-10 pr-10 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary focus:ring-0 cursor-pointer outline-none transition-all">
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
                                class="px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary outline-none">
                                @foreach ($months as $index => $name)
                                    <option value="{{ $index }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="selectedYear"
                                class="px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary outline-none">
                                @foreach ($years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period === 'yearly')
                        <select wire:model.live="selectedYear"
                            class="px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark text-sm font-medium focus:border-primary outline-none">
                            @foreach ($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    @elseif($period === 'custom')
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-stroke rounded-lg dark:bg-boxdark dark:border-strokedark shadow-sm">
                            <input type="date" wire:model.live="startDate"
                                class="bg-transparent border-none focus:ring-0 text-sm font-medium cursor-pointer" />
                            <span class="text-gray-300 font-bold">/</span>
                            <input type="date" wire:model.live="endDate"
                                class="bg-transparent border-none focus:ring-0 text-sm font-medium cursor-pointer" />
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if ($mainTab === 'current_patients')
        <!-- Tab: Pasien Dirawat -->
        <div wire:key="tab-current-patients" class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-primary/10 text-primary rounded-xl">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Total Pasien Dirawat Saat Ini</h3>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-0.5">Real-time Census</p>
                    </div>
                </div>
                <div class="text-right">
                    <span
                        class="text-4xl font-black text-primary">{{ number_format($currentPatients->count()) }}</span>
                    <span class="text-sm font-bold text-gray-400 block uppercase tracking-tighter">Pasien Aktif</span>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl border border-stroke shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-meta-4">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest">Pasien</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">No.
                                    RM</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Kamar
                                    / Bangsal</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Kelas
                                </th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">
                                    Penjamin</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Tgl
                                    Masuk</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-center">Lama
                                    Rawat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke dark:divide-strokedark">
                            @forelse($currentPatients as $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-primary transition-colors">{{ $p->nm_pasien }}</span>
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase">{{ $p->no_rawat }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="px-2 py-1 bg-gray-100 dark:bg-meta-4 rounded text-xs font-black text-gray-600 dark:text-gray-300">{{ $p->no_rkm_medis }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-black text-gray-700 dark:text-gray-200">{{ $p->nm_bangsal }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-xs font-bold text-indigo-600">{{ $p->kelas }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="text-[10px] font-black text-emerald-600 uppercase">{{ $p->png_jawab }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-bold">{{ Carbon\Carbon::parse($p->tgl_masuk)->format('d/m/Y') }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $p->jam_masuk }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $p->lama_inap > 5 ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }}">
                                            {{ $p->lama_inap }} Hari
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada pasien yang ditemukan dalam daftar rawat inap saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($mainTab === 'recap')
        <!-- Tab: Statistik & Rekap -->
        <div wire:key="tab-recap" class="space-y-8">
            <!-- Row 1: Primary KPIs & Trends -->
            <div class="grid grid-cols-1 gap-6 {{ $mainView === 'chart' ? 'lg:grid-cols-3' : '' }}">
                <div
                    class="relative overflow-hidden p-6 bg-gradient-to-br from-violet-600 to-indigo-800 rounded-3xl shadow-xl border border-white/10 {{ $mainView === 'list' ? 'grid grid-cols-1 md:grid-cols-12 gap-8' : 'flex flex-col justify-between min-h-[300px]' }}">
                    <!-- Background Decoration -->
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <span class="icon-[solar--users-group-rounded-bold] text-[120px] text-white"></span>
                    </div>

                    @if ($mainView === 'list')
                        <!-- Total Patients & Gender Breakdown -->
                        <div class="relative z-10 text-white flex flex-col justify-center md:col-span-3">
                            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-violet-200 mb-2">Total
                                Pasien</p>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-6xl font-black leading-none">
                                    {{ number_format($recapData->sum('total_pasien')) }}</h3>
                                <span class="text-sm font-bold text-violet-300">Jiwa</span>
                            </div>
                            <div class="flex items-center gap-3 mt-6">
                                <div class="flex flex-col">
                                    <span
                                        class="text-[9px] font-black text-violet-300 uppercase tracking-widest">Laki-laki</span>
                                    <span
                                        class="text-xl font-black text-blue-300">{{ number_format($recapData->sum('total_laki')) }}</span>
                                </div>
                                <div class="w-px h-8 bg-white/20"></div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[9px] font-black text-violet-300 uppercase tracking-widest">Perempuan</span>
                                    <span
                                        class="text-xl font-black text-pink-300">{{ number_format($recapData->sum('total_perempuan')) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Age Group Breakdown Redesigned -->
                        <div class="relative z-10 text-white md:col-span-6 border-x border-white/10 px-8">
                            <p
                                class="text-[11px] font-black uppercase tracking-[0.2em] text-violet-200 mb-6 text-center">
                                Rincian Kelompok Usia & Gender</p>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($demographics['age'] as $age)
                                    <div
                                        class="flex flex-col p-3 bg-white/10 rounded-2xl border border-white/10 shadow-sm backdrop-blur-sm group hover:bg-white/20 transition-all">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-[9px] font-black text-violet-100 uppercase tracking-tighter truncate w-3/4">{{ str_replace('Pasien ', '', $age->kelompok_umur) }}</span>
                                            <span
                                                class="text-xs font-black bg-white/20 px-1.5 py-0.5 rounded-md">{{ number_format($age->total) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                                            <div class="flex-1 flex items-center gap-1 justify-center">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                                <span
                                                    class="text-[10px] font-black text-blue-200">{{ number_format($age->laki) }}</span>
                                            </div>
                                            <div class="flex-1 flex items-center gap-1 justify-center">
                                                <span class="w-1.5 h-1.5 rounded-full bg-pink-400"></span>
                                                <span
                                                    class="text-[10px] font-black text-pink-200">{{ number_format($age->perempuan) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Total Maintenance Days -->
                        <div class="relative z-10 text-white flex flex-col justify-center items-center md:col-span-3">
                            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-violet-200 mb-2">Hari
                                Perawatan</p>
                            <h3 class="text-5xl font-black leading-none">
                                {{ number_format($recapData->sum('total_hp')) }}</h3>
                            <div class="mt-4 px-4 py-1.5 bg-white/10 rounded-full border border-white/10">
                                <span class="text-[10px] font-black text-violet-100 uppercase tracking-[0.1em]">Total
                                    HP Periode</span>
                            </div>
                        </div>
                    @else
                        <!-- Compact View for Chart Mode -->
                        <div class="relative z-10 text-white">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-violet-100">Total Pasien
                                (Periode)</p>
                            <h3 class="text-5xl font-black mt-2">{{ number_format($recapData->sum('total_pasien')) }}
                            </h3>
                            <div class="flex items-center gap-6 mt-6">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                    <span class="text-sm font-black">L:
                                        {{ number_format($recapData->sum('total_laki')) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-pink-400"></span>
                                    <span class="text-sm font-black">P:
                                        {{ number_format($recapData->sum('total_perempuan')) }}</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="mt-auto pt-6 border-t border-white/10 flex items-center justify-between text-white">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-violet-200 uppercase tracking-widest">Total
                                    Hari Perawatan</span>
                                <span class="text-2xl font-black">{{ number_format($recapData->sum('total_hp')) }}
                                    HP</span>
                            </div>
                            <span class="icon-[solar--users-group-rounded-bold] text-5xl opacity-40"></span>
                        </div>
                    @endif
                </div>

                <!-- Trend Admission/Discharge - ONLY IN CHART MODE -->
                @if ($mainView === 'chart')
                    <div
                        class="lg:col-span-2 bg-white dark:bg-boxdark p-6 rounded-3xl border border-stroke dark:border-strokedark shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h4
                                class="text-sm font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                                <span class="icon-[solar--graph-up-bold-duotone] text-lg text-primary"></span>
                                Tren Pasien Masuk & Keluar
                            </h4>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded-full bg-indigo-600"></span><span
                                        class="text-[10px] font-bold text-gray-500 uppercase">Masuk</span></div>
                                <div class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded-full bg-emerald-500"></span><span
                                        class="text-[10px] font-bold text-gray-500 uppercase">Keluar</span></div>
                            </div>
                        </div>
                        <div class="h-[200px]" wire:ignore wire:key="chart-recap-trend">
                            <x-chart chartId="chartTrendInOut" chartType="line" :labels="$overall['charts']['trend']['labels']" :datasets="$overall['charts']['trend']['datasets']" />
                        </div>
                    </div>
                @endif
            </div>

            <!-- Row 2: Summary Stats (Divided into 2 Groups) -->
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
                <!-- Group 1: Volume Pasien (3 Cards) -->
                <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Admissions Period -->
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-indigo-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Masuk
                            (Periode)</span>
                        <div class="flex items-center gap-2">
                            <h4 class="text-2xl font-black text-indigo-600">
                                {{ number_format($snapshotStats['admissions']) }}</h4>
                            <span class="icon-[solar--user-plus-bold-duotone] text-lg text-indigo-400"></span>
                        </div>
                    </div>

                    <!-- Discharges Period -->
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Keluar
                            (Periode)</span>
                        <div class="flex items-center gap-2">
                            <h4 class="text-2xl font-black text-emerald-600">
                                {{ number_format($snapshotStats['discharges']) }}</h4>
                            <span class="icon-[solar--user-check-bold-duotone] text-lg text-emerald-400"></span>
                        </div>
                    </div>

                    <!-- TNI Patients (Period) -->
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-red-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pasien Dinas
                            (Periode)</span>
                        <div class="flex items-center gap-2">
                            <h4 class="text-2xl font-black text-red-600">
                                {{ number_format($snapshotStats['tni_patients']) }}</h4>
                            <span class="icon-[solar--medal-ribbon-star-bold-duotone] text-lg text-red-400"></span>
                        </div>
                    </div>
                </div>

                <!-- Group 2: Performance Indicators (4 Cards) -->
                <div class="lg:col-span-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-emerald-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">BOR</span>
                        <h4 class="text-2xl font-black text-emerald-600">{{ number_format($overall['bor'], 1) }}%</h4>
                        <p class="text-[8px] font-bold text-gray-400 mt-1 uppercase">Occupancy</p>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-indigo-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">ALOS</span>
                        <h4 class="text-2xl font-black text-indigo-600">{{ number_format($overall['alos'], 1) }}</h4>
                        <p class="text-[8px] font-bold text-gray-400 mt-1 uppercase">Avg Stay</p>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-amber-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">BTO</span>
                        <h4 class="text-2xl font-black text-amber-600">{{ number_format($overall['bto'], 1) }}</h4>
                        <p class="text-[8px] font-bold text-gray-400 mt-1 uppercase">Turnover</p>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col items-center text-center group hover:border-red-500 transition-all">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">GDR</span>
                        <h4 class="text-2xl font-black text-red-600">{{ number_format($overall['gdr'], 1) }}</h4>
                        <p class="text-[8px] font-bold text-gray-400 mt-1 uppercase">Death Rate</p>
                    </div>
                </div>
            </div>

            <!-- Row 3: Ward Breakdown -->
            <div class="space-y-4">
                @if ($mainView === 'list')
                    <div
                        class="bg-white rounded-2xl border border-stroke shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[1200px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-meta-4">
                                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Bangsal
                                        </th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center">
                                            Kelas</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center bg-gray-100 dark:bg-meta-4/50">
                                            TT</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center text-primary">
                                            Terisi</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center bg-blue-50 dark:bg-blue-900/10">
                                            Total</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center text-blue-600">
                                            L</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center text-pink-600">
                                            P</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center text-emerald-600">
                                            Pulang</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center text-amber-600">
                                            Rujuk</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center">
                                            APS</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center text-red-600">
                                            Mati</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center">
                                            HP</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center bg-purple-50 dark:bg-purple-900/10">
                                            ALOS</th>
                                        <th
                                            class="px-3 py-4 text-[10px] font-black uppercase tracking-widest text-center bg-green-50 dark:bg-green-900/10">
                                            BOR (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                    @php $diffDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1; @endphp
                                    @forelse($recapData as $item)
                                        @php $bor = $item->kapasitas > 0 && $diffDays > 0 ? ($item->total_hp / ($item->kapasitas * $diffDays)) * 100 : 0; @endphp
                                        <tr
                                            class="hover:bg-gray-50 dark:hover:bg-meta-4/20 transition-colors text-[13px]">
                                            <td class="px-4 py-4 font-bold text-gray-700 dark:text-gray-300">
                                                {{ $item->nm_bangsal }}</td>
                                            <td class="px-3 py-4 text-center"><span
                                                    class="px-2 py-0.5 text-[9px] font-black rounded-md bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-400">{{ $item->kelas }}</span>
                                            </td>
                                            <td class="px-3 py-4 text-center font-black bg-gray-50 dark:bg-meta-4/30">
                                                {{ number_format($item->kapasitas) }}</td>
                                            <td class="px-3 py-4 text-center text-indigo-600 font-black">
                                                {{ number_format($item->terisi) }}</td>
                                            <td
                                                class="px-3 py-4 text-center bg-blue-50/30 dark:bg-blue-900/5 font-black">
                                                {{ number_format($item->total_pasien) }}</td>
                                            <td class="px-3 py-4 text-center text-blue-500 font-bold">
                                                {{ number_format($item->total_laki) }}</td>
                                            <td class="px-3 py-4 text-center text-pink-500 font-bold">
                                                {{ number_format($item->total_perempuan) }}</td>
                                            <td class="px-3 py-4 text-center text-emerald-600 font-bold">
                                                {{ number_format($item->jumlah_pulang) }}</td>
                                            <td class="px-3 py-4 text-center text-amber-600 font-bold">
                                                {{ number_format($item->jumlah_dirujuk) }}</td>
                                            <td class="px-3 py-4 text-center text-gray-500 font-bold">
                                                {{ number_format($item->jumlah_aps) }}</td>
                                            <td class="px-3 py-4 text-center text-red-600 font-bold">
                                                {{ number_format($item->jumlah_meninggal) }}</td>
                                            <td class="px-3 py-4 text-center font-bold text-gray-400">
                                                {{ number_format($item->total_hp) }}</td>
                                            <td
                                                class="px-3 py-4 text-center font-black bg-purple-50/50 dark:bg-purple-900/5 text-purple-600">
                                                {{ number_format($item->rata_lama_hari, 1) }}</td>
                                            <td
                                                class="px-3 py-4 text-center font-black bg-green-50/50 dark:bg-green-900/5 {{ $bor > 85 ? 'text-red-500' : ($bor < 60 ? 'text-orange-500' : 'text-emerald-600') }}">
                                                {{ number_format($bor, 1) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="px-4 py-10 text-center text-gray-500">Tidak ada
                                                data rekapitulasi untuk periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--user-bold-duotone] text-lg text-primary"></span>Perbandingan
                            Total Pasien per Bangsal
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-recap-wards"><x-chart
                                chartId="mainChartWards" chartType="bar" barType="x" :labels="$overall['charts']['wards_patients']['labels']"
                                :datasets="$overall['charts']['wards_patients']['datasets']" /></div>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--chart-square-bold-duotone] text-lg text-primary"></span>Rata-rata
                            BOR per Bangsal (%)
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-recap-bor"><x-chart chartId="mainChartBOR"
                                chartType="bar" barType="x" :labels="$overall['charts']['wards_bor']['labels']" :datasets="$overall['charts']['wards_bor']['datasets']" /></div>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--clock-circle-bold-duotone] text-lg text-primary"></span>Rata-rata
                            Lama Hari (ALOS) per Bangsal
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-recap-alos"><x-chart
                                chartId="mainChartALOS" chartType="bar" barType="x" :labels="$overall['charts']['wards_alos']['labels']"
                                :datasets="$overall['charts']['wards_alos']['datasets']" /></div>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--refresh-bold-duotone] text-lg text-primary"></span>Bed Turn Over (BTO) per Bangsal
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-recap-bto"><x-chart
                                chartId="mainChartBTO" chartType="bar" barType="x" :labels="$overall['charts']['wards_bto']['labels']"
                                :datasets="$overall['charts']['wards_bto']['datasets']" /></div>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--danger-bold-duotone] text-lg text-primary"></span>Gross Death Rate (GDR) per Bangsal
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-recap-gdr"><x-chart
                                chartId="mainChartGDR" chartType="bar" barType="x" :labels="$overall['charts']['wards_gdr']['labels']"
                                :datasets="$overall['charts']['wards_gdr']['datasets']" /></div>
                    </div>
                @endif
            </div>

            <!-- Row 4: Demographic & Discharge Analysis - ONLY IN CHART MODE -->
            @if ($mainView === 'chart')
                <div
                    class="bg-gray-50/50 dark:bg-meta-4/5 p-6 rounded-3xl border border-stroke dark:border-strokedark space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-primary/10 text-primary rounded-lg">
                            <span class="icon-[solar--chart-bold-duotone] text-xl"></span>
                        </div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-widest">Analisis
                            Demografi & Kepulangan</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 text-center">
                                Jenis Kelamin</h4>
                            <div class="h-48 flex items-center justify-center" wire:ignore
                                wire:key="chart-main-gender">
                                <x-chart chartId="chartGender" chartType="doughnut" :labels="$demographics['charts']['gender']['labels']"
                                    :datasets="$demographics['charts']['gender']['datasets']" />
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 text-center">
                                Status Kepulangan</h4>
                            <div class="h-48 flex items-center justify-center" wire:ignore
                                wire:key="chart-main-discharge">
                                <x-chart chartId="chartDischarge" chartType="pie" :labels="$demographics['charts']['discharge']['labels']"
                                    :datasets="$demographics['charts']['discharge']['datasets']" />
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 text-center">
                                Kelompok Umur</h4>
                            <div class="h-64" wire:ignore wire:key="chart-main-age">
                                <x-chart chartId="chartAge" chartType="bar" barType="x" :labels="$demographics['charts']['age']['labels']"
                                    :datasets="$demographics['charts']['age']['datasets']" />
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 text-center">
                                Top Jenis Bayar</h4>
                            <div class="h-64" wire:ignore wire:key="chart-main-ins">
                                <x-chart chartId="chartInsurance" chartType="bar" barType="y" :labels="$demographics['charts']['insurance']['labels']"
                                    :datasets="$demographics['charts']['insurance']['datasets']" />
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @elseif($mainTab === 'snapshot')
        <!-- Tab: Snapshot Okupansi Real-time -->
        <div wire:key="tab-snapshot" class="space-y-8">
            <!-- Snapshot Header Stats Redesigned -->
            <div
                class="bg-gradient-to-br from-emerald-500 to-teal-700 rounded-3xl shadow-xl border border-white/10 overflow-hidden relative group">
                <!-- Background Icon -->
                <span class="absolute right-0 bottom-0 p-8 opacity-10 pointer-events-none">
                    <span class="icon-[solar--bed-bold] text-[180px] text-white"></span>
                </span>

                <div class="p-8 relative z-10 flex flex-col lg:flex-row gap-8">
                    <!-- Left Section: Overall Summary -->
                    <div class="flex-shrink-0 flex flex-col justify-center border-r border-white/10 pr-12">
                        <p class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-50 mb-3">Kapasitas Bed
                            Keseluruhan</p>
                        <div class="flex items-baseline gap-3">
                            <h3 class="text-7xl font-black text-white leading-none">
                                {{ number_format($snapshotStats['occupied']) }}</h3>
                            <span class="text-2xl font-bold text-emerald-200/60">/
                                {{ number_format($snapshotStats['total_bed']) }}</span>
                        </div>
                        <div class="mt-6">
                            <span
                                class="px-5 py-2 bg-white/20 backdrop-blur-md rounded-2xl text-xs font-black text-white uppercase tracking-widest border border-white/10 inline-flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                {{ number_format($snapshotStats['available']) }} Bed Kosong Saat Ini
                            </span>
                        </div>
                    </div>

                    <!-- Right Section: Class Breakdown -->
                    <div class="flex-1">
                        <p class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-50 mb-6">Rekapitulasi
                            Okupansi Per Kelas</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($realtimeClassStats as $stat)
                                @php
                                    $occ = $stat->kapasitas > 0 ? ($stat->terisi / $stat->kapasitas) * 100 : 0;
                                @endphp
                                <div
                                    class="bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/10 flex flex-col group hover:bg-white/20 transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <span
                                            class="text-[10px] font-black text-emerald-100 uppercase tracking-widest">{{ $stat->kelas }}</span>
                                        <span
                                            class="text-[10px] font-black text-white bg-emerald-600/50 px-2 py-0.5 rounded-full">{{ number_format($occ, 0) }}%</span>
                                    </div>
                                    <div class="flex items-baseline gap-1 mt-1">
                                        <span
                                            class="text-2xl font-black text-white">{{ number_format($stat->terisi) }}</span>
                                        <span class="text-xs font-bold text-emerald-200/50">/
                                            {{ number_format($stat->kapasitas) }}</span>
                                    </div>
                                    <!-- Mini Progress Bar -->
                                    <div class="mt-4 w-full bg-white/10 h-1 rounded-full overflow-hidden">
                                        <div class="bg-emerald-300 h-full rounded-full transition-all duration-1000"
                                            style="width: {{ $occ }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Snapshot Views -->
            @if ($snapshotView === 'list')
                <div class="space-y-8">
                    @foreach ($recapData->groupBy('kelas') as $kelas => $bangsals)
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 px-2">
                                <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                                <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                    KATEGORI: {{ $kelas }}</h3>
                                <div class="flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-meta-4 rounded-full">
                                    <span class="text-[10px] font-bold text-gray-500">Total Bed:
                                        {{ number_format($bangsals->sum('kapasitas')) }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-[10px] font-bold text-primary">Terisi:
                                        {{ number_format($bangsals->sum('terisi')) }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach ($bangsals as $b)
                                    @php
                                        $occPercent = $b->kapasitas > 0 ? ($b->terisi / $b->kapasitas) * 100 : 0;
                                        $statusColor = match (true) {
                                            $occPercent >= 90 => 'text-red-600 bg-red-50 border-red-100',
                                            $occPercent >= 70 => 'text-orange-600 bg-orange-50 border-orange-100',
                                            $occPercent >= 40 => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                                            default => 'text-blue-600 bg-blue-50 border-blue-100',
                                        };
                                        $barColor = match (true) {
                                            $occPercent >= 90
                                                => 'bg-gradient-to-r from-red-500 to-rose-600 shadow-[0_0_10px_rgba(244,63,94,0.3)]',
                                            $occPercent >= 70
                                                => 'bg-gradient-to-r from-orange-400 to-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.2)]',
                                            $occPercent >= 40
                                                => 'bg-gradient-to-r from-emerald-400 to-teal-500 shadow-[0_0_10px_rgba(16,185,129,0.2)]',
                                            default
                                                => 'bg-gradient-to-r from-blue-500 to-indigo-600 shadow-[0_0_10px_rgba(59,130,246,0.2)]',
                                        };
                                    @endphp
                                    <div
                                        class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden flex flex-col group hover:border-primary transition-all hover:shadow-md">
                                        <div
                                            class="p-4 border-b border-stroke dark:border-strokedark flex items-center justify-between">
                                            <h4
                                                class="text-[10px] font-black uppercase tracking-widest text-gray-700 dark:text-gray-200 truncate pr-2">
                                                {{ $b->nm_bangsal }}</h4>
                                            <span
                                                class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $statusColor }} {{ $occPercent >= 90 ? 'animate-pulse' : '' }}">{{ number_format($occPercent, 0) }}%</span>
                                        </div>
                                        <div class="p-5 flex-1 space-y-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex flex-col"><span
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Terisi</span><span
                                                        class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($b->terisi) }}</span>
                                                </div>
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 dark:bg-meta-4/30">
                                                    <span
                                                        class="icon-[solar--bed-bold-duotone] text-xl text-gray-400"></span>
                                                </div>
                                                <div class="flex flex-col text-right"><span
                                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Kapasitas</span><span
                                                        class="text-2xl font-black text-gray-300 dark:text-gray-500">{{ number_format($b->kapasitas) }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="relative w-full bg-gray-100 dark:bg-meta-4 h-3.5 rounded-full overflow-hidden shadow-inner">
                                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $barColor }}"
                                                    style="width: {{ $occPercent }}%">
                                                    <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite] pointer-events-none"
                                                        style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transform: skewX(-20deg);">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="space-y-6">
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--pie-chart-bold-duotone] text-lg text-primary"></span>Okupansi
                            per Kategori Kelas
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-snap-class"><x-chart
                                chartId="chartSnapshotClass" chartType="bar" barType="x" :labels="$snapshotCharts['class_occupancy']['labels']"
                                :datasets="$snapshotCharts['class_occupancy']['datasets']" /></div>
                    </div>
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                        <h4
                            class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                            <span class="icon-[solar--graph-bold-duotone] text-lg text-primary"></span>Okupansi
                            Real-time per Bangsal (%)
                        </h4>
                        <div class="h-96" wire:ignore wire:key="chart-snap-ward"><x-chart
                                chartId="chartSnapshotWard" chartType="bar" barType="x" :labels="$snapshotCharts['ward_occupancy']['labels']"
                                :datasets="$snapshotCharts['ward_occupancy']['datasets']" /></div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Modals -->
    <x-modal name="class-modal" size="2xl" modalTitle="Okupansi Per Kelas">
        <div class="p-4 max-h-[60vh] overflow-y-auto bg-gray-50/50 dark:bg-meta-4/5">
            <div class="grid grid-cols-1 gap-2">
                @foreach ($realtimeClassStats as $stat)
                    @php
                        $occ = $stat->kapasitas > 0 ? ($stat->terisi / $stat->kapasitas) * 100 : 0;
                        $barColor = match (true) {
                            $occ > 90 => 'bg-red-500',
                            $occ > 70 => 'bg-orange-500',
                            $occ > 40 => 'bg-emerald-500',
                            default => 'bg-blue-600',
                        };
                        $badgeColor = match (true) {
                            $occ > 90 => 'text-red-600 bg-red-50 dark:bg-red-900/10',
                            $occ > 70 => 'text-orange-600 bg-orange-50 dark:bg-orange-900/10',
                            $occ > 40 => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/10',
                            default => 'text-blue-600 bg-blue-50 dark:bg-blue-900/10',
                        };
                    @endphp
                    <div
                        class="flex items-center justify-between p-3.5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm group hover:border-emerald-500 transition-all">
                        <div class="flex items-center gap-4 flex-1">
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-xl font-black text-xs {{ $badgeColor }}">
                                {{ number_format($occ, 0) }}%</div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1"><span
                                        class="text-xs font-black text-gray-700 dark:text-gray-200 uppercase tracking-wider">{{ $stat->kelas }}</span><span
                                        class="text-[11px] font-bold text-gray-400"><span
                                            class="text-gray-700 dark:text-gray-200">{{ number_format($stat->terisi) }}</span>
                                        / {{ number_format($stat->kapasitas) }} Bed</span></div>
                                <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-1000"
                                        style="width: {{ $occ }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>

    @script
        <script>
            Alpine.data('chartComponent', (chartId, chartType, barType, initialLabels, initialDatasets) => ({
                chart: null,
                _updateHandler: null,
                _initTimeout: null,
                init() {
                    this.initChart({
                        labels: JSON.parse(JSON.stringify(initialLabels)),
                        datasets: JSON.parse(JSON.stringify(initialDatasets))
                    });

                    this._updateHandler = (event) => {
                        // Force deep copy to break any remaining reactivity/Proxy links
                        const payload = JSON.parse(JSON.stringify(event.detail));
                        if (!payload || !payload.labels) return;

                        // Check if chart exists and is healthy
                        const canvas = this.$refs.chartContainer ? this.$refs.chartContainer.querySelector(
                            'canvas') : null;
                        const existingChart = canvas ? Chart.getChart(canvas) : null;

                        if (existingChart && document.body.contains(canvas)) {
                            console.log(`Updating existing Chart instance: [${chartId}]`);
                            try {
                                existingChart.data.labels = payload.labels;
                                existingChart.data.datasets = payload.datasets;
                                existingChart.update();
                                this.chart = existingChart; // Sync reference
                            } catch (e) {
                                console.warn(`Update failed for [${chartId}], falling back to re-init:`, e);
                                this.initChart(payload);
                            }
                        } else {
                            console.log(`Initializing new Chart instance: [${chartId}]`);
                            this.initChart(payload);
                        }
                    };

                    window.addEventListener(`refreshChartData-${chartId}`, this._updateHandler);
                },
                destroy() {
                    if (this._initTimeout) clearTimeout(this._initTimeout);
                    window.removeEventListener(`refreshChartData-${chartId}`, this._updateHandler);
                    if (this.chart) {
                        try {
                            this.chart.destroy();
                        } catch (e) {}
                        this.chart = null;
                    }
                },
                initChart(data) {
                    if (this._initTimeout) clearTimeout(this._initTimeout);

                    this._initTimeout = setTimeout(() => {
                        if (!this.$refs.chartContainer) return;

                        let canvas = this.$refs.chartContainer.querySelector('canvas');
                        if (!canvas) {
                            canvas = document.createElement('canvas');
                            canvas.id = chartId;
                            canvas.className = 'w-full h-full';
                            this.$refs.chartContainer.appendChild(canvas);
                        }

                        if (this.chart) {
                            try {
                                this.chart.destroy();
                            } catch (e) {}
                            this.chart = null;
                        }

                        const ctx = canvas.getContext('2d');
                        if (!ctx) return;

                        try {
                            this.chart = new Chart(ctx, {
                                type: chartType,
                                data: {
                                    labels: [...(data.labels || [])],
                                    datasets: (data.datasets || []).map(ds => ({
                                        ...ds
                                    }))
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        },
                                        x: {
                                            beginAtZero: true
                                        },
                                    },
                                    indexAxis: barType,
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 500
                                    } // Enable animation for smooth updates
                                }
                            });
                        } catch (e) {
                            console.error(`Chart init error [${chartId}]:`, e);
                        }
                    }, 50);
                }
            }));

            const handleRefresh = (eventName, chartMappings) => {
                Livewire.on(eventName, (eventData) => {
                    try {
                        console.log(`Livewire Event [${eventName}] received:`, eventData);

                        let payload = null;
                        if (eventData && eventData.charts) {
                            payload = eventData.charts;
                        } else if (Array.isArray(eventData) && eventData[0] && eventData[0].charts) {
                            payload = eventData[0].charts;
                        } else if (eventData && typeof eventData === 'object' && !Array.isArray(eventData)) {
                            payload = eventData.charts || Object.values(eventData).find(v => v && v.charts)?.charts;
                        }

                        if (!payload) {
                            console.warn(`No charts payload found for ${eventName}`);
                            return;
                        }

                        const cleanCharts = JSON.parse(JSON.stringify(payload));

                        Alpine.nextTick(() => {
                            setTimeout(() => {
                                chartMappings.forEach(mapping => {
                                    const chartData = cleanCharts[mapping.prop];
                                    if (chartData) {
                                        window.dispatchEvent(new CustomEvent(
                                            `refreshChartData-${mapping.name}`, {
                                                detail: chartData
                                            }));
                                    }
                                });
                            }, 150);
                        });
                    } catch (e) {
                        console.error(`Error refreshing ${eventName}:`, e);
                    }
                });
            };

            handleRefresh('refresh-all-charts', [{
                    name: 'chartGender',
                    prop: 'gender'
                },
                {
                    name: 'chartAge',
                    prop: 'age'
                },
                {
                    name: 'chartInsurance',
                    prop: 'insurance'
                },
                {
                    name: 'chartDischarge',
                    prop: 'discharge'
                }
            ]);

            handleRefresh('refresh-main-charts', [{
                    name: 'mainChartWards',
                    prop: 'wards_patients'
                },
                {
                    name: 'mainChartBOR',
                    prop: 'wards_bor'
                },
                {
                    name: 'mainChartALOS',
                    prop: 'wards_alos'
                },
                {
                    name: 'mainChartBTO',
                    prop: 'wards_bto'
                },
                {
                    name: 'mainChartGDR',
                    prop: 'wards_gdr'
                },
                {
                    name: 'chartTrendInOut',
                    prop: 'trend'
                }
            ]);

            handleRefresh('refresh-snapshot-charts', [{
                    name: 'chartSnapshotClass',
                    prop: 'class_occupancy'
                },
                {
                    name: 'chartSnapshotWard',
                    prop: 'ward_occupancy'
                }
            ]);
        </script>
    @endscript
</div>
</x-content>
