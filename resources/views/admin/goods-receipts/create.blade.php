@extends('layouts.admin')
@section('title', __('purchases.create_goods_receipt'))
@section('page-title', __('purchases.create_goods_receipt'))
@section('content')
<x-ui.page-header :title="__('purchases.create_goods_receipt')" :subtitle="__('purchases.create_goods_receipt_subtitle')" icon="heroicon-o-inbox-arrow-down" />

<x-ui.card>
    <form method="POST" action="{{ route('admin.goods-receipts.store') }}">
        @csrf
        <div class="row">
            <x-ui.select name="purchase_order_id" :label="__('purchases.purchase_order')" required>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.select name="warehouse_id" :label="__('dashboard.warehouse')" required>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.input name="received_at" :label="__('purchases.received_date')" type="date" :value="now()->toDateString()" required />
        </div>

        <div class="row mt-1">
            <x-ui.select name="items[0][purchase_order_item_id]" :label="__('purchases.purchase_item')" required>
                @foreach($orders as $order)
                    @foreach($order->items as $item)
                        <option value="{{ $item->id }}">{{ $order->order_number }} - {{ $item->product?->name }}</option>
                    @endforeach
                @endforeach
            </x-ui.select>
            <x-ui.input name="items[0][quantity]" :label="__('common.quantity')" type="number" step="0.0001" required />
        </div>

        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.goods-receipts.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="success" icon="heroicon-o-inbox-arrow-down">{{ __('purchases.goods_receipts') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection





