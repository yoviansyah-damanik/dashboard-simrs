{{--
    Jika butuh bantuan dalam pengembangan ataupun ingin mentraktir kopi, silahkan hubungi saya.
    Yoviansyah Rizki Pratama
    +62 812 2277 8197
    yoviansyahrizkypratama@gmail.com
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ Vite::image('logo-icon.png') }}" type="image/x-icon">

    <title>{{ ($title ?? env('APP_NAME')) . ' - ' . env('HOSPITAL_NAME', '') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': true }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
sidebarToggle = JSON.parse(localStorage.getItem('sidebarToggle'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
$watch('sidebarToggle', value => localStorage.setItem('sidebarToggle', JSON.stringify(value)));"
    :class="{
        'dark text-bodydark bg-boxdark-2': darkMode ===
            true,
        'after:overflow-hidden after:inset-0 after:z-[98] after:absolute after:bg-primary/10 after:backdrop-blur-sm': sidebarToggle ===
            true
    }">
    <x-preloader />

    <div class="flex h-screen mx-auto overflow-hidden">
        <x-sidebar />
        <div class="relative flex flex-col flex-1">
            <x-header />
            <main class="flex-1 w-full px-6 overflow-x-hidden overflow-y-auto py-9">
                {{ $slot }}
            </main>
            <x-footer />
        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>

</html>
