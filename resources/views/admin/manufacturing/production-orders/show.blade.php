@extends('layouts.admin')
@section('title','Production Order')
@section('page-title', __('manufacturing.production_orders'))
@section('content')
<x-ui.page-header title="Production {{ $order->order_number }}" subtitle="BOM execution and output details." icon="heroicon-o-wrench-screwdriver">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.manufacturing.production-orders.index')">{{ __('common.back') }}</x-ui.button>
            @if(($order->status->value ?? $order->status)!=='completed')
                <form method="POST" action="{{ route('admin.manufacturing.complete',$order) }}">@csrf<x-ui.button variant="success" type="submit" icon="heroicon-o-check-badge">{{ __('common.update') }}</x-ui.button></form>
            @endif
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">
    <x-ui.smart-button label="Status" :value="strtoupper((string)($order->status->value ?? $order->status))" icon="heroicon-o-flag" />
    <x-ui.smart-button label="Planned Qty" :value="number_format((float)$order->planned_quantity,2)" icon="heroicon-o-scale" />
    <x-ui.smart-button label="Produced Qty" :value="number_format((float)$order->produced_quantity,2)" icon="heroicon-o-cube" />
</div>

<x-ui.table-shell title="BOM Components" :count="count($order->bom?->items ?? [])">
    <table>
        <thead><tr><th>Component</th><th style="text-align:right;">Qty Per Unit</th></tr></thead>
        <tbody>
        @forelse($order->bom?->items ?? [] as $item)
            <tr><td>{{ $item->component?->name }}</td><td style="text-align:right;">{{ number_format((float)$item->quantity,2) }}</td></tr>
        @empty
            <tr><td colspan="2" class="muted">No component rows.</td></tr>
        @endforelse
        </tbody>
    </table>
</x-ui.table-shell>
@endsection





