@props(['status' => 'unknown'])

@php
    $rawStatus = $status;
    if ($rawStatus instanceof \BackedEnum) {
        $rawStatus = $rawStatus->value;
    } elseif ($rawStatus instanceof \UnitEnum) {
        $rawStatus = $rawStatus->name;
    }

    $normalized = strtolower((string) ($rawStatus ?? 'unknown'));
    $tone = 'neutral';

    if (in_array($normalized, ['paid', 'completed', 'delivered', 'active', 'open', 'in_stock', 'transfer_in', 'purchase', 'production', 'return'], true)) {
        $tone = 'success';
    } elseif (in_array($normalized, ['confirmed', 'packed', 'shipped', 'in_transit', 'processing'], true)) {
        $tone = 'info';
    } elseif (in_array($normalized, ['pending', 'draft', 'partially_paid', 'partial', 'low_stock', 'adjustment'], true)) {
        $tone = 'warning';
    } elseif (in_array($normalized, ['cancelled', 'failed', 'damage', 'out_of_stock', 'sale', 'transfer_out'], true)) {
        $tone = 'danger';
    } elseif (in_array($normalized, ['overstock'], true)) {
        $tone = 'info';
    }

    $label = __('common.status_values.' . $normalized);
    if ($label === 'common.status_values.' . $normalized) {
        $label = str_replace('_', ' ', ucfirst($normalized));
    }
@endphp

<x-ui.badge :tone="$tone">
    {{ $label }}
</x-ui.badge>

