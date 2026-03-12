@props([
    'type' => 'info',
    'message',
])

@php
    $icon = match($type) {
        'success' => 'heroicon-o-check-circle',
        'error' => 'heroicon-o-exclamation-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        default => 'heroicon-o-information-circle',
    };

    $tone = match($type) {
        'success' => 'success',
        'error' => 'error',
        'warning' => 'warning',
        default => 'info',
    };
@endphp

<div class="panel" x-data="{open:true}" x-show="open" x-transition>
    <div class="panel-body flash-row flash-{{ $tone }}">
        <div class="flash-copy">
            <x-dynamic-component :component="$icon" class="h-5 w-5" />
            <span>{{ $message }}</span>
        </div>
        <button type="button" class="icon-btn" style="width:1.8rem;height:1.8rem;" @click="open=false" aria-label="{{ __('common.dismiss') }}">
            <x-heroicon-o-x-mark class="h-4 w-4" />
        </button>
    </div>
</div>

