@props([
    'tahun' => now()->year,
    'bulan' => now()->month,
    'showBulan' => true,
])

<div class="flex flex-wrap items-end gap-3 p-4 bg-gray-50 dark:bg-meta-4/30 rounded-2xl border border-stroke dark:border-strokedark print:hidden">
    <div>
        <label class="block text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
        <select wire:model.live="tahun"
            class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    @if ($showBulan)
        <div>
            <label class="block text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
            <select wire:model.live="bulan"
                class="px-4 py-2.5 bg-white border border-stroke rounded-xl dark:bg-boxdark dark:border-strokedark text-sm font-bold focus:border-primary outline-none shadow-sm">
                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $m => $nama)
                    <option value="{{ $m }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div wire:loading.flex class="flex items-center gap-2 text-sm font-bold text-primary">
        <span class="icon-[solar--refresh-bold-duotone] animate-spin text-lg"></span>
        <span>Memuat data...</span>
    </div>
</div>
