<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ Vite::image('logo-icon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ Vite::image('logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ Vite::image('logo-icon.png') }}">

    <title>@yield('code', 'Error') - @yield('title', 'Terjadi Kesalahan') | {{ config('app.hospital_name', 'Rumkit Tk. IV Padangsidimpuan') }}</title>
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
    @php
        $setting = cache()->remember('simrs_setting_profile', 3600, function () {
            try {
                return (array) \Illuminate\Support\Facades\DB::connection('simrs')
                    ->table('setting')
                    ->select('nama_instansi', 'kontak', 'email')
                    ->first();
            } catch (\Throwable $e) {
                return [];
            }
        });

        $hospitalName = $setting['nama_instansi'] ?? config('app.hospital_name', 'Rumkit Tk. IV Padangsidimpuan');
        $contactNumber = $setting['kontak'] ?? '081260811173';
        $contactEmail = $setting['email'] ?? 'rumkittnipsp@gmail.com';
        $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $contactNumber));
    @endphp

    <div x-data="{
            darkMode: JSON.parse(localStorage.getItem('darkMode')) ?? true
        }"
        x-init="$watch('darkMode', value => {
            localStorage.setItem('darkMode', JSON.stringify(value));
            document.documentElement.classList.toggle('dark', value);
        });
        document.documentElement.classList.toggle('dark', darkMode);"
        class="min-h-screen w-full relative flex flex-col justify-between p-4 sm:p-6 lg:p-8 overflow-hidden">

        <!-- Background Ambient Glow & Mesh Orbs -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            @yield('ambient_glow')
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-[140px]"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-teal-500/10 dark:bg-teal-500/15 rounded-full blur-[140px]"></div>
            <div class="absolute inset-0 bg-radial-gradient from-transparent via-transparent to-slate-900/10 dark:to-black/40"></div>
        </div>

        <!-- Top Header: Logo RS & Dark Mode Switcher -->
        <header class="relative z-10 w-full max-w-5xl mx-auto flex items-center justify-between py-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                <div class="w-10 h-10 rounded-xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-center p-1.5 shadow-sm group-hover:border-emerald-500/40 transition-all duration-300">
                    <img src="{{ Vite::image('logo-icon.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-black bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-400 dark:to-cyan-400 bg-clip-text text-transparent tracking-wide">
                        {{ env('APP_NAME', 'SIMRS') }}
                    </span>
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate max-w-[220px] sm:max-w-none">
                        {{ $hospitalName }}
                    </span>
                </div>
            </a>

            <!-- Dark Mode Toggle Button -->
            <button type="button" @click="darkMode = !darkMode"
                class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-sm text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/30 transition-all duration-200 cursor-pointer active:scale-95 group"
                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                <template x-if="darkMode">
                    <div class="flex items-center gap-1.5">
                        <span class="icon-[solar--sun-2-bold-duotone] text-lg text-amber-400 group-hover:rotate-45 transition-transform duration-300"></span>
                        <span class="text-xs font-bold tracking-wide hidden sm:inline-block">Light</span>
                    </div>
                </template>
                <template x-if="!darkMode">
                    <div class="flex items-center gap-1.5">
                        <span class="icon-[solar--moon-stars-bold-duotone] text-lg text-emerald-600 group-hover:-rotate-12 transition-transform duration-300"></span>
                        <span class="text-xs font-bold tracking-wide hidden sm:inline-block">Dark</span>
                    </div>
                </template>
            </button>
        </header>

        <!-- Main Content: Error Card -->
        <main class="relative z-10 w-full max-w-2xl mx-auto my-auto py-8">
            <div class="relative bg-white/85 dark:bg-slate-900/85 backdrop-blur-2xl rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-2xl p-6 sm:p-10 text-center overflow-hidden">
                <!-- Inner Decorative Glow -->
                <div class="absolute -top-24 -left-24 w-48 h-48 @yield('badge_glow', 'bg-emerald-500/10') rounded-full blur-2xl pointer-events-none"></div>

                <!-- Icon Capsule -->
                <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 rounded-3xl @yield('badge_bg', 'bg-emerald-500/10 dark:bg-emerald-500/15') @yield('badge_text', 'text-emerald-600 dark:text-emerald-400') @yield('badge_border', 'border border-emerald-500/20') shadow-inner mb-6 relative">
                    <span class="@yield('icon_class', 'icon-[solar--danger-triangle-bold-duotone]') text-4xl sm:text-5xl"></span>
                    <span class="absolute -bottom-2 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-black uppercase tracking-wider bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm @yield('badge_text', 'text-emerald-600 dark:text-emerald-400')">
                        HTTP @yield('code', 'ERR')
                    </span>
                </div>

                <!-- Big Numeric Status Code Graphic -->
                <h1 class="text-5xl sm:text-7xl font-black font-mono tracking-tight leading-none mb-3 @yield('title_gradient', 'bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-400 dark:to-cyan-400 bg-clip-text text-transparent')">
                    @yield('code', 'Error')
                </h1>

                <!-- Title & Description -->
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white mb-2">
                    @yield('title', 'Terjadi Kesalahan')
                </h2>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg mx-auto mb-8 font-medium">
                    @yield('message', 'Permintaan Anda tidak dapat diproses oleh sistem. Silakan periksa kembali atau hubungi tim teknis.')
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @yield('actions')
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 transition-all duration-200 active:scale-95">
                        <span class="icon-[solar--home-2-bold-duotone] text-lg"></span>
                        <span>Ke Beranda</span>
                    </a>
                    <button type="button" onclick="history.back()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-bold border border-slate-200 dark:border-slate-700 transition-all duration-200 active:scale-95">
                        <span class="icon-[solar--arrow-left-bold-duotone] text-lg"></span>
                        <span>Halaman Sebelumnya</span>
                    </button>
                </div>

                <!-- Technical Support Contact Footnote -->
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-semibold">Butuh bantuan IT SIMRS?</span>
                    @if ($waNumber)
                        <a href="https://wa.me/{{ $waNumber }}?text=Halo%20Tim%20IT%20SIMRS,%20saya%20mengalami%20kendala%20error%20HTTP%20@yield('code')"
                            target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            <span class="icon-[solar--chat-round-dots-bold-duotone] text-sm"></span>
                            <span>WhatsApp Support</span>
                        </a>
                    @endif
                    @if ($contactEmail)
                        <a href="mailto:{{ $contactEmail }}?subject=Laporan%20Kendala%20SIMRS%20HTTP%20@yield('code')"
                            class="inline-flex items-center gap-1.5 font-bold text-slate-600 dark:text-slate-300 hover:underline">
                            <span class="icon-[solar--letter-bold-duotone] text-sm"></span>
                            <span>{{ $contactEmail }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-5xl mx-auto text-center py-2 text-xs font-semibold text-slate-400 dark:text-slate-500">
            &copy; {{ date('Y') }} {{ $hospitalName }} &bull; Sistem Informasi Manajemen Rumah Sakit
        </footer>
    </div>
</body>

</html>
