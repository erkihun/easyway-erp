@extends('layouts.admin')
@section('title', __('inventory.low_stock'))
@section('page-title', __('inventory.low_stock'))
@section('page-subtitle', __('inventory.low_stock_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.low_stock')" :subtitle="__('inventory.low_stock_subtitle')" icon="heroicon-o-exclamation-triangle">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.adjustments')">{{ __('inventory.adjustments') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.ledger')">{{ __('inventory.ledger') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.inventory.index') }}" class="subnav-tab">{{ __('inventory.title') }}</a>
    <a href="{{ route('admin.inventory.ledger') }}" class="subnav-tab">{{ __('inventory.ledger') }}</a>
    <a href="{{ route('admin.inventory.movements') }}" class="subnav-tab">{{ __('inventory.movements') }}</a>
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab">{{ __('inventory.warehouses_view') }}</a>
    <a href="{{ route('admin.inventory.adjustments') }}" class="subnav-tab">{{ __('inventory.adjustments') }}</a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="subnav-tab is-active">{{ __('inventory.low_stock') }}</a>
    <a href="{{ route('admin.inventory.valuation') }}" class="subnav-tab">{{ __('inventory.valuation') }}</a>
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
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.low-stock')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('inventory.low_stock')" :count="$rows->total()">
    @if($rows->isEmpty())
        <x-ui.empty-state :title="__('inventory.no_low_stock_alerts')" :description="__('inventory.no_low_stock_alerts_help')" icon="heroicon-o-shield-check" />
    @else
        <table>
            <thead>
            <tr>
                <th>{{ __('dashboard.product') }}</th>
                <th>{{ __('products.sku') }}</th>
                <th>{{ __('dashboard.warehouse') }}</th>
                <th style="text-align:right;">{{ __('inventory.stock') }}</th>
                <th style="text-align:right;">{{ __('products.low_stock_threshold') }}</th>
                <th style="text-align:right;">{{ __('inventory.shortage') }}</th>
                <th>{{ __('inventory.stock_status') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
                @php
                    $stock = (float) $row->stock;
                    $status = $stock <= 0 ? 'out_of_stock' : 'low_stock';
                @endphp
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->sku }}</td>
                    <td>{{ $row->warehouse_name }}</td>
                    <td style="text-align:right;">{{ number_format($stock,2) }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->low_stock_threshold,2) }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->shortage,2) }}</td>
                    <td><x-ui.status-badge :status="$status" /></td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            <x-ui.button size="sm" variant="outline" :href="route('admin.products.show', $row->id)">{{ __('common.view') }}</x-ui.button>
                            @can('manage_purchases')
                                <x-ui.button size="sm" variant="secondary" :href="route('admin.purchases.create')">{{ __('purchases.create') }}</x-ui.button>
                            @endcan
                        </x-ui.table-actions>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-1">{{ $rows->links() }}</div>
    @endif
</x-ui.table-shell>
@endsection

