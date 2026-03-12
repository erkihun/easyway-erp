@extends('layouts.admin')
@section('title', __('purchases.goods_receipt_details'))
@section('page-title', __('purchases.goods_receipt_details'))
@section('content')
<x-ui.page-header :title="__('purchases.goods_receipt_details_title', ['number' => $receipt->receipt_number])" :subtitle="__('purchases.goods_receipt_details_subtitle')" icon="heroicon-o-document-check">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.goods-receipts.index')">{{ __('common.back') }}</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.purchases.show',$receipt->purchaseOrder)" icon="heroicon-o-clipboard-document-list">{{ __('dashboard.purchase') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">
    <x-ui.smart-button :label="__('dashboard.receipt')" :value="$receipt->receipt_number" icon="heroicon-o-document-check" />
    <x-ui.smart-button :label="__('dashboard.purchase')" :value="$receipt->purchaseOrder?->order_number" icon="heroicon-o-clipboard-document-list" />
    <x-ui.smart-button :label="__('dashboard.warehouse')" :value="$receipt->warehouse?->name" icon="heroicon-o-building-storefront" />
    <x-ui.smart-button :label="__('common.date')" :value="(string)$receipt->received_at" icon="heroicon-o-calendar" />
</div>

<x-ui.table-shell :title="__('common.purchase_items')" :count="count($receipt->purchaseOrder?->items ?? [])">
    <table>
        <thead><tr><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('common.ordered') }}</th><th style="text-align:right;">{{ __('common.received') }}</th></tr></thead>
        <tbody>
        @forelse($receipt->purchaseOrder?->items ?? [] as $item)
            <tr>
                <td>{{ $item->product?->name }}</td>
                <td style="text-align:right;">{{ number_format((float)$item->quantity,2) }}</td>
                <td style="text-align:right;">{{ number_format((float)$item->received_quantity,2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="muted">{{ __('common.no_items_found') }}</td></tr>
        @endforelse
        </tbody>
    </table>
</x-ui.table-shell>
@endsection






