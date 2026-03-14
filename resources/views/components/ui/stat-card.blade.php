@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'default',
    'helper' => null,
])

<div class="kpi-card tone-{{ $tone }}">
    <div class="kpi-card-head">
        <span class="kpi-card-label">{{ $label }}</span>
        @if($icon)
            <span class="kpi-card-icon-wrap">
                <x-dynamic-component :component="$icon" class="h-5 w-5 kpi-card-icon" />
            </span>
        @endif
    </div>
    <div class="kpi-card-value">{{ $value }}</div>
    @if($helper)
        <div class="kpi-card-helper">{{ $helper }}</div>
    @endif
</div>
