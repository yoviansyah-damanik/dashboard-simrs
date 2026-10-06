<header
    class="sticky top-0 z-40 flex w-full items-center justify-between px-4 sm:px-6 py-2.5 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 shadow-sm transition-colors duration-200">
    {{-- Sisi Kiri: Tombol Toggle Menu --}}
    <div class="flex items-center gap-2 sm:gap-3">
        <button type="button" @click.stop="sidebarToggle = !sidebarToggle"
            class="group inline-flex items-center gap-2 h-9 px-3 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/15 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 text-emerald-800 dark:text-emerald-300 transition-all duration-200 active:scale-95 cursor-pointer"
            title="Buka / Tutup Menu Navigasi">
            <span class="icon-[solar--hamburger-menu-bold-duotone] text-lg text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-200"></span>
            <span class="hidden sm:inline-block text-xs font-bold tracking-wide text-emerald-900 dark:text-emerald-200">Menu</span>
        </button>
    </div>

    {{-- Sisi Kanan: Jam Real-Time & Kartu Profil Pengguna --}}
    <div class="flex items-center gap-3 sm:gap-4">
        {{-- Widget Waktu & Tanggal Real-Time Modern (Tanpa Border) --}}
        <div class="flex items-center gap-2.5 h-9 px-3 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 backdrop-blur-sm">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-500/15 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 shrink-0">
                <span class="icon-[solar--clock-circle-bold-duotone] text-sm"></span>
            </div>
            <div class="flex flex-col justify-center leading-none">
                <span id="time" class="text-xs sm:text-[13px] font-black font-mono tracking-wider text-slate-800 dark:text-white"></span>
                <span id="date" class="text-[9px] font-semibold text-emerald-700/80 dark:text-emerald-300/80 mt-0.5"></span>
            </div>
        </div>

        {{-- Separator Antara Jam & Tombol Akun --}}
        <div class="h-5 w-px bg-slate-200 dark:bg-slate-800" aria-hidden="true"></div>

        {{-- Area Profil Pengguna dengan Dropdown --}}
        <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
            <button type="button" @click.prevent="dropdownOpen = !dropdownOpen"
                class="flex items-center gap-2.5 sm:gap-3 p-1 sm:px-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/[0.06] transition duration-200 cursor-pointer group">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full overflow-hidden ring-2 ring-emerald-500/40 bg-slate-200 dark:bg-slate-800 shrink-0 group-hover:ring-emerald-500 transition">
                    @php
                        $userName = auth()->user()?->name ?? 'Pengguna SIMRS';
                        $userRole = auth()->user()?->role_name ?? 'Staf';
                        $avatarIndex = (abs(crc32((string) (auth()->id() ?? '1'))) % 5) + 1;
                    @endphp
                    <img src="{{ Vite::image('user/' . $avatarIndex . '.png') }}"
                        alt="{{ $userName }}" class="w-full h-full object-cover" />
                </div>
                <div class="hidden text-left sm:block">
                    <div class="text-xs font-bold text-slate-800 dark:text-white truncate max-w-[140px] group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors">{{ $userName }}</div>
                    <div class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide truncate max-w-[140px]">
                        {{ $userRole }}
                    </div>
                </div>
                <span class="icon-[solar--alt-arrow-down-bold] text-xs text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200 transition-transform duration-200 shrink-0"
                    :class="{ 'rotate-180 text-emerald-600 dark:text-emerald-400': dropdownOpen }"></span>
            </button>

            {{-- Dropdown Card Floating --}}
            <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                class="absolute right-0 mt-2 w-64 rounded-2xl bg-white/95 dark:bg-slate-950/95 backdrop-blur-2xl border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl p-2 z-50 overflow-hidden"
                x-cloak>
                {{-- Header Pengguna di Dropdown --}}
                <div class="px-3 py-2.5 mb-1 border-b border-slate-200/80 dark:border-slate-800/80">
                    <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $userName }}</p>
                    <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mt-0.5">
                        {{ $userRole }}
                    </p>
                </div>

                {{-- Menu Cepat Dropdown --}}
                <ul class="space-y-0.5 py-1">
                    @foreach ($menus as $menu)
                        <li>
                            <a href="{{ $menu['href'] }}" wire:navigate
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/80 dark:text-slate-300 dark:hover:text-white dark:hover:bg-white/[0.06] transition duration-150">
                                <span class="{{ $menu['icon'] }} size-4 text-emerald-600 dark:text-emerald-400"></span>
                                <span>{{ $menu['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Saklar Mode Gelap --}}
                <div class="my-1 px-3 py-2 border-t border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 font-medium">
                    <div class="flex items-center gap-2">
                        <span class="icon-[solar--moon-stars-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                        <span>Mode Gelap</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" :checked="darkMode" @change="darkMode = !darkMode" class="sr-only peer" />
                        <div
                            class="w-8 h-4.5 bg-slate-300 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:bg-emerald-500 transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:after:bg-slate-300 peer-checked:after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:after:translate-x-3.5">
                        </div>
                    </label>
                </div>

                {{-- Tombol Logout --}}
                <div class="pt-1">
                    <livewire:auth.logout />
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script type="module">
        moment.locale('id');
        function updateClock() {
            const timeEl = document.getElementById('time');
            const dateEl = document.getElementById('date');
            if (timeEl) timeEl.innerHTML = moment().format('HH:mm:ss');
            if (dateEl) dateEl.innerHTML = moment().format('LL');
        }
        updateClock();
        window.setInterval(updateClock, 1000);
    </script>
@endpush
