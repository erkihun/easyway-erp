@extends('layouts.admin')
@section('title', __('products.edit'))
@section('page-title', __('products.edit'))
@section('content')
<x-ui.page-header :title="__('products.edit')" :subtitle="__('products.subtitle')" icon="heroicon-o-pencil-square">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.products.show',$product)">{{ __('common.view') }}</x-ui.button>
            @can('delete_products')
                <form method="POST" action="{{ route('admin.products.destroy',$product) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" type="submit" icon="heroicon-o-trash">{{ __('common.delete') }}</x-ui.button>
                </form>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.products.update',$product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" :value="$product->name" required />
                <x-ui.input name="sku" :label="__('products.sku')" :value="$product->sku" :help="__('common.optional')" />
                <x-ui.input name="barcode" :label="__('products.barcode')" :value="$product->barcode" :help="__('common.optional')" />
                <x-ui.input type="number" step="0.0001" name="low_stock_threshold" :label="__('products.low_stock_threshold')" :value="$product->low_stock_threshold" />
            </div>
            <div class="row mt-1">
                <x-ui.select name="product_category_id" :label="__('products.category')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}" @selected($product->product_category_id===$c->id)>{{ $c->name }}</option>@endforeach
                </x-ui.select>
                <x-ui.select name="product_brand_id" :label="__('products.brand')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($brands as $b)<option value="{{ $b->id }}" @selected($product->product_brand_id===$b->id)>{{ $b->name }}</option>@endforeach
                </x-ui.select>
                <x-ui.select name="unit_of_measure_id" :label="__('products.unit')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($units as $u)<option value="{{ $u->id }}" @selected($product->unit_of_measure_id===$u->id)>{{ $u->name }} ({{ $u->symbol }})</option>@endforeach
                </x-ui.select>
                <x-ui.input type="file" name="image" :label="__('products.image')" accept="image/*" :help="__('products.image_help')" />
            </div>
            <x-ui.textarea class="mt-1" name="description" :label="__('common.description')">{{ old('description',$product->description) }}</x-ui.textarea>

            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.products.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button variant="secondary" type="submit" name="stay" value="1">{{ __('common.save') }} &amp; {{ __('common.edit') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection





