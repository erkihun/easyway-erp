@extends('layouts.admin')
@section('title', __('transfers.create'))
@section('page-title', __('transfers.create'))
@section('content')
<x-ui.page-header :title="__('transfers.create')" :subtitle="__('transfers.subtitle')" icon="heroicon-o-arrow-path" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.transfers.store') }}">
            @csrf
            <div class="row">
                <x-ui.select name="source_warehouse_id" :label="__('transfers.source_warehouse')" required>
                    <option value="">{{ __('transfers.source_warehouse') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.select name="destination_warehouse_id" :label="__('transfers.destination_warehouse')" required>
                    <option value="">{{ __('transfers.destination_warehouse') }}</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input type="date" name="transfer_date" :label="__('common.date')" :value="now()->toDateString()" required />
            </div>
            <div class="row mt-1">
                <x-ui.select name="items[0][product_id]" :label="__('dashboard.product')" required>
                    <option value="">{{ __('dashboard.product') }}</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input name="items[0][quantity]" :label="__('dashboard.qty')" type="number" step="0.0001" required errorKey="items.0.quantity" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.transfers.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('transfers.create') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

