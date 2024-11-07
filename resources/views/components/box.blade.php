<div
    {{ $attributes->merge([
        'class' =>
            'rounded-md border border-stroke px-7.5 py-6 shadow-default dark:border-strokedark relative overflow-hidden bg-white dark:bg-boxdark',
    ]) }}>

    <div @class([
        'absolute top-0 right-0 flex items-center justify-center bg-gradient-to-tr from-meta-3 to-primary-500 text-whiten dark:bg-meta-4 transition-[width_height] duration-700',
        'w-[200%] h-[200%] rounded-bl-none' => $isActive,
        'w-16 h-16 rounded-bl-full' => !$isActive,
    ])>
    </div>

    <div class="absolute top-0 right-0 flex items-center justify-center w-16 h-16 rounded-bl-full text-whiten">
        <span @class(['size-6 ml-3 mb-3', $icon])></span>
    </div>

    <div class="relative flex items-end justify-between mt-4">
        <div class="flex-1">
            <div>
                <h4 @class([
                    'font-extrabold text-black text-title-lg dark:text-white transition duration-700',
                    'text-secondary-500' => $isActive,
                ])>
                    {{ $value }}
                </h4>
                <span @class([
                    'text-sm font-medium transition duration-700',
                    'text-whiten' => $isActive,
                ])>{{ $title }}</span>
            </div>

            @if ($percentage)
                @if ($isUp)
                    <span class="flex items-center gap-1 text-sm font-medium text-meta-3">
                        {{ $percentage }}%
                        <span class="i-ph-arrow-up"></span>
                    </span>
                @else
                    <span class="flex items-center gap-1 text-sm font-medium text-meta-1">
                        {{ $percentage }}%
                        <span class="i-ph-arrow-down"></span>
                    </span>
                @endif
            @endif
        </div>

        {{ $slot }}
    </div>
</div>
