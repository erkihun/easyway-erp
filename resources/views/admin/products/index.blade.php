@extends('layouts.admin')
@section('title', __('products.title'))
@section('page-title', __('products.title'))
@section('page-subtitle', __('products.subtitle'))
@section('content')
<x-ui.page-header :title="__('products.title')" :subtitle="__('products.subtitle')" icon="heroicon-o-cube">
    <x-slot:actions>
        <x-ui.page-actions>
            @can('create_products')
                <x-ui.button icon="heroicon-o-plus" :href="route('admin.products.create')">{{ __('products.create') }}</x-ui.button>
            @endcan
            @can('view_reports')
                <x-ui.button variant="secondary" icon="heroicon-o-arrow-down-tray" :href="route('admin.products.export')">{{ __('reports.export_excel') }}</x-ui.button>
            @endcan
            @can('manage_stock')
                <x-ui.button variant="outline" icon="heroicon-o-exclamation-triangle" :href="route('admin.inventory.low-stock')">{{ __('inventory.low_stock') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :value="request('q')" :placeholder="__('common.search').' SKU, '.__('products.barcode').', '.__('common.name')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q'))
        <x-ui.button variant="ghost" size="sm" icon="heroicon-o-arrow-path" :href="route('admin.products.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
    @can('create_products')
        <label class="btn btn-outline btn-sm" style="cursor:pointer;">
            <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
            <span>{{ __('reports.export_csv') }}</span>
            <input type="file" name="file" form="import-products-form" style="display:none;" onchange="document.getElementById('import-products-form').submit();">
        </label>
    @endcan
</x-ui.filter-bar>

<form id="import-products-form" method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="file" name="file" />
</form>

<div class="panel">
    <div class="panel-body">
        @if($products->isEmpty())
            <x-ui.empty-state :title="__('common.no_records_found')" :description="__('products.subtitle')" icon="heroicon-o-cube" />
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>{{ __('products.sku') }}</th><th>{{ __('products.barcode') }}</th><th>{{ __('common.name') }}</th><th>{{ __('products.category') }}</th><th>{{ __('products.brand') }}</th><th>{{ __('products.unit') }}</th><th style="text-align:right;">{{ __('products.low_stock_threshold') }}</th><th class="actions-col">{{ __('common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->barcode }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>{{ $product->brand?->name ?? '—' }}</td>
                            <td>{{ $product->unit?->symbol ?? '—' }}</td>
                            <td style="text-align:right;">{{ number_format((float)$product->low_stock_threshold,2) }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="outline" size="sm" :href="route('admin.products.show',$product)">{{ __('common.view') }}</x-ui.button>
                                    @can('update_products')
                                        <x-ui.button variant="ghost" size="sm" :href="route('admin.products.edit',$product)">{{ __('common.edit') }}</x-ui.button>
                                    @endcan
                                    <x-ui.dropdown-actions :label="__('common.more')">
                                        <x-ui.button variant="ghost" size="sm" icon="heroicon-o-qr-code" :href="route('admin.products.barcode',$product)">{{ __('products.barcode') }}</x-ui.button>
                                        @can('manage_stock')
                                            <x-ui.button variant="ghost" size="sm" icon="heroicon-o-archive-box" :href="route('admin.inventory.movements')">{{ __('inventory.title') }}</x-ui.button>
                                        @endcan
                                        @can('delete_products')
                                            <form method="POST" action="{{ route('admin.products.destroy',$product) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="danger" size="sm" type="submit" icon="heroicon-o-trash">{{ __('common.delete') }}</x-ui.button>
                                            </form>
                                        @endcan
                                    </x-ui.dropdown-actions>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection





