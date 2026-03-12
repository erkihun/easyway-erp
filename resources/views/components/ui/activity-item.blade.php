@props([
    'title',
    'meta' => null,
    'icon' => 'heroicon-o-clock',
])

<div style="border:1px solid #e6edf5;border-radius:10px;padding:.55rem .6rem;display:grid;gap:.2rem;">
    <div style="display:flex;align-items:center;gap:.35rem;font-weight:600;">
        <x-dynamic-component :component="$icon" class="h-4 w-4 page-header-icon" />
        <span>{{ $title }}</span>
    </div>
    @if($meta)
        <div class="muted" style="font-size:.82rem;">{{ $meta }}</div>
    @endif
    {{ $slot }}
</div>

