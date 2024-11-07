<div @class([
    'w-full bg-white p-6 rounded-md shadow-md cursor-pointer transition-all relative overflow-hidden',
]) x-on:click="$wire.setShow('{{ $title }}')">
    <div @class([
        'absolute top-0 right-0 flex items-center justify-center bg-gradient-to-tr from-meta-3 to-primary-500 text-whiten dark:bg-meta-4 transition-[width_height] duration-700',
        'w-[200%] h-[200%] rounded-bl-none' => $isActive,
        'w-16 h-16 rounded-bl-full' => !$isActive,
    ])>
    </div>
    <div class="absolute top-0 right-0 flex items-center justify-center w-16 h-16 rounded-bl-full text-whiten">
        <span class="mb-3 ml-3 size-6 i-ph-door"></span>
    </div>

    <div class="relative mb-3 text-center">
        <div @class([
            'text-2xl font-bold transition-all delay-200',
            'text-secondary-500' => $isActive,
            'text-primary-500' => !$isActive,
        ])>
            {{ $title }}
        </div>
        <div @class([
            'transition-all delay-200',
            'text-whiten' => $isActive,
            'text-primary-500' => !$isActive,
        ])>
            Total Kamar: {{ $total }}
        </div>
    </div>

    <div class="relative flex gap-4">
        <div @class([
            'flex flex-col items-center justify-center flex-1 gap-1 p-2 text-center rounded-md transition-all delay-200',
            'bg-secondary-100' => $isActive,
            'bg-primary-100' => !$isActive,
        ])>
            Kamar Tersedia
            <div class="text-2xl font-semibold delay-200 text-primary-500">
                {{ $available }}
            </div>
        </div>
        <div @class([
            'flex flex-col items-center justify-center flex-1 gap-1 p-2 text-center rounded-md transition-all delay-200',
            'bg-secondary-100' => $isActive,
            'bg-primary-100' => !$isActive,
        ])>
            Kamar Terisi
            <div class="text-2xl font-semibold delay-200 text-primary-500">
                {{ $filled }}
            </div>
        </div>
    </div>
</div>
