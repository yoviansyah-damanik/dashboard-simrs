<x-content>
    <x-breadcrumb title="Rekap Stok Obat Farmasi" :items="[
        ['title' => 'Layanan Penunjang Medis'],
        ['title' => 'Farmasi', 'url' => route('pharmacy')],
        ['title' => 'Rekap Stok Obat']
    ]" />

    {{-- KOP CETAK RESMI (Hanya Muncul Saat Print) --}}
    <div class="hidden print:block mb-6 border-b-2 border-black pb-4 text-center">
        <h2 class="text-xl font-black uppercase tracking-wide">{{ $profil->nama_instansi ?? 'RUMAH SAKIT' }}</h2>
        <p class="text-xs text-gray-600 mt-0.5">{{ $profil->alamat_instansi ?? '' }}, {{ $profil->kabupaten ?? '' }}</p>
        <p class="text-xs text-gray-600">Telp: {{ $profil->kontak ?? '-' }} | Email: {{ $profil->email ?? '-' }}</p>
        <div class="mt-3 inline-block border-y border-black px-6 py-1">
            <h3 class="text-sm font-bold uppercase tracking-widest">LAPORAN REKAPITULASI STOK OBAT & MONITORING AMBANG BATAS</h3>
        </div>
        <p class="text-[10px] text-gray-500 mt-1">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div class="space-y-6">
        {{-- HEADER & DESKRIPSI --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-[solar--box-bold-duotone] text-2xl text-emerald-600 dark:text-emerald-400"></span>
                    Monitoring & Rekapitulasi Stok Obat Farmasi
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Pemantauan ketersediaan obat, identifikasi obat kritis/akan habis, stok kosong, dan valuasi aset farmasi
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="exportPdf" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-boxdark border border-stroke dark:border-strokedark text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-meta-4/40 shadow-sm transition">
                    <span wire:loading.remove wire:target="exportPdf" class="flex items-center gap-1.5">
                        <span class="icon-[solar--file-text-bold-duotone] text-base text-rose-500"></span>
                        <span>Cetak PDF Data</span>
                    </span>
                    <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
                        <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
                        <span>Menyiapkan PDF...</span>
                    </span>
                </button>
            </div>
        </div>

        {{-- KPI CARDS MONITORING STOK --}}
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3.5">
            {{-- Card 1: Total Jenis Obat --}}
            <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Total Item</span>
                    <span class="p-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-lg">
                        <span class="icon-[solar--pills-bold-duotone] text-base"></span>
                    </span>
                </div>
                <div class="text-2xl font-black text-gray-800 dark:text-white">
                    {{ number_format($summary['total_items'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Item obat aktif di katalog</p>
            </div>

            {{-- Card 2: Total Fisik Stok --}}
            <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Fisik Stok</span>
                    <span class="p-1.5 bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 rounded-lg">
                        <span class="icon-[solar--box-minimalistic-bold-duotone] text-base"></span>
                    </span>
                </div>
                <div class="text-2xl font-black text-gray-800 dark:text-white">
                    {{ number_format($summary['total_fisik'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Akumulasi unit di semua depo</p>
            </div>

            {{-- Card 3: Obat Akan Habis / Menipis (DIHIGHLIGHT) --}}
            <div class="p-4 bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl shadow-md relative overflow-hidden group">
                <div class="absolute -right-3 -bottom-3 text-white/10 group-hover:scale-110 transition duration-300">
                    <span class="icon-[solar--danger-triangle-bold] text-7xl"></span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[11px] font-black uppercase tracking-wider text-amber-100 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            Akan Habis
                        </span>
                        <span class="p-1.5 bg-white/20 rounded-lg text-white">
                            <span class="icon-[solar--bell-ringing-bold-duotone] text-base"></span>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-white">
                        {{ number_format($summary['total_menipis'], 0, ',', '.') }}
                    </div>
                    <p class="text-[10px] text-amber-100 font-medium mt-1">Stok &le; batas minimal</p>
                </div>
            </div>

            {{-- Card 4: Obat Habis / Kosong --}}
            <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-rose-200 dark:border-rose-900/50 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Stok Kosong</span>
                    <span class="p-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 rounded-lg">
                        <span class="icon-[solar--close-circle-bold-duotone] text-base"></span>
                    </span>
                </div>
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400">
                    {{ number_format($summary['total_habis'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Stok sama dengan 0</p>
            </div>

            {{-- Card 5: Obat Stok Aman --}}
            <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Stok Aman</span>
                    <span class="p-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-lg">
                        <span class="icon-[solar--check-circle-bold-duotone] text-base"></span>
                    </span>
                </div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ number_format($summary['total_aman'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Stok melebihi batas minimal</p>
            </div>

            {{-- Card 6: Nilai Aset Stok --}}
            <div class="p-4 bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Nilai Aset</span>
                    <span class="p-1.5 bg-purple-50 dark:bg-purple-900/30 text-purple-600 rounded-lg">
                        <span class="icon-[solar--wallet-money-bold-duotone] text-base"></span>
                    </span>
                </div>
                <div class="text-lg font-black text-purple-700 dark:text-purple-400 truncate" title="Rp {{ number_format($summary['total_nilai_aset'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['total_nilai_aset'] / 1000000, 1, ',', '.') }} Jt
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Total valuasi harga beli</p>
            </div>
        </div>

        {{-- FILTER & KONTROL DATA --}}
        <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm space-y-3 no-print">
            {{-- Quick Filter Status Pills --}}
            <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-stroke/60 dark:border-strokedark/60">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" wire:click="setStatusFilter('semua')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusStok === 'semua' ? 'bg-gray-800 text-white dark:bg-white dark:text-gray-900 shadow-sm' : 'bg-gray-100 dark:bg-meta-4/60 text-gray-600 dark:text-gray-400 hover:bg-gray-200' }}">
                        Semua ({{ number_format($summary['total_items']) }})
                    </button>
                    <button type="button" wire:click="setStatusFilter('menipis')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $statusStok === 'menipis' ? 'bg-amber-600 text-white shadow-sm ring-2 ring-amber-500/30' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100 border border-amber-200/60 dark:border-amber-900/50' }}">
                        <span class="icon-[solar--danger-triangle-bold] text-sm"></span>
                        <span>Akan Habis ({{ number_format($summary['total_menipis']) }})</span>
                    </button>
                    <button type="button" wire:click="setStatusFilter('habis')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $statusStok === 'habis' ? 'bg-rose-600 text-white shadow-sm ring-2 ring-rose-500/30' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 border border-rose-200/60 dark:border-rose-900/50' }}">
                        <span class="icon-[solar--close-circle-bold] text-sm"></span>
                        <span>Habis ({{ number_format($summary['total_habis']) }})</span>
                    </button>
                    <button type="button" wire:click="setStatusFilter('aman')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $statusStok === 'aman' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 border border-emerald-200/60 dark:border-emerald-900/50' }}">
                        <span class="icon-[solar--check-circle-bold] text-sm"></span>
                        <span>Aman ({{ number_format($summary['total_aman']) }})</span>
                    </button>
                    <button type="button" wire:click="setStatusFilter('near_expired')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $statusStok === 'near_expired' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 border border-indigo-200/60 dark:border-indigo-900/50' }}">
                        <span class="icon-[solar--hourglass-line-bold] text-sm"></span>
                        <span>Mendekati Expired ({{ number_format($summary['total_near_expired']) }})</span>
                    </button>
                    @if ($summary['total_expired'] > 0)
                        <button type="button" wire:click="setStatusFilter('expired')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $statusStok === 'expired' ? 'bg-red-700 text-white shadow-sm' : 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 hover:bg-red-100 border border-red-200/60 dark:border-red-900/50' }}">
                            <span class="icon-[solar--shield-warning-bold] text-sm"></span>
                            <span>Kadaluarsa ({{ number_format($summary['total_expired']) }})</span>
                        </button>
                    @endif
                </div>

                <div class="text-xs text-gray-400">
                    Menampilkan {{ $stockList->total() }} obat
                </div>
            </div>

            {{-- Input Search & Dropdowns --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                {{-- Search Box --}}
                <div class="md:col-span-6 relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama obat, kode, atau letak rak..."
                        class="w-full pl-9 pr-9 py-2 bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-xl text-xs font-medium text-gray-800 dark:text-white focus:border-emerald-500 focus:bg-white outline-none transition" />
                    <span class="icon-[solar--magnifer-bold-duotone] absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></span>
                    @if ($search)
                        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <span class="icon-[solar--close-circle-bold] text-sm"></span>
                        </button>
                    @endif
                </div>

                {{-- Filter Depo / Gudang --}}
                <div class="md:col-span-3">
                    <select wire:model.live="depo"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-xl text-xs font-medium text-gray-800 dark:text-white focus:border-emerald-500 outline-none transition cursor-pointer">
                        <option value="semua">Semua Gudang & Depo</option>
                        @foreach ($summary['depos'] as $d)
                            <option value="{{ $d->kd_bangsal }}">{{ $d->nama_depo }} ({{ number_format($d->total_unit) }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kategori --}}
                <div class="md:col-span-2">
                    <select wire:model.live="kategori"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-xl text-xs font-medium text-gray-800 dark:text-white focus:border-emerald-500 outline-none transition cursor-pointer">
                        <option value="semua">Semua Kategori</option>
                        @foreach ($summary['kategori_list'] as $kat)
                            <option value="{{ $kat->kode }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Per Page --}}
                <div class="md:col-span-1">
                    <select wire:model.live="perPage"
                        class="w-full px-2 py-2 bg-gray-50 dark:bg-meta-4/40 border border-stroke dark:border-strokedark rounded-xl text-xs font-medium text-gray-800 dark:text-white focus:border-emerald-500 outline-none transition cursor-pointer text-center">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TABEL STOK OBAT --}}
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-meta-4/60 text-gray-700 dark:text-gray-300 font-bold border-b border-stroke dark:border-strokedark">
                            <th class="py-3 px-3 w-10 text-center">No</th>
                            <th class="py-3 px-3 cursor-pointer hover:text-emerald-600 transition" wire:click="sortBy('nama_brng')">
                                <div class="flex items-center gap-1">
                                    <span>Nama Obat & Spesifikasi</span>
                                    @if ($sortField === 'nama_brng')
                                        <span class="icon-[solar--sort-vertical-bold] text-emerald-600"></span>
                                    @endif
                                </div>
                            </th>
                            <th class="py-3 px-3 text-center">Kategori / Rak</th>
                            <th class="py-3 px-3 text-center">Status Stok</th>
                            <th class="py-3 px-3 text-center cursor-pointer hover:text-emerald-600 transition" wire:click="sortBy('stok')">
                                <div class="flex items-center justify-center gap-1">
                                    <span>Stok Fisik</span>
                                    @if ($sortField === 'stok')
                                        <span class="icon-[solar--sort-vertical-bold] text-emerald-600"></span>
                                    @endif
                                </div>
                            </th>
                            <th class="py-3 px-3 text-center">Minimal</th>
                            <th class="py-3 px-3 text-center">Apotek</th>
                            <th class="py-3 px-3 text-center">Gudang</th>
                            <th class="py-3 px-3 text-center cursor-pointer hover:text-emerald-600 transition" wire:click="sortBy('expire')">
                                <div class="flex items-center justify-center gap-1">
                                    <span>Kadaluarsa</span>
                                    @if ($sortField === 'expire')
                                        <span class="icon-[solar--sort-vertical-bold] text-emerald-600"></span>
                                    @endif
                                </div>
                            </th>
                            <th class="py-3 px-3 text-right cursor-pointer hover:text-emerald-600 transition" wire:click="sortBy('nilai_aset')">
                                <div class="flex items-center justify-end gap-1">
                                    <span>Nilai Aset</span>
                                    @if ($sortField === 'nilai_aset')
                                        <span class="icon-[solar--sort-vertical-bold] text-emerald-600"></span>
                                    @endif
                                </div>
                            </th>
                            <th class="py-3 px-3 text-center w-16 no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                        @forelse ($stockList as $index => $item)
                            <tr class="transition duration-150 {{ $item->is_habis ? 'bg-rose-50/50 dark:bg-rose-950/20 hover:bg-rose-100/50 border-l-4 border-l-rose-500' : ($item->is_akan_habis ? 'bg-amber-50/60 dark:bg-amber-950/25 hover:bg-amber-100/60 border-l-4 border-l-amber-500' : 'hover:bg-gray-50/80 dark:hover:bg-meta-4/20 border-l-4 border-l-transparent') }}">
                                <td class="py-2.5 px-3 text-center text-gray-500">
                                    {{ $stockList->firstItem() + $index }}
                                </td>

                                {{-- Nama Obat & Kode --}}
                                <td class="py-2.5 px-3">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800 dark:text-white {{ $item->is_akan_habis ? 'text-amber-900 dark:text-amber-200' : '' }}">
                                            {{ $item->nama_brng }}
                                        </span>
                                        <div class="flex items-center gap-2 text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                            <span class="font-mono bg-gray-100 dark:bg-meta-4 px-1 rounded">{{ $item->kode_brng }}</span>
                                            <span>Satuan: <strong class="text-gray-700 dark:text-gray-300">{{ $item->kode_sat }}</strong></span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori / Rak --}}
                                <td class="py-2.5 px-3 text-center">
                                    <span class="block text-gray-700 dark:text-gray-300 font-medium">{{ $item->nama_kategori }}</span>
                                    <span class="text-[10px] text-gray-400">Rak: {{ $item->letak_barang }}</span>
                                </td>

                                {{-- Status Stok Badge --}}
                                <td class="py-2.5 px-3 text-center">
                                    @if ($item->is_habis)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            Stok Kosong
                                        </span>
                                    @elseif ($item->is_akan_habis)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-300 dark:border-amber-700 animate-pulse">
                                            <span class="icon-[solar--danger-triangle-bold] text-xs text-amber-600"></span>
                                            Akan Habis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Aman
                                        </span>
                                    @endif
                                </td>

                                {{-- Stok Fisik --}}
                                <td class="py-2.5 px-3 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-black {{ $item->is_habis ? 'text-rose-600' : ($item->is_akan_habis ? 'text-amber-700 dark:text-amber-300' : 'text-gray-800 dark:text-white') }}">
                                            {{ number_format($item->total_stok, 0, ',', '.') }}
                                        </span>
                                        {{-- Visual Bar Ratio terhadap Stok Minimal --}}
                                        @php
                                            $ratio = $item->ambang_kritis > 0 ? min(100, round(($item->total_stok / $item->ambang_kritis) * 100)) : 100;
                                        @endphp
                                        <div class="w-16 h-1.5 bg-gray-200 dark:bg-meta-4 rounded-full overflow-hidden mt-1">
                                            <div class="h-full {{ $item->is_habis ? 'bg-rose-500' : ($item->is_akan_habis ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                                style="width: {{ $ratio }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Stok Minimal --}}
                                <td class="py-2.5 px-3 text-center text-gray-500 font-semibold">
                                    {{ number_format($item->stokminimal, 0, ',', '.') }}
                                </td>

                                {{-- Depo Apotek --}}
                                <td class="py-2.5 px-3 text-center">
                                    <span class="font-bold {{ $item->stok_apotek > 0 ? 'text-cyan-700 dark:text-cyan-400' : 'text-gray-400' }}">
                                        {{ number_format($item->stok_apotek, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Gudang Farmasi --}}
                                <td class="py-2.5 px-3 text-center">
                                    <span class="font-bold {{ $item->stok_gudang > 0 ? 'text-indigo-700 dark:text-indigo-400' : 'text-gray-400' }}">
                                        {{ number_format($item->stok_gudang, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Kadaluarsa --}}
                                <td class="py-2.5 px-3 text-center">
                                    @if ($item->is_expired)
                                        <span class="text-rose-600 font-black text-[11px] block">
                                            {{ $item->expire ? date('d/m/Y', strtotime($item->expire)) : '-' }}
                                        </span>
                                        <span class="text-[9px] text-rose-500 font-bold">Kadaluarsa</span>
                                    @elseif ($item->is_near_expired)
                                        <span class="text-amber-600 font-bold text-[11px] block">
                                            {{ $item->expire ? date('d/m/Y', strtotime($item->expire)) : '-' }}
                                        </span>
                                        <span class="text-[9px] text-amber-500 font-medium">Sisa {{ $item->days_to_expire }} hari</span>
                                    @else
                                        <span class="text-gray-600 dark:text-gray-400 text-[11px]">
                                            {{ $item->expire && $item->expire !== '0000-00-00' ? date('d/m/Y', strtotime($item->expire)) : '-' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Nilai Aset --}}
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-gray-800 dark:text-gray-200">
                                    Rp {{ number_format($item->nilai_aset, 0, ',', '.') }}
                                </td>

                                {{-- Tombol Detail --}}
                                <td class="py-2.5 px-3 text-center no-print">
                                    <button type="button" wire:click="openDetail('{{ $item->kode_brng }}')"
                                        class="p-1.5 rounded-lg bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-emerald-700 dark:bg-meta-4/60 dark:hover:bg-emerald-950/40 dark:text-gray-300 dark:hover:text-emerald-400 transition"
                                        title="Rincian Depo & Batch">
                                        <span class="icon-[solar--eye-bold-duotone] text-base"></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="icon-[solar--box-minimalistic-bold-duotone] text-3xl text-gray-300"></span>
                                        <span>Tidak ada data stok obat yang sesuai dengan filter.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($stockList->hasPages())
                <div class="p-4 border-t border-stroke dark:border-strokedark no-print">
                    {{ $stockList->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL DETAIL STOK PER DEPO & BATCH --}}
    @if ($detailModalOpen && $selectedDrugDetail)
        <div class="fixed inset-0 z-[999] flex items-center !mt-0 justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-2xl max-w-xl w-full p-6 space-y-4 animate-scale-up"
                @click.outside="$wire.closeDetail()">
                <div class="flex items-center justify-between pb-3 border-b border-stroke dark:border-strokedark">
                    <div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <span class="icon-[solar--box-bold-duotone] text-emerald-600 text-lg"></span>
                            Rincian Stok Depo & Batch
                        </h4>
                        <div class="flex items-center gap-2 mt-0.5">
                            <p class="text-xs text-gray-500 font-mono">{{ $selectedDrugDetail['obat']->kode_brng }} &bull; {{ $selectedDrugDetail['obat']->nama_brng }}</p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif (Status 1)
                            </span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <span class="icon-[solar--close-circle-bold] text-2xl"></span>
                    </button>
                </div>

                {{-- Info Ringkas Obat --}}
                <div class="grid grid-cols-3 gap-2.5 text-center text-xs">
                    <div class="p-2.5 bg-gray-50 dark:bg-meta-4/30 rounded-xl border border-stroke dark:border-strokedark">
                        <span class="text-[10px] text-gray-400">Total Stok</span>
                        <div class="text-base font-black text-emerald-600 mt-0.5">
                            {{ number_format($selectedDrugDetail['total_stok'], 0, ',', '.') }} {{ $selectedDrugDetail['obat']->kode_sat }}
                        </div>
                    </div>
                    <div class="p-2.5 bg-gray-50 dark:bg-meta-4/30 rounded-xl border border-stroke dark:border-strokedark">
                        <span class="text-[10px] text-gray-400">Stok Minimal</span>
                        <div class="text-base font-black text-amber-600 mt-0.5">
                            {{ number_format($selectedDrugDetail['obat']->stokminimal, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-2.5 bg-gray-50 dark:bg-meta-4/30 rounded-xl border border-stroke dark:border-strokedark">
                        <span class="text-[10px] text-gray-400">Harga Beli</span>
                        <div class="text-base font-black text-gray-800 dark:text-white mt-0.5">
                            Rp {{ number_format($selectedDrugDetail['obat']->h_beli, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Tabel Per Depo --}}
                <div class="border border-stroke dark:border-strokedark rounded-2xl overflow-hidden max-h-60 overflow-y-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-100 dark:bg-meta-4/60 text-gray-600 dark:text-gray-300 font-bold">
                            <tr>
                                <th class="py-2.5 px-3">Gudang / Depo</th>
                                <th class="py-2.5 px-3">No. Batch</th>
                                <th class="py-2.5 px-3">No. Faktur</th>
                                <th class="py-2.5 px-3 text-right">Stok Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                            @forelse ($selectedDrugDetail['gudang'] as $g)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20">
                                    <td class="py-2 px-3 font-bold text-gray-800 dark:text-white">{{ $g->nama_depo }}</td>
                                    <td class="py-2 px-3 text-gray-500 font-mono">{{ $g->no_batch ?: '-' }}</td>
                                    <td class="py-2 px-3 text-gray-500 font-mono">{{ $g->no_faktur ?: '-' }}</td>
                                    <td class="py-2 px-3 text-right font-black text-emerald-600">
                                        {{ number_format($g->stok, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-400">Belum ada catatan lokasi depo untuk obat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" wire:click="closeDetail"
                        class="px-4 py-2 bg-gray-100 dark:bg-meta-4 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold hover:bg-gray-200 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-content>
