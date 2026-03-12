@extends('layouts.admin')
@section('title', __('inventory.title'))
@section('page-title', __('inventory.title'))
@section('page-subtitle', __('inventory.overview_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.title')" :subtitle="__('inventory.overview_subtitle')" icon="heroicon-o-archive-box">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.ledger')">{{ __('inventory.view_ledger') }}</x-ui.button>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.adjustments')">{{ __('inventory.adjustments') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.low-stock')">{{ __('inventory.low_stock') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.valuation')">{{ __('inventory.view_valuation') }}</x-ui.button>
            @can('view_reports')
                <x-ui.button variant="outline" size="sm" :href="route('admin.reports.inventory')">{{ __('reports.inventory') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.inventory.index') }}" class="subnav-tab is-active">{{ __('inventory.title') }}</a>
    <a href="{{ route('admin.inventory.ledger') }}" class="subnav-tab">{{ __('inventory.ledger') }}</a>
    <a href="{{ route('admin.inventory.movements') }}" class="subnav-tab">{{ __('inventory.movements') }}</a>
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab">{{ __('inventory.warehouses_view') }}</a>
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
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr));">
    <x-ui.stat-card :label="__('inventory.total_stock_items')" :value="number_format((float)$kpis['total_stock_items'])" icon="heroicon-o-cube" />
    <x-ui.stat-card :label="__('inventory.total_quantity_on_hand')" :value="number_format((float)$kpis['total_quantity_on_hand'],2)" icon="heroicon-o-calculator" />
    <x-ui.stat-card :label="__('inventory.low_stock_products')" :value="number_format((float)$kpis['low_stock_products'])" icon="heroicon-o-exclamation-triangle" tone="warning" />
    <x-ui.stat-card :label="__('inventory.out_of_stock_products')" :value="number_format((float)$kpis['out_of_stock_products'])" icon="heroicon-o-x-circle" tone="danger" />
    <x-ui.stat-card :label="__('inventory.warehouses_with_stock')" :value="number_format((float)$kpis['warehouses_with_stock'])" icon="heroicon-o-building-storefront" />
    <x-ui.stat-card :label="__('inventory.inventory_value')" :value="number_format((float)$kpis['inventory_value'],2)" icon="heroicon-o-banknotes" tone="success" />
</div>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));">
    <x-ui.table-shell :title="__('inventory.highest_value_products')" :count="$highValueProducts->count()">
        <table>
            <thead><tr><th>{{ __('dashboard.product') }}</th><th>{{ __('products.sku') }}</th><th style="text-align:right;">{{ __('inventory.value') }}</th></tr></thead>
            <tbody>
            @forelse($highValueProducts as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->sku }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->value,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">{{ __('common.no_records_found') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>

    <x-ui.table-shell :title="__('dashboard.warehouse_distribution')" :count="$warehouseDistribution->count()">
        <table>
            <thead><tr><th>{{ __('dashboard.warehouse') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th></tr></thead>
            <tbody>
            @forelse($warehouseDistribution as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->qty,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="muted">{{ __('common.no_records_found') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>

    <x-ui.table-shell :title="__('inventory.recent_adjustments')" :count="$recentAdjustments->count()">
        <table>
            <thead><tr><th>{{ __('common.date') }}</th><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('inventory.delta') }}</th></tr></thead>
            <tbody>
            @forelse($recentAdjustments as $row)
                <tr>
                    <td>{{ $row->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $row->product?->name }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->quantity_delta,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">{{ __('inventory.no_adjustments') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>
</div>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(360px,1fr));">
    <x-ui.table-shell :title="__('inventory.low_stock')" :count="$lowStockItems->count()">
        <table>
            <thead><tr><th>{{ __('dashboard.product') }}</th><th>{{ __('dashboard.warehouse') }}</th><th style="text-align:right;">{{ __('inventory.stock') }}</th><th style="text-align:right;">{{ __('inventory.shortage') }}</th></tr></thead>
            <tbody>
            @forelse($lowStockItems as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->warehouse_name }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->cached_quantity,2) }}</td>
                    <td style="text-align:right;">{{ number_format((float)$row->shortage,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">{{ __('inventory.no_low_stock_alerts') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>

    <x-ui.table-shell :title="__('dashboard.recent_stock_movements')" :count="$recentMovements->count()">
        <table>
            <thead><tr><th>{{ __('common.date') }}</th><th>{{ __('inventory.movement_type') }}</th><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th></tr></thead>
            <tbody>
            @forelse($recentMovements as $m)
                <tr>
                    <td>{{ $m->created_at?->format('Y-m-d H:i') }}</td>
                    <td><x-ui.status-badge :status="$m->movement_type" /></td>
                    <td>{{ $m->product?->name }}</td>
                    <td style="text-align:right;color:{{ (float)$m->quantity >= 0 ? '#047857' : '#b91c1c' }};">{{ number_format((float)$m->quantity,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">{{ __('inventory.no_movement_records') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.table-shell>
</div>

<x-ui.table-shell :title="__('inventory.warehouses_view')" :count="$stockRows->total()">
    @if($stockRows->isEmpty())
        <x-ui.empty-state :title="__('inventory.no_stock_records')" :description="__('inventory.no_stock_records_help')" icon="heroicon-o-archive-box" />
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
            </tr>
            </thead>
            <tbody>
            @foreach($stockRows as $row)
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
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-1">{{ $stockRows->links() }}</div>
    @endif
</x-ui.table-shell>
@endsection

