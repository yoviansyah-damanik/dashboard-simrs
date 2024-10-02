<header class="sticky top-0 flex w-full bg-white z-[50] drop-shadow-1 dark:bg-boxdark dark:drop-shadow-none">
    <div class="flex items-center justify-end flex-grow gap-3 md:gap-5 lg:gap-7 sm:justify-end shadow-2 ">
        <div>
            <div id="serverTime" class="text-lg font-medium md:text-xl lg:text-3xl text-primary text-end">
            </div>
            <div id="serverDate" class="text-sm font-light lg:text-base">

            </div>
        </div>
        {{-- User Area --}}
        <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
            <a class="flex items-center gap-4 px-4 py-4 bg-gradient-to-r from-primary to-primarydark dark:bg-slate-800 md:px-6"
                href="#" @click.prevent="dropdownOpen = ! dropdownOpen">
                <div class="hidden text-right lg:block lg:w-64">
                    <div class="text-base font-medium truncate text-secondary">{{ auth()->user()->name }}</div>
                    <div class="text-sm font-normal text-white truncate">{{ auth()->user()->role_name }}</div>
                </div>

                <span class="w-12 h-12 overflow-hidden rounded-full">
                    <img src="{{ Vite::image('user/' . rand(1, 5) . '.png') }}" alt="User" />
                </span>

                {{-- <svg :class="dropdownOpen && 'rotate-180'" class="hidden fill-current sm:block" width="12"
                    height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z"
                        fill="" />
                </svg> --}}
            </a>

            {{-- Dropdown Start --}}
            <div x-show="dropdownOpen" x-transition
                class="absolute right-0 flex flex-col w-screen bg-white border rounded-sm lg:left-0 lg:w-auto border-stroke shadow-default dark:border-strokedark dark:bg-boxdark">
                <ul class="flex flex-col lg:hidden">
                    <li>
                        <div
                            class="flex flex-col items-center px-5 py-3 text-sm font-normal text-center duration-300 ease-in-out">
                            <span
                                class="block text-sm font-medium text-black dark:text-white">{{ auth()->user()->name }}</span>
                            <span class="block text-xs font-normal">{{ auth()->user()->role_name }}</span>
                        </div>
                    </li>
                </ul>
                <ul class="flex flex-col border-b border-stroke dark:border-strokedark">
                    @foreach ($menus as $menu)
                        <li>
                            <a href="{{ $menu['href'] }}"
                                class="flex items-center gap-3.5 px-5 py-3 text-sm font-normal duration-300 ease-in-out hover:bg-primary/5 hover:text-primary lg:text-base">
                                <span class="{{ $menu['icon'] }}"></span>
                                {{ $menu['title'] }}
                            </a>
                        </li>
                    @endforeach
                    <li class="border-t border-b">
                        <div class="flex items-center justify-between px-5 py-3">
                            Dark Mode
                            {{-- Dark Mode Toggler --}}
                            <label :class="darkMode ? 'bg-primary' : 'bg-stroke'"
                                class="relative m-0 block h-7.5 w-14 rounded-full">
                                <input type="checkbox" :value="darkMode" @change="darkMode = !darkMode"
                                    class="absolute top-0 z-50 w-full h-full m-0 opacity-0 cursor-pointer" />
                                <span :class="darkMode && '!right-1 !translate-x-full'"
                                    class="absolute flex items-center justify-center w-6 h-6 duration-75 ease-linear translate-x-0 -translate-y-1/2 bg-white rounded-full left-1 top-1/2 shadow-switcher">
                                    <span class="dark:hidden">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.99992 12.6666C10.5772 12.6666 12.6666 10.5772 12.6666 7.99992C12.6666 5.42259 10.5772 3.33325 7.99992 3.33325C5.42259 3.33325 3.33325 5.42259 3.33325 7.99992C3.33325 10.5772 5.42259 12.6666 7.99992 12.6666Z"
                                                fill="#969AA1" />
                                            <path
                                                d="M8.00008 15.3067C7.63341 15.3067 7.33342 15.0334 7.33342 14.6667V14.6134C7.33342 14.2467 7.63341 13.9467 8.00008 13.9467C8.36675 13.9467 8.66675 14.2467 8.66675 14.6134C8.66675 14.9801 8.36675 15.3067 8.00008 15.3067ZM12.7601 13.4267C12.5867 13.4267 12.4201 13.3601 12.2867 13.2334L12.2001 13.1467C11.9401 12.8867 11.9401 12.4667 12.2001 12.2067C12.4601 11.9467 12.8801 11.9467 13.1401 12.2067L13.2267 12.2934C13.4867 12.5534 13.4867 12.9734 13.2267 13.2334C13.1001 13.3601 12.9334 13.4267 12.7601 13.4267ZM3.24008 13.4267C3.06675 13.4267 2.90008 13.3601 2.76675 13.2334C2.50675 12.9734 2.50675 12.5534 2.76675 12.2934L2.85342 12.2067C3.11342 11.9467 3.53341 11.9467 3.79341 12.2067C4.05341 12.4667 4.05341 12.8867 3.79341 13.1467L3.70675 13.2334C3.58008 13.3601 3.40675 13.4267 3.24008 13.4267ZM14.6667 8.66675H14.6134C14.2467 8.66675 13.9467 8.36675 13.9467 8.00008C13.9467 7.63341 14.2467 7.33342 14.6134 7.33342C14.9801 7.33342 15.3067 7.63341 15.3067 8.00008C15.3067 8.36675 15.0334 8.66675 14.6667 8.66675ZM1.38675 8.66675H1.33341C0.966748 8.66675 0.666748 8.36675 0.666748 8.00008C0.666748 7.63341 0.966748 7.33342 1.33341 7.33342C1.70008 7.33342 2.02675 7.63341 2.02675 8.00008C2.02675 8.36675 1.75341 8.66675 1.38675 8.66675ZM12.6734 3.99341C12.5001 3.99341 12.3334 3.92675 12.2001 3.80008C11.9401 3.54008 11.9401 3.12008 12.2001 2.86008L12.2867 2.77341C12.5467 2.51341 12.9667 2.51341 13.2267 2.77341C13.4867 3.03341 13.4867 3.45341 13.2267 3.71341L13.1401 3.80008C13.0134 3.92675 12.8467 3.99341 12.6734 3.99341ZM3.32675 3.99341C3.15341 3.99341 2.98675 3.92675 2.85342 3.80008L2.76675 3.70675C2.50675 3.44675 2.50675 3.02675 2.76675 2.76675C3.02675 2.50675 3.44675 2.50675 3.70675 2.76675L3.79341 2.85342C4.05341 3.11342 4.05341 3.53341 3.79341 3.79341C3.66675 3.92675 3.49341 3.99341 3.32675 3.99341ZM8.00008 2.02675C7.63341 2.02675 7.33342 1.75341 7.33342 1.38675V1.33341C7.33342 0.966748 7.63341 0.666748 8.00008 0.666748C8.36675 0.666748 8.66675 0.966748 8.66675 1.33341C8.66675 1.70008 8.36675 2.02675 8.00008 2.02675Z"
                                                fill="#969AA1" />
                                        </svg>
                                    </span>
                                    <span class="hidden dark:inline-block">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M14.3533 10.62C14.2466 10.44 13.9466 10.16 13.1999 10.2933C12.7866 10.3667 12.3666 10.4 11.9466 10.38C10.3933 10.3133 8.98659 9.6 8.00659 8.5C7.13993 7.53333 6.60659 6.27333 6.59993 4.91333C6.59993 4.15333 6.74659 3.42 7.04659 2.72666C7.33993 2.05333 7.13326 1.7 6.98659 1.55333C6.83326 1.4 6.47326 1.18666 5.76659 1.48C3.03993 2.62666 1.35326 5.36 1.55326 8.28666C1.75326 11.04 3.68659 13.3933 6.24659 14.28C6.85993 14.4933 7.50659 14.62 8.17326 14.6467C8.27993 14.6533 8.38659 14.66 8.49326 14.66C10.7266 14.66 12.8199 13.6067 14.1399 11.8133C14.5866 11.1933 14.4666 10.8 14.3533 10.62Z"
                                                fill="#969AA1" />
                                        </svg>
                                    </span>
                                </span>
                            </label>
                            {{-- Dark Mode Toggler --}}
                        </div>
                    </li>
                </ul>
                <livewire:auth.logout />
            </div>
            {{-- Dropdown End --}}
        </div>
        {{-- User Area --}}
    </div>
</header>

@push('scripts')
    <script type="module">
        moment.locale('id')
        window.setInterval(function() {
            document.getElementById('serverTime').innerHTML = moment().format('H:m:s');
            document.getElementById('serverDate').innerHTML = moment().format('LL');
        }, 1000);
    </script>
@endpush
