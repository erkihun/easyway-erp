@props([
    'title' => null,
    'description' => null,
    'icon' => 'heroicon-o-inbox',
])

<div class="empty-state panel">
    <div class="panel-body empty-state-body">
        <x-dynamic-component :component="$icon" class="h-8 w-8 empty-state-icon" />
        <div class="empty-state-title">{{ $title ?? __('common.no_records_found') }}</div>
        <div class="empty-state-description">{{ $description ?? __('common.no_data_found') }}</div>
    </div>
</div>

