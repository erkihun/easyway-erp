@extends('layouts.admin')
@section('title', __('inventory.valuation'))
@section('page-title', __('inventory.valuation'))
@section('page-subtitle', __('inventory.valuation_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.valuation')" :subtitle="__('inventory.valuation_subtitle')" icon="heroicon-o-banknotes">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.ledger')">{{ __('inventory.ledger') }}</x-ui.button>
            @can('view_reports')
                <x-ui.button variant="outline" size="sm" :href="route('admin.reports.valuation')">{{ __('reports.export_excel') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.inventory.index') }}" class="subnav-tab">{{ __('inventory.title') }}</a>
    <a href="{{ route('admin.inventory.ledger') }}" class="subnav-tab">{{ __('inventory.ledger') }}</a>
    <a href="{{ route('admin.inventory.movements') }}" class="subnav-tab">{{ __('inventory.movements') }}</a>
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab">{{ __('inventory.warehouses_view') }}</a>
    <a href="{{ route('admin.inventory.adjustments') }}" class="subnav-tab">{{ __('inventory.adjustments') }}</a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="subnav-tab">{{ __('inventory.low_stock') }}</a>
    <a href="{{ route('admin.inventory.valuation') }}" class="subnav-tab is-active">{{ __('inventory.valuation') }}</a>
</x-ui.subnav-tabs>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :placeholder="__('inventory.search_sku_product')" :value="request('q')" />
    <x-ui.select name="warehouse_id" :label="__('dashboard.warehouse')">
        <option value="">{{ __('inventory.all_warehouses') }}</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') === $warehouse->id)>{{ $warehouse->name }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('warehouse_id'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.valuation')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
    <x-ui.stat-card :label="__('inventory.total_inventory_value')" :value="number_format((float)$kpis['total_value'],2)" icon="heroicon-o-banknotes" tone="success" />
    <x-ui.stat-card :label="__('inventory.average_unit_cost')" :value="number_format((float)$kpis['avg_unit_cost'],4)" icon="heroicon-o-calculator" />
    <x-ui.stat-card :label="__('inventory.highest_value_warehouse')" :value="$kpis['top_warehouse_name']" :helper="number_format((float)$kpis['top_warehouse_value'],2)" icon="heroicon-o-building-storefront" />
    <x-ui.stat-card :label="__('inventory.highest_value_product')" :value="$kpis['top_product_name']" :helper="number_format((float)$kpis['top_product_value'],2)" icon="heroicon-o-cube" />
</div>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));">
    <x-ui.table-shell :title="__('inventory.value_by_warehouse')" :count="$warehouseValues->count()">
        <table>
            <thead><tr><th>{{ __('dashboard.warehouse') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th><th style="text-align:right;">{{ __('inventory.value') }}</th></tr></thead>
            <tbody>
            @forelse($warehouseValues as $row)
                <tr>
                    <td>{{ $row->warehouse_name }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->total_qty,2) }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->total_value,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">{{ __('inventory.no_valuation') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>

    <x-ui.table-shell :title="__('inventory.value_by_product')" :count="$productValues->count()">
        <table>
            <thead><tr><th>{{ __('dashboard.product') }}</th><th>{{ __('products.sku') }}</th><th style="text-align:right;">{{ __('inventory.value') }}</th></tr></thead>
            <tbody>
            @forelse($productValues as $row)
                <tr>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->sku }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->total_value,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">{{ __('inventory.no_valuation') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>
</div>

<x-ui.table-shell :title="__('inventory.valuation')" :count="$rows->total()">
    @if($rows->isEmpty())
        <x-ui.empty-state :title="__('inventory.no_valuation')" :description="__('inventory.valuation_subtitle')" icon="heroicon-o-banknotes" />
    @else
        <table>
            <thead>
            <tr>
                <th>{{ __('dashboard.product') }}</th>
                <th>{{ __('products.sku') }}</th>
                <th>{{ __('dashboard.warehouse') }}</th>
                <th style="text-align:right;">{{ __('dashboard.qty') }}</th>
                <th style="text-align:right;">{{ __('inventory.unit_cost') }}</th>
                <th style="text-align:right;">{{ __('inventory.value') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->sku }}</td>
                    <td>{{ $row->warehouse_name }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->quantity,2) }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->unit_cost,4) }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->value,2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-1">{{ $rows->links() }}</div>
    @endif
</x-ui.table-shell>
@endsection

