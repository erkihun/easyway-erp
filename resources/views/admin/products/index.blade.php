@extends('layouts.admin')
@section('title', __('products.title'))
@section('page-title', __('products.title'))
@section('page-subtitle', __('products.subtitle'))
@section('content')
<x-ui.page-header :title="__('products.title')" :subtitle="__('products.subtitle')" icon="heroicon-o-cube">
    <x-slot:actions>
        <x-ui.page-actions>
            @can('create_products')
                <x-ui.button icon="heroicon-o-plus" :href="route('admin.products.create')">{{ __('products.create_product') }}</x-ui.button>
            @endcan
            @can('view_reports')
                <x-ui.button variant="secondary" icon="heroicon-o-arrow-down-tray" :href="route('admin.products.export')">{{ __('reports.export_excel') }}</x-ui.button>
            @endcan
            @canany(['view_categories','create_categories','update_categories','delete_categories'])
                <x-ui.button variant="ghost" size="sm" :href="route('admin.product-categories.index')">{{ __('navigation.product_categories') }}</x-ui.button>
            @endcanany
            @canany(['view_brands','create_brands','update_brands','delete_brands'])
                <x-ui.button variant="ghost" size="sm" :href="route('admin.product-brands.index')">{{ __('navigation.product_brands') }}</x-ui.button>
            @endcanany
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="kpi-grid mb-1">
    <x-ui.stat-card :label="__('products.title')" :value="number_format($stats['total'])" icon="heroicon-o-cube" />
    <x-ui.stat-card :label="__('products.active_products')" :value="number_format($stats['active'])" icon="heroicon-o-check-circle" tone="success" />
    <x-ui.stat-card :label="__('products.low_stock_products')" :value="number_format($stats['low_stock'])" icon="heroicon-o-exclamation-triangle" tone="warning" />
    <x-ui.stat-card :label="__('products.out_of_stock_products')" :value="number_format($stats['out_of_stock'])" icon="heroicon-o-x-circle" tone="danger" />
</div>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :value="request('q')" :placeholder="__('common.search').' SKU / '.__('products.barcode').' / '.__('common.name')" />
    <x-ui.select name="category_id" :label="__('products.category')">
        <option value="">{{ __('common.all') }} {{ __('products.category') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') === $category->id)>{{ $category->name }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.select name="brand_id" :label="__('products.brand')">
        <option value="">{{ __('common.all') }} {{ __('products.brand') }}</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}" @selected(request('brand_id') === $brand->id)>{{ $brand->name }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.select name="status" :label="__('common.status')">
        <option value="">{{ __('common.all') }} {{ __('common.status') }}</option>
        <option value="active" @selected(request('status') === 'active')>{{ __('common.status_values.active') }}</option>
        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('common.status_values.inactive') }}</option>
    </x-ui.select>
    <x-ui.select name="stock_status" :label="__('products.stock_status')">
        <option value="">{{ __('products.all_stock') }}</option>
        <option value="in_stock" @selected(request('stock_status') === 'in_stock')>{{ __('products.in_stock') }}</option>
        <option value="low_stock" @selected(request('stock_status') === 'low_stock')>{{ __('products.low_stock') }}</option>
        <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>{{ __('products.out_of_stock') }}</option>
    </x-ui.select>
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('category_id') || request()->filled('brand_id') || request()->filled('status') || request()->filled('stock_status'))
        <x-ui.button variant="ghost" size="sm" icon="heroicon-o-arrow-path" :href="route('admin.products.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('products.directory')" :count="$products->total()">
    <x-ui.table compact>
        <thead>
            <tr>
                <th>{{ __('products.image') }}</th>
                <th>{{ __('common.name') }}</th>
                <th>{{ __('products.sku') }}</th>
                <th>{{ __('products.category') }}</th>
                <th>{{ __('products.brand') }}</th>
                <th>{{ __('products.selling_price') }}</th>
                <th>{{ __('products.stock_status') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                @php
                    $totalStock = (float) ($product->total_stock ?? 0);
                    $stockLabel = __('products.in_stock');
                    $stockTone = 'success';
                    if ($totalStock <= 0) {
                        $stockLabel = __('products.out_of_stock');
                        $stockTone = 'danger';
                    } elseif ($totalStock <= (float) $product->low_stock_threshold) {
                        $stockLabel = __('products.low_stock');
                        $stockTone = 'warning';
                    }
                @endphp
                <tr>
                    <td>
                        @if($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid #e2e8f0;" />
                        @else
                            <div style="width:40px;height:40px;border-radius:10px;border:1px dashed #cbd5e1;background:#f8fafc;display:grid;place-items:center;color:#64748b;">-</div>
                        @endif
                    </td>
                    <td>
                        <div class="font-medium">{{ $product->name }}</div>
                        <div class="muted" style="font-size:.75rem;">{{ $product->barcode ?: '-' }}</div>
                    </td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td>{{ $product->brand?->name ?? '-' }}</td>
                    <td>{{ number_format((float) $product->selling_price, 2) }}</td>
                    <td>
                        <x-ui.badge :tone="$stockTone">{{ $stockLabel }}</x-ui.badge>
                        <div class="muted" style="font-size:.72rem;margin-top:.2rem;">{{ number_format($totalStock, 2) }}</div>
                    </td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            @can('view_products')
                                <x-ui.button variant="outline" size="sm" :href="route('admin.products.show', $product)">{{ __('common.view') }}</x-ui.button>
                            @endcan
                            @can('update_products')
                                <x-ui.button variant="secondary" size="sm" :href="route('admin.products.edit', $product)">{{ __('common.edit') }}</x-ui.button>
                            @endcan
                            @can('manage_stock')
                                <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.ledger', ['product_id' => $product->id])">{{ __('products.stock_by_warehouse') }}</x-ui.button>
                            @endcan
                            @can('delete_products')
                                <form method="POST" action="{{ route('admin.products.destroy',$product) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button variant="danger" size="sm" type="submit">{{ __('common.delete') }}</x-ui.button>
                                </form>
                            @endcan
                        </x-ui.table-actions>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">{{ __('common.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
    <div class="mt-1">{{ $products->links() }}</div>
</x-ui.table-shell>
@endsection

