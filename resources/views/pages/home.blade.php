<x-content>
    <x-breadcrumb title="Beranda" :items="[['title' => 'Beranda']]" />

    <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Section 1: Welcome Hero Command Center -->
        <div class="relative p-8 sm:p-10 rounded-[3rem] bg-slate-900 text-white overflow-hidden shadow-2xl shadow-slate-900/20 group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-indigo-900/30"></div>
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] -mr-48 -mt-48 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-indigo-500/10 rounded-full blur-[100px] -ml-24 -mb-24"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="space-y-5">
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-500/10 backdrop-blur-md rounded-2xl border border-emerald-500/20 text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        SIMRS Command Center
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tighter leading-[0.95] text-transparent bg-clip-text bg-gradient-to-r from-white via-white to-emerald-200">
                            Selamat Datang, <br/>
                            <span class="text-emerald-400">{{ auth()->user()->name }}</span>
                        </h1>
                    </div>
                    <p class="max-w-xl text-slate-400 text-sm md:text-base font-medium leading-relaxed">
                        Pantau ekosistem operasional, kapasitas tempat tidur, kedaruratan IGD, layanan penunjang medis, dan indikator mutu pelayanan rumah sakit secara terpadu.
                    </p>
                </div>

                <div class="flex items-center gap-4 shrink-0">
                    <div class="p-5 sm:p-6 bg-white/5 backdrop-blur-2xl rounded-[2.5rem] border border-white/10 shadow-2xl group-hover:border-emerald-500/30 transition-colors duration-700">
                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <div class="text-3xl sm:text-4xl font-black text-white leading-none mb-1">{{ now()->format('d') }}</div>
                                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 opacity-80">{{ now()->translatedFormat('M Y') }}</div>
                            </div>
                            <div class="w-px h-12 bg-white/10"></div>
                            <div class="text-center">
                                <div class="text-3xl sm:text-4xl font-black text-white leading-none mb-1">{{ now()->format('H:i') }}</div>
                                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: 5 KPI Operational Cards (Hari Ini) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- 1. Outpatient Today -->
            <a href="{{ route('outpatient.recap') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.2rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-emerald-500/40 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:bg-emerald-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h4 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->outpatientToday) }}</h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rawat Jalan</span>
                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black uppercase">Hari Ini</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 2. Inpatient Active -->
            <a href="{{ route('inpatient.recap') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.2rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-blue-500/40 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:bg-blue-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <span class="icon-[solar--hospital-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h4 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->inpatientActive) }}</h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rawat Inap</span>
                            <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[8px] font-black uppercase">Sedang Inap</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 3. Emergency Today (IGD) -->
            <a href="{{ route('emergency.recap') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.2rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-rose-500/40 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full group-hover:bg-rose-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <span class="icon-[solar--danger-circle-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h4 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->emergencyToday) }}</h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kunjungan IGD</span>
                            <span class="px-1.5 py-0.5 rounded bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[8px] font-black uppercase">Hari Ini</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 4. Room Occupancy Stats -->
            <a href="{{ route('room') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.2rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-cyan-500/40 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-cyan-500/5 rounded-full group-hover:bg-cyan-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <span class="icon-[solar--bed-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <h4 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->roomStats['available']) }}</h4>
                            <span class="text-xs font-bold text-gray-400">/ {{ $this->roomStats['total'] }} TT</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kamar Kosong</span>
                            <span class="px-1.5 py-0.5 rounded bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-[8px] font-black uppercase">{{ $this->roomStats['occupancy_rate'] }}% Terisi</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 5. Operation Schedule Today -->
            <a href="{{ route('operation-schedule.recap') }}" wire:navigate class="group relative p-6 bg-white dark:bg-boxdark rounded-[2.2rem] border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-amber-500/40 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:bg-amber-500/10 transition-colors"></div>
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <span class="icon-[solar--mask-happly-bold-duotone] text-2xl"></span>
                    </div>
                    <div>
                        <h4 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">{{ number_format($this->operationToday['total']) }}</h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jadwal Operasi</span>
                            <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[8px] font-black uppercase">
                                @if ($this->operationToday['total'] > 0)
                                    {{ $this->operationToday['finished'] }} Selesai
                                @else
                                    Hari Ini
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Section 3: Layanan Penunjang Medis & SDM Dokter (4 Cards Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php $ancillary = $this->ancillaryStats; @endphp

            <!-- Laboratorium -->
            <a href="{{ route('laboratory') }}" wire:navigate class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm hover:shadow-lg hover:border-purple-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                        <span class="icon-[solar--test-tube-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded-md uppercase">
                        Penunjang
                    </span>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laboratorium</h5>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($ancillary['laboratory']['today']) }}</span>
                        <span class="text-[11px] text-gray-400">Hari ini</span>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                        Total bulan ini: <span class="font-black text-gray-700 dark:text-gray-300">{{ number_format($ancillary['laboratory']['month']) }}</span> tes
                    </div>
                </div>
            </a>

            <!-- Radiologi -->
            <a href="{{ route('radiology') }}" wire:navigate class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm hover:shadow-lg hover:border-indigo-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                        <span class="icon-[solar--scanner-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-md uppercase">
                        Diagnostik
                    </span>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Radiologi</h5>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($ancillary['radiology']['today']) }}</span>
                        <span class="text-[11px] text-gray-400">Hari ini</span>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                        Total bulan ini: <span class="font-black text-gray-700 dark:text-gray-300">{{ number_format($ancillary['radiology']['month']) }}</span> foto
                    </div>
                </div>
            </a>

            <!-- Farmasi (Resep Obat) -->
            <a href="{{ route('pharmacy') }}" wire:navigate class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm hover:shadow-lg hover:border-amber-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                        <span class="icon-[solar--pill-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md uppercase">
                        Farmasi
                    </span>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Resep Obat</h5>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($ancillary['pharmacy']['today']) }}</span>
                        <span class="text-[11px] text-gray-400">Resep hari ini</span>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                        Total bulan ini: <span class="font-black text-gray-700 dark:text-gray-300">{{ number_format($ancillary['pharmacy']['month']) }}</span> resep
                    </div>
                </div>
            </a>

            <!-- SDM Medis (Dokter) -->
            <a href="{{ route('medical-personnel') }}" wire:navigate class="p-5 bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm hover:shadow-lg hover:border-emerald-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <span class="icon-[solar--stethoscope-bold-duotone] text-xl"></span>
                    </div>
                    <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md uppercase">
                        SDM Medis
                    </span>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Dokter Aktif</h5>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($ancillary['medical_staff']['total']) }}</span>
                        <span class="text-[11px] text-gray-400">Dokter</span>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                        <span class="font-black text-emerald-600 dark:text-emerald-400">{{ number_format($ancillary['medical_staff']['specialists']) }}</span> Dokter Spesialis
                    </div>
                </div>
            </a>
        </div>

        <!-- Section 4: Trends & Payer Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tren Pelayanan 15 Hari Terakhir (Span 2) -->
            <div x-data="{ tab: 'inpatient' }" class="lg:col-span-2 bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <span class="icon-[solar--graph-new-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Tren Pelayanan Harian</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aktivitas 15 Hari Terakhir</p>
                            </div>
                        </div>
                    </div>

                    <!-- Segmented Tab Switcher (5 Departemen) -->
                    <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-meta-4/60 rounded-xl border border-stroke/50 dark:border-strokedark/50 overflow-x-auto scrollbar-none shrink-0 max-w-full">
                        <button type="button" @click="tab = 'inpatient'"
                            :class="tab === 'inpatient' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Ranap</span>
                        </button>
                        <button type="button" @click="tab = 'outpatient'"
                            :class="tab === 'outpatient' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Ralan</span>
                        </button>
                        <button type="button" @click="tab = 'emergency'"
                            :class="tab === 'emergency' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>IGD</span>
                        </button>
                        <button type="button" @click="tab = 'pharmacy'"
                            :class="tab === 'pharmacy' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Farmasi</span>
                        </button>
                        <button type="button" @click="tab = 'laboratory'"
                            :class="tab === 'laboratory' ? 'bg-white dark:bg-boxdark text-gray-800 dark:text-white font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span>Lab</span>
                        </button>
                    </div>
                </div>

                <!-- Chart Canvas Container -->
                <div class="h-[310px] w-full relative">
                    <div x-show="tab === 'inpatient'" class="w-full h-full" x-data="chartComponent('chartHomeInTrend', 'line', @js($this->inpatientTrend))">
                        <canvas id="chartHomeInTrend" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="tab === 'outpatient'" class="w-full h-full" x-data="chartComponent('chartHomeOutTrend', 'line', @js($this->outpatientTrend))">
                        <canvas id="chartHomeOutTrend" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="tab === 'emergency'" class="w-full h-full" x-data="chartComponent('chartHomeIgdTrend', 'line', @js($this->emergencyTrend))">
                        <canvas id="chartHomeIgdTrend" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="tab === 'pharmacy'" class="w-full h-full" x-data="chartComponent('chartHomePharmacyTrend', 'line', @js($this->pharmacyTrend))">
                        <canvas id="chartHomePharmacyTrend" class="w-full h-full"></canvas>
                    </div>
                    <div x-show="tab === 'laboratory'" class="w-full h-full" x-data="chartComponent('chartHomeLabTrend', 'line', @js($this->laboratoryTrend))">
                        <canvas id="chartHomeLabTrend" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribusi Penjamin Pasien (Donut Chart) -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70">
                        <div>
                            <h4 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">Distribusi Penjamin</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Bulan {{ now()->translatedFormat('F Y') }}</p>
                        </div>
                        <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                            {{ number_format($this->payerDistribution['total']) }} Pasien
                        </span>
                    </div>

                    <!-- Donut Chart Canvas -->
                    <div class="relative h-[180px] my-4 flex items-center justify-center" x-data="donutChartComponent('chartPayerDonut', @js($this->payerDistribution))">
                        <canvas id="chartPayerDonut"></canvas>
                    </div>
                </div>

                <!-- Legend & Metrics List -->
                <div class="space-y-2 pt-2 border-t border-stroke/70 dark:border-strokedark/70">
                    @foreach ($this->payerDistribution['items'] as $payer)
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $payer['color'] }}"></span>
                                <span class="font-bold text-gray-700 dark:text-gray-300 truncate">{{ $payer['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-black text-gray-800 dark:text-white">{{ number_format($payer['total']) }}</span>
                                <span class="text-[11px] font-bold {{ $payer['textColor'] }}">({{ $payer['percent'] }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section 4B: Tren Kunjungan Tahunan Rumah Sakit (Januari – Desember) -->
        <div class="bg-white dark:bg-boxdark p-6 sm:p-8 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-stroke/70 dark:border-strokedark/70 mb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <span class="icon-[solar--chart-2-bold-duotone] text-2xl"></span>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Tren Kunjungan Tahunan Rumah Sakit</h4>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Perbandingan volume pelayanan bulanan tahun {{ $this->yearlyVisitsTrend['year'] }} (Rawat Jalan, Rawat Inap, dan Gawat Darurat)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- KPI Pills Summary -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="px-3.5 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-xs">
                        <span class="text-[10px] font-bold uppercase text-blue-600 dark:text-blue-400 block">Total Ralan</span>
                        <span class="font-black text-blue-700 dark:text-blue-300">{{ number_format($this->yearlyVisitsTrend['totals']['ralan']) }}</span>
                    </div>
                    <div class="px-3.5 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs">
                        <span class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400 block">Total Ranap</span>
                        <span class="font-black text-emerald-700 dark:text-emerald-300">{{ number_format($this->yearlyVisitsTrend['totals']['ranap']) }}</span>
                    </div>
                    <div class="px-3.5 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs">
                        <span class="text-[10px] font-bold uppercase text-rose-600 dark:text-rose-400 block">Total IGD</span>
                        <span class="font-black text-rose-700 dark:text-rose-300">{{ number_format($this->yearlyVisitsTrend['totals']['igd']) }}</span>
                    </div>
                    <div class="px-3.5 py-1.5 rounded-xl bg-gray-900 text-white dark:bg-white dark:text-gray-900 text-xs shadow-sm">
                        <span class="text-[10px] font-bold uppercase opacity-75 block">Akumulasi RS</span>
                        <span class="font-black">{{ number_format(array_sum($this->yearlyVisitsTrend['totals'])) }}</span>
                    </div>
                </div>
            </div>

            <!-- Yearly Multi-Line Chart Canvas -->
            <div class="h-[320px] w-full relative" x-data="chartComponent('chartHomeYearlyTrend', 'line', @js($this->yearlyVisitsTrend))">
                <canvas id="chartHomeYearlyTrend" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Section: Demografi Pasien (Tren Gender & Distribusi Kelompok Usia) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tren Pasien Berdasarkan Gender (Laki-laki vs Perempuan) - Span 2 -->
            <div class="lg:col-span-2 bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500/10 to-pink-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <span class="icon-[solar--users-group-rounded-bold-duotone] text-2xl"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base sm:text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Tren Demografi Gender</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                        Tahun {{ now()->year }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">Komparasi tren kunjungan pasien Laki-laki vs Perempuan per bulan</p>
                            </div>
                        </div>

                        <!-- Quick Stats Pills -->
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">Pria:</span>
                                <span class="text-xs font-black text-blue-600 dark:text-blue-400">{{ number_format($this->demographicGenderTrend['totalPria']) }}</span>
                                <span class="text-[10px] font-bold text-blue-500/80">({{ $this->demographicGenderTrend['ratioPria'] }}%)</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-pink-50 dark:bg-pink-500/10 border border-pink-200/50 dark:border-pink-500/20">
                                <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">Wanita:</span>
                                <span class="text-xs font-black text-pink-600 dark:text-pink-400">{{ number_format($this->demographicGenderTrend['totalWanita']) }}</span>
                                <span class="text-[10px] font-bold text-pink-500/80">({{ $this->demographicGenderTrend['ratioWanita'] }}%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Kategori TNI / POLRI / Umum -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5 p-3.5 rounded-2xl bg-gray-50/80 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 shrink-0">
                                <span class="icon-[solar--shield-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">TNI AD / Kes</div>
                                <div class="text-sm font-black text-gray-800 dark:text-white">
                                    {{ number_format($this->demographicCategorySummary['tni']) }}
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 ml-0.5">({{ $this->demographicCategorySummary['pctTni'] }}%)</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 sm:border-x sm:border-stroke/60 sm:dark:border-strokedark/60 sm:px-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                                <span class="icon-[solar--shield-minimalistic-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">POLRI</div>
                                <div class="text-sm font-black text-gray-800 dark:text-white">
                                    {{ number_format($this->demographicCategorySummary['polri']) }}
                                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 ml-0.5">({{ $this->demographicCategorySummary['pctPolri'] }}%)</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                                <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Masyarakat Umum</div>
                                <div class="text-sm font-black text-gray-800 dark:text-white">
                                    {{ number_format($this->demographicCategorySummary['umum']) }}
                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 ml-0.5">({{ $this->demographicCategorySummary['pctUmum'] }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gender Multi-Line Chart Canvas -->
                    <div class="h-[280px] w-full relative" x-data="chartComponent('chartHomeGenderTrend', 'line', @js($this->demographicGenderTrend))">
                        <canvas id="chartHomeGenderTrend" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribusi Pasien Berdasarkan Kelompok Umur (Span 1) -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                <span class="icon-[solar--pie-chart-2-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Kelompok Usia</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Distribusi Umur {{ now()->year }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-black text-purple-600 dark:text-purple-400 bg-purple-500/10 px-2.5 py-0.5 rounded-lg">
                            {{ number_format($this->demographicAgeDistribution['total']) }} Pasien
                        </span>
                    </div>

                    <!-- Age Bracket List (Berdasarkan Tabel kelompok_umur SIMRS) -->
                    <div class="space-y-2.5 max-h-[460px] overflow-y-auto pr-1">
                        @foreach ($this->demographicAgeDistribution['items'] as $item)
                            <div class="p-2.5 sm:p-3 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/50 dark:border-strokedark/50 hover:border-purple-500/30 transition-all">
                                <div class="flex items-center justify-between text-xs font-bold mb-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-6 h-6 rounded-lg {{ $item['badge'] }} flex items-center justify-center shrink-0">
                                            @switch($item['kode'])
                                                @case('NEO')
                                                    <span class="icon-[solar--sleeping-circle-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('BAY')
                                                    <span class="icon-[solar--hearts-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('BAL')
                                                    <span class="icon-[solar--sticker-smile-circle-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('ANK')
                                                    <span class="icon-[solar--user-circle-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('RMJ')
                                                    <span class="icon-[solar--running-round-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('DWS')
                                                    <span class="icon-[solar--users-group-rounded-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('PRL')
                                                    <span class="icon-[solar--user-hand-up-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('LNS')
                                                    <span class="icon-[solar--shield-user-bold-duotone] text-sm"></span>
                                                    @break
                                                @default
                                                    <span class="icon-[solar--user-bold-duotone] text-sm"></span>
                                            @endswitch
                                        </span>
                                        <div class="flex items-baseline gap-1.5 min-w-0">
                                            <span class="text-gray-800 dark:text-white font-black text-xs">{{ $item['group'] }}</span>
                                            @if (!empty($item['rangeDesc']))
                                                <span class="text-[10px] text-gray-400 font-semibold">({{ $item['rangeDesc'] }})</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="font-black text-gray-800 dark:text-white text-xs">
                                            {{ number_format($item['total']) }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $item['badge'] }}">
                                            {{ $item['percent'] }}%
                                        </span>
                                    </div>
                                </div>

                                <!-- Bar Progress -->
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden mb-1.5">
                                    <div class="h-full {{ $item['barColor'] }} transition-all duration-500 rounded-full" style="width: {{ $item['barWidth'] }}%"></div>
                                </div>

                                <!-- Gender Breakdown within Bracket -->
                                <div class="flex items-center justify-between text-[10px] text-gray-400 font-medium">
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Laki-laki: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($item['pria']) }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
                                        Perempuan: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($item['wanita']) }}</strong>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Tren Pelayanan Pasien Dinas (TNI AD, TNI AL, TNI AU & POLRI) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tren Pelayanan Pasien Dinas (12 Bulan) - Span 2 -->
            <div class="lg:col-span-2 bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <span class="icon-[solar--shield-bold-duotone] text-2xl"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base sm:text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Tren Pasien Dinas (TNI & POLRI)</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        Tahun {{ now()->year }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">Tren bulanan pelayanan rawat jalan, rawat inap, dan IGD personel dinas</p>
                            </div>
                        </div>

                        <!-- Summary Pills -->
                        <div class="flex items-center flex-wrap gap-2">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-meta-4 border border-stroke dark:border-strokedark">
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">Total Dinas:</span>
                                <span class="text-xs font-black text-gray-800 dark:text-white">{{ number_format($this->dinasPatientsTrend['totalDinas']) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">TNI:</span>
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">{{ number_format($this->dinasPatientsTrend['totalTni'] ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">POLRI:</span>
                                <span class="text-xs font-black text-blue-600 dark:text-blue-400">{{ number_format($this->dinasPatientsTrend['totalPolri'] ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-500/10 border border-sky-200/50 dark:border-sky-500/20">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">Ralan:</span>
                                <span class="text-xs font-black text-sky-600 dark:text-sky-400">{{ number_format($this->dinasPatientsTrend['totalRalan']) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200/50 dark:border-amber-500/20">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">Ranap:</span>
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400">{{ number_format($this->dinasPatientsTrend['totalRanap']) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/50 dark:border-rose-500/20">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">IGD:</span>
                                <span class="text-xs font-black text-rose-600 dark:text-rose-400">{{ number_format($this->dinasPatientsTrend['totalIgd']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Kesatuan / Satuan Pill Bar -->
                    <div class="flex items-center flex-wrap gap-2 mb-5 p-3 rounded-2xl bg-gray-50/80 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1 shrink-0">
                            <span class="icon-[solar--flag-bold-duotone] text-sm text-amber-500"></span>
                            Satuan Utama:
                        </span>
                        @foreach ($this->dinasCategoryBreakdown['topSatuan'] as $sat)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-boxdark border border-stroke dark:border-strokedark shadow-2xs">
                                <span class="text-gray-700 dark:text-gray-300">{{ $sat->nama_satuan }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                    {{ number_format($sat->total) }}
                                </span>
                            </span>
                        @endforeach
                    </div>

                    <!-- Line Chart Canvas -->
                    <div class="h-[280px] w-full relative" x-data="chartComponent('chartHomeDinasTrend', 'line', @js($this->dinasPatientsTrend))">
                        <canvas id="chartHomeDinasTrend" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Komposisi Golongan Personel Dinas - Span 1 -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <span class="icon-[solar--medal-ribbon-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Golongan Dinas</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Segmentasi Militer & Keluarga</p>
                            </div>
                        </div>
                        <a href="{{ route('patient-report') }}" wire:navigate class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1">
                            <span>Laporan</span>
                            <span class="icon-[solar--arrow-right-line-duotone]"></span>
                        </a>
                    </div>

                    <!-- List Golongan -->
                    <div class="space-y-3">
                        @foreach ($this->dinasCategoryBreakdown['items'] as $item)
                            <div class="p-3 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/50 dark:border-strokedark/50 hover:border-amber-500/30 transition-all">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-6 h-6 rounded-lg {{ $item['badge'] }} flex items-center justify-center shrink-0">
                                            @switch($item['kategori'])
                                                @case('Militer / Anggota Aktif')
                                                    <span class="icon-[solar--shield-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('ASN / PNS')
                                                    <span class="icon-[solar--user-id-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('Keluarga Personel')
                                                    <span class="icon-[solar--users-group-rounded-bold-duotone] text-sm"></span>
                                                    @break
                                                @case('Purnawirawan')
                                                    <span class="icon-[solar--medal-ribbon-star-bold-duotone] text-sm"></span>
                                                    @break
                                                @default
                                                    <span class="icon-[solar--shield-bold-duotone] text-sm"></span>
                                            @endswitch
                                        </span>
                                        <span class="text-gray-800 dark:text-white font-black text-xs truncate">{{ $item['kategori'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="font-black text-gray-800 dark:text-white text-xs">
                                            {{ number_format($item['total']) }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $item['badge'] }}">
                                            {{ $item['percent'] }}%
                                        </span>
                                    </div>
                                </div>

                                <!-- Bar Progress -->
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden mb-1.5">
                                    <div class="h-full {{ $item['barColor'] }} transition-all duration-500 rounded-full" style="width: {{ $item['barWidth'] }}%"></div>
                                </div>

                                <!-- Breakdown Pelayanan -->
                                <div class="flex items-center justify-between text-[10px] text-gray-400 font-medium">
                                    <span>Ralan: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($item['ralan']) }}</strong></span>
                                    <span>Ranap: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($item['ranap']) }}</strong></span>
                                    <span>IGD: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ number_format($item['igd']) }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Matra Distribution Badges -->
                    <div class="mt-4 pt-3 border-t border-stroke/60 dark:border-strokedark/60 flex items-center justify-between text-[10px]">
                        <span class="text-gray-400 font-bold uppercase flex items-center gap-1">
                            <span class="icon-[solar--flag-bold-duotone] text-xs text-amber-500"></span>
                            Matra:
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black">
                                <span class="icon-[solar--shield-check-bold-duotone] text-xs"></span>
                                TNI AD: 651
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 font-black">
                                <span class="icon-[solar--water-sun-bold-duotone] text-xs"></span>
                                TNI AL: 4
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black">
                                <span class="icon-[solar--shield-minimalistic-bold-duotone] text-xs"></span>
                                POLRI: 2
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Kapasitas Bed, Top 5 Poliklinik, & Top 5 Diagnosa Morbiditas (3 Kolom Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 1. Okupansi Tempat Tidur per Kelas (Bed Occupancy Breakdown) -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                <span class="icon-[solar--bed-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Kapasitas Bed Kelas</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Kondisi Bed Terkini</p>
                            </div>
                        </div>
                        <a href="{{ route('room') }}" wire:navigate class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                            <span>Detail</span>
                            <span class="icon-[solar--arrow-right-line-duotone]"></span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($this->bedOccupancyByClass as $item)
                            @php
                                $rate = $item['rate'];
                                $barColor = $rate >= 85 ? 'bg-rose-500' : ($rate >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                                $badgeColor = $rate >= 85 ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400' : ($rate >= 75 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400');
                            @endphp
                            <div class="p-3 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/50 dark:border-strokedark/50">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-gray-800 dark:text-white font-black">{{ $item['kelas'] }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $badgeColor }}">
                                            {{ $rate }}%
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400">
                                        <span class="font-black text-gray-800 dark:text-white">{{ $item['isi'] }}</span>/{{ $item['total'] }} TT
                                        <span class="text-emerald-600 dark:text-emerald-400 ml-1">({{ $item['kosong'] }} Ksg)</span>
                                    </div>
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden">
                                    <div class="h-full {{ $barColor }} transition-all duration-500" style="width: {{ min($rate, 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">Data kamar tidak ditemukan.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 2. Top 5 Poliklinik Terpadat -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <span class="icon-[solar--stethoscope-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Top 5 Poliklinik</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Bulan {{ now()->translatedFormat('M Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('outpatient.recap') }}" wire:navigate class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                            <span>Rekap</span>
                            <span class="icon-[solar--arrow-right-line-duotone]"></span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($this->topPolyclinics as $poli)
                            @php
                                $rankBadges = [
                                    1 => 'bg-amber-500 text-white shadow-sm ring-1 ring-amber-500/30',
                                    2 => 'bg-slate-400 text-white',
                                    3 => 'bg-amber-700/80 text-white',
                                    4 => 'bg-gray-200 dark:bg-meta-4 text-gray-600 dark:text-gray-300',
                                    5 => 'bg-gray-200 dark:bg-meta-4 text-gray-600 dark:text-gray-300',
                                ];
                            @endphp
                            <div class="p-3 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/50 dark:border-strokedark/50">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black shrink-0 {{ $rankBadges[$poli['rank']] ?? 'bg-gray-200 text-gray-700' }}">
                                            {{ $poli['rank'] }}
                                        </span>
                                        <span class="text-gray-800 dark:text-white font-black truncate text-[11px]">{{ $poli['name'] }}</span>
                                    </div>
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 shrink-0 text-[11px] ml-1">
                                        {{ number_format($poli['total']) }}
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $poli['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">Belum ada kunjungan poli bulan ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 3. Top 5 Diagnosa Morbiditas (ICD-10) -->
            <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400">
                                <span class="icon-[solar--shield-warning-bold-duotone] text-xl"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">Top 5 Penyakit (ICD-10)</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Morbiditas Bulan {{ now()->translatedFormat('M Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('icd.icd10') }}" wire:navigate class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                            <span>ICD-10</span>
                            <span class="icon-[solar--arrow-right-line-duotone]"></span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($this->topDiagnoses as $diag)
                            <div class="p-3 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/50 dark:border-strokedark/50">
                                <div class="flex items-center justify-between text-xs font-bold mb-1">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-600 dark:text-rose-400 font-mono text-[10px] font-black shrink-0">
                                            {{ $diag['code'] }}
                                        </span>
                                        <span class="text-gray-800 dark:text-white font-bold truncate text-[11px]" title="{{ $diag['name'] }}">
                                            {{ $diag['name'] }}
                                        </span>
                                    </div>
                                    <span class="font-black text-rose-600 dark:text-rose-400 shrink-0 text-[11px] ml-1">
                                        {{ number_format($diag['total']) }}
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden">
                                    <div class="h-full bg-rose-500 transition-all duration-500" style="width: {{ $diag['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">Belum ada data diagnosa bulan ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Mutu Pelayanan & Akreditasi Rumah Sakit (INM & SPM Kemenkes RI) -->
        @php $mutu = $this->mutuSummary; @endphp
        <div class="space-y-6">
            <div class="bg-white dark:bg-boxdark p-6 sm:p-8 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm">
                <!-- Header Banner -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 pb-6 border-b border-stroke/70 dark:border-strokedark/70 mb-6">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 shrink-0">
                            <span class="icon-[solar--diploma-verified-bold-duotone] text-2xl"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Mutu & Akreditasi Rumah Sakit</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    Standar Kemenkes RI
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Rekapitulasi Capaian 13 Indikator Nasional Mutu (Permenkes 30/2022) dan Standar Pelayanan Minimal (Kepmenkes 129/2008) Bulan {{ now()->translatedFormat('F Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Action Badges -->
                    <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                        <button type="button" wire:click="refreshMutuCache" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 dark:bg-meta-4/60 dark:hover:bg-meta-4 text-gray-700 dark:text-gray-300 transition"
                            title="Perbarui Data Mutu">
                            <span wire:loading.remove wire:target="refreshMutuCache" class="icon-[solar--refresh-circle-bold-duotone] text-base"></span>
                            <span wire:loading wire:target="refreshMutuCache" class="icon-[solar--spinner-line-duotone] animate-spin text-base"></span>
                            <span class="text-[11px]">Sync Mutu</span>
                        </button>
                        <a href="{{ route('mutu.inm') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition">
                            <span class="icon-[solar--chart-bold-duotone] text-base"></span>
                            <span>Detail INM</span>
                        </a>
                        <a href="{{ route('mutu.spm') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition">
                            <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-base"></span>
                            <span>Detail SPM</span>
                        </a>
                    </div>
                </div>

                <!-- 4 Top Executive Quality Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- 1. Capaian INM Nasional -->
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent border border-emerald-500/20 dark:bg-meta-4/20 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">13 Indikator INM</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $mutu['inm']['compliance_percent'] >= 80 ? 'bg-emerald-500/15 text-emerald-600' : 'bg-amber-500/15 text-amber-600' }}">
                                    {{ $mutu['inm']['compliance_percent'] }}% Target
                                </span>
                            </div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                                {{ $mutu['inm']['average_score'] }}<span class="text-base font-bold text-gray-400">%</span>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Skor Rata-rata Kepatuhan Mutu
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-stroke/50 dark:border-strokedark/50 flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">Tercapai:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $mutu['inm']['achieved'] }} dari {{ $mutu['inm']['total'] }} Indikator</span>
                        </div>
                    </div>

                    <!-- 2. Capaian SPM RS -->
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-500/5 via-transparent to-transparent border border-blue-500/20 dark:bg-meta-4/20 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400">Standar Pelayanan (SPM)</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $mutu['spm']['percent'] >= 80 ? 'bg-blue-500/15 text-blue-600' : 'bg-amber-500/15 text-amber-600' }}">
                                    {{ $mutu['spm']['percent'] }}% SPM
                                </span>
                            </div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                                {{ $mutu['spm']['achieved'] }}<span class="text-base font-bold text-gray-400">/{{ $mutu['spm']['total'] }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Indikator Unit Memenuhi SPM
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-stroke/50 dark:border-strokedark/50 flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">Cakupan:</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">5 Unit Pelayanan Utama</span>
                        </div>
                    </div>

                    <!-- 3. Keselamatan Pasien (IKP) -->
                    <a href="{{ route('mutu.ikp') }}" wire:navigate class="p-5 rounded-2xl bg-gradient-to-br from-amber-500/5 via-transparent to-transparent border border-amber-500/20 dark:bg-meta-4/20 flex flex-col justify-between hover:border-amber-500/40 transition group">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Keselamatan Pasien (IKP)</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-500/10 text-emerald-600">
                                    0 Sentinel
                                </span>
                            </div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                                {{ $mutu['ikp']['total'] }}<span class="text-base font-bold text-gray-400"> Kasus</span>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Insiden Dilaporkan Bulan Ini
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-stroke/50 dark:border-strokedark/50 flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">Status Risiko:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 group-hover:underline">Terkendali &rarr;</span>
                        </div>
                    </a>

                    <!-- 4. Surveilans PPI / HAIs -->
                    <a href="{{ route('mutu.ppi') }}" wire:navigate class="p-5 rounded-2xl bg-gradient-to-br from-purple-500/5 via-transparent to-transparent border border-purple-500/20 dark:bg-meta-4/20 flex flex-col justify-between hover:border-purple-500/40 transition group">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">Surveilans Infeksi (PPI)</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-purple-500/10 text-purple-600">
                                    HAIs
                                </span>
                            </div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white tracking-tight">
                                {{ $mutu['ppi']['total_infeksi'] }}<span class="text-base font-bold text-gray-400"> Infeksi</span>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                Laju Infeksi per 1.000 Hari Pasang
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-stroke/50 dark:border-strokedark/50 flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">Audit Bundle:</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400 group-hover:underline">{{ $mutu['ppi']['bundle_rata'] }}% Kepatuhan &rarr;</span>
                        </div>
                    </a>
                </div>

                <!-- Dual Columns: 6 INM Priorities + SPM 5 Unit Matrix -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
                    <!-- Kolom 1: 6 Indikator INM Prioritas -->
                    <div class="p-5 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-stroke/60 dark:border-strokedark/60">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Highlight Indikator Nasional Mutu (INM)</h4>
                            </div>
                            <a href="{{ route('mutu.inm') }}" wire:navigate class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                <span>13 INM</span>
                                <span class="icon-[solar--arrow-right-line-duotone]"></span>
                            </a>
                        </div>

                        <div class="space-y-3">
                            @foreach ($mutu['inm']['key_indicators'] as $ind)
                                <div class="p-3 rounded-xl bg-white dark:bg-boxdark border border-stroke/50 dark:border-strokedark/50 shadow-2xs">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="{{ $ind['icon'] ?? 'icon-[solar--shield-check-bold-duotone]' }} text-base {{ $ind['color'] ?? 'text-emerald-500' }} shrink-0"></span>
                                            <span class="text-gray-800 dark:text-white font-bold truncate text-[11px]" title="{{ $ind['title'] }}">
                                                {{ $ind['short_label'] ?? $ind['title'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="font-black text-gray-800 dark:text-white text-xs">
                                                {{ $ind['rate'] }}{{ $ind['unit'] }}
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $ind['is_achieved'] ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400' }}">
                                                {{ $ind['status_badge'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-full h-1.5 bg-gray-100 dark:bg-meta-4 rounded-full overflow-hidden">
                                        <div class="h-full {{ $ind['is_achieved'] ? 'bg-emerald-500' : 'bg-rose-500' }} transition-all duration-500" style="width: {{ min($ind['rate'], 100) }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-gray-400 mt-1">
                                        <span>Target Standar: <strong class="text-gray-600 dark:text-gray-300 font-bold">{{ $ind['standard_label'] }}</strong></span>
                                        <span>{{ $ind['code'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kolom 2: SPM per 5 Unit Pelayanan -->
                    <div class="p-5 rounded-2xl bg-gray-50/70 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-stroke/60 dark:border-strokedark/60">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                <h4 class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">Kepatuhan SPM per Unit Pelayanan</h4>
                            </div>
                            <a href="{{ route('mutu.spm') }}" wire:navigate class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                <span>Semua Unit</span>
                                <span class="icon-[solar--arrow-right-line-duotone]"></span>
                            </a>
                        </div>

                        <div class="space-y-3">
                            @foreach ($mutu['spm']['sections_summary'] as $sec)
                                <div class="p-3 rounded-xl bg-white dark:bg-boxdark border border-stroke/50 dark:border-strokedark/50 shadow-2xs">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            @php
                                                $unitIcons = [
                                                    'admisi' => 'icon-[solar--user-id-bold-duotone] text-indigo-500',
                                                    'igd' => 'icon-[solar--danger-circle-bold-duotone] text-rose-500',
                                                    'ralan' => 'icon-[solar--stethoscope-bold-duotone] text-blue-500',
                                                    'ranap' => 'icon-[solar--hospital-bold-duotone] text-emerald-500',
                                                    'farmasi' => 'icon-[solar--pill-bold-duotone] text-amber-500',
                                                    'penunjang' => 'icon-[solar--test-tube-bold-duotone] text-purple-500',
                                                ];
                                                $unitIcon = $unitIcons[$sec['key']] ?? 'icon-[solar--shield-check-bold-duotone] text-emerald-500';
                                            @endphp
                                            <span class="{{ $unitIcon }} text-base shrink-0"></span>
                                            <span class="text-gray-800 dark:text-white font-bold truncate text-[11px]">
                                                {{ $sec['unit'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="font-black text-gray-800 dark:text-white text-xs">
                                                {{ $sec['achieved'] }}/{{ $sec['total'] }}
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $sec['rate'] >= 80 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($sec['rate'] > 0 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400') }}">
                                                {{ $sec['rate'] }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-full h-1.5 bg-gray-100 dark:bg-meta-4 rounded-full overflow-hidden">
                                        <div class="h-full {{ $sec['rate'] >= 80 ? 'bg-emerald-500' : ($sec['rate'] > 0 ? 'bg-amber-500' : 'bg-rose-500') }} transition-all duration-500" style="width: {{ $sec['rate'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Indikator Mutu Pelayanan Rawat Inap (Bulan Ini) & Akses Cepat -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <!-- Indikator Mutu Pelayanan (Span 3) -->
            <div class="xl:col-span-3 bg-white dark:bg-boxdark p-6 sm:p-8 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stroke/70 dark:border-strokedark/70 mb-6">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <span class="icon-[solar--chart-square-bold-duotone] text-2xl"></span>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Indikator Mutu Pelayanan RS</h4>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Evaluasi efisiensi dan mortalitas rawat inap bulan {{ now()->translatedFormat('F Y') }} (Total {{ $this->clinicalIndicators['beds'] }} Bed Aktif)
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('indicator-matrix') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 transition">
                        <span>Matriks Lengkap</span>
                        <span class="icon-[solar--arrow-right-line-duotone] text-base"></span>
                    </a>
                </div>

                <!-- 6 Metric Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        $indicatorList = [
                            'bor' => ['name' => 'BOR', 'desc' => 'Bed Occupancy', 'color' => 'text-emerald-600 dark:text-emerald-400'],
                            'alos' => ['name' => 'ALOS', 'desc' => 'Length of Stay', 'color' => 'text-blue-600 dark:text-blue-400'],
                            'toi' => ['name' => 'TOI', 'desc' => 'Turn Over Interval', 'color' => 'text-purple-600 dark:text-purple-400'],
                            'bto' => ['name' => 'BTO', 'desc' => 'Bed Turn Over', 'color' => 'text-amber-600 dark:text-amber-400'],
                            'ndr' => ['name' => 'NDR', 'desc' => 'Net Death Rate', 'color' => 'text-pink-600 dark:text-pink-400'],
                            'gdr' => ['name' => 'GDR', 'desc' => 'Gross Death Rate', 'color' => 'text-rose-600 dark:text-rose-400'],
                        ];
                    @endphp

                    @foreach ($indicatorList as $key => $meta)
                        @php
                            $item = $this->clinicalIndicators[$key];
                        @endphp
                        <div class="p-4 rounded-2xl bg-gray-50/80 dark:bg-meta-4/30 border border-stroke/60 dark:border-strokedark/60 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $meta['name'] }}</span>
                                    @if ($item['isIdeal'])
                                        <span class="w-2 h-2 rounded-full bg-emerald-500" title="Sesuai Standar"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-rose-500" title="Perlu Evaluasi"></span>
                                    @endif
                                </div>
                                <p class="text-[9px] text-gray-400 truncate mt-0.5">{{ $meta['desc'] }}</p>
                            </div>

                            <div class="mt-4">
                                <div class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">
                                    {{ $item['value'] }}<span class="text-xs ml-0.5 font-bold text-gray-400">{{ $item['unit'] }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-[10px]">
                                    <span class="text-gray-400">Target:</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $item['target'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Akses Cepat (Span 1) -->
            <div class="xl:col-span-1 bg-white dark:bg-boxdark p-6 sm:p-8 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight mb-4">Akses Cepat Layanan</h4>
                    <div class="space-y-2.5">
                        <a href="{{ route('outpatient.recap') }}" wire:navigate class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-meta-4/40 hover:bg-emerald-500/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="icon-[solar--users-group-two-rounded-bold-duotone] text-base"></span>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Rawat Jalan</span>
                            </div>
                            <span class="icon-[solar--arrow-right-line-duotone] text-xs text-gray-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></span>
                        </a>

                        <a href="{{ route('inpatient.recap') }}" wire:navigate class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-meta-4/40 hover:bg-blue-500/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="icon-[solar--hospital-bold-duotone] text-base"></span>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Rawat Inap</span>
                            </div>
                            <span class="icon-[solar--arrow-right-line-duotone] text-xs text-gray-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all"></span>
                        </a>

                        <a href="{{ route('emergency.recap') }}" wire:navigate class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-meta-4/40 hover:bg-rose-500/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="icon-[solar--danger-circle-bold-duotone] text-base"></span>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Gawat Darurat (IGD)</span>
                            </div>
                            <span class="icon-[solar--arrow-right-line-duotone] text-xs text-gray-400 group-hover:text-rose-600 group-hover:translate-x-0.5 transition-all"></span>
                        </a>

                        <a href="{{ route('room') }}" wire:navigate class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-meta-4/40 hover:bg-cyan-500/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="icon-[solar--bed-bold-duotone] text-base"></span>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Status Kamar</span>
                            </div>
                            <span class="icon-[solar--arrow-right-line-duotone] text-xs text-gray-400 group-hover:text-cyan-600 group-hover:translate-x-0.5 transition-all"></span>
                        </a>

                        <a href="{{ route('operation-schedule.recap') }}" wire:navigate class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-meta-4/40 hover:bg-amber-500/10 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="icon-[solar--mask-happly-bold-duotone] text-base"></span>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Jadwal Operasi</span>
                            </div>
                            <span class="icon-[solar--arrow-right-line-duotone] text-xs text-gray-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></span>
                        </a>
                    </div>
                </div>

                <div class="pt-4 border-t border-stroke/70 dark:border-strokedark/70">
                    <a href="{{ route('patient-report') }}" wire:navigate class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-meta-4 text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition">
                        <span class="icon-[solar--document-text-bold-duotone] text-base"></span>
                        <span>Laporan Kunjungan & Pasien</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            // Alpine Component: Line Trend Charts
            Alpine.data('chartComponent', (chartId, chartType, initialData) => ({
                chart: null,
                init() {
                    this.$nextTick(() => {
                        this.renderChart(initialData);
                    });
                },
                renderChart(data) {
                    const canvas = document.getElementById(chartId);
                    if (!canvas) return;

                    if (this.chart) {
                        try { this.chart.destroy(); } catch (e) {}
                        this.chart = null;
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.04)';
                    const textColor = isDark ? '#94A3B8' : '#64748B';

                    this.chart = new Chart(ctx, {
                        type: chartType,
                        data: {
                            labels: [...(data.labels || [])],
                            datasets: (data.datasets || []).map(ds => ({ ...ds }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 600, easing: 'easeOutQuart' },
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        color: textColor,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        padding: 12,
                                        font: { size: 11, weight: '600' }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
                                    titleColor: isDark ? '#F1F5F9' : '#0F172A',
                                    bodyColor: isDark ? '#CBD5E1' : '#334155',
                                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                    borderWidth: 1,
                                    padding: 10,
                                    boxPadding: 4,
                                    usePointStyle: true,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: gridColor },
                                    ticks: { color: textColor, font: { size: 10 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: textColor, font: { size: 10, weight: 'bold' } }
                                }
                            }
                        }
                    });
                }
            }));

            // Alpine Component: Donut Payer Chart
            Alpine.data('donutChartComponent', (chartId, initialData) => ({
                chart: null,
                init() {
                    this.$nextTick(() => {
                        this.renderChart(initialData);
                    });
                },
                renderChart(data) {
                    const canvas = document.getElementById(chartId);
                    if (!canvas) return;

                    if (this.chart) {
                        try { this.chart.destroy(); } catch (e) {}
                        this.chart = null;
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const isDark = document.documentElement.classList.contains('dark');

                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: [...(data.labels || [])],
                            datasets: (data.datasets || []).map(ds => ({ ...ds }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            animation: { duration: 700, easing: 'easeOutQuart' },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
                                    titleColor: isDark ? '#F1F5F9' : '#0F172A',
                                    bodyColor: isDark ? '#CBD5E1' : '#334155',
                                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                    borderWidth: 1,
                                    padding: 10,
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const val = context.raw || 0;
                                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                            return ` ${context.label}: ${val.toLocaleString('id-ID')} (${pct}%)`;
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
