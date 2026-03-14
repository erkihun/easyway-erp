@props([
    'title',
    'subtitle' => null,
    'icon' => 'heroicon-o-chart-bar',
])

<div class="panel glass-card">
    <div class="panel-body">
        <div class="chart-card-head">
            <div>
                <div class="chart-card-title">
                    <x-dynamic-component :component="$icon" class="h-5 w-5 page-header-icon" />
                    <span>{{ $title }}</span>
                </div>
                @if($subtitle)
                    <div class="chart-card-subtitle">{{ $subtitle }}</div>
                @endif
            </div>
            {{ $actions ?? '' }}
        </div>

        {{ $slot }}
    </div>
</div>

