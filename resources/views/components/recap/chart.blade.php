<div {{ $attributes->merge(['class' => 'p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1']) }}>
    <h4 class="mb-6 font-bold text-center text-primary-500 font-lg">{{ $title }}</h4>
    {{ $slot }}
</div>
