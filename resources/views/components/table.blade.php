<div x-data='{ id: $id("table") }' class="w-full">
    <div class="{{ $baseClass }}">
        <table :id="id" class="{{ $tableClass }}">
            @if ($slot->isEmpty())
                @if (isset($header) && !$heading->isEmpty())
                    {{ $heading }}
                @else
                    <thead class="{{ $theadClass }}">
                        @foreach ($columns as $column)
                            <th class="{{ $thClass }}">{{ $column }}</th>
                        @endforeach
                    </thead>
                @endif

                <tbody>
                    {{ $body }}
                </tbody>
            @else
                {{ $slot }}
            @endif
        </table>
    </div>

    @if (isset($paginate) && !$paginate->isEmpty())
        <div class="px-8 py-3 bg-white dark:bg-slate-800 min-h-14">
            {{ $paginate }}
        </div>
    @endif
</div>
