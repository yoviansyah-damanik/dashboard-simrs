<x-content>
    <x-breadcrumb title="Pengaturan Akun" :items="[['title' => 'Pengaturan Akun']]" />

    @php
        $user = auth()->user();
        $avatarIndex = (abs(crc32((string) ($user->id ?? '1'))) % 5) + 1;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
        {{-- KOLOM KIRI: Profil Ringkas & Navigasi Tab --}}
        <div class="lg:col-span-4 space-y-4">
            {{-- Kartu Profil Pengguna --}}
            <div class="p-5 sm:p-6 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm text-center relative overflow-hidden backdrop-blur-md">
                <div class="relative inline-block mx-auto mb-3.5">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden ring-4 ring-emerald-500/20 bg-slate-100 dark:bg-slate-800 shadow-md mx-auto">
                        <img src="{{ Vite::image('user/' . $avatarIndex . '.png') }}" alt="{{ $user->name }}"
                            class="w-full h-full object-cover" />
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 flex items-center justify-center text-white"
                        title="Akun Aktif">
                        <span class="icon-[solar--check-read-bold] text-xs"></span>
                    </span>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white leading-tight">
                    {{ $user->name }}
                </h3>
                <p class="text-xs font-mono text-slate-400 mt-0.5">
                    {{ '@' . ($user->username ?? 'pengguna') }}
                </p>

                <div class="mt-3 flex flex-wrap items-center justify-center gap-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                        {{ $user->role_name ?? 'Staf SIMRS' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ $user->email }}
                    </span>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 text-left space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <span class="icon-[solar--hospital-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                            <span>Instansi</span>
                        </span>
                        <span class="font-semibold text-slate-700 dark:text-slate-200 truncate max-w-[150px]">
                            {{ config('app.hospital_name') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <span class="icon-[solar--shield-check-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                            <span>Status Sesi</span>
                        </span>
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Terautentikasi</span>
                        </span>
                    </div>

                    @if ($latestLogin)
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1.5">
                                <span class="icon-[solar--clock-circle-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                                <span>Login Terakhir</span>
                            </span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">
                                {{ $latestLogin->login_at?->diffForHumans() }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Menu Tab Navigasi --}}
            <div class="p-2 bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-1 backdrop-blur-md">
                @foreach ($types as $item)
                    @php $isActive = $type === $item['value']; @endphp
                    <button type="button" wire:click="switchTab('{{ $item['value'] }}')"
                        class="w-full flex items-center gap-3 p-3 rounded-xl text-left transition duration-150 cursor-pointer {{ $isActive ? 'bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-500/30 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $isActive ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                            <span class="{{ $item['icon'] }} text-lg"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs sm:text-sm font-bold truncate">
                                {{ $item['title'] }}
                            </div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate mt-0.5">
                                {{ $item['desc'] }}
                            </div>
                        </div>
                        @if ($isActive)
                            <span class="icon-[solar--alt-arrow-right-bold] text-sm text-emerald-600 dark:text-emerald-400 shrink-0"></span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- KOLOM KANAN: Isi Konten Tab Terpilih --}}
        <div class="lg:col-span-8">
            {{-- TAB 1: INFORMASI AKUN --}}
            @if ($type === 'account')
                <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-7 backdrop-blur-md space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--user-circle-bold-duotone] text-2xl"></span>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Informasi Akun</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui profil dan identitas akun SIMRS Anda</p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveAccount" class="space-y-4">
                        {{-- Nama Pengguna (Readonly) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Nama Pengguna (Username)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="icon-[solar--mention-square-bold-duotone] text-lg"></span>
                                </span>
                                <input type="text" value="{{ $username }}" readonly
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-mono cursor-not-allowed outline-none" />
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Username terikat dengan identitas pegawai SIMRS dan tidak dapat diubah sembarangan.</p>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Nama Lengkap
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="icon-[solar--user-bold-duotone] text-lg"></span>
                                </span>
                                <input type="text" wire:model="name"
                                    placeholder="Masukkan nama lengkap..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('name') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200 dark:border-slate-700/80' }} rounded-xl text-xs sm:text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" />
                            </div>
                            @error('name')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="icon-[solar--letter-bold-duotone] text-lg"></span>
                                </span>
                                <input type="email" wire:model="email"
                                    placeholder="nama@email.com"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('email') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200 dark:border-slate-700/80' }} rounded-xl text-xs sm:text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" />
                            </div>
                            @error('email')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="pt-3 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 active:scale-95 transition-all cursor-pointer">
                                <span wire:loading.remove wire:target="saveAccount" class="icon-[solar--diskette-bold] text-base"></span>
                                <span wire:loading wire:target="saveAccount" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- TAB 2: KEAMANAN KATA SANDI --}}
            @if ($type === 'password')
                <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-7 backdrop-blur-md space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--lock-password-bold-duotone] text-2xl"></span>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Keamanan Kata Sandi</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ubah kata sandi akun secara berkala demi keamanan data rekam medis</p>
                            </div>
                        </div>
                    </div>

                    {{-- Panduan Syarat Password --}}
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/70 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                        <div class="font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                            <span class="icon-[solar--info-circle-bold] text-emerald-600 dark:text-emerald-400"></span>
                            <span>Ketentuan Keamanan Sandi:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-500 dark:text-slate-400 pl-1">
                            <li>Minimal 8 karakter kombinasi</li>
                            <li>Mengandung perpaduan huruf besar, huruf kecil, dan angka</li>
                        </ul>
                    </div>

                    <form wire:submit.prevent="savePassword" class="space-y-4">
                        {{-- Kata Sandi Saat Ini --}}
                        <div x-data="{ show: false }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Kata Sandi Saat Ini
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="icon-[solar--lock-keyhole-bold-duotone] text-lg"></span>
                                </span>
                                <input :type="show ? 'text' : 'password'" wire:model="currentPassword"
                                    placeholder="Masukkan kata sandi lama..."
                                    class="w-full pl-10 pr-11 py-2.5 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('currentPassword') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200 dark:border-slate-700/80' }} rounded-xl text-xs sm:text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" />
                                <button type="button" @click="show = !show"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                    <span x-show="!show" class="icon-[solar--eye-bold-duotone] text-lg"></span>
                                    <span x-show="show" class="icon-[solar--eye-closed-bold-duotone] text-lg" style="display: none;"></span>
                                </button>
                            </div>
                            @error('currentPassword')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kata Sandi Baru --}}
                        <div x-data="{ show: false }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="icon-[solar--key-bold-duotone] text-lg"></span>
                                </span>
                                <input :type="show ? 'text' : 'password'" wire:model="newPassword"
                                    placeholder="Minimal 8 karakter huruf & angka..."
                                    class="w-full pl-10 pr-11 py-2.5 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('newPassword') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200 dark:border-slate-700/80' }} rounded-xl text-xs sm:text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" />
                                <button type="button" @click="show = !show"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                    <span x-show="!show" class="icon-[solar--eye-bold-duotone] text-lg"></span>
                                    <span x-show="show" class="icon-[solar--eye-closed-bold-duotone] text-lg" style="display: none;"></span>
                                </button>
                            </div>
                            @error('newPassword')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ulangi Kata Sandi Baru --}}
                        <div x-data="{ show: false }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Ulangi Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="icon-[solar--check-circle-bold-duotone] text-lg"></span>
                                </span>
                                <input :type="show ? 'text' : 'password'" wire:model="rePassword"
                                    placeholder="Konfirmasi kata sandi baru..."
                                    class="w-full pl-10 pr-11 py-2.5 bg-slate-50 dark:bg-slate-800/50 border {{ $errors->has('rePassword') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200 dark:border-slate-700/80' }} rounded-xl text-xs sm:text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" />
                                <button type="button" @click="show = !show"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                    <span x-show="!show" class="icon-[solar--eye-bold-duotone] text-lg"></span>
                                    <span x-show="show" class="icon-[solar--eye-closed-bold-duotone] text-lg" style="display: none;"></span>
                                </button>
                            </div>
                            @error('rePassword')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Simpan Sandi --}}
                        <div class="pt-3 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 active:scale-95 transition-all cursor-pointer">
                                <span wire:loading.remove wire:target="savePassword" class="icon-[solar--shield-check-bold] text-base"></span>
                                <span wire:loading wire:target="savePassword" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <span>Perbarui Kata Sandi</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- TAB 3: RIWAYAT LOGIN --}}
            @if ($type === 'history')
                <div class="bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6 backdrop-blur-md space-y-5">
                    {{-- Header Riwayat --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <span class="icon-[solar--shield-check-bold-duotone] text-2xl"></span>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Riwayat Sesi & Login</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar rekaman waktu, peramban, dan alamat IP masuk</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 self-start sm:self-auto">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ number_format($totalLoginCount) }} Sesi Tercatat</span>
                        </span>
                    </div>

                    {{-- Informasi Sesi Terkini --}}
                    @if ($latestLogin)
                        <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-transparent border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                    <span class="icon-[solar--laptop-bold-duotone] text-xl"></span>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                        <span>Sesi Login Terakhir</span>
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] uppercase font-black">Terbaru</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                        {{ $latestLogin->browser }} &bull; {{ $latestLogin->platform }} &bull; IP: {{ $latestLogin->ip_address }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold text-left sm:text-right">
                                <div>{{ $latestLogin->login_at?->format('d M Y, H:i') }} WIB</div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ $latestLogin->login_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabel Log Riwayat Login --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                        <table class="w-full text-left border-collapse min-w-[650px]">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="px-4 py-3">Waktu Masuk</th>
                                    <th class="px-3 py-3">Perangkat</th>
                                    <th class="px-3 py-3">Peramban (Browser)</th>
                                    <th class="px-3 py-3">Sistem Operasi</th>
                                    <th class="px-4 py-3 text-right">Alamat IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs">
                                @forelse ($histories as $history)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                        {{-- Waktu Masuk --}}
                                        <td class="px-4 py-3.5">
                                            <div class="font-bold text-slate-800 dark:text-white">
                                                {{ $history->login_at?->format('d M Y, H:i:s') }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $history->login_at?->diffForHumans() }}
                                            </div>
                                        </td>

                                        {{-- Perangkat --}}
                                        <td class="px-3 py-3.5">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-semibold">
                                                @if (Str::contains(strtolower($history->device_type ?? ''), 'mobile'))
                                                    <span class="icon-[solar--smartphone-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                                                    <span>Ponsel</span>
                                                @elseif (Str::contains(strtolower($history->device_type ?? ''), 'tablet'))
                                                    <span class="icon-[solar--tablet-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                                                    <span>Tablet</span>
                                                @else
                                                    <span class="icon-[solar--laptop-bold-duotone] text-emerald-600 dark:text-emerald-400 text-sm"></span>
                                                    <span>Komputer</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Peramban --}}
                                        <td class="px-3 py-3.5 font-medium text-slate-700 dark:text-slate-300">
                                            <div class="flex items-center gap-1.5">
                                                <span class="icon-[solar--global-bold-duotone] text-slate-400 text-sm"></span>
                                                <span class="truncate max-w-[150px]">{{ $history->browser ?: 'Tidak diketahui' }}</span>
                                            </div>
                                        </td>

                                        {{-- Sistem Operasi --}}
                                        <td class="px-3 py-3.5 font-medium text-slate-600 dark:text-slate-300">
                                            {{ $history->platform ?: '-' }}
                                        </td>

                                        {{-- Alamat IP --}}
                                        <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[11px]">
                                                {{ $history->ip_address ?: '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <span class="icon-[solar--history-bold-duotone] text-3xl mb-1.5 text-slate-300 dark:text-slate-600"></span>
                                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Belum ada riwayat sesi login tercatat.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi Riwayat --}}
                    @if ($histories->hasPages())
                        <div class="pt-2">
                            {{ $histories->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-content>
