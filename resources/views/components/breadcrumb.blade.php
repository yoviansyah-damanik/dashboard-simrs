<div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="font-bold text-primarydark font-title text-title-md2 dark:text-white">
        {{ $title }}
    </h2>

    <nav>
        <ol class="flex items-center gap-2">
            @foreach ($items as $item)
                @if (!empty($item['href']))
                    <li
                        class="after:content-['/'] after:mx-1 after:text-bodydark2 after:pointer-events-none after:last:content-none">
                        <a class="font-medium" href="{{ $item['href'] }}" wire:navigate>{{ $item['title'] }}</a>
                    </li>
                @else
                    <li
                        class="font-medium text-primary after:content-['/'] after:mx-1 after:text-bodydark2 after:pointer-events-none after:last:content-none">
                        {{ $item['title'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
