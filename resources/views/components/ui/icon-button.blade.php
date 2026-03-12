@props([
    'href' => null,
    'type' => 'button',
    'icon' => 'heroicon-o-ellipsis-horizontal',
    'label' => null,
    'variant' => 'ghost',
])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-'.$variant.' btn-icon']) }} aria-label="{{ $label ?? __('common.actions') }}" title="{{ $label ?? __('common.actions') }}">
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-'.$variant.' btn-icon']) }} aria-label="{{ $label ?? __('common.actions') }}" title="{{ $label ?? __('common.actions') }}">
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    </button>
@endif

