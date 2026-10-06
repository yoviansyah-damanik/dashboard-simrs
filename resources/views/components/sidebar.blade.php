<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="fixed left-0 top-0 z-[99] flex h-screen w-72 flex-col bg-white/95 dark:bg-slate-950/95 border-r border-slate-200/90 dark:border-slate-800/80 duration-300 ease-in-out backdrop-blur-2xl shadow-xl dark:shadow-2xl"
    @click.outside="sidebarToggle = false" x-on:livewire:navigated.window="sidebarToggle = false">
    {{-- Tombol Toggle Hamburger (Mobile Floating) --}}
    <div class="absolute top-4 -right-12 lg:hidden">
        <button
            class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/95 dark:bg-slate-900/95 border border-slate-200 dark:border-slate-700/80 text-emerald-600 dark:text-emerald-400 shadow-xl backdrop-blur-md hover:text-emerald-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            @click.stop="sidebarToggle = !sidebarToggle" title="Tutup Menu">
            <span class="icon-[solar--close-circle-bold] text-2xl" x-show="sidebarToggle"></span>
            <span class="icon-[solar--hamburger-menu-bold] text-2xl" x-show="!sidebarToggle"></span>
        </button>
    </div>

    {{-- SIDEBAR HEADER / BRANDING --}}
    <div
        class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-800/80 bg-gradient-to-b from-slate-50/80 dark:from-white/[0.02] to-transparent">
        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3.5 group">
            <div class="relative flex items-center justify-center shrink-0">
                <div
                    class="absolute inset-0 bg-emerald-500/20 rounded-full blur-md scale-125 opacity-70 group-hover:opacity-100 transition duration-300 pointer-events-none">
                </div>
                <img src="{{ Vite::image('logo.png') }}" class="relative h-11 w-auto object-contain drop-shadow"
                    alt="Logo" />
            </div>
            <div class="min-w-0 flex-1">
                <div
                    class="relative font-black text-sm tracking-tight uppercase truncate bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-teal-700 to-slate-900 dark:from-emerald-400 dark:via-teal-200 dark:to-white leading-tight">
                    {{ env('APP_NAME') }}
                </div>
                <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate leading-5">
                    {{ config('app.hospital_name') }}
                </div>
                <div
                    class="inline-flex items-center gap-1 text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="leading-tight">{{ GeneralHelper::getVersion()['version'] }}</span>
                </div>
            </div>
        </a>
    </div>
    {{-- SIDEBAR HEADER --}}

    {{-- DAFTAR NAVIGASI MENU --}}
    <div class="flex flex-col flex-1 overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="px-3.5 py-4 space-y-6" x-data="{ selected: $persist('home') }">
            @foreach ($menus as $menu)
                <div>
                    <h3
                        class="mb-2 px-3 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-400/80">
                        {{ $menu['title'] }}
                    </h3>

                    <ul class="flex flex-col gap-1">
                        @forelse ($menu['items'] as $item)
                            @if (!empty($item['items']))
                                @php
                                    $hasActiveChild = collect($item['items'])->some(fn($x) => $x['isActive'] ?? false);
                                @endphp
                                <li>
                                    <a href="#"
                                        @click.prevent="selected = (selected === '{{ Str::of($item['title'])->lower()->snake() }}' ? '' : '{{ Str::of($item['title'])->lower()->snake() }}')"
                                        @class([
                                            'group relative flex items-center justify-between gap-2.5 rounded-xl px-3 py-2.5 text-xs font-semibold duration-200 ease-in-out transition-all',
                                            'bg-slate-100 text-slate-900 dark:bg-white/[0.04] dark:text-white border border-slate-200 dark:border-white/10' => $hasActiveChild,
                                            'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-white/[0.04]' => !$hasActiveChild,
                                        ])>
                                        <div class="flex items-center gap-2.5 truncate">
                                            <span
                                                class="{{ $item['icon'] }} size-4.5 text-emerald-600 dark:text-emerald-400 shrink-0"></span>
                                            <span class="truncate">{{ $item['title'] }}</span>
                                        </div>

                                        <span
                                            class="icon-[solar--alt-arrow-down-bold] text-xs text-slate-400 transition-transform duration-200 shrink-0"
                                            :class="{
                                                'rotate-180 text-emerald-600 dark:text-emerald-400': (
                                                    selected === '{{ Str::of($item['title'])->lower()->snake() }}')
                                            }"></span>
                                    </a>

                                    <div class="overflow-hidden transition-all duration-200"
                                        :class="(selected === '{{ Str::of($item['title'])->lower()->snake() }}') ? 'block' :
                                        'hidden'">
                                        <ul
                                            class="my-1.5 ml-4 flex flex-col gap-1 border-l border-slate-200 dark:border-slate-800/90 pl-3">
                                            @forelse ($item['items'] as $item_)
                                                <li>
                                                    <a href="{{ $item_['href'] }}" @class([
                                                        'group relative flex items-center justify-between gap-2 px-2.5 py-1.5 text-xs rounded-lg duration-150 transition-all',
                                                        'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 font-bold' =>
                                                            $item_['isActive'],
                                                        'text-slate-500 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/[0.03] font-medium' => !$item_[
                                                            'isActive'
                                                        ],
                                                    ])
                                                        wire:navigate>
                                                        <span class="truncate">{{ $item_['title'] }}</span>
                                                        @if ($item_['isActive'])
                                                            <span
                                                                class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 shrink-0"></span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @empty
                                                <li>
                                                    <span
                                                        class="block px-3 py-1.5 text-xs text-slate-400 dark:text-slate-500 italic">
                                                        Tidak ada menu
                                                    </span>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </li>
                            @else
                                @if ($item['href'] != '#')
                                    <li>
                                        <a href="{{ $item['href'] }}"
                                            @click.prevent="selected = (selected === '{{ Str::of($item['title'])->lower()->snake() }}' ? '' : '{{ Str::of($item['title'])->lower()->snake() }}')"
                                            @class([
                                                'group relative flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-semibold duration-200 ease-in-out transition-all',
                                                'bg-gradient-to-r from-emerald-500/15 to-teal-500/10 text-emerald-800 border border-emerald-500/30 dark:from-emerald-500/20 dark:to-teal-500/10 dark:text-emerald-300 shadow-sm' =>
                                                    $item['isActive'],
                                                'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-white/[0.04]' => !$item[
                                                    'isActive'
                                                ],
                                            ]) wire:navigate>
                                            <span
                                                class="{{ $item['icon'] }} size-4.5 text-emerald-600 dark:text-emerald-400 shrink-0"></span>
                                            <span class="truncate">{{ $item['title'] }}</span>
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <span @class([
                                            'group relative flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-semibold duration-200 ease-in-out transition-all opacity-60 cursor-not-allowed',
                                            'bg-gradient-to-r from-emerald-500/15 to-teal-500/10 text-emerald-800 border border-emerald-500/30 dark:from-emerald-500/20 dark:to-teal-500/10 dark:text-emerald-300' =>
                                                $item['isActive'],
                                            'text-slate-400 dark:text-slate-400' => !$item['isActive'],
                                        ])>
                                            <span
                                                class="{{ $item['icon'] }} size-4.5 text-emerald-600 dark:text-emerald-400 shrink-0"></span>
                                            <span class="truncate">{{ $item['title'] }}</span>
                                        </span>
                                    </li>
                                @endif
                            @endif
                        @empty
                            <li>
                                <span
                                    class="block px-3 py-2 text-xs text-center text-slate-400 dark:text-slate-500 italic">
                                    Tidak ada menu ditemukan
                                </span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </nav>
    </div>
</aside>
