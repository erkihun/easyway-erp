@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'default',
])

<div class="kpi-card tone-{{ $tone }}">
    <div class="kpi-card-head">
        <span class="kpi-card-label">{{ $label }}</span>
        @if($icon)
            <x-dynamic-component :component="$icon" class="h-5 w-5 kpi-card-icon" />
        @endif
    </div>
    <div class="kpi-card-value">{{ $value }}</div>
</div>


