@extends('layouts.admin')
@section('title', __('sales.create'))
@section('page-title', __('sales.create'))
@section('content')
<x-ui.page-header :title="__('sales.create')" :subtitle="__('sales.subtitle')" icon="heroicon-o-banknotes" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.sales.store') }}">
            @csrf
            <div class="row">
                <x-ui.select name="customer_id" :label="__('navigation.customers')">
                    <option value="">{{ __('sales.walk_in') }}</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input type="date" name="order_date" :label="__('common.date')" :value="now()->toDateString()" required />
            </div>
            <div class="row mt-1">
                <x-ui.select name="items[0][product_id]" :label="__('dashboard.product')" required errorKey="items.0.product_id">
                    <option value="">{{ __('dashboard.product') }}</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.select name="items[0][warehouse_id]" :label="__('dashboard.warehouse')" required errorKey="items.0.warehouse_id">
                    <option value="">{{ __('dashboard.warehouse') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input name="items[0][quantity]" :label="__('dashboard.qty')" type="number" step="0.0001" required errorKey="items.0.quantity" />
                <x-ui.input name="items[0][unit_price]" :label="__('common.price')" type="number" step="0.0001" required errorKey="items.0.unit_price" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.sales.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('sales.create') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

