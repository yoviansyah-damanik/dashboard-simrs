@props(['stats'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach ($stats as $label => $value)
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-stroke dark:border-strokedark shadow-sm p-5 text-center">
            <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">{{ $label }}</p>
            <h4 class="text-2xl font-black text-primary">{{ number_format($value) }}</h4>
        </div>
    @endforeach
</div>
