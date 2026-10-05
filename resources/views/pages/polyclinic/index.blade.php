<x-content>
    <x-breadcrumb title="Poliklinik" :items="[['title' => 'Poliklinik']]" />

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
        <div class="flex-1 max-w-md">
            <x-form.input type="search" block wire:model.live.debounce.500ms="search"
                placeholder="Cari berdasarkan nama atau kode poliklinik..." />
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <span class="icon-[solar--hospital-bold-duotone] text-primary text-lg"></span>
            <span class="font-bold text-gray-700 dark:text-white">{{ count($poliklinik) }}</span>
            <span>unit poliklinik</span>
        </div>
    </div>

    <!-- Polyclinic Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($poliklinik as $poli)
            @php
                $theme = $poli->theme;
            @endphp
            <div class="group relative flex flex-col justify-between p-5 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-xs hover:shadow-xl hover:-translate-y-1 {{ $theme['border_hover'] }} transition-all duration-300 overflow-hidden">
                {{-- Decorative Ambient Glow on Hover --}}
                <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full blur-2xl opacity-0 group-hover:opacity-30 transition-opacity duration-500 pointer-events-none {{ str_replace('text-', 'bg-', $theme['color']) }}"></div>

                <div>
                    {{-- Top Header Row: Specialty Icon & Code Badge --}}
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl {{ $theme['bg'] }} flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 transition-transform duration-300">
                            <span class="{{ $theme['icon'] }} text-2xl"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-[11px] font-bold px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                {{ $poli->kd_poli }}
                            </span>
                        </div>
                    </div>

                    {{-- Polyclinic Name --}}
                    <h3 class="text-base font-bold text-gray-800 dark:text-white group-hover:text-primary transition-colors line-clamp-2 min-h-[3rem] leading-snug">
                        {{ $poli->nm_poli }}
                    </h3>
                </div>

                {{-- Card Footer: Metrics & Action --}}
                <div class="mt-4 pt-3.5 border-t border-stroke/70 dark:border-strokedark/70">
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        {{-- Dokter Praktek --}}
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-gray-50/80 dark:bg-meta-4/30">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                                <span class="icon-[solar--stethoscope-bold-duotone] text-sm"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter truncate">Dokter</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                                    {{ $poli->total_dokter }} <span class="text-[10px] font-normal text-gray-400">Dr</span>
                                </p>
                            </div>
                        </div>

                        {{-- Pasien Bulan Ini --}}
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-gray-50/80 dark:bg-meta-4/30">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                                <span class="icon-[solar--users-group-rounded-bold-duotone] text-sm"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter truncate">Bulan Ini</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                                    {{ number_format($poli->total_pasien_bulan_ini) }} <span class="text-[10px] font-normal text-gray-400">Pasien</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Action: Buka Rawat Jalan --}}
                    <a href="{{ route('outpatient', ['polyclinic' => $poli->kd_poli]) }}" wire:navigate
                        class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 bg-gray-100 hover:bg-primary hover:text-white dark:bg-meta-4 dark:hover:bg-primary transition-all duration-200 group/btn">
                        <span>Lihat Pasien</span>
                        <span class="icon-[solar--arrow-right-up-linear] text-sm group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform"></span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-meta-4 rounded-3xl flex items-center justify-center text-gray-300 dark:text-gray-600 mb-4 shadow-inner">
                    <span class="icon-[solar--hospital-bold-duotone] text-4xl"></span>
                </div>
                <h4 class="text-lg font-bold text-gray-700 dark:text-white">Tidak Ada Poliklinik</h4>
                <p class="text-sm text-gray-400 mt-1">Tidak dapat menemukan poliklinik dengan kata kunci "{{ $search }}"</p>
            </div>
        @endforelse
    </div>
</x-content>
