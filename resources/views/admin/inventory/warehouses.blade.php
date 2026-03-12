@extends('layouts.admin')
@section('title', __('inventory.warehouses_view'))
@section('page-title', __('inventory.warehouses_view'))
@section('page-subtitle', __('inventory.warehouses_view_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.warehouses_view')" :subtitle="__('inventory.warehouses_view_subtitle')" icon="heroicon-o-building-storefront">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.ledger')">{{ __('inventory.ledger') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.low-stock')">{{ __('inventory.low_stock') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.inventory.index') }}" class="subnav-tab">{{ __('inventory.title') }}</a>
    <a href="{{ route('admin.inventory.ledger') }}" class="subnav-tab">{{ __('inventory.ledger') }}</a>
    <a href="{{ route('admin.inventory.movements') }}" class="subnav-tab">{{ __('inventory.movements') }}</a>
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab is-active">{{ __('inventory.warehouses_view') }}</a>
    <a href="{{ route('admin.inventory.adjustments') }}" class="subnav-tab">{{ __('inventory.adjustments') }}</a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="subnav-tab">{{ __('inventory.low_stock') }}</a>
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
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.warehouses')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
    @foreach($warehouseSummary as $summary)
        <x-ui.card>
            <div class="grid" style="gap:.35rem;">
                <strong>{{ $summary->name }}</strong>
                <div class="muted" style="font-size:.78rem;">{{ __('inventory.total_stock_items') }}: {{ number_format((float)$summary->item_count) }}</div>
                <div class="muted" style="font-size:.78rem;">{{ __('inventory.on_hand') }}: {{ number_format((float)$summary->on_hand,2) }}</div>
                <div class="muted" style="font-size:.78rem;">{{ __('inventory.reserved') }}: {{ number_format((float)$summary->reserved,2) }}</div>
                <div class="muted" style="font-size:.78rem;">{{ __('inventory.available') }}: {{ number_format((float)$summary->available,2) }}</div>
            </div>
        </x-ui.card>
    @endforeach
</div>

<x-ui.table-shell :title="__('inventory.warehouses_view')" :count="$rows->total()">
    @if($rows->isEmpty())
        <x-ui.empty-state :title="__('inventory.no_stock_records')" :description="__('inventory.no_stock_records_help')" icon="heroicon-o-building-storefront" />
    @else
        <table>
            <thead>
            <tr>
                <th>{{ __('dashboard.warehouse') }}</th>
                <th>{{ __('dashboard.product') }}</th>
                <th>{{ __('products.sku') }}</th>
                <th style="text-align:right;">{{ __('inventory.on_hand') }}</th>
                <th style="text-align:right;">{{ __('inventory.reserved') }}</th>
                <th style="text-align:right;">{{ __('inventory.available') }}</th>
                <th style="text-align:right;">{{ __('products.low_stock_threshold') }}</th>
                <th>{{ __('inventory.stock_status') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
                @php
                    $onHand = (float) $row->cached_quantity;
                    $reserved = (float) $row->reserved_quantity;
                    $available = $onHand - $reserved;
                    $threshold = (float) $row->low_stock_threshold;
                    $status = $onHand <= 0 ? 'out_of_stock' : ($onHand <= $threshold ? 'low_stock' : 'in_stock');
                @endphp
                <tr>
                    <td>{{ $row->warehouse_name }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->sku }}</td>
                    <td style="text-align:right;">{{ number_format($onHand,2) }}</td>
                    <td style="text-align:right;">{{ number_format($reserved,2) }}</td>
                    <td style="text-align:right;">{{ number_format($available,2) }}</td>
                    <td style="text-align:right;">{{ number_format($threshold,2) }}</td>
                    <td><x-ui.status-badge :status="$status" /></td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            <x-ui.button size="sm" variant="outline" :href="route('admin.products.show', $row->product_id)">{{ __('common.view') }}</x-ui.button>
                            <x-ui.button size="sm" variant="ghost" :href="route('admin.inventory.ledger', ['warehouse_id' => $row->warehouse_id, 'product_id' => $row->product_id])">{{ __('inventory.ledger') }}</x-ui.button>
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
