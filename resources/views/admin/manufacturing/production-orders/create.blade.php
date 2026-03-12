@extends('layouts.admin')
@section('title','Create Production Order')
@section('page-title', __('manufacturing.production_orders'))
@section('content')
<x-ui.page-header title="Create Production Order" subtitle="Plan a production run from a BOM and destination warehouse." icon="heroicon-o-wrench-screwdriver" />

<x-ui.card>
    <form method="POST" action="{{ route('admin.manufacturing.production-orders.store') }}">
        @csrf
        <div class="row">
            <x-ui.select name="bom_id" label="Bill of Materials" required>
                @foreach($boms as $bom)
                    <option value="{{ $bom->id }}">{{ $bom->code }} - {{ $bom->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select name="product_id" label="Finished Product" required>
                @foreach($boms as $bom)
                    <option value="{{ $bom->product_id }}">{{ $bom->product?->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select name="warehouse_id" label="Output Warehouse" required>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
            </x-ui.select>
        </div>

        <div class="row mt-1">
            <x-ui.input name="planned_quantity" label="Planned Quantity" type="number" step="0.0001" required />
            <x-ui.input name="planned_date" label="Planned Date" type="date" :value="now()->toDateString()" />
        </div>

        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.manufacturing.production-orders.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button type="submit" icon="heroicon-o-check">{{ __('manufacturing.production_orders') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection





