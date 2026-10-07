<x-content>
    <x-breadcrumb title="Kamar" :items="[['title' => 'Monitoring Kamar']]" />

    @php
        $totalBedRS = $rooms->sum('total');
        $totalTersediaRS = $rooms->sum('tersedia');
        $totalTerisiRS = $rooms->sum('terisi');
        $borRS = $totalBedRS > 0 ? round(($totalTerisiRS / $totalBedRS) * 100, 1) : 0;
    @endphp

    {{-- Ringkasan Kapasitas Bed Rumah Sakit (4 KPI Cards) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        {{-- Card 1: Total Kapasitas --}}
        <div
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition hover:shadow-md">
            <div
                class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
                <span class="icon-[solar--bed-bold-duotone] text-2xl"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate">Total Kapasitas</p>
                <p class="text-lg sm:text-2xl font-black font-mono text-slate-800 dark:text-white leading-tight">
                    {{ number_format($totalBedRS) }}
                    <span class="text-xs font-semibold font-body text-slate-400">Bed</span>
                </p>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">Semua kelas aktif</p>
            </div>
        </div>

        {{-- Card 2: Bed Tersedia --}}
        <div
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition hover:shadow-md">
            <div
                class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <span class="icon-[solar--check-circle-bold-duotone] text-2xl"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate">Bed Tersedia</p>
                <p
                    class="text-lg sm:text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400 leading-tight">
                    {{ number_format($totalTersediaRS) }}
                    <span class="text-xs font-semibold font-body text-slate-400">Bed</span>
                </p>
                <p
                    class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-medium truncate mt-0.5 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Siap digunakan</span>
                </p>
            </div>
        </div>

        {{-- Card 3: Bed Terisi --}}
        <div
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition hover:shadow-md">
            <div
                class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <span class="icon-[solar--user-bold-duotone] text-2xl"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate">Bed Terisi</p>
                <p class="text-lg sm:text-2xl font-black font-mono text-indigo-600 dark:text-indigo-400 leading-tight">
                    {{ number_format($totalTerisiRS) }}
                    <span class="text-xs font-semibold font-body text-slate-400">Bed</span>
                </p>
                <p class="text-[10px] text-indigo-600/80 dark:text-indigo-400/80 font-medium truncate mt-0.5">Sedang
                    dirawat</p>
            </div>
        </div>

        {{-- Card 4: Tingkat Okupansi (BOR) --}}
        <div
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition hover:shadow-md">
            <div
                class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                <span class="icon-[solar--pie-chart-2-bold-duotone] text-2xl"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate">Tingkat Okupansi</p>
                <p class="text-lg sm:text-2xl font-black font-mono text-teal-600 dark:text-teal-400 leading-tight">
                    {{ number_format($borRS, 1) }}%
                </p>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">BOR Real-time RS</p>
            </div>
        </div>
    </div>

    {{-- Section Header: Pilihan Kelas Kamar --}}
    <div class="flex items-center justify-between mb-3.5">
        <div>
            <h2
                class="text-sm sm:text-base font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-4 rounded-full bg-emerald-500 inline-block"></span>
                <span>Kategori Kelas Kamar</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Klik kartu kelas kamar untuk membuka detail seluruh tempat tidur dalam pop-up modal.
            </p>
        </div>
    </div>

    {{-- Grid Kartu Kelas Kamar --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4 mb-8">
        @foreach ($rooms as $key => $room)
            <x-room-by-class-item :title="$key" :isActive="$roomActive === $key" :total="$room['total']" :available="$room['tersedia']"
                :filled="$room['terisi']" />
        @endforeach
    </div>

    {{-- Ringkasan Matriks Okupansi Kelas Kamar --}}
    <div
        class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm mb-6">
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800/80 mb-4">
            <div class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <span class="icon-[solar--chart-square-bold-duotone] text-lg"></span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">
                        Komparasi Okupansi Antar Kelas
                    </h3>
                    <p class="text-xs text-slate-400">Rekapitulasi persentase keterisian tempat tidur rumah sakit</p>
                </div>
            </div>
        </div>

        <div class="space-y-3.5">
            @foreach ($rooms as $key => $room)
                @php
                    $classTotal = $room['total'];
                    $classTerisi = $room['terisi'];
                    $classTersedia = $room['tersedia'];
                    $classPct = $classTotal > 0 ? round(($classTerisi / $classTotal) * 100, 1) : 0;
                    $classMeta = \App\Helpers\StatusHelper::getRoomClassMeta($key);
                @endphp
                <div
                    class="p-3 sm:p-3.5 rounded-xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition hover:bg-slate-100/60 dark:hover:bg-slate-800/60">
                    <div class="sm:w-56 shrink-0 flex items-center justify-between sm:justify-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $classMeta['iconColor'] }}">
                            <span class="{{ $classMeta['icon'] }} text-base sm:text-lg"></span>
                        </div>
                        <div class="min-w-0">
                            <span
                                class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight block truncate">{{ $key }}</span>
                            <span
                                class="text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400 block">({{ $classTotal }}
                                Bed)</span>
                        </div>
                    </div>

                    {{-- Visual Progress Bar --}}
                    <div class="flex-1 min-w-0 flex items-center gap-3">
                        <div class="flex-1 h-2.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex">
                            <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-500 transition-all duration-700"
                                style="width: {{ $classPct }}%"></div>
                        </div>
                        <span
                            class="w-12 text-right text-xs font-mono font-bold text-slate-700 dark:text-slate-300 shrink-0">
                            {{ $classPct }}%
                        </span>
                    </div>

                    {{-- Stats & Button Aksi Cepat --}}
                    <div
                        class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60 dark:border-slate-700/60">
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span
                                class="text-emerald-600 dark:text-emerald-400 font-bold font-mono">{{ $classTersedia }}</span>
                            Kosong &bull;
                            <span
                                class="text-indigo-600 dark:text-indigo-400 font-bold font-mono">{{ $classTerisi }}</span>
                            Terisi
                        </div>
                        <button type="button" wire:click="setShow('{{ $key }}')"
                            class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-700 hover:text-white dark:text-emerald-400 dark:hover:text-white text-xs font-bold transition cursor-pointer">
                            Buka Modal &rarr;
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL DETAIL KAMAR TEMPAT TIDUR PER KELAS --}}
    @if ($roomActive)
        @php
            $activeRoomsCollection = collect($roomList ?? []);
            $activeTotal = $activeRoomsCollection->count();
            $activeTersedia = $activeRoomsCollection->where('status', 'KOSONG')->count();
            $activeTerisi = $activeRoomsCollection->where('status', '!=', 'KOSONG')->count();
            $activeBOR = $activeTotal > 0 ? round(($activeTerisi / $activeTotal) * 100, 1) : 0;
            $activeMeta = \App\Helpers\StatusHelper::getRoomClassMeta($roomActive);
        @endphp

        <div x-data="{
            searchQuery: '',
            filterStatus: 'all',
            matchesRoom(room) {
                const q = this.searchQuery.toLowerCase().trim();
                const matchesSearch = !q ||
                    (room.kode_kamar && room.kode_kamar.toLowerCase().includes(q)) ||
                    (room.bangsal && room.bangsal.nama_bangsal && room.bangsal.nama_bangsal.toLowerCase().includes(q));
        
                const matchesStatus = this.filterStatus === 'all' ||
                    (this.filterStatus === 'KOSONG' && room.status === 'KOSONG') ||
                    (this.filterStatus === 'ISI' && room.status !== 'KOSONG');
        
                return matchesSearch && matchesStatus;
            }
        }" @keydown.escape.window="$wire.closeModal()"
            class="fixed inset-0 z-50 flex items-center !mt-0 justify-center p-3 sm:p-5 md:p-8 bg-slate-950/75 backdrop-blur-md overflow-hidden animate-in fade-in duration-200"
            role="dialog" aria-modal="true">

            {{-- Backdrop Klik untuk Menutup --}}
            <div class="fixed inset-0 -z-10 cursor-pointer" wire:click="closeModal"></div>

            {{-- Kotak Dialog Modal Utama --}}
            <div
                class="relative w-full max-w-5xl max-h-[92vh] sm:max-h-[88vh] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">

                {{-- Garis Aksen Gradien Atas --}}
                <div
                    class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-indigo-500">
                </div>

                {{-- Header Modal --}}
                <div
                    class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br text-white flex items-center justify-center shadow-md shrink-0 {{ $activeMeta['glowGradient'] }}">
                            <span class="{{ $activeMeta['icon'] }} text-xl sm:text-2xl"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3
                                    class="text-base sm:text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight truncate">
                                    Detail Kamar: {{ $roomActive }}
                                </h3>
                                <span
                                    class="hidden xs:inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Live</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">
                                Total: <strong class="text-slate-700 dark:text-slate-200 font-mono">{{ $activeTotal }}
                                    Bed</strong>
                                &bull; Tersedia: <strong
                                    class="text-emerald-600 dark:text-emerald-400 font-mono">{{ $activeTersedia }}</strong>
                                &bull; Terisi: <strong
                                    class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $activeTerisi }}</strong>
                                &bull; BOR: <strong
                                    class="text-teal-600 dark:text-teal-400 font-mono">{{ $activeBOR }}%</strong>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Toolbar Pencarian & Filter Cepat (Sticky di Bawah Header Modal) --}}
                <div
                    class="p-3 sm:p-4 bg-slate-50/80 dark:bg-slate-950/40 border-b border-slate-200/80 dark:border-slate-800/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 shrink-0">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-1 max-w-sm">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <span class="icon-[solar--magnifer-bold-duotone] text-sm sm:text-base"></span>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="Cari kode bed atau nama bangsal..."
                            class="w-full pl-9 pr-8 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition shadow-xs" />
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs p-0.5">
                            <span class="icon-[solar--close-circle-bold]"></span>
                        </button>
                    </div>

                    {{-- Filter Tabs (Semua, Tersedia, Terisi) --}}
                    <div
                        class="inline-flex items-center p-1 rounded-xl bg-slate-200/60 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 self-start sm:self-auto overflow-x-auto max-w-full">
                        <button type="button" @click="filterStatus = 'all'"
                            :class="filterStatus === 'all' ?
                                'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-xs font-bold' :
                                'text-slate-600 dark:text-slate-400 hover:text-slate-800 font-medium'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition cursor-pointer whitespace-nowrap">
                            Semua ({{ $activeTotal }})
                        </button>
                        <button type="button" @click="filterStatus = 'KOSONG'"
                            :class="filterStatus === 'KOSONG' ?
                                'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-xs font-bold' :
                                'text-slate-600 dark:text-slate-400 hover:text-emerald-600 font-medium'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Tersedia ({{ $activeTersedia }})</span>
                        </button>
                        <button type="button" @click="filterStatus = 'ISI'"
                            :class="filterStatus === 'ISI' ?
                                'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs font-bold' :
                                'text-slate-600 dark:text-slate-400 hover:text-indigo-600 font-medium'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            <span>Terisi ({{ $activeTerisi }})</span>
                        </button>
                    </div>
                </div>

                {{-- Modal Body: Scrollable Grid Kamar Bed --}}
                <div
                    class="flex-1 overflow-y-auto p-3.5 sm:p-5 md:p-6 custom-scrollbar bg-slate-50/30 dark:bg-slate-950/20">
                    <div
                        class="grid grid-cols-2 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2.5 sm:gap-3.5">
                        @foreach ($roomList as $room)
                            @php
                                $isTerisi = $room['status'] !== 'KOSONG';
                            @endphp
                            <div x-show="matchesRoom(@js($room))"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition-all duration-200 flex flex-col justify-between group hover:shadow-md hover:-translate-y-0.5 {{ $isTerisi ? 'bg-white dark:bg-slate-900 border-indigo-100 dark:border-indigo-950/60 hover:border-indigo-500/40' : 'bg-white dark:bg-slate-900 border-emerald-100 dark:border-emerald-950/60 hover:border-emerald-500/40' }}">

                                {{-- Bagian Atas: Badge Status & Kelas --}}
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-2">
                                        <span
                                            class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full {{ $isTerisi ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/40' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $isTerisi ? 'bg-indigo-500' : 'bg-emerald-500' }}"></span>
                                            <span>{{ $isTerisi ? 'Terisi' : 'Kosong' }}</span>
                                        </span>

                                        <span class="text-[9px] font-mono font-bold text-slate-400 truncate">
                                            {{ $room['kelas'] }}
                                        </span>
                                    </div>

                                    {{-- Center: Icon Bed & Nomor Kamar --}}
                                    <div class="flex flex-col items-center text-center my-1">
                                        <div
                                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center mb-1.5 {{ $isTerisi ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                                            <span class="icon-[solar--bed-bold-duotone] text-xl sm:text-2xl"></span>
                                        </div>
                                        <span
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">No.
                                            Kamar</span>
                                        <span
                                            class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight truncate w-full"
                                            title="{{ $room['kode_kamar'] }}">
                                            {{ $room['kode_kamar'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Bagian Bawah: Informasi Bangsal & Tarif --}}
                                <div
                                    class="pt-2 mt-1.5 border-t border-slate-100 dark:border-slate-800/80 text-[10px] sm:text-[11px] space-y-0.5">
                                    <div class="flex items-center gap-1 text-slate-600 dark:text-slate-300 font-semibold truncate"
                                        title="{{ $room['bangsal']['nama_bangsal'] ?? 'Unit Tidak Diketahui' }}">
                                        <span
                                            class="icon-[solar--hospital-bold-duotone] text-xs text-slate-400 shrink-0"></span>
                                        <span class="truncate">{{ $room['bangsal']['nama_bangsal'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-400 text-[10px]">
                                        <span>Tarif</span>
                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            Rp{{ number_format($room['tarif_kamar'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div
                    class="p-3 sm:p-4 border-t border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 font-medium">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Siap Pakai</span>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <span>Terisi Pasien</span>
                        </span>
                    </div>

                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-content>
