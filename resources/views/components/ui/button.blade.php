@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'disabled' => false,
])

@php
    $class = 'btn btn-' . $variant . ' btn-' . $size;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }} @if($disabled) aria-disabled="true" @endif>
        @if($icon)
            <x-dynamic-component :component="$icon" class="h-4 w-4" />
        @endif
        <span class="btn-label">{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }} @if($disabled) disabled @endif>
        @if($icon)
            <x-dynamic-component :component="$icon" class="h-4 w-4" />
        @endif
        <span class="btn-label">{{ $slot }}</span>
    </button>
@endif


