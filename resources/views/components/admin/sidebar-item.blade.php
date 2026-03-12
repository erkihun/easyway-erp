@props([
    'label',
    'route',
    'icon',
    'activePattern',
    'isMobile' => false,
])

@php
    $isActive = request()->routeIs($activePattern);
@endphp

<a
    href="{{ route($route) }}"
    class="sidebar-item flex items-center gap-2 {{ $isActive ? 'is-active' : '' }}"
    data-tooltip="{{ $label }}"
    @if($isMobile) @click="sidebarOpen = false" @endif
>
    <x-dynamic-component :component="$icon" class="h-5 w-5 sidebar-item-icon" />
    <span class="sidebar-item-label" x-show="{{ $isMobile ? 'true' : '!sidebarCollapsed' }}" x-transition.opacity>
        {{ $label }}
    </span>
</a>


