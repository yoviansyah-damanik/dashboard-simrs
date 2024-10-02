<td {{ $attributes->class(['px-3 sm:px-4 py-2 align-top', $centered ? 'text-center' : '']) }}
    {{ $attributes->whereStartsWith('colspan') }}>
    {{ $slot }}
</td>
