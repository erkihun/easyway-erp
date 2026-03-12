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
            <span style="display:grid;place-items:center;width:1.9rem;height:1.9rem;border-radius:10px;background:#eef2ff;">
                <x-dynamic-component :component="$icon" class="h-5 w-5 kpi-card-icon" />
            </span>
        @endif
    </div>
    <div class="kpi-card-value">{{ $value }}</div>
    @if($helper)
        <div class="muted" style="font-size:.78rem;margin-top:.2rem;">{{ $helper }}</div>
    @endif
</div>

