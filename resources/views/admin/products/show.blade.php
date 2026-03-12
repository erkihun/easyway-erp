@extends('layouts.admin')
@section('title', __('products.details'))
@section('page-title', __('products.details'))
@section('content')
<x-ui.page-header :title="$product->name" :subtitle="__('products.sku').': '.$product->sku.' · '.__('products.barcode').': '.$product->barcode" icon="heroicon-o-cube">
    <x-slot:actions>
        <x-ui.page-actions>
            @can('update_products')
                <x-ui.button icon="heroicon-o-pencil-square" :href="route('admin.products.edit',$product)">{{ __('products.edit') }}</x-ui.button>
            @endcan
            <x-ui.button variant="outline" icon="heroicon-o-qr-code" :href="route('admin.products.barcode',$product)">{{ __('products.barcode') }}</x-ui.button>
            @can('manage_stock')
                <x-ui.button variant="secondary" icon="heroicon-o-archive-box" :href="route('admin.inventory.movements')">{{ __('dashboard.recent_stock_movements') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">
    <x-ui.smart-button :label="__('products.low_stock_threshold')" :value="number_format((float)$product->low_stock_threshold,2)" icon="heroicon-o-exclamation-triangle" variant="warning" />
    <x-ui.smart-button :label="__('products.variants')" :value="$product->variants->count()" icon="heroicon-o-squares-plus" />
    <x-ui.smart-button :label="__('products.images')" :value="$product->images->count()" icon="heroicon-o-photo" />
    <x-ui.smart-button :label="__('navigation.warehouses')" :value="__('inventory.title')" icon="heroicon-o-building-storefront" :href="route('admin.inventory.index')" />
</div>

<div class="panel mb-1"><div class="panel-body"><div class="row"><div><div class="muted">{{ __('common.name') }}</div><strong>{{ $product->name }}</strong></div><div><div class="muted">{{ __('products.sku') }}</div><strong>{{ $product->sku }}</strong></div><div><div class="muted">{{ __('products.barcode') }}</div><strong>{{ $product->barcode }}</strong></div><div><div class="muted">{{ __('products.low_stock_threshold') }}</div><strong>{{ number_format((float)$product->low_stock_threshold,2) }}</strong></div></div></div></div>

<div class="panel"><div class="panel-body"><h3 style="margin-top:0;">{{ __('products.variants') }}</h3>
    @if($product->variants->isEmpty())
        <x-ui.empty-state :title="__('products.no_variants')" :description="__('products.no_variants_help')" icon="heroicon-o-squares-plus" />
    @else
        <div class="table-wrap"><table><thead><tr><th>{{ __('common.name') }}</th><th>{{ __('products.sku') }}</th><th style="text-align:right;">{{ __('products.price') }}</th></tr></thead><tbody>@foreach($product->variants as $variant)<tr><td>{{ $variant->name }}</td><td>{{ $variant->sku }}</td><td style="text-align:right;">{{ number_format((float)$variant->price,2) }}</td></tr>@endforeach</tbody></table></div>
    @endif
</div></div>
@endsection

