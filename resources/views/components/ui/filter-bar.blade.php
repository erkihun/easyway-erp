@props([
    'action' => null,
    'method' => 'GET',
])

<form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" class="filter-bar panel mb-1">
    <div class="panel-body filter-bar-body">
        @if(strtoupper($method) !== 'GET')
            @csrf
        @endif
        {{ $slot }}
    </div>
</form>


