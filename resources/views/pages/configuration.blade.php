<x-content>
    <x-breadcrumb title="Pengaturan Sistem" :items="[['title' => 'Pengaturan Sistem']]" />

    <div class="space-y-6">
        <!-- 1. Banner Header & Diagnostik Cepat -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 p-6 sm:p-8 text-white shadow-xl border border-slate-700/50">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-0 right-1/4 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 shadow-inner">
                        <span class="icon-[solar--settings-bold-duotone] text-3xl"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">Pengaturan Dashboard SIMRS</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                v{{ config('app.version', '2.2.0') }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl font-medium">
                            Kelola parameter filter master data SIMRS Khanza, verifikasi koneksi basis data, profil instansi, dan pemeliharaan performa sistem.
                        </p>
                    </div>
                </div>

                <!-- Status Badge Koneksi SIMRS -->
                <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-2xl p-3 sm:p-4 backdrop-blur-md self-start md:self-auto shrink-0">
                    <div class="w-3 h-3 rounded-full {{ $simrsStatus['connected'] ? 'bg-emerald-400 animate-pulse' : 'bg-rose-500' }}"></div>
                    <div class="text-left">
                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Database SIMRS Khanza</div>
                        <div class="text-xs font-bold flex items-center gap-1.5">
                            <span class="{{ $simrsStatus['connected'] ? 'text-emerald-300' : 'text-rose-400' }}">
                                {{ $simrsStatus['connected'] ? 'Terhubung Normal' : 'Terputus / Galat' }}
                            </span>
                            @if ($simrsStatus['connected'])
                                <span class="text-slate-400 font-mono text-[10px]">({{ $simrsStatus['latency'] }} ms)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Alert Feedback Interaktif -->
        @if ($successMessage)
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-between gap-3 text-emerald-700 dark:text-emerald-400 animate-in fade-in duration-200">
                <div class="flex items-center gap-2.5 text-xs font-bold">
                    <span class="icon-[solar--check-circle-bold] text-lg text-emerald-600 dark:text-emerald-400 shrink-0"></span>
                    <span>{{ $successMessage }}</span>
                </div>
                <button type="button" wire:click="dismissAlert" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200 cursor-pointer">
                    <span class="icon-[solar--close-circle-linear] text-lg"></span>
                </button>
            </div>
        @endif

        @if ($errorMessage)
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-between gap-3 text-rose-700 dark:text-rose-400 animate-in fade-in duration-200">
                <div class="flex items-center gap-2.5 text-xs font-bold">
                    <span class="icon-[solar--danger-triangle-bold] text-lg text-rose-600 dark:text-rose-400 shrink-0"></span>
                    <span>{{ $errorMessage }}</span>
                </div>
                <button type="button" wire:click="dismissAlert" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-200 cursor-pointer">
                    <span class="icon-[solar--close-circle-linear] text-lg"></span>
                </button>
            </div>
        @endif

        <!-- 3. Navigasi Tab Pengaturan -->
        <div class="flex items-center gap-2 border-b border-stroke dark:border-strokedark pb-2 overflow-x-auto">
            <button type="button" wire:click="$set('activeTab', 'filter')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'filter' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-meta-4 border border-stroke dark:border-strokedark' }}">
                <span class="icon-[solar--tuning-2-bold-duotone] text-base"></span>
                <span>Filter Master Data</span>
            </button>

            <button type="button" wire:click="$set('activeTab', 'hospital')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'hospital' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-meta-4 border border-stroke dark:border-strokedark' }}">
                <span class="icon-[solar--hospital-bold-duotone] text-base"></span>
                <span>Profil Rumah Sakit</span>
            </button>

            <button type="button" wire:click="$set('activeTab', 'database')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'database' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-meta-4 border border-stroke dark:border-strokedark' }}">
                <span class="icon-[solar--database-bold-duotone] text-base"></span>
                <span>Koneksi & Database</span>
            </button>

            <button type="button" wire:click="$set('activeTab', 'maintenance')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'maintenance' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-boxdark text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-meta-4 border border-stroke dark:border-strokedark' }}">
                <span class="icon-[solar--server-square-bold-duotone] text-base"></span>
                <span>Pemeliharaan Sistem</span>
            </button>
        </div>

        <!-- 4. Konten Tab -->
        @if ($activeTab === 'filter')
            <!-- TAB 1: Filter Master Data SIMRS Khanza -->
            <div class="space-y-4">
                <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-stroke/70 dark:border-strokedark/70">
                        <div>
                            <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                Penyesuaian Filter Entitas SIMRS Khanza
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Atur apakah data yang berstatus nonaktif di basis data SIMRS Khanza tetap dimuat pada opsi dropdown filter laporan dashboard.
                            </p>
                        </div>
                        <button type="button" wire:click="resetToDefault"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-meta-4 hover:bg-gray-200 dark:hover:bg-meta-4/80 transition cursor-pointer self-start sm:self-auto shrink-0">
                            <span class="icon-[solar--refresh-bold-duotone] text-sm"></span>
                            <span>Reset Default</span>
                        </button>
                    </div>

                    <div class="divide-y divide-stroke/60 dark:divide-strokedark/60 mt-2">
                        <!-- 1. Poliklinik -->
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-gray-800 dark:text-white">Sertakan Poliklinik Nonaktif</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-500">WITH_INACTIVE_POLYCLINIC</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Jika diaktifkan, unit rawat jalan yang berstatus nonaktif (<code class="text-[11px] font-mono bg-gray-100 dark:bg-meta-4 px-1 rounded">status = '0'</code>) di SIMRS Khanza tetap muncul di dropdown pilihan poliklinik.
                                </p>
                            </div>
                            <button type="button" wire:click="toggleConfig('WITH_INACTIVE_POLYCLINIC')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $withInactivePolyclinic ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-meta-4' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $withInactivePolyclinic ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        <!-- 2. Penjamin / Penanggung Jawab -->
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-gray-800 dark:text-white">Sertakan Penjamin / Penjab Nonaktif</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-500">WITH_INACTIVE_PERSON_RESPONSIBILITY</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Jika diaktifkan, jenis pembayaran atau asuransi penjamin yang sudah dinonaktifkan di SIMRS Khanza tetap dimuat pada filter jenis bayar.
                                </p>
                            </div>
                            <button type="button" wire:click="toggleConfig('WITH_INACTIVE_PERSON_RESPONSIBILITY')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $withInactivePersonResponsibility ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-meta-4' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $withInactivePersonResponsibility ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        <!-- 3. Dokter DPJP -->
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-gray-800 dark:text-white">Sertakan Dokter DPJP Nonaktif</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-500">WITH_INACTIVE_DOCTOR</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Jika diaktifkan, data dokter yang sudah tidak bertugas atau berstatus nonaktif (<code class="text-[11px] font-mono bg-gray-100 dark:bg-meta-4 px-1 rounded">status = '0'</code>) tetap dapat dipilih pada dropdown DPJP.
                                </p>
                            </div>
                            <button type="button" wire:click="toggleConfig('WITH_INACTIVE_DOCTOR')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $withInactiveDoctor ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-meta-4' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $withInactiveDoctor ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        <!-- 4. Kamar Perawatan -->
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-gray-800 dark:text-white">Sertakan Kamar Perawatan Nonaktif</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-500">WITH_INACTIVE_ROOM</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Jika diaktifkan, kamar perawatan rawat inap yang dinonaktifkan di tabel kamar SIMRS tetap dimuat dalam matriks ketersediaan tempat tidur.
                                </p>
                            </div>
                            <button type="button" wire:click="toggleConfig('WITH_INACTIVE_ROOM')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $withInactiveRoom ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-meta-4' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $withInactiveRoom ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        <!-- 5. Bangsal / Ruangan -->
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-gray-800 dark:text-white">Sertakan Bangsal / Ruangan Nonaktif</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-meta-4 text-gray-500">WITH_INACTIVE_WARD</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Jika diaktifkan, bangsal/gedung perawatan yang dinonaktifkan di tabel bangsal SIMRS tetap disertakan pada filter pemetaan rawat inap.
                                </p>
                            </div>
                            <button type="button" wire:click="toggleConfig('WITH_INACTIVE_WARD')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $withInactiveWard ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-meta-4' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $withInactiveWard ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Box Tata Kelola -->
                <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-start gap-3 text-xs text-blue-800 dark:text-blue-300">
                    <span class="icon-[solar--info-circle-bold-duotone] text-lg text-blue-600 shrink-0 mt-0.5"></span>
                    <p class="leading-relaxed">
                        <strong>Rekomendasi Standar SIMRS Khanza:</strong> Biarkan opsi di atas pada posisi <em>Nonaktif (Off)</em> untuk memastikan filter hanya menyajikan data yang masih beroperasi aktif, sehingga laporan harian tetap ringkas, bersih, dan akurat.
                    </p>
                </div>
            </div>
        @endif

        @if ($activeTab === 'hospital')
            <!-- TAB 2: Profil Rumah Sakit (Tabel Setting SIMRS) -->
            <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark p-6 sm:p-7 shadow-sm">
                <div class="flex items-center gap-3 pb-5 border-b border-stroke/70 dark:border-strokedark/70">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                        <span class="icon-[solar--hospital-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">
                            Profil Instansi Rumah Sakit (Kop Surat PDF)
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Data profil ini dibaca otomatis dari tabel <code class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">setting</code> database SIMRS Khanza dan digunakan sebagai header resmi seluruh ekspor laporan PDF.
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <!-- Logo RS -->
                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/80 dark:border-strokedark/80 flex flex-col items-center justify-center text-center">
                        <div class="w-28 h-28 rounded-2xl bg-white dark:bg-boxdark p-3 border border-stroke dark:border-strokedark shadow-sm flex items-center justify-center overflow-hidden mb-3">
                            @if (!empty($hospitalProfile['logo']))
                                <img src="{{ $hospitalProfile['logo'] }}" alt="Logo RS" class="max-w-full max-h-full object-contain" />
                            @else
                                <span class="icon-[solar--hospital-bold-duotone] text-4xl text-gray-400"></span>
                            @endif
                        </div>
                        <span class="text-xs font-black text-gray-800 dark:text-white">Logo Rumah Sakit</span>
                        <span class="text-[10px] text-gray-400 mt-0.5">Digunakan pada Kop Laporan PDF</span>
                    </div>

                    <!-- Metadata Profil -->
                    <div class="md:col-span-2 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                                <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Nama Rumah Sakit</span>
                                <span class="text-sm font-black text-gray-800 dark:text-white block">{{ $hospitalProfile['nama'] ?? '-' }}</span>
                            </div>

                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                                <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Kontak Telepon</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200 block">{{ $hospitalProfile['telepon'] ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                            <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Alamat Instansi</span>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 block leading-relaxed">{{ $hospitalProfile['alamat'] ?? '-' }}</span>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/60 dark:border-strokedark/60">
                            <span class="text-[10px] font-black uppercase text-gray-400 block mb-1">Surat Elektronik (Email)</span>
                            <span class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-300 block">{{ $hospitalProfile['email'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'database')
            <!-- TAB 3: Koneksi & Infrastruktur Database -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Koneksi SIMRS Khanza -->
                <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <span class="icon-[solar--database-bold-duotone] text-lg"></span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-800 dark:text-white">Koneksi SIMRS Khanza</h4>
                                    <span class="text-[10px] text-gray-400 font-mono">connection: 'simrs' (read-only)</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $simrsStatus['connected'] ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/20' }}">
                                {{ $simrsStatus['connected'] ? 'Online' : 'Offline' }}
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between py-1 border-b border-stroke/40 dark:border-strokedark/40">
                                <span class="text-gray-400 font-medium">Database Host</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white">{{ $simrsStatus['host'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-stroke/40 dark:border-strokedark/40">
                                <span class="text-gray-400 font-medium">Nama Database</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white">{{ $simrsStatus['database'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-stroke/40 dark:border-strokedark/40">
                                <span class="text-gray-400 font-medium">Versi Server DBMS</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white">{{ $simrsStatus['version'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span class="text-gray-400 font-medium">Latensi Respon</span>
                                <span class="font-mono font-black text-emerald-600 dark:text-emerald-400">{{ $simrsStatus['latency'] }} ms</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Koneksi Dashboard Internal -->
                <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-stroke/70 dark:border-strokedark/70 mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                    <span class="icon-[solar--server-square-bold-duotone] text-lg"></span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-800 dark:text-white">Database Dashboard</h4>
                                    <span class="text-[10px] text-gray-400 font-mono">connection: default</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                Aktif
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between py-1 border-b border-stroke/40 dark:border-strokedark/40">
                                <span class="text-gray-400 font-medium">Driver DBMS</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white uppercase">{{ config('database.default') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-stroke/40 dark:border-strokedark/40">
                                <span class="text-gray-400 font-medium">Host Server</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white">{{ config('database.connections.' . config('database.default') . '.host', '127.0.0.1') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-stroke/40 dark:border-strokedark/40">
                                <span class="text-gray-400 font-medium">Database Name</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white">{{ config('database.connections.' . config('database.default') . '.database', '-') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span class="text-gray-400 font-medium">Cache Driver</span>
                                <span class="font-mono font-bold text-gray-800 dark:text-white uppercase">{{ config('cache.default', 'file') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'maintenance')
            <!-- TAB 4: Pemeliharaan & Operasi Sistem -->
            <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark p-6 sm:p-7 shadow-sm">
                <div class="flex items-center gap-3 pb-5 border-b border-stroke/70 dark:border-strokedark/70 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                        <span class="icon-[solar--shield-check-bold-duotone] text-xl"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight">
                            Pemeliharaan Performa & Cache Sistem
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Jalankan utilitas pembersihan memori sementara untuk memastikan kestabilan data dan tampilan visualisasi grafik.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kartu Bersihkan Cache -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/80 dark:border-strokedark/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="icon-[solar--trash-bin-trash-bold-duotone] text-lg text-emerald-600 dark:text-emerald-400"></span>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Bersihkan Cache Aplikasi & Tampilan</h4>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Menghapus cache query SIMRS, cache kop surat PDF, dan kompilasi template Blade. Sangat berguna setelah perubahan data master atau update versi.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-stroke/50 dark:border-strokedark/50">
                            <button type="button" wire:click="clearSystemCache" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition cursor-pointer">
                                <span wire:loading.remove wire:target="clearSystemCache" class="icon-[solar--refresh-bold-duotone] text-base"></span>
                                <span wire:loading wire:target="clearSystemCache" class="icon-[solar--restart-bold] text-base animate-spin"></span>
                                <span>Bersihkan Cache Sekarang</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kartu Reset Parameter Filter -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-meta-4/20 border border-stroke/80 dark:border-strokedark/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="icon-[solar--refresh-bold-duotone] text-lg text-amber-600 dark:text-amber-400"></span>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Reset Konfigurasi Filter ke Default</h4>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Mengembalikan pengaturan seluruh filter master data SIMRS Khanza (Poliklinik, Penjab, Dokter, Kamar, Bangsal) ke setelan standar aman (hanya data aktif).
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-stroke/50 dark:border-strokedark/50">
                            <button type="button" wire:click="resetToDefault" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition cursor-pointer">
                                <span wire:loading.remove wire:target="resetToDefault" class="icon-[solar--refresh-bold-duotone] text-base"></span>
                                <span wire:loading wire:target="resetToDefault" class="icon-[solar--restart-bold] text-base animate-spin"></span>
                                <span>Reset ke Nilai Default</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-content>
