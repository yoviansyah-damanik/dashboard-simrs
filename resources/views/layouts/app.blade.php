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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ Vite::image('logo-icon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ Vite::image('logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ Vite::image('logo-icon.png') }}">

    <title>{{ ($title ?? env('APP_NAME', 'Dashboard SIMRS')) . ' - ' . config('app.hospital_name', 'Rumkit Tk. IV Padangsidimpuan') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': true }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')) ?? true;
sidebarToggle = JSON.parse(localStorage.getItem('sidebarToggle')) ?? false;
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
$watch('sidebarToggle', value => localStorage.setItem('sidebarToggle', JSON.stringify(value)));"
    :class="{
        'dark': darkMode === true,
        'after:overflow-hidden after:inset-0 after:z-[90] after:fixed after:bg-slate-950/70 after:backdrop-blur-sm': sidebarToggle === true
    }"
    class="font-body selection:bg-emerald-500 selection:text-white min-h-screen overflow-x-hidden antialiased bg-slate-50 dark:bg-[#090d16] text-slate-800 dark:text-slate-100">
    {{-- <x-preloader /> --}}

    <div class="relative flex h-screen overflow-hidden bg-slate-50/60 dark:bg-[#090d16]">
        {{-- Ambient Glow / Semi-semi Cahaya Latar Belakang --}}
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
            {{-- Cahaya 1: Atas Kanan Emerald Glow --}}
            <div class="absolute -top-32 right-1/4 h-[520px] w-[520px] rounded-full bg-emerald-400/15 dark:bg-emerald-500/10 blur-[130px]"></div>
            {{-- Cahaya 2: Tengah Kiri Teal Glow --}}
            <div class="absolute top-1/3 -left-20 h-[460px] w-[460px] rounded-full bg-teal-400/15 dark:bg-teal-500/10 blur-[130px]"></div>
            {{-- Cahaya 3: Bawah Kanan Emerald/Cyan Soft Aura --}}
            <div class="absolute -bottom-24 right-10 h-[440px] w-[440px] rounded-full bg-cyan-500/10 dark:bg-emerald-600/10 blur-[140px]"></div>
            {{-- Pola Gradasi Radial Halus --}}
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-10%,rgba(16,185,129,0.06),transparent)] dark:bg-[radial-gradient(ellipse_80%_50%_at_50%_-10%,rgba(16,185,129,0.12),transparent)]"></div>
        </div>

        <x-sidebar />

        <div class="relative z-10 flex flex-col flex-1 min-w-0 h-screen overflow-hidden">
            <x-header />

            <div class="relative flex-1 min-w-0 overflow-x-hidden overflow-y-auto">
                <main class="min-h-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                    {{ $slot }}
                </main>
            </div>

            <x-footer />
        </div>
    </div>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>

</html>
