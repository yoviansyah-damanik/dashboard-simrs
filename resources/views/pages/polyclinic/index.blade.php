<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white uppercase tracking-tighter">Master Data Poliklinik</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar layanan dan unit poliklinik yang tersedia.</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="relative group">
                <input type="text" wire:model.live="search" placeholder="Cari poliklinik..."
                    class="w-64 pl-10 pr-4 py-2.5 bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <span class="icon-[solar--magnifer-bold-duotone] text-lg"></span>
                </div>
            </div>
            <div class="p-2.5 bg-primary/10 text-primary rounded-xl border border-primary/20">
                <span class="text-sm font-black">{{ count($this->poliklinik) }} Unit</span>
            </div>
        </div>
    </div>

    <!-- Polyclinic Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($this->poliklinik as $poli)
            <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300 group overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500">
                            <span class="icon-[solar--hospital-bold-duotone] text-2xl"></span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ ($poli->status ?? '1') == '1' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                            {{ ($poli->status ?? '1') == '1' ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                    
                    <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-2 min-h-[3rem]">
                        {{ $poli->nm_poli }}
                    </h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Kode: {{ $poli->kd_poli }}</p>
                </div>

                <!-- Card Body: Fees -->
                <div class="px-6 py-4 bg-gray-50/50 dark:bg-meta-4/20 border-t border-stroke dark:border-strokedark flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Reg. Baru</span>
                        <span class="text-xs font-black text-indigo-600">Rp{{ number_format($poli->registrasi ?? 0) }}</span>
                    </div>
                    <div class="w-px h-6 bg-stroke dark:border-strokedark"></div>
                    <div class="flex flex-col items-end">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Reg. Lama</span>
                        <span class="text-xs font-black text-gray-700 dark:text-gray-300">Rp{{ number_format($poli->registrasilama ?? 0) }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-meta-4 rounded-full flex items-center justify-center text-gray-300 mb-4">
                    <span class="icon-[solar--document-add-bold-duotone] text-4xl"></span>
                </div>
                <h4 class="text-lg font-bold text-gray-700 dark:text-white">Tidak Ada Poliklinik</h4>
                <p class="text-sm text-gray-400">Tidak dapat menemukan poliklinik dengan kata kunci "{{ $search }}"</p>
            </div>
        @endforelse
    </div>
</div>
