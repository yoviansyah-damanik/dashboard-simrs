<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="fixed left-0 top-0 z-[99] flex h-screen w-72.5 flex-col bg-primary-500 duration-300 ease-linear dark:bg-boxdark drop-shadow-1
dark:drop-shadow-none"
    @click.outside="sidebarToggle = false">
    {{-- Hamburger Toggle BTN --}}
    <div class="absolute mx-4 my-4 left-full">
        <button
            class="flex items-center gap-3 p-3 transition duration-150 rounded-md shadow-sm z-99999 bg-primary-500 dark:bg-boxdark"
            @click.stop="sidebarToggle = !sidebarToggle">
            <div class="relative block h-5.5 w-5.5 cursor-pointer">
                <span class="absolute right-0 w-full h-full du-block">
                    <span
                        class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-white delay-[0] duration-200 ease-in-out"
                        :class="{ '!w-full delay-300 dark:!bg-white': !sidebarToggle }"></span>
                    <span
                        class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-white delay-150 duration-200 ease-in-out"
                        :class="{ '!w-full delay-400 dark:!bg-white': !sidebarToggle }"></span>
                    <span
                        class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-white delay-200 duration-200 ease-in-out"
                        :class="{ '!w-full delay-500 dark:!bg-white': !sidebarToggle }"></span>
                </span>
                <span class="absolute right-0 w-full h-full rotate-45 du-block">
                    <span
                        class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-white delay-300 duration-200 ease-in-out"
                        :class="{ '!h-0 delay-[0] dark:!bg-white': !sidebarToggle }"></span>
                    <span
                        class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-white duration-200 ease-in-out"
                        :class="{ '!h-0 delay-200 dark:!bg-white': !sidebarToggle }"></span>
                </span>
            </div>
            <div class="hidden text-white lg:block">
                Menu
            </div>
        </button>
    </div>
    {{-- Hamburger Toggle BTN --}}

    {{-- SIDEBAR HEADER --}}
    <div class="text-center px-6 py-5.5 lg:py-6.5 bg-primarydark dark:bg-slate-800">
        <a href="{{ route('home') }}" wire:navigate class="inline">
            <img src="{{ Vite::image('logo.png') }}" class="h-full mx-auto max-h-32" alt="Logo" />
        </a>
    </div>

    <div class="px-3 py-3 mt-3 mb-3 bg-primarydark dark:bg-slate-800">
        <div class="font-bold text-center text-secondary-500">
            {{ config('app.name') }}
        </div>
        <div class="text-sm font-normal text-center text-secondary-500">
            {{ config('app.hospital_name') }}
        </div>
        <div class="text-xs text-center text-whiten">
            <x-button color="transparent" size="sm">
                {{ GeneralHelper::getVersion()['version'] }}
            </x-button>
        </div>
    </div>
    {{-- SIDEBAR HEADER --}}

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="px-3 py-4 lg:px-4" x-data="{ selected: $persist('home') }">
            @foreach ($menus as $menu)
                <h3 class="mb-4 ml-4 text-sm font-bold tracking-[.25em] uppercase font-title text-secondary-500">
                    {{ $menu['title'] }}</h3>

                <ul class="mb-6 flex flex-col gap-1.5">
                    @forelse ($menu['items'] as $item)
                        @if (!empty($item['items']))
                            <li>
                                <a href="#"
                                    @click.prevent="selected = (selected === '{{ Str::of($item['title'])->lower()->snake() }}' ? '':'{{ Str::of($item['title'])->lower()->snake() }}')"
                                    @class([
                                        'group relative flex items-center gap-2.5 rounded-md px-3 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-primarydark dark:hover:bg-meta-4',
                                        'bg-primarydark dark:bg-meta-4' => collect($item['items'])->some(
                                            fn($x) => $x['isActive']),
                                    ])>
                                    <span class="{{ $item['icon'] }} size-5"></span>

                                    {{ $item['title'] }}

                                    <svg class="absolute transition -translate-y-1/2 fill-current right-4 top-1/2"
                                        :class="{
                                            'rotate-180': (
                                                selected === '{{ Str::of($item['title'])->lower()->snake() }}')
                                        }"
                                        width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"
                                            fill="" />
                                    </svg>
                                </a>

                                <div class="overflow-hidden transform translate"
                                    :class="(selected === '{{ Str::of($item['title'])->lower()->snake() }}') ? 'block' :
                                    'hidden'">
                                    <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-6">
                                        @forelse ($item['items'] as $item_)
                                            <li>
                                                <a href="{{ $item_['href'] }}" @class([
                                                    'group relative flex items-center gap-2.5 rounded-md px-3 font-medium text-bodydark2 duration-300 ease-in-out hover:text-white',
                                                    '!text-white' => $item_['isActive'],
                                                ])
                                                    wire:navigate>
                                                    {{ $item_['title'] }}
                                                </a>
                                            </li>
                                        @empty
                                            <li>
                                                <a href="#"
                                                    class="relative block px-3 py-2 text-center duration-300 ease-in-out rounded-md group text-bodydark1 hover:bg-primarydark dark:hover:bg-meta-4">
                                                    Tidak ada menu ditemukan
                                                </a>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </li>
                        @else
                            @if ($item['href'] != '#')
                                <li>
                                    <a href="{{ $item['href'] }}"
                                        @click.prevent="selected = (selected === '{{ Str::of($item['title'])->lower()->snake() }}' ? '':'{{ Str::of($item['title'])->lower()->snake() }}')"
                                        @class([
                                            'group relative flex items-center gap-2.5 rounded-md px-3 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-primarydark dark:hover:bg-meta-4',
                                            'bg-primarydark dark:bg-meta-4' => $item['isActive'],
                                        ]) wire:navigate>
                                        <span class="{{ $item['icon'] }} size-5"></span>

                                        {{ $item['title'] }}
                                    </a>
                                </li>
                            @else
                                <li>
                                    <span @class([
                                        'group relative flex items-center gap-2.5 rounded-md px-3 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-primarydark dark:hover:bg-meta-4',
                                        'bg-primarydark dark:bg-meta-4' => $item['isActive'],
                                    ])>
                                        <span class="{{ $item['icon'] }} size-5"></span>

                                        {{ $item['title'] }}
                                    </span>
                                </li>
                            @endif
                        @endif
                    @empty
                        <li>
                            <a href="#"
                                class="relative block px-3 py-2 text-center duration-300 ease-in-out rounded-md group text-bodydark1 hover:bg-primarydark dark:hover:bg-meta-4">
                                Tidak ada menu ditemukan
                            </a>
                        </li>
                    @endforelse
                </ul>
            @endforeach
        </nav>
    </div>
</aside>
