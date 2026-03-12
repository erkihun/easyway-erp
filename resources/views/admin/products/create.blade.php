@extends('layouts.admin')
@section('title', __('products.create'))
@section('page-title', __('products.create'))
@section('content')
<x-ui.page-header :title="__('products.create')" :subtitle="__('products.subtitle')" icon="heroicon-o-plus-circle" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" required />
                <x-ui.input name="sku" :label="__('products.sku')" :help="__('common.optional')" />
                <x-ui.input name="barcode" :label="__('products.barcode')" :help="__('common.optional')" />
                <x-ui.input type="number" step="0.0001" name="low_stock_threshold" :label="__('products.low_stock_threshold')" value="0" />
            </div>
            <div class="row mt-1">
                <x-ui.select name="product_category_id" :label="__('products.category')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </x-ui.select>
                <x-ui.select name="product_brand_id" :label="__('products.brand')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($brands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </x-ui.select>
                <x-ui.select name="unit_of_measure_id" :label="__('products.unit')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($units as $u)<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->symbol }})</option>@endforeach
                </x-ui.select>
                <x-ui.input type="file" name="image" :label="__('products.image')" accept="image/*" :help="__('products.image_help')" />
            </div>
            <x-ui.textarea class="mt-1" name="description" :label="__('common.description')" />

            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.products.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button variant="secondary" type="submit" name="stay" value="1">{{ __('common.save') }} &amp; {{ __('common.edit') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }} {{ __('products.title') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection





