@extends('layouts.admin')
@section('title', __('purchases.create'))
@section('page-title', __('purchases.create'))
@section('content')
<x-ui.page-header :title="__('purchases.create')" :subtitle="__('purchases.subtitle')" icon="heroicon-o-clipboard-document-list" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.purchases.store') }}">
            @csrf
            <div class="row">
                <x-ui.select name="supplier_id" :label="__('navigation.suppliers')">
                    <option value="">{{ __('purchases.supplier_optional') }}</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
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
                <x-ui.input name="items[0][quantity]" :label="__('dashboard.qty')" type="number" step="0.0001" required errorKey="items.0.quantity" />
                <x-ui.input name="items[0][unit_cost]" :label="__('purchases.unit_cost')" type="number" step="0.0001" required errorKey="items.0.unit_cost" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.purchases.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('purchases.create') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

