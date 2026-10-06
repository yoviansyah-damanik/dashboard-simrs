<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ Vite::image('logo-icon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ Vite::image('logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ Vite::image('logo-icon.png') }}">

    <title>{{ ($title ?? env('APP_NAME')) . ' - ' . config('app.hospital_name', 'Rumkit Tk. IV Padangsidimpuan') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'false') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-body antialiased min-h-screen selection:bg-emerald-500 selection:text-white overflow-x-hidden transition-colors duration-300">
    {{ $slot }}

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
</body>

</html>
