@props([
    'href',
    'label',
    'icon' => 'heroicon-o-arrow-right',
    'variant' => 'outline',
])

<x-ui.button :href="$href" :icon="$icon" :variant="$variant" size="sm">
    {{ $label }}
</x-ui.button>

