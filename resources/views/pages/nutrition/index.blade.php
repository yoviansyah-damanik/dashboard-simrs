<x-content>
    <x-breadcrumb title="Gizi" :items="[['title' => 'Layanan Penunjang Medis'], ['title' => 'Gizi'], ['title' => $activeTab === 'permintaan' ? 'Permintaan Diet' : 'Asuhan Gizi']]" />

    <div class="space-y-6">
        {{-- Header Controls & Quick Navigation --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            {{-- Tabs --}}
            <div class="inline-flex p-1 bg-gray-100 dark:bg-meta-4 rounded-2xl border border-stroke dark:border-strokedark self-start">
                <button type="button" wire:click="$set('activeTab', 'permintaan')"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ $activeTab === 'permintaan' ? 'bg-white dark:bg-boxdark text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white' }}">
                    <span class="icon-[solar--cup-first-bold-duotone] text-base"></span>
                    Permintaan Diet Pasien
                </button>
                <button type="button" wire:click="$set('activeTab', 'asuhan')"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ $activeTab === 'asuhan' ? 'bg-white dark:bg-boxdark text-primary shadow-sm' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white' }}">
                    <span class="icon-[solar--clipboard-heart-bold-duotone] text-base"></span>
                    Evaluasi Asuhan Gizi
                </button>
            </div>

            {{-- Action Navigation Buttons --}}
            <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                <a href="{{ route('ancillary.summary') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white dark:bg-boxdark dark:text-gray-300 border border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4/40 transition-all shadow-xs"
                    title="Buka Ringkasan Layanan Penunjang">
                    <span class="icon-[solar--widget-2-bold-duotone] text-sm text-emerald-600"></span>
                    <span>Ringkasan Penunjang</span>
                </a>
                <a href="{{ route('ancillary.yearly-matrix', ['activeTab' => 'gizi']) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-gray-700 bg-white dark:bg-boxdark dark:text-gray-300 border border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4/40 transition-all shadow-xs"
                    title="Buka Matriks Indikator Tahunan">
                    <span class="icon-[solar--checklist-minimalistic-bold-duotone] text-sm text-amber-600"></span>
                    <span>Matriks Indikator</span>
                </a>
                <a href="{{ route('nutrition.recap') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-amber-700 bg-amber-500/10 hover:bg-amber-500/20 dark:text-amber-300 dark:bg-amber-900/30 border border-amber-500/20 transition-all">
                    <span class="icon-[solar--chart-bold-duotone] text-base"></span>
                    Buka Rekap Permintaan Diet
                    <span class="icon-[solar--arrow-right-line-duotone] text-sm"></span>
                </a>
            </div>
        </div>

        @if ($activeTab === 'permintaan')
            {{-- KPI STATS ROW --}}
            @if ($summary)
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3.5">
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white p-4 rounded-2xl shadow-sm flex flex-col justify-between col-span-2 md:col-span-1">
                        <span class="text-[11px] font-black uppercase tracking-widest text-amber-100">Total Permintaan</span>
                        <div class="mt-2 flex items-baseline justify-between">
                            <span class="text-3xl font-black">{{ number_format($summary['total_porsi'], 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-amber-100">Porsi</span>
                        </div>
                        <div class="text-[11px] text-amber-100/80 mt-1 font-semibold">
                            {{ number_format($summary['total_pasien'], 0, ',', '.') }} Pasien Terlayani
                        </div>
                    </div>

                    <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Makan Pagi
                        </span>
                        <div class="mt-2 flex items-baseline justify-between">
                            <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($summary['pagi'], 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">Porsi</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden mt-1.5">
                            @php $pagiPct = $summary['total_porsi'] > 0 ? round(($summary['pagi'] / $summary['total_porsi']) * 100) : 0; @endphp
                            <div class="h-full bg-amber-500" style="width: {{ $pagiPct }}%"></div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Makan Siang
                        </span>
                        <div class="mt-2 flex items-baseline justify-between">
                            <span class="text-2xl font-black text-orange-600 dark:text-orange-400">{{ number_format($summary['siang'], 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">Porsi</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden mt-1.5">
                            @php $siangPct = $summary['total_porsi'] > 0 ? round(($summary['siang'] / $summary['total_porsi']) * 100) : 0; @endphp
                            <div class="h-full bg-orange-500" style="width: {{ $siangPct }}%"></div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Makan Sore / Malam
                        </span>
                        <div class="mt-2 flex items-baseline justify-between">
                            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($summary['sore'], 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">Porsi</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-meta-4 h-1.5 rounded-full overflow-hidden mt-1.5">
                            @php $sorePct = $summary['total_porsi'] > 0 ? round(($summary['sore'] / $summary['total_porsi']) * 100) : 0; @endphp
                            <div class="h-full bg-indigo-500" style="width: {{ $sorePct }}%"></div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-black uppercase tracking-widest text-gray-400">Varian Diet</span>
                        <div class="mt-2 flex items-baseline justify-between">
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($summary['total_jenis_diet'], 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">Jenis</span>
                        </div>
                        <span class="text-[11px] text-gray-400 mt-1 font-semibold truncate">Aktif Diminta</span>
                    </div>
                </div>
            @endif

            {{-- FILTER CARD --}}
            <div class="bg-white dark:bg-boxdark p-5 rounded-3xl border border-stroke dark:border-strokedark shadow-sm space-y-4">
                {{-- Row 1: Search & Period --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6 relative">
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Cari nama pasien, No RM, No Rawat, kamar, atau jenis diet..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-semibold focus:border-amber-500 focus:bg-white dark:focus:bg-boxdark outline-none transition" />
                        <span class="icon-[solar--magnifer-linear] absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></span>
                    </div>

                    <div class="md:col-span-3">
                        <select wire:model.live="period"
                            class="w-full py-2.5 px-3 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 outline-none focus:border-amber-500 cursor-pointer">
                            <option value="today">Hari Ini</option>
                            <option value="yesterday">Kemarin</option>
                            <option value="last_7_days">7 Hari Terakhir</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="all">Semua Periode</option>
                            <option value="custom">Rentang Khusus (Custom)</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <select wire:model.live="waktu"
                            class="w-full py-2.5 px-3 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 outline-none focus:border-amber-500 cursor-pointer">
                            <option value="semua">Semua Waktu Makan</option>
                            <option value="Pagi">Makan Pagi</option>
                            <option value="Siang">Makan Siang</option>
                            <option value="Sore">Makan Sore / Malam</option>
                        </select>
                    </div>
                </div>

                {{-- Row 2: Secondary Dropdowns & Custom Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-1 border-t border-stroke/60 dark:border-strokedark/60">
                    <div class="lg:col-span-4">
                        <select wire:model.live="kdDiet"
                            class="w-full py-2 px-3 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 outline-none focus:border-amber-500 cursor-pointer">
                            <option value="semua">Semua Jenis Diet</option>
                            @foreach ($dietList as $d)
                                <option value="{{ $d->kd_diet }}">{{ $d->kd_diet }} - {{ $d->nama_diet }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-4">
                        <select wire:model.live="kdBangsal"
                            class="w-full py-2 px-3 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 outline-none focus:border-amber-500 cursor-pointer">
                            <option value="semua">Semua Bangsal / Ruangan</option>
                            @foreach ($bangsalList as $b)
                                <option value="{{ $b->kd_bangsal }}">{{ $b->nm_bangsal }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($period === 'custom')
                        <div class="lg:col-span-3 flex items-center gap-1.5">
                            <input type="date" wire:model.live="startDate"
                                class="w-full py-1.5 px-2 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold outline-none focus:border-amber-500" />
                            <span class="text-xs text-gray-400 font-bold">-</span>
                            <input type="date" wire:model.live="endDate"
                                class="w-full py-1.5 px-2 bg-gray-50 dark:bg-meta-4 border border-stroke dark:border-strokedark rounded-xl text-xs font-bold outline-none focus:border-amber-500" />
                        </div>
                    @else
                        <div class="lg:col-span-3 flex items-center text-xs text-gray-400 font-semibold px-1">
                            <span>Periode: {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua' }} s/d {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Semua' }}</span>
                        </div>
                    @endif

                    <div class="lg:col-span-1 flex items-center justify-end">
                        <button type="button" wire:click="resetFilters"
                            class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 dark:hover:bg-opacity-80 text-gray-500 transition"
                            title="Reset Semua Filter">
                            <span class="icon-[solar--restart-bold] text-base"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- TABLE PERMINTAAN DIET --}}
            <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-meta-4/40 border-b border-stroke dark:border-strokedark text-[11px] font-black uppercase tracking-wider text-gray-400">
                            <tr>
                                <th class="py-3 px-3 text-center w-12">#</th>
                                <th class="py-3 px-3">Waktu & Tanggal</th>
                                <th class="py-3 px-3">No. Rawat & RM</th>
                                <th class="py-3 px-3">Nama Pasien</th>
                                <th class="py-3 px-3">Lokasi Bangsal & Kamar</th>
                                <th class="py-3 px-3 text-center">Jenis Diet</th>
                                <th class="py-3 px-3 text-center">Sisa Makanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke/60 dark:divide-strokedark/60 font-medium">
                            @forelse ($dietOrders as $idx => $order)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-meta-4/20 transition-colors">
                                    <td class="py-3 px-3 text-center font-bold text-gray-400">
                                        {{ $dietOrders->firstItem() + $idx }}
                                    </td>

                                    {{-- Waktu & Tanggal --}}
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2">
                                            @if (str_starts_with($order->waktu, 'Pagi'))
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                    <span class="icon-[solar--sun-2-bold-duotone] text-xs"></span>
                                                    Pagi
                                                </span>
                                            @elseif (str_starts_with($order->waktu, 'Siang'))
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">
                                                    <span class="icon-[solar--sun-bold-duotone] text-xs"></span>
                                                    Siang
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                    <span class="icon-[solar--moon-stars-bold-duotone] text-xs"></span>
                                                    Sore / Malam
                                                </span>
                                            @endif
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                {{ date('d/m/Y', strtotime($order->tanggal)) }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- No. Rawat & RM --}}
                                    <td class="py-3 px-3 font-mono">
                                        <div class="font-bold text-gray-800 dark:text-white">{{ $order->no_rawat }}</div>
                                        <div class="text-[11px] text-gray-400">RM: {{ $order->no_rkm_medis ?: '-' }}</div>
                                    </td>

                                    {{-- Pasien --}}
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $order->nm_pasien }}</div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $order->jk === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $order->umurdaftar }} {{ $order->sttsumur ?: 'Th' }}
                                        </div>
                                    </td>

                                    {{-- Bangsal & Kamar --}}
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-gray-800 dark:text-white line-clamp-1">
                                            {{ $order->nm_bangsal ?: $order->kd_bangsal ?: '-' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 font-mono">
                                            Kamar: {{ $order->kd_kamar ?: '-' }} {{ $order->kelas ? '(' . $order->kelas . ')' : '' }}
                                        </div>
                                    </td>

                                    {{-- Jenis Diet --}}
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60 shadow-sm">
                                            <span class="icon-[solar--cup-first-bold-duotone] text-sm text-emerald-600"></span>
                                            {{ $order->nama_diet ?: $order->kd_diet }}
                                        </span>
                                    </td>

                                    {{-- Sisa Makanan --}}
                                    <td class="py-3 px-3 text-center text-[11px]">
                                        @if ($order->sisa_karbo !== null || $order->sisa_hewani !== null)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold bg-gray-100 text-gray-600 dark:bg-meta-4 dark:text-gray-300">
                                                Tercatat
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <span class="icon-[solar--cup-first-bold-duotone] text-4xl text-gray-300"></span>
                                            <span class="font-bold text-sm text-gray-500">Tidak ada data permintaan diet yang sesuai filter.</span>
                                            <span class="text-xs text-gray-400">Coba ubah filter rentang tanggal, waktu makan, atau kata kunci pencarian.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($dietOrders->hasPages())
                    <div class="p-4 border-t border-stroke dark:border-strokedark">
                        {{ $dietOrders->links() }}
                    </div>
                @endif
            </div>

        @else
            {{-- TAB: ASUHAN GIZI --}}
            <div class="space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row">
                    <x-form.input type="search" block class="flex-1" wire:model.live.debounce.750ms="search"
                        placeholder="Cari berdasarkan Nomor RM, Nama Pasien, atau No Rawat" />
                    <div class="flex flex-1 gap-3">
                        <x-form.input block type="date" wire:model.live='startDate' :max="date('Y-m-d')" />
                        <x-form.input block type="date" wire:model.live='endDate' :max="date('Y-m-d')" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <x-form.select label="Perpage" block :items="$limits" wire:model.live='perPage' />
                    <x-form.select label="Jenis Kelamin" block :items="$genders" wire:model.live='gender' />
                </div>

                @if ($records && $records->count() > 0)
                    <div class="space-y-4">
                        @foreach ($records as $record)
                            <x-nutrition-item :$record />
                        @endforeach
                    </div>
                @else
                    <x-no-data />
                @endif

                @if ($records && method_exists($records, 'links'))
                    <x-pagination>
                        {{ $records->links() }}
                    </x-pagination>
                @endif
            </div>
        @endif
    </div>
</x-content>
