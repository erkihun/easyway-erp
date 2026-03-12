@extends('layouts.admin')
@section('title', __('inventory.ledger'))
@section('page-title', __('inventory.ledger'))
@section('page-subtitle', __('inventory.ledger_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.ledger')" :subtitle="__('inventory.ledger_subtitle')" icon="heroicon-o-book-open">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.adjustments')">{{ __('inventory.adjustments') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.low-stock')">{{ __('inventory.low_stock') }}</x-ui.button>
            @can('view_reports')
                <x-ui.button variant="secondary" size="sm" :href="route('admin.reports.inventory')">{{ __('reports.export_excel') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.inventory.index') }}" class="subnav-tab">{{ __('inventory.title') }}</a>
    <a href="{{ route('admin.inventory.ledger') }}" class="subnav-tab is-active">{{ __('inventory.ledger') }}</a>
    <a href="{{ route('admin.inventory.movements') }}" class="subnav-tab">{{ __('inventory.movements') }}</a>
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab">{{ __('inventory.warehouses_view') }}</a>
    <a href="{{ route('admin.inventory.adjustments') }}" class="subnav-tab">{{ __('inventory.adjustments') }}</a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="subnav-tab">{{ __('inventory.low_stock') }}</a>
    <a href="{{ route('admin.inventory.valuation') }}" class="subnav-tab">{{ __('inventory.valuation') }}</a>
</x-ui.subnav-tabs>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :placeholder="__('inventory.search_reference')" :value="request('q')" />
    <x-ui.select name="warehouse_id" :label="__('dashboard.warehouse')">
        <option value="">{{ __('inventory.all_warehouses') }}</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') === $warehouse->id)>{{ $warehouse->name }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.select name="product_id" :label="__('dashboard.product')">
        <option value="">{{ __('inventory.all_products') }}</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" @selected(request('product_id') === $product->id)>{{ $product->name }} ({{ $product->sku }})</option>
        @endforeach
    </x-ui.select>
    <x-ui.select name="movement_type" :label="__('inventory.movement_type')">
        <option value="">{{ __('inventory.all_movement_types') }}</option>
        @foreach(\App\Enums\StockMovementType::cases() as $type)
            <option value="{{ $type->value }}" @selected(request('movement_type') === $type->value)>{{ __('common.status_values.'.$type->value) }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.input type="date" name="date_from" :label="__('inventory.date_from')" :value="request('date_from')" />
    <x-ui.input type="date" name="date_to" :label="__('inventory.date_to')" :value="request('date_to')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('warehouse_id') || request()->filled('product_id') || request()->filled('movement_type') || request()->filled('date_from') || request()->filled('date_to'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.ledger')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('inventory.ledger')" :count="$movements->total()">
    @if($movements->isEmpty())
        <x-ui.empty-state :title="__('inventory.no_movement_records')" :description="__('inventory.no_movement_records_help')" icon="heroicon-o-book-open" />
    @else
        <table>
            <thead>
            <tr>
                <th>{{ __('common.date') }}</th>
                <th>{{ __('dashboard.product') }}</th>
                <th>{{ __('products.sku') }}</th>
                <th>{{ __('dashboard.warehouse') }}</th>
                <th>{{ __('inventory.movement_type') }}</th>
                <th style="text-align:right;">{{ __('dashboard.qty') }}</th>
                <th>{{ __('inventory.reference_type') }}</th>
                <th>{{ __('inventory.reference_id') }}</th>
                <th>{{ __('inventory.reason') }}</th>
                <th>{{ __('inventory.created_by') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($movements as $movement)
                @php $qty = (float) $movement->quantity; @endphp
                <tr>
                    <td>{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $movement->product?->name }}</td>
                    <td>{{ $movement->product?->sku }}</td>
                    <td>{{ $movement->warehouse?->name }}</td>
                    <td><x-ui.status-badge :status="$movement->movement_type" /></td>
                    <td style="text-align:right;color:{{ $qty >= 0 ? '#047857' : '#b91c1c' }};">{{ number_format($qty,2) }}</td>
                    <td>{{ $movement->reference_type ?? '-' }}</td>
                    <td>{{ $movement->reference_id ?? '-' }}</td>
                    <td>{{ $movement->reason ?? '-' }}</td>
                    <td>{{ $movement->creator?->name ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-1">{{ $movements->links() }}</div>
    @endif
</x-ui.table-shell>
@endsection

