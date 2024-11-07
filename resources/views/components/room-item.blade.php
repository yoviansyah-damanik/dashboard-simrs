<div class="w-full p-6 bg-white rounded-md shadow-md cursor-pointer" x-on:click="$wire.setShow('{{ $title }}')">
    <div class="mb-3 text-center">
        <div class="text-2xl font-bold text-primary-500">
            {{ $title }}
        </div>
        Total Bed: {{ $total }}
    </div>

    <div class="flex gap-4">
        <div
            class="flex flex-col items-center justify-center flex-1 gap-1 p-2 text-center text-white rounded-md bg-primary-500">
            Bed Tersedia
            <div class="text-2xl font-semibold text-secondary-500">
                {{ $available }}
            </div>
        </div>
        <div
            class="flex flex-col items-center justify-center flex-1 gap-1 p-2 text-center text-white rounded-md bg-primary-500">
            Bed Terisi
            <div class="text-2xl font-semibold text-secondary-500">
                {{ $filled }}
            </div>
        </div>
    </div>
</div>
