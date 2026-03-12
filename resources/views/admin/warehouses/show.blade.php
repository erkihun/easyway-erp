@extends('layouts.admin')
@section('title', __('warehouses.details'))
@section('page-title', __('warehouses.details'))
@section('content')
<x-ui.page-header :title="$warehouse->name" :subtitle="__('warehouses.code_prefix', ['code' => $warehouse->code]).' · '.($warehouse->location ?: __('warehouses.no_location'))" icon="heroicon-o-building-storefront">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" :href="route('admin.warehouses.edit',$warehouse)" icon="heroicon-o-pencil-square">{{ __('common.edit') }}</x-ui.button>
            <x-ui.button variant="outline" :href="route('admin.inventory.index')" icon="heroicon-o-archive-box">{{ __('common.view') }} {{ __('inventory.title') }}</x-ui.button>
            @can('manage_transfers')
                <x-ui.button :href="route('admin.transfers.create')" icon="heroicon-o-arrow-right-circle">{{ __('transfers.title') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
    <x-ui.smart-button :label="__('warehouses.stock_items')" :value="$movementSummary->count()" icon="heroicon-o-archive-box" :href="route('admin.inventory.index')" />
    <x-ui.smart-button :label="__('warehouses.transfers_in_out')" :value="$recentMovements->count()" icon="heroicon-o-arrow-path" :href="route('admin.transfers.index')" />
    <x-ui.smart-button :label="__('warehouses.adjustments')" :value="__('common.open')" icon="heroicon-o-adjustments-horizontal" :href="route('admin.inventory.adjustments')" />
</div>

<div class="panel mb-1"><div class="panel-body"><h3 style="margin-top:0;">{{ __('warehouses.stock_movement_summary') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('common.type') }}</th><th style="text-align:right;">{{ __('common.quantity') }}</th></tr></thead><tbody>@forelse($movementSummary as $row)<tr><td><x-ui.status-badge :status="$row->movement_type" /></td><td style="text-align:right;">{{ number_format((float)$row->qty,2) }}</td></tr>@empty<tr><td colspan="2" class="muted">{{ __('warehouses.no_movements') }}</td></tr>@endforelse</tbody></table></div></div></div>

<div class="panel"><div class="panel-body"><h3 style="margin-top:0;">{{ __('warehouses.recent_movements') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('common.date') }}</th><th>{{ __('dashboard.product') }}</th><th>{{ __('common.type') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th></tr></thead><tbody>@forelse($recentMovements as $m)<tr><td>{{ $m->created_at?->format('Y-m-d H:i') }}</td><td>{{ $m->product?->name }}</td><td><x-ui.status-badge :status="$m->movement_type" /></td><td style="text-align:right;">{{ number_format((float)$m->quantity,2) }}</td></tr>@empty<tr><td colspan="4" class="muted">{{ __('warehouses.no_movement_records') }}</td></tr>@endforelse</tbody></table></div></div></div>
@endsection





