@props([
    'label' => null,
    'icon' => 'heroicon-o-chevron-down',
])

<div class="dropdown" x-data="{open:false}" @click.outside="open=false" @keydown.escape.window="open=false">
    <button type="button" class="btn btn-outline btn-sm" @click="open = !open" :aria-expanded="open ? 'true' : 'false'">
        <span>{{ $label ?? __('common.more') }}</span>
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    </button>
    <div class="dropdown-menu" x-show="open" x-transition x-cloak>
        {{ $slot }}
    </div>
</div>

