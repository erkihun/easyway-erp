@extends('layouts.admin')
@section('title', __('transfers.details'))
@section('page-title', __('transfers.details'))
@section('content')
<x-ui.page-header :title="__('transfers.details_title', ['number' => $transfer->transfer_number])" :subtitle="($transfer->sourceWarehouse?->name ?? __('common.none')).' → '.($transfer->destinationWarehouse?->name ?? __('common.none'))" icon="heroicon-o-arrow-path">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.transfers.index')">{{ __('common.back') }}</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.warehouses.show',$transfer->sourceWarehouse)" icon="heroicon-o-arrow-uturn-left">{{ __('transfers.source_warehouse') }}</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.warehouses.show',$transfer->destinationWarehouse)" icon="heroicon-o-arrow-uturn-right">{{ __('transfers.destination_warehouse') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">
    <x-ui.smart-button :label="__('transfers.status')" :value="strtoupper((string)($transfer->status->value ?? $transfer->status))" icon="heroicon-o-flag" />
    <x-ui.smart-button :label="__('common.items')" :value="$transfer->items->count()" icon="heroicon-o-archive-box" />
    <x-ui.smart-button :label="__('transfers.ledger_entries')" :value="$movements->count()" icon="heroicon-o-arrows-right-left" />
</div>

<div class="panel mb-1"><div class="panel-body"><h3 style="margin-top:0;">{{ __('transfers.transfer_items') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th></tr></thead><tbody>@forelse($transfer->items as $i)<tr><td>{{ $i->product?->name }}</td><td style="text-align:right;">{{ number_format((float)$i->quantity,2) }}</td></tr>@empty<tr><td colspan="2" class="muted">{{ __('common.no_items') }}</td></tr>@endforelse</tbody></table></div></div></div>
<div class="panel"><div class="panel-body"><h3 style="margin-top:0;">{{ __('transfers.ledger_movements') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('common.type') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th><th>{{ __('common.date') }}</th></tr></thead><tbody>@forelse($movements as $m)<tr><td><x-ui.status-badge :status="$m->movement_type" /></td><td style="text-align:right;">{{ number_format((float)$m->quantity,2) }}</td><td>{{ $m->created_at }}</td></tr>@empty<tr><td colspan="3" class="muted">{{ __('transfers.no_linked_movements') }}</td></tr>@endforelse</tbody></table></div></div></div>
@endsection





