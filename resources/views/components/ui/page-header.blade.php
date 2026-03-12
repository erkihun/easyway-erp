@props([
    'title',
    'subtitle' => null,
    'icon' => null,
])

<div class="page-header mb-1">
    <div class="page-header-copy">
        <div class="page-header-title-row">
            @if($icon)
                <x-dynamic-component :component="$icon" class="h-5 w-5 page-header-icon" />
            @endif
            <h2 class="page-header-title">{{ $title }}</h2>
        </div>
        @if($subtitle)
            <p class="page-header-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if(trim($actions ?? '') !== '')
        <div class="page-header-actions">
            {{ $actions }}
        </div>
    @endif
</div>


