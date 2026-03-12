@extends('layouts.admin')
@section('title', __('purchases.details'))
@section('page-title', __('purchases.details'))
@section('content')
<x-ui.page-header :title="__('purchases.details_title', ['number' => $order->order_number])" :subtitle="__('purchases.details_subtitle')" icon="heroicon-o-clipboard-document-list">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.purchases.index')">{{ __('common.back') }}</x-ui.button>
            @can('manage_purchases')
                <x-ui.button variant="success" icon="heroicon-o-inbox-arrow-down" :href="route('admin.goods-receipts.create')">{{ __('purchases.create_goods_receipt') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
    <x-ui.smart-button :label="__('common.status')" :value="strtoupper((string)($order->status->value ?? $order->status))" icon="heroicon-o-flag" />
    <x-ui.smart-button :label="__('common.line_items')" :value="$order->items->count()" icon="heroicon-o-list-bullet" />
    <x-ui.smart-button :label="__('purchases.goods_receipts')" :value="$receipts->count()" icon="heroicon-o-inbox-arrow-down" />
    <x-ui.smart-button :label="__('common.total')" :value="number_format((float)$order->total_amount,2)" icon="heroicon-o-banknotes" />
</div>

<div class="panel mb-1"><div class="panel-body"><h3 style="margin-top:0;">{{ __('common.line_items') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th><th style="text-align:right;">{{ __('common.received') }}</th><th style="text-align:right;">{{ __('purchases.unit_cost') }}</th></tr></thead><tbody>@forelse($order->items as $item)<tr><td>{{ $item->product?->name }}</td><td style="text-align:right;">{{ number_format((float)$item->quantity,2) }}</td><td style="text-align:right;">{{ number_format((float)$item->received_quantity,2) }}</td><td style="text-align:right;">{{ number_format((float)$item->unit_cost,2) }}</td></tr>@empty<tr><td colspan="4" class="muted">{{ __('common.no_items') }}</td></tr>@endforelse</tbody></table></div></div></div>

<div class="panel mb-1"><div class="panel-body"><h3 style="margin-top:0;">{{ __('purchases.receive_stock') }}</h3><form method="POST" action="{{ route('admin.goods-receipts.store') }}">@csrf<input type="hidden" name="purchase_order_id" value="{{ $order->id }}"><div class="row"><x-ui.select name="warehouse_id" :label="__('dashboard.warehouse')" required>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</x-ui.select><x-ui.input type="date" name="received_at" :label="__('common.date')" :value="now()->toDateString()" required /></div><div class="row mt-1"><x-ui.select name="items[0][purchase_order_item_id]" :label="__('purchases.purchase_item')" required>@foreach($order->items as $item)<option value="{{ $item->id }}">{{ $item->product?->name }} ({{ $item->quantity - $item->received_quantity }} {{ __('purchases.remaining') }})</option>@endforeach</x-ui.select><x-ui.input name="items[0][quantity]" :label="__('dashboard.qty')" type="number" step="0.0001" required errorKey="items.0.quantity" /></div><div class="form-actions-sticky mt-1"><x-ui.button variant="ghost" :href="route('admin.goods-receipts.index')">{{ __('common.cancel') }}</x-ui.button><x-ui.button variant="success" type="submit" icon="heroicon-o-inbox-arrow-down">{{ __('purchases.goods_receipts') }}</x-ui.button></div></form></div></div>

<div class="panel"><div class="panel-body"><h3 style="margin-top:0;">{{ __('purchases.receipt_history') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('dashboard.receipt') }}</th><th>{{ __('common.date') }}</th><th>{{ __('dashboard.warehouse') }}</th></tr></thead><tbody>@forelse($receipts as $r)<tr><td><a class="link" href="{{ route('admin.goods-receipts.show',$r) }}">{{ $r->receipt_number }}</a></td><td>{{ $r->received_at }}</td><td>{{ $r->warehouse?->name }}</td></tr>@empty<tr><td colspan="3" class="muted">{{ __('purchases.no_receipts') }}</td></tr>@endforelse</tbody></table></div></div></div>
@endsection






