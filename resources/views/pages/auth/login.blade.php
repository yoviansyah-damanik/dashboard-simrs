@php
    $setting =
        $setting ??
        cache()->remember('simrs_setting_profile', 3600, function () {
            try {
                return (array) \Illuminate\Support\Facades\DB::connection('simrs')
                    ->table('setting')
                    ->select('nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi', 'kontak', 'email')
                    ->first();
            } catch (\Throwable $e) {
                return [];
            }
        });

    $hospitalName = $setting['nama_instansi'] ?? config('app.hospital_name', 'Rumkit Tk. IV Padangsidimpuan');
    $kabupatenName = $setting['kabupaten'] ?? 'Padangsidimpuan';
    $hospitalAddress = $setting['alamat_instansi'] ?? 'Jl. Sudirman No. 1, Losung Batu';
    $hospitalCity = $setting['kabupaten'] ?? 'Padangsidimpuan';
    $contactNumber = $setting['kontak'] ?? '081260811173';
    $contactEmail = $setting['email'] ?? 'rumkittnipsp@gmail.com';
    $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $contactNumber));

    $allVersions = \App\Helpers\GeneralHelper::getAllVersions();
    $currentVersion = \App\Helpers\GeneralHelper::getVersion();
@endphp

<div x-data="{
    showHelp: false,
    showChangelog: false,
    darkMode: JSON.parse(localStorage.getItem('darkMode')) ?? true
}" x-init="$watch('darkMode', value => {
    localStorage.setItem('darkMode', JSON.stringify(value));
    document.documentElement.classList.toggle('dark', value);
});
document.documentElement.classList.toggle('dark', darkMode);"
    class="min-h-dvh w-full relative flex items-center justify-center p-3 sm:p-6 lg:p-12 overflow-hidden bg-slate-50 dark:bg-slate-950 font-body transition-colors duration-300">

    {{-- Tombol Toggle Dark Mode (Kanan Atas) --}}
    <div class="fixed top-3 right-3 sm:top-5 sm:right-6 z-50">
        <button type="button" @click="darkMode = !darkMode"
            class="flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl sm:rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 shadow-md sm:shadow-lg text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/30 transition-all duration-200 cursor-pointer active:scale-95 group"
            :title="darkMode ? 'Beralih ke Mode Terang (Light Mode)' : 'Beralih ke Mode Gelap (Dark Mode)'">
            <template x-if="darkMode">
                <div class="flex items-center gap-1.5">
                    <span
                        class="icon-[solar--sun-2-bold-duotone] text-base sm:text-lg text-amber-400 group-hover:rotate-45 transition-transform duration-300"></span>
                    <span class="text-xs font-bold tracking-wide hidden sm:inline-block">Light</span>
                </div>
            </template>
            <template x-if="!darkMode">
                <div class="flex items-center gap-1.5">
                    <span
                        class="icon-[solar--moon-stars-bold-duotone] text-base sm:text-lg text-emerald-600 group-hover:-rotate-12 transition-transform duration-300"></span>
                    <span class="text-xs font-bold tracking-wide hidden sm:inline-block">Dark</span>
                </div>
            </template>
        </button>
    </div>

    {{-- Background Image dengan Overlay Gelap & Gradient Kedalaman --}}
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat scale-105 filter blur-[1px] opacity-25 dark:opacity-40 transition-transform duration-1000"
        style="background-image: url('{{ Vite::image('login-bg.png') }}');">
    </div>

    {{-- Layer Gradien & Mesh Grid untuk Estetika Medis Premium --}}
    <div
        class="absolute inset-0 bg-gradient-to-tr from-slate-100/90 via-slate-50/95 to-emerald-50/70 dark:from-slate-950 dark:via-slate-950/92 dark:to-emerald-950/80 transition-colors duration-300">
    </div>
    <div
        class="absolute inset-0 bg-[linear-gradient(to_right,#00000008_1px,transparent_1px),linear-gradient(to_bottom,#00000008_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:36px_36px] pointer-events-none">
    </div>

    {{-- Efek Ambient Glow / Cahaya Dinamis --}}
    <div
        class="absolute -top-32 -left-32 w-80 sm:w-96 h-80 sm:h-96 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-[100px] sm:blur-[120px] pointer-events-none animate-pulse">
    </div>
    <div
        class="absolute -bottom-32 -right-32 w-80 sm:w-96 h-80 sm:h-96 bg-teal-500/10 dark:bg-teal-500/15 rounded-full blur-[100px] sm:blur-[120px] pointer-events-none">
    </div>

    {{-- Kontainer Utama Grid --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-14 items-center">

        {{-- Kolom Kiri: Identitas Rumah Sakit & Fitur Unggulan (Hanya Desktop) --}}
        <div class="hidden lg:flex lg:col-span-7 flex-col justify-center space-y-6">
            {{-- Logo Resmi Rumah Sakit --}}
            <div class="relative inline-flex items-center group w-fit">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-emerald-500/30 to-teal-400/20 rounded-full blur-2xl scale-125 opacity-70 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                </div>
                <img src="{{ Vite::image('logo.png') }}" alt="{{ $hospitalName }}" title="{{ $hospitalName }}"
                    class="relative h-20 lg:h-24 w-auto object-contain filter drop-shadow-[0_12px_24px_rgba(0,0,0,0.6)] group-hover:scale-105 transition-transform duration-300" />
            </div>

            {{-- Judul & Deskripsi Ringkas Institusi --}}
            <div class="space-y-2">
                <h1 class="tracking-tight leading-tight uppercase space-y-1">
                    <span
                        class="block text-4xl lg:text-5xl font-black bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-teal-700 to-slate-900 dark:from-emerald-400 dark:via-teal-200 dark:to-white drop-shadow-[0_4px_16px_rgba(16,185,129,0.25)]">
                        {{ env('APP_NAME') }}
                    </span>
                    <span
                        class="block text-xl lg:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
                        {{ $hospitalName }}
                    </span>
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal max-w-xl pt-0.5">
                    Portal analitik dan visualisasi data pelayanan SIMRS untuk monitoring operasional serta evaluasi
                    kinerja rumah sakit secara real-time.
                </p>
            </div>

            {{-- Kartu Fitur Unggulan (Grid 3 Pilar Ringkas) --}}
            <div class="grid grid-cols-3 gap-3 pt-1">
                {{-- Pilar 1 --}}
                <div
                    class="p-3.5 rounded-2xl bg-white/80 dark:bg-white/[0.03] border border-slate-200/80 dark:border-white/10 backdrop-blur-md transition hover:bg-white/95 dark:hover:bg-white/[0.06] hover:border-emerald-500/30 shadow-sm dark:shadow-none">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2.5">
                        <span class="icon-[solar--database-bold-duotone] text-lg"></span>
                    </div>
                    <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Data SIMRS
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">Terintegrasi langsung
                        basis data rekam medis.</p>
                </div>

                {{-- Pilar 2 --}}
                <div
                    class="p-3.5 rounded-2xl bg-white/80 dark:bg-white/[0.03] border border-slate-200/80 dark:border-white/10 backdrop-blur-md transition hover:bg-white/95 dark:hover:bg-white/[0.06] hover:border-teal-500/30 shadow-sm dark:shadow-none">
                    <div
                        class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2.5">
                        <span class="icon-[solar--pie-chart-2-bold-duotone] text-lg"></span>
                    </div>
                    <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Visualisasi
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">Grafik tren kunjungan,
                        bayar, & demografi.</p>
                </div>

                {{-- Pilar 3 --}}
                <div
                    class="p-3.5 rounded-2xl bg-white/80 dark:bg-white/[0.03] border border-slate-200/80 dark:border-white/10 backdrop-blur-md transition hover:bg-white/95 dark:hover:bg-white/[0.06] hover:border-cyan-500/30 shadow-sm dark:shadow-none">
                    <div
                        class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-2.5">
                        <span class="icon-[solar--graph-up-bold-duotone] text-lg"></span>
                    </div>
                    <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Monitoring
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">Pantauan statistik
                        pelayanan ralan, ranap & mutu.</p>
                </div>
            </div>

            {{-- Informasi Lokasi Institusi --}}
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                <span
                    class="icon-[solar--map-point-bold-duotone] text-emerald-500 dark:text-emerald-400 text-sm shrink-0"></span>
                <span>{{ $hospitalAddress }}{{ !empty($hospitalCity) ? ', ' . $hospitalCity : '' }}</span>
            </div>
        </div>

        {{-- Kolom Kanan: Kartu Form Login Glassmorphism (Sentris di Mobile) --}}
        <div class="lg:col-span-5 w-full flex justify-center">
            <div
                class="relative w-full max-w-md bg-white/95 dark:bg-slate-900/85 backdrop-blur-2xl border border-slate-200/90 dark:border-white/10 rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-9 shadow-2xl shadow-slate-200/70 dark:shadow-black/80 ring-1 ring-slate-200/50 dark:ring-white/10 overflow-hidden">
                {{-- Garis Aksen Neon di Atas Kartu --}}
                <div
                    class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600">
                </div>
                <div
                    class="absolute -top-14 -right-14 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none">
                </div>

                {{-- Header Mobile: Logo + Nama Aplikasi Ringkas (Hanya di Mobile) --}}
                <div class="lg:hidden text-center mb-4">
                    <div class="relative inline-flex items-center justify-center mb-2">
                        <div
                            class="absolute inset-0 bg-emerald-500/20 rounded-full blur-lg scale-125 pointer-events-none">
                        </div>
                        <img src="{{ Vite::image('logo.png') }}" alt="{{ $hospitalName }}"
                            class="relative h-18 w-auto object-contain drop-shadow" />
                    </div>
                    <h1
                        class="text-lg font-black uppercase tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-teal-700 to-slate-900 dark:from-emerald-400 dark:via-teal-200 dark:to-white leading-tight">
                        {{ env('APP_NAME') }}
                    </h1>
                    <p
                        class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate max-w-xs mx-auto mt-0.5">
                        {{ $hospitalName }}
                    </p>
                </div>

                {{-- Header Form Desktop (Hanya di Layar Besar) --}}
                <div class="hidden lg:block mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-inner">
                            <span class="icon-[solar--lock-keyhole-minimalistic-bold-duotone] text-xl"></span>
                        </div>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                            Portal Masuk
                        </span>
                    </div>

                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Masuk ke Sistem</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Masukkan akun resmi SIMRS Anda.</p>
                </div>

                {{-- Form Autentikasi Livewire --}}
                <form wire:submit="login" class="space-y-3 sm:space-y-4">
                    {{-- Input Nama Pengguna / Username --}}
                    <div>
                        <label for="login-username"
                            class="block mb-1 text-[11px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Nama Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-500 dark:text-emerald-400">
                                <span class="icon-[solar--user-bold-duotone] text-base sm:text-lg"></span>
                            </div>
                            <input id="login-username" type="text" wire:model.blur="username" required autofocus
                                placeholder="Masukkan nama pengguna..."
                                class="w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 bg-slate-50 dark:bg-slate-950/60 border border-slate-300 dark:border-slate-700/80 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-3 focus:ring-emerald-500/15 transition-all shadow-inner" />
                        </div>
                        @error('username')
                            <p
                                class="mt-1 text-[11px] text-rose-500 dark:text-rose-400 flex items-center gap-1 font-semibold">
                                <span class="icon-[solar--danger-circle-bold] text-xs"></span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- Input Kata Sandi dengan Toggle Show/Hide Password --}}
                    <div x-data="{ showPassword: false }">
                        <label for="login-password"
                            class="block mb-1 text-[11px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-500 dark:text-emerald-400">
                                <span class="icon-[solar--lock-keyhole-bold-duotone] text-base sm:text-lg"></span>
                            </div>
                            <input id="login-password" :type="showPassword ? 'text' : 'password'"
                                wire:model.blur="password" required placeholder="Masukkan kata sandi..."
                                class="w-full pl-10 sm:pl-11 pr-10 sm:pr-11 py-2.5 sm:py-3 bg-slate-50 dark:bg-slate-950/60 border border-slate-300 dark:border-slate-700/80 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-3 focus:ring-emerald-500/15 transition-all shadow-inner" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white transition p-1 cursor-pointer"
                                :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                <span x-show="!showPassword"
                                    class="icon-[solar--eye-bold-duotone] text-base sm:text-lg"></span>
                                <span x-show="showPassword"
                                    class="icon-[solar--eye-closed-bold-duotone] text-base sm:text-lg"></span>
                            </button>
                        </div>
                        @error('password')
                            <p
                                class="mt-1 text-[11px] text-rose-500 dark:text-rose-400 flex items-center gap-1 font-semibold">
                                <span class="icon-[solar--danger-circle-bold] text-xs"></span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- Baris Remember Me & Bantuan --}}
                    <div class="flex items-center justify-between pt-0.5">
                        <label for="login-remember"
                            class="inline-flex items-center gap-2 cursor-pointer select-none group">
                            <div class="relative flex items-center justify-center">
                                <input id="login-remember" type="checkbox" wire:model="rememberMe"
                                    class="sr-only peer" />
                                <div
                                    class="w-4.5 h-4.5 rounded-md border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-950/70 group-hover:border-emerald-500/50 group-hover:bg-slate-200 dark:group-hover:bg-slate-900/80 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500/40 peer-checked:bg-gradient-to-tr peer-checked:from-emerald-600 peer-checked:to-teal-500 peer-checked:border-emerald-400 transition-all duration-200">
                                </div>
                                <svg class="w-3 h-3 text-white absolute pointer-events-none opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-200 drop-shadow"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <span
                                class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-300 font-semibold group-hover:text-slate-900 dark:group-hover:text-white transition">
                                Ingatkan Saya
                            </span>
                        </label>

                        <button type="button" @click="showHelp = true"
                            class="text-[11px] sm:text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold transition cursor-pointer">
                            Bantuan?
                        </button>
                    </div>

                    {{-- Tombol Submit Login --}}
                    <div class="pt-1 sm:pt-2">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-2.5 sm:py-3.5 px-5 rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm tracking-wide text-white uppercase bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 active:scale-[0.99] transition-all duration-200 shadow-lg shadow-emerald-600/30 border border-emerald-400/20 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                            {{-- Kondisi Normal --}}
                            <span wire:loading.remove wire:target="login" class="flex items-center gap-1.5 sm:gap-2">
                                <span class="icon-[solar--login-2-bold-duotone] text-base sm:text-lg"></span>
                                <span>Masuk Dashboard</span>
                            </span>

                            {{-- Kondisi Memuat / Verifikasi --}}
                            <span wire:loading wire:target="login" class="flex items-center gap-1.5 sm:gap-2">
                                <span
                                    class="icon-[solar--refresh-bold-duotone] animate-spin text-base sm:text-lg"></span>
                                <span>Memverifikasi...</span>
                            </span>
                        </button>
                    </div>

                    {{-- Informasi Versi Aplikasi & Tautan Change Log --}}
                    <div
                        class="pt-3 border-t border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1.5 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Dashboard SIMRS</span>
                        </div>
                        <button type="button" @click="showChangelog = true"
                            class="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 hover:border-emerald-500/40 transition cursor-pointer active:scale-95 group"
                            title="Klik untuk melihat Detail Catatan Rilis & Change Log">
                            <span class="icon-[solar--history-bold-duotone] text-xs"></span>
                            <span>{{ $currentVersion['version'] ?? 'v2.0.0' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Dialog Bantuan IT & Kontak Darurat --}}
    <div x-show="showHelp" x-cloak
        class="fixed inset-0 z-50 flex items-center !mt-0 justify-center p-4 bg-black/80 backdrop-blur-md"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="showHelp = false"
            class="relative w-full max-w-sm sm:max-w-md p-5 sm:p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl shadow-2xl text-slate-800 dark:text-slate-100"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">

            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-white/10">
                <div class="flex items-center gap-2">
                    <div
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <span class="icon-[solar--headphones-round-bold-duotone] text-base"></span>
                    </div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Bantuan SIMRS</h3>
                </div>
                <button type="button" @click="showHelp = false"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition p-1 cursor-pointer">
                    <span class="icon-[solar--close-circle-bold] text-lg sm:text-xl"></span>
                </button>
            </div>

            <div class="space-y-3 py-3 text-xs text-slate-600 dark:text-slate-300">
                <p class="leading-relaxed">
                    Jika Anda lupa kata sandi atau mengalami kendala login, silakan hubungi tim IT SIMRS
                    {{ $hospitalName }}:
                </p>

                <div
                    class="space-y-2 p-3 rounded-xl bg-slate-50 dark:bg-white/[0.03] border border-slate-200 dark:border-white/5">
                    <div class="flex items-center gap-2">
                        <span
                            class="icon-[solar--phone-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                        <span class="font-bold text-slate-800 dark:text-white">WhatsApp:</span>
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                            class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">{{ $contactNumber }}</a>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="icon-[solar--letter-bold-duotone] text-teal-600 dark:text-teal-400 text-sm"></span>
                        <span class="font-bold text-slate-800 dark:text-white">Email:</span>
                        <span class="text-slate-600 dark:text-slate-300">{{ $contactEmail }}</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Sertakan Nama Lengkap, Unit/Poli, dan ID Pengguna saat mengajukan reset.
                </p>
            </div>

            <div class="pt-1">
                <button type="button" @click="showHelp = false"
                    class="w-full py-2 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Dialog Detail Change Log & Riwayat Pembaruan Versi --}}
    <div x-show="showChangelog" x-cloak
        class="fixed inset-0 z-50 flex items-center !mt-0 justify-center p-3 sm:p-5 bg-black/80 backdrop-blur-md overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="showChangelog = false"
            class="relative w-full max-w-2xl sm:max-w-3xl my-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[88vh] text-slate-800 dark:text-slate-100"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">

            {{-- Modal Header --}}
            <div
                class="flex items-center justify-between px-5 py-4 sm:px-6 sm:py-5 border-b border-slate-200 dark:border-white/10 bg-slate-50/70 dark:bg-white/[0.02]">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <span class="icon-[solar--history-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3
                                class="text-sm sm:text-base font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                Catatan Rilis & Riwayat Versi
                            </h3>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                {{ $currentVersion['version'] ?? 'v2.0.0' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Riwayat pembaruan modul, peningkatan performa, dan fitur Dashboard SIMRS
                        </p>
                    </div>
                </div>
                <button type="button" @click="showChangelog = false"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition p-1 cursor-pointer"
                    title="Tutup Modal">
                    <span class="icon-[solar--close-circle-bold] text-2xl"></span>
                </button>
            </div>

            {{-- Modal Body (Scrollable List) --}}
            <div class="overflow-y-auto p-5 sm:p-6 space-y-6 divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($allVersions as $idx => $v)
                    <div class="{{ $idx > 0 ? 'pt-6' : '' }} space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-sm sm:text-base font-black text-slate-900 dark:text-white">
                                    v{{ ltrim($v['version'], 'v') }}
                                </span>
                                @if ($idx === 0)
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-sm shadow-emerald-500/30">
                                        Versi Aktif
                                    </span>
                                @endif
                                @if (!empty($v['badge']))
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300">
                                        {{ $v['badge'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                <span
                                    class="icon-[solar--calendar-minimalistic-bold-duotone] text-sm text-emerald-600 dark:text-emerald-400"></span>
                                <span>{{ \Carbon\Carbon::parse($v['date'])->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>

                        @if (!empty($v['title']))
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                                {{ $v['title'] }}
                            </h4>
                        @endif

                        <ul class="space-y-2 pt-1">
                            @foreach ($v['changeLog'] ?? [] as $item)
                                <li
                                    class="flex items-start gap-2.5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                    <span
                                        class="icon-[solar--check-circle-bold-duotone] text-sm text-emerald-500 shrink-0 mt-0.5"></span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Tidak ada riwayat catatan rilis yang ditemukan.
                    </div>
                @endforelse
            </div>

            {{-- Modal Footer --}}
            <div
                class="flex items-center justify-between px-5 py-3 sm:px-6 sm:py-4 border-t border-slate-200 dark:border-white/10 bg-slate-50/70 dark:bg-white/[0.02] text-xs">
                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Total {{ count($allVersions) }} rilis versi tercatat</span>
                </div>
                <button type="button" @click="showChangelog = false"
                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
