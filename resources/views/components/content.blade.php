<div id="content" {{ $attributes->merge(['class' => 'relative space-y-3 sm:space-y-4 w-full']) }}>
    <x-content.alert />

    {{ $slot }}
</div>
