@props([
    'label',
    'route',
    'icon',
    'activePattern',
    'isMobile' => false,
])

@php
    $isActive = request()->routeIs($activePattern);
    $lucideMap = [
        'heroicon-o-home' => 'layout-dashboard',
        'heroicon-o-cube' => 'package-2',
        'heroicon-o-squares-2x2' => 'blocks',
        'heroicon-o-tag' => 'tag',
        'heroicon-o-archive-box' => 'boxes',
        'heroicon-o-book-open' => 'book-open',
        'heroicon-o-arrow-path' => 'arrow-right-left',
        'heroicon-o-building-storefront' => 'warehouse',
        'heroicon-o-adjustments-horizontal' => 'sliders-horizontal',
        'heroicon-o-exclamation-triangle' => 'triangle-alert',
        'heroicon-o-banknotes' => 'banknote',
        'heroicon-o-clipboard-document-list' => 'clipboard-list',
        'heroicon-o-wrench-screwdriver' => 'factory',
        'heroicon-o-shopping-cart' => 'shopping-cart',
        'heroicon-o-users' => 'users',
        'heroicon-o-truck' => 'truck',
        'heroicon-o-calculator' => 'calculator',
        'heroicon-o-document-text' => 'file-text',
        'heroicon-o-credit-card' => 'credit-card',
        'heroicon-o-receipt-percent' => 'receipt',
        'heroicon-o-chart-bar' => 'bar-chart-3',
        'heroicon-o-user-group' => 'users-round',
        'heroicon-o-user-circle' => 'circle-user-round',
        'heroicon-o-shield-check' => 'shield-check',
        'heroicon-o-lock-closed' => 'lock',
        'heroicon-o-cog-6-tooth' => 'settings',
    ];
    $lucideIcon = $lucideMap[$icon] ?? 'circle';
@endphp

<a
    href="{{ route($route) }}"
    class="sidebar-item flex items-center gap-2 {{ $isActive ? 'is-active' : '' }}"
    data-tooltip="{{ $label }}"
    @if($isMobile) @click="sidebarOpen = false" @endif
>
    <span class="sidebar-item-icon-wrap" aria-hidden="true">
        <i data-lucide="{{ $lucideIcon }}" class="sidebar-item-icon"></i>
    </span>
    <span class="sidebar-item-label" x-show="{{ $isMobile ? 'true' : '!sidebarCollapsed' }}" x-transition.opacity>
        {{ $label }}
    </span>
</a>


