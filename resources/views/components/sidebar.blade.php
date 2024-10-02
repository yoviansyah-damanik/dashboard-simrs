<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="absolute left-0 top-0 z-[99] flex h-screen w-72.5 flex-col bg-primary duration-300 ease-linear dark:bg-boxdark drop-shadow-1
dark:drop-shadow-none"
    @click.outside="sidebarToggle = false">
    {{-- Hamburger Toggle BTN --}}
    <div class="absolute mx-4 my-4 left-full">
        <button
            class="flex items-center gap-3 p-3 transition duration-150 rounded-md shadow-sm z-99999 bg-primary dark:bg-boxdark"
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
        <div class="font-bold text-center text-secondary">
            {{ config('app.name') }}
        </div>
        <div class="text-sm font-normal text-center text-secondary">
            {{ config('app.hospital_name') }}
        </div>
        <div class="text-xs text-center text-whiten">
            {{ GeneralHelper::getVersion() }}
        </div>
    </div>
    {{-- SIDEBAR HEADER --}}

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="px-3 py-4 lg:px-4" x-data="{ selected: $persist('home') }">
            @foreach ($menus as $menu)
                <h3 class="mb-4 ml-4 text-sm font-bold uppercase font-title text-secondary">{{ $menu['title'] }}</h3>

                <ul class="mb-6 flex flex-col gap-1.5">
                    @foreach ($menu['items'] as $item)
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
                                        @foreach ($item['items'] as $item_)
                                            <li>
                                                <a href="{{ $item_['href'] }}" @class([
                                                    'group relative flex items-center gap-2.5 rounded-md px-3 font-medium text-bodydark2 duration-300 ease-in-out hover:text-white',
                                                    '!text-white' => $item_['isActive'],
                                                ])
                                                    wire:navigate>
                                                    {{ $item_['title'] }}
                                                </a>
                                            </li>
                                        @endforeach
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
                    @endforeach
                </ul>
            @endforeach
            {{-- <div>
                <h3 class="mb-4 ml-4 text-sm font-medium text-bodydark1">MENU</h3>

                <ul class="mb-6 flex flex-col gap-1.5">
                    <li>
                        <a class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-primarydark dark:hover:bg-meta-4"
                            href="#" @click.prevent="selected = (selected === 'Dashboard' ? '':'Dashboard')"
                            :class="{
                                'bg-primarydark dark:bg-meta-4': (selected === 'Dashboard') || (page === 'ecommerce' ||
                                    page === 'analytics' || page === 'stocks')
                            }">
                            <span class="i-ph-archive-box size-5"></span>

                            Dashboard

                            <svg class="absolute transition -translate-y-1/2 fill-current right-4 top-1/2"
                                :class="{ 'rotate-180': (selected === 'Dashboard') }" width="20" height="20"
                                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"
                                    fill="" />
                            </svg>
                        </a>

                        <div class="overflow-hidden transform translate"
                            :class="(selected === 'Dashboard') ? 'block' : 'hidden'">
                            <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-6">
                                <li>
                                    <a class="group relative flex items-center gap-2.5 rounded-md px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:text-white"
                                        href="index.html" :class="page === 'ecommerce' && '!text-white'">eCommerce
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-primarydark dark:hover:bg-meta-4"
                            href="calendar.html" @click="selected = (selected === 'Calendar' ? '':'Calendar')"
                            :class="{ 'bg-primarydark dark:bg-meta-4': (selected === 'Calendar') && (page === 'calendar') }">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.7499 2.9812H14.2874V2.36245C14.2874 2.02495 14.0062 1.71558 13.6405 1.71558C13.2749 1.71558 12.9937 1.99683 12.9937 2.36245V2.9812H4.97803V2.36245C4.97803 2.02495 4.69678 1.71558 4.33115 1.71558C3.96553 1.71558 3.68428 1.99683 3.68428 2.36245V2.9812H2.2499C1.29365 2.9812 0.478027 3.7687 0.478027 4.75308V14.5406C0.478027 15.4968 1.26553 16.3125 2.2499 16.3125H15.7499C16.7062 16.3125 17.5218 15.525 17.5218 14.5406V4.72495C17.5218 3.7687 16.7062 2.9812 15.7499 2.9812ZM1.77178 8.21245H4.1624V10.9968H1.77178V8.21245ZM5.42803 8.21245H8.38115V10.9968H5.42803V8.21245ZM8.38115 12.2625V15.0187H5.42803V12.2625H8.38115ZM9.64678 12.2625H12.5999V15.0187H9.64678V12.2625ZM9.64678 10.9968V8.21245H12.5999V10.9968H9.64678ZM13.8374 8.21245H16.228V10.9968H13.8374V8.21245ZM2.2499 4.24683H3.7124V4.83745C3.7124 5.17495 3.99365 5.48433 4.35928 5.48433C4.7249 5.48433 5.00615 5.20308 5.00615 4.83745V4.24683H13.0499V4.83745C13.0499 5.17495 13.3312 5.48433 13.6968 5.48433C14.0624 5.48433 14.3437 5.20308 14.3437 4.83745V4.24683H15.7499C16.0312 4.24683 16.2562 4.47183 16.2562 4.75308V6.94683H1.77178V4.75308C1.77178 4.47183 1.96865 4.24683 2.2499 4.24683ZM1.77178 14.5125V12.2343H4.1624V14.9906H2.2499C1.96865 15.0187 1.77178 14.7937 1.77178 14.5125ZM15.7499 15.0187H13.8374V12.2625H16.228V14.5406C16.2562 14.7937 16.0312 15.0187 15.7499 15.0187Z"
                                    fill="" />
                            </svg>

                            Calendar
                        </a>
                    </li>
                </ul>
            </div> --}}
        </nav>
    </div>
</aside>
