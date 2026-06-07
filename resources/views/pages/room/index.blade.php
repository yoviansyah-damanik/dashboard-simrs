<x-content>
    <x-breadcrumb title="Kamar" :items="[['title' => 'Kamar']]" />

    <x-room-item-container>
        @foreach ($rooms as $key => $room)
            <x-room-by-class-item :title="$key" :isActive="$roomActive === $key" :total="$room['total']" :available="$room['tersedia']"
                :filled="$room['terisi']" />
        @endforeach
    </x-room-item-container>

    <div class="block h-1 !my-9 bg-primary-500"></div>

    @if ($roomActive)
        <div class="mt-8 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <!-- Premium Header Design with vibrant colors -->
            <div
                class="relative p-6 rounded-[2.5rem] bg-white dark:bg-boxdark border-y border-stroke dark:border-strokedark shadow-2xl shadow-gray-200/50 dark:shadow-none overflow-hidden group">
                <!-- Background Accent -->
                <div
                    class="absolute right-0 top-0 w-64 h-64 bg-primary/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:bg-primary/20 transition-colors duration-700">
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-600 blur-xl opacity-30 animate-pulse"></div>
                            <div
                                class="relative w-16 h-16 rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-xl shadow-blue-500/40">
                                <span class="icon-[solar--bed-bold-duotone] text-3xl"></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase tracking-widest">Live
                                    Monitoring</span>
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></div>
                            </div>
                            <h3
                                class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tighter leading-none">
                                Detail Kamar: <span class="text-blue-600">{{ $roomActive }}</span>
                            </h3>
                            <p
                                class="text-xs text-gray-500 font-bold uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                                <span class="w-10 h-0.5 bg-blue-600"></span>
                                Daftar Unit & Status Ketersediaan Bed
                            </p>
                        </div>
                    </div>

                    <button wire:click="$set('roomActive', null)"
                        class="group/btn flex items-center gap-3 px-6 py-3 bg-red-50 dark:bg-red-900/10 hover:bg-red-600 rounded-2xl transition-all duration-500 border border-red-100 dark:border-red-900/30">
                        <span
                            class="text-[10px] font-black text-red-600 group-hover/btn:text-white uppercase tracking-widest">Tutup
                            Panel</span>
                        <div
                            class="w-8 h-8 rounded-xl bg-white dark:bg-boxdark flex items-center justify-center shadow-sm group-hover/btn:rotate-180 transition-transform duration-700">
                            <span class="icon-[solar--close-circle-bold-duotone] text-lg text-red-500"></span>
                        </div>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                @foreach ($roomList as $room)
                    <div class="relative group perspective-1000">
                        <div
                            class="relative p-6 rounded-[2rem] border-2 transition-all duration-500 overflow-hidden {{ $room['status'] === 'ISI' ? 'bg-white dark:bg-boxdark border-indigo-100 dark:border-indigo-900/30 shadow-indigo-100/50' : 'bg-white dark:bg-boxdark border-emerald-100 dark:border-emerald-900/30 shadow-emerald-100/50' }} hover:shadow-2xl hover:-translate-y-2">
                            <!-- Background Accent -->
                            <div
                                class="absolute -right-4 -top-4 w-16 h-16 rounded-full opacity-10 {{ $room['status'] === 'ISI' ? 'bg-indigo-600' : 'bg-emerald-600' }}">
                            </div>

                            <div class="flex flex-col items-center text-center gap-4 relative z-10">
                                <!-- Status Icon with Glow -->
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 blur-lg opacity-40 {{ $room['status'] === 'ISI' ? 'bg-indigo-500' : 'bg-emerald-500' }}">
                                    </div>
                                    <div
                                        class="relative w-14 h-14 rounded-2xl flex items-center justify-center {{ $room['status'] === 'ISI' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' }}">
                                        <span class="icon-[solar--bed-bold-duotone] text-3xl"></span>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest">ID Kamar
                                    </div>
                                    <div
                                        class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tight">
                                        {{ $room['kode_kamar'] }}</div>
                                </div>

                                <!-- Status Badge -->
                                <div class="w-full">
                                    <span
                                        class="inline-flex w-full items-center justify-center py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] {{ $room['status'] === 'ISI' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                                        {{ $room['status'] === 'ISI' ? 'Terisi' : 'Kosong' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Premium Tooltip -->
                        <div
                            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-4 w-56 p-4 bg-gray-900/95 dark:bg-black/95 backdrop-blur-md text-white rounded-2xl opacity-0 group-hover:opacity-100 pointer-events-none transition-all duration-300 z-50 shadow-2xl scale-95 group-hover:scale-100">
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center gap-2 border-b border-white/10 pb-2">
                                    <span class="icon-[solar--hospital-bold-duotone] text-primary"></span>
                                    <div class="text-[10px] font-black uppercase tracking-tight">
                                        {{ $room['bangsal']['nama_bangsal'] ?? 'Unit Tidak Diketahui' }}</div>
                                </div>
                                <div class="flex justify-between items-center text-[9px]">
                                    <span class="text-gray-400 font-bold uppercase tracking-widest">Tarif Kamar</span>
                                    <span
                                        class="text-xs font-black text-emerald-400">Rp{{ number_format($room['tarif_kamar']) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[9px]">
                                    <span class="text-gray-400 font-bold uppercase tracking-widest">Kelas</span>
                                    <span class="font-black">{{ $room['kelas'] }}</span>
                                </div>
                            </div>
                            <!-- Arrow -->
                            <div
                                class="absolute top-full left-1/2 -translate-x-1/2 w-3 h-3 bg-gray-900/95 rotate-45 -mt-1.5">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="py-20 flex flex-col items-center justify-center text-center space-y-4">
            <div
                class="w-24 h-24 bg-gray-100 dark:bg-meta-4 rounded-full flex items-center justify-center text-gray-300">
                <span class="icon-[solar--bed-bold-duotone] text-5xl"></span>
            </div>
            <div>
                <h4 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight">Pilih Kelas Kamar
                </h4>
                <p class="text-sm text-gray-400">Klik salah satu kartu kelas di atas untuk melihat detail tempat tidur.
                </p>
            </div>
        </div>
    @endif
</x-content>
