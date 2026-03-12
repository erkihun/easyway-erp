@props([
    'label',
    'value' => null,
    'href' => null,
    'icon' => 'heroicon-o-square-3-stack-3d',
    'variant' => 'default',
])

@php
    $classes = 'smart-btn smart-btn-'.$variant;
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
<div {{ $attributes->merge(['class' => $classes]) }}>
@endif
    <x-dynamic-component :component="$icon" class="h-5 w-5 smart-btn-icon" />
    <div class="smart-btn-copy">
        <div class="smart-btn-label">{{ $label }}</div>
        @if($value !== null)
            <div class="smart-btn-value">{{ $value }}</div>
        @endif
    </div>
@if($href)
</a>
@else
</div>
@endif


