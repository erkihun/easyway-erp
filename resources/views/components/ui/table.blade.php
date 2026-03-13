@props([
    'compact' => false,
])

<div {{ $attributes->merge(['class' => 'table-wrap']) }}>
    <table @class(['erp-table', 'erp-table-compact' => $compact])>
        {{ $slot }}
    </table>
</div>

