@extends('layouts.admin')
@section('title','POS Checkout')
@section('page-title', __('pos.checkout'))
@section('content')
<x-ui.page-header title="POS Checkout" subtitle="Create quick retail orders and post ledger movements." icon="heroicon-o-receipt-percent" />

<x-ui.card>
    <form method="POST" action="{{ route('admin.pos.checkout') }}">
        @csrf
        <div class="row">
            <x-ui.select name="pos_session_id" label="Session" required>
                @foreach($sessions as $s)<option value="{{ $s->id }}">{{ $s->id }} - {{ $s->warehouse?->name }}</option>@endforeach
            </x-ui.select>
            <x-ui.select name="customer_id" label="Customer">
                <option value="">Walk-in</option>
                @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </x-ui.select>
        </div>
        <div class="row mt-1">
            <x-ui.select name="items[0][product_id]" label="Product" required>
                @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode }})</option>@endforeach
            </x-ui.select>
            <x-ui.select name="items[0][warehouse_id]" label="Warehouse" required>
                @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
            </x-ui.select>
            <x-ui.input name="items[0][quantity]" label="Quantity" type="number" step="0.0001" required />
            <x-ui.input name="items[0][unit_price]" label="Unit Price" type="number" step="0.0001" required />
        </div>
        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.pos.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button variant="success" type="submit" icon="heroicon-o-check">{{ __('pos.checkout') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection




