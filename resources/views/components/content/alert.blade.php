@if (session('alert'))
    <x-alert :type="session('alert-type')">
        {{ session('msg') }}
    </x-alert>
@endif
