<x-content>
    <x-slot:title>Catatan Rilis & Perubahan Sistem (Change Log)</x-slot:title>

    <div class="space-y-6">
        <!-- 1. Header Banner Change Log -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border border-stroke dark:border-strokedark shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <span class="icon-[solar--history-bold-duotone] text-2xl"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-gray-800 dark:text-white tracking-tight">
                            Catatan Rilis Aplikasi (Change Log)
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Versi Aktif: v{{ $latestVersion }}</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-400 mt-0.5">
                        Dokumentasi Riwayat Pembaruan Fitur, Perbaikan Bug, dan Peningkatan Kinerja SIMRS Dashboard
                    </p>
                </div>
            </div>

            <!-- Stats Ringkas -->
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-2xl bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark text-right">
                    <span class="text-[10px] font-black uppercase text-gray-400 block tracking-wider">Total Rilis</span>
                    <span class="text-base font-black text-gray-800 dark:text-white">{{ $totalVersions }} Versi</span>
                </div>
                <div class="px-4 py-2 rounded-2xl bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark text-right">
                    <span class="text-[10px] font-black uppercase text-gray-400 block tracking-wider">Pembaruan Terakhir</span>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        {{ \Carbon\Carbon::parse($latestDate)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Filter & Pencarian Catatan Rilis -->
        <div class="bg-white dark:bg-boxdark p-5 rounded-[2rem] border border-stroke dark:border-strokedark shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Filter Tipe Rilis -->
            <div class="flex items-center gap-1.5">
                <button type="button" wire:click="$set('selectedType', 'all')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $selectedType === 'all' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    Semua Rilis ({{ $totalVersions }})
                </button>
                <button type="button" wire:click="$set('selectedType', 'major')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $selectedType === 'major' ? 'bg-purple-600 text-white shadow-2xs' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    Major Release
                </button>
                <button type="button" wire:click="$set('selectedType', 'minor')"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $selectedType === 'minor' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-gray-100 dark:bg-meta-4 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    Feature & Enhancement
                </button>
            </div>

            <!-- Input Pencarian -->
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <span class="icon-[solar--magnifer-linear] text-base"></span>
                </span>
                <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari fitur atau catatan perubahan..."
                    class="w-full pl-10 pr-4 py-2 text-xs font-bold rounded-xl border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4 text-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 transition">
                @if ($search)
                    <button type="button" wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <span class="icon-[solar--close-circle-bold] text-base"></span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 3. Timeline Riwayat Versi -->
        <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:inset-0 before:left-3 sm:before:left-4 before:w-0.5 before:bg-gradient-to-b before:from-emerald-500 before:via-blue-500/40 before:to-gray-200 dark:before:to-meta-4">
            @forelse ($versions as $idx => $release)
                @php
                    $isLatest = $idx === 0 && empty($search) && $selectedType === 'all';
                    $isMajor = ($release['type'] ?? '') === 'major';
                @endphp
                <div class="relative">
                    <!-- Dot Indikator Timeline -->
                    <div class="absolute -left-6 sm:-left-8 top-5 flex items-center justify-center">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center {{ $isLatest ? 'bg-emerald-500 ring-4 ring-emerald-500/20 text-white' : ($isMajor ? 'bg-purple-600 ring-4 ring-purple-600/20 text-white' : 'bg-gray-300 dark:bg-meta-4 text-gray-600 dark:text-gray-300') }} shadow-sm">
                            <span class="icon-[solar--check-circle-bold] text-xs"></span>
                        </span>
                    </div>

                    <!-- Release Card -->
                    <div class="bg-white dark:bg-boxdark p-6 sm:p-7 rounded-[2.5rem] border {{ $isLatest ? 'border-emerald-500/40 dark:border-emerald-500/40 ring-2 ring-emerald-500/10' : 'border-stroke dark:border-strokedark' }} shadow-sm hover:shadow-md transition">
                        <!-- Header Release -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-5">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono text-xl sm:text-2xl font-black text-gray-800 dark:text-white tracking-tight">
                                    v{{ $release['version'] }}
                                </span>

                                @if (!empty($release['badge']))
                                    <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ $isMajor ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' }}">
                                        {{ $release['badge'] }}
                                    </span>
                                @endif

                                @if ($isLatest)
                                    <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow-2xs">
                                        Rilis Terkini
                                    </span>
                                @endif
                            </div>

                            @if (!empty($release['date']))
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                                    <span class="icon-[solar--calendar-bold-duotone] text-base text-gray-500"></span>
                                    <span>{{ \Carbon\Carbon::parse($release['date'])->translatedFormat('d F Y') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Judul Ringkasan Rilis -->
                        @if (!empty($release['title']))
                            <h4 class="text-sm sm:text-base font-extrabold text-gray-800 dark:text-white mb-4">
                                {{ $release['title'] }}
                            </h4>
                        @endif

                        <!-- Daftar Catatan Perubahan (Change Log Items) -->
                        <div class="space-y-2.5">
                            @forelse ($release['changeLog'] ?? [] as $log)
                                <div class="flex items-start gap-3 p-2.5 rounded-xl bg-gray-50/60 dark:bg-meta-4/20 border border-stroke/40 dark:border-strokedark/40">
                                    <span class="w-5 h-5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <span class="icon-[solar--check-read-linear] text-xs font-black"></span>
                                    </span>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 font-medium leading-relaxed">
                                        {{ $log }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">Tidak ada rincian catatan untuk versi ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-boxdark p-12 rounded-[2.5rem] border border-stroke dark:border-strokedark text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-meta-4 flex items-center justify-center mx-auto text-gray-400">
                        <span class="icon-[solar--magnifer-linear] text-2xl"></span>
                    </div>
                    <h4 class="text-sm font-black text-gray-800 dark:text-white">Tidak ada catatan rilis yang cocok</h4>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto">
                        Coba gunakan kata kunci pencarian lain atau pilih filter "Semua Rilis".
                    </p>
                    <button type="button" wire:click="resetFilters" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold cursor-pointer">
                        Reset Pencarian
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</x-content>
