@props([
    'title' => null,
    'count' => null,
])

<x-ui.card {{ $attributes }}>
    @if($title || $count !== null)
        <x-slot:header>
            <div class="table-shell-head">
                @if($title)<h3 class="table-shell-title">{{ $title }}</h3>@endif
                @if($count !== null)<x-ui.badge tone="neutral">{{ $count }}</x-ui.badge>@endif
            </div>
        </x-slot:header>
    @endif

    <div class="table-wrap">
        {{ $slot }}
    </div>
</x-ui.card>

