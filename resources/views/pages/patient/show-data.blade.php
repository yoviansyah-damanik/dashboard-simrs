<div class="p-0">
    {{-- Loading State --}}
    <div wire:loading.flex class="items-center justify-center py-20">
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-t-primary rounded-full animate-spin"></div>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-700">Menyiapkan Informasi Pasien</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-tighter mt-1">Sistem sedang mengambil data klinis...</p>
            </div>
        </div>
    </div>

    <div wire:loading.remove>
        @if ($patient)
            <div class="flex flex-col">
                {{-- Modern Header / Profile Banner --}}
                <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800 p-6 sm:p-8">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                    
                    <div class="relative flex flex-col md:flex-row gap-6 items-center">
                        <div class="shrink-0">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white shadow-2xl">
                                <i class="i-ph-user-circle-duotone text-5xl sm:text-6xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-2">
                                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $patient['data']['nama'] }}</h2>
                                <div class="flex items-center justify-center md:justify-start gap-2">
                                    <span class="px-2.5 py-0.5 bg-emerald-500 text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                                        {{ $patient['data']['no_rekam_medis'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-center md:justify-start gap-4 text-slate-300 text-xs font-medium">
                                <div class="flex items-center gap-1.5">
                                    <i class="i-ph-identification-card"></i>
                                    {{ $patient['data']['nik'] ?: 'NIK Belum Terdaftar' }}
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="i-ph-phone"></i>
                                    {{ $patient['data']['no_telp'] ?: '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 flex flex-col items-center md:items-end gap-1">
                            <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white text-[10px] font-black rounded-lg uppercase tracking-widest border border-white/10">
                                {{ $patient['data']['jenis_pasien'] }}
                            </span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-2">Terdaftar Sejak</p>
                            <p class="text-sm font-black text-white leading-none">{{ $patient['tgl_daftar'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Content Body --}}
                <div class="p-6 sm:p-8 bg-white dark:bg-slate-800">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        {{-- Left Column: Core Medical Data --}}
                        <div class="lg:col-span-2 space-y-8">
                            
                            {{-- Vital Information Group --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gol. Darah</p>
                                    <p class="text-lg font-black text-red-600">{{ $patient['data']['gol_darah'] ?: '-' }}</p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jenis Kelamin</p>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-200">{{ $patient['data']['jenis_kelamin'] }}</p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Umur</p>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-200">{{ $patient['data']['umur'] }}</p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Agama</p>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-200">{{ $patient['data']['agama'] }}</p>
                                </div>
                            </div>

                            {{-- Detailed Information Sections --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <h4 class="text-xs font-black text-primary uppercase tracking-[0.2em] flex items-center gap-2">
                                        <span class="w-2 h-2 bg-primary rounded-full"></span>
                                        Identitas Utama
                                    </h4>
                                    <div class="space-y-3">
                                        <x-detail-item label="Tempat, Tgl Lahir" :value="$patient['data']['tempat_lahir'] . ', ' . $patient['data']['tanggal_lahir']" icon="i-ph-calendar-duotone" />
                                        <x-detail-item label="Status Nikah" :value="$patient['data']['status_nikah']" icon="i-ph-heart-duotone" />
                                        <x-detail-item label="Nama Ibu Kandung" :value="$patient['data']['nama_ibu']" icon="i-ph-users-duotone" />
                                        <x-detail-item label="Suku Bangsa" :value="$patient['data']['suku_bangsa']" icon="i-ph-flag-duotone" />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="text-xs font-black text-primary uppercase tracking-[0.2em] flex items-center gap-2">
                                        <span class="w-2 h-2 bg-primary rounded-full"></span>
                                        Data Administratif
                                    </h4>
                                    <div class="space-y-3">
                                        <x-detail-item label="Pendidikan" :value="$patient['data']['pendidikan']" icon="i-ph-graduation-cap-duotone" />
                                        <x-detail-item label="Pekerjaan" :value="$patient['data']['pekerjaan']" icon="i-ph-briefcase-duotone" />
                                        <x-detail-item label="Metode Pembayaran" :value="$patient['data']['jenis_bayar']" icon="i-ph-wallet-duotone" />
                                        <x-detail-item label="No. BPJS / Asuransi" :value="$patient['data']['no_peserta'] ?: '-'" icon="i-ph-credit-card-duotone" />
                                    </div>
                                </div>
                            </div>

                            {{-- Full Width Address --}}
                            <div class="bg-primary/5 dark:bg-primary/10 p-5 rounded-2xl border border-primary/10">
                                <div class="flex items-center gap-2 mb-2 text-primary">
                                    <i class="i-ph-map-pin-duotone text-xl"></i>
                                    <h4 class="text-xs font-black uppercase tracking-widest">Domisili Pasien</h4>
                                </div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-relaxed">
                                    {{ $patient['data']['alamat'] }}
                                </p>
                            </div>
                        </div>

                        {{-- Right Column: Responsible Party & Quick Stats --}}
                        <div class="space-y-8">
                            <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 space-y-6">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] text-center">Penanggung Jawab</h4>
                                
                                <div class="flex flex-col items-center text-center gap-2">
                                    <div class="w-16 h-16 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 shadow-sm border border-slate-100 dark:border-slate-700">
                                        <i class="i-ph-user-focus text-3xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ $patient['penanggungjawab']['nama'] }}</p>
                                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest mt-1">{{ $patient['penanggungjawab']['status'] }}</p>
                                    </div>
                                </div>

                                <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Pekerjaan PJ</p>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $patient['penanggungjawab']['pekerjaan'] }}</p>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Alamat PJ</p>
                                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 leading-relaxed italic">
                                            "{{ $patient['penanggungjawab']['alamat'] }}, {{ $patient['penanggungjawab']['kelurahan'] }}, {{ $patient['penanggungjawab']['kecamatan'] }}, {{ $patient['penanggungjawab']['kabupaten'] }}"
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Additional Tags / Info --}}
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center text-emerald-600">
                                        <i class="i-ph-check-circle-duotone text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-tighter">Status Rekam Medis</p>
                                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-400">Aktif & Terverifikasi</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                <i class="i-ph-selection-background-duotone text-7xl mb-4 opacity-10"></i>
                <p class="text-sm font-bold opacity-30">Pilih pasien untuk menampilkan data klinis</p>
            </div>
        @endif
    </div>
</div>



