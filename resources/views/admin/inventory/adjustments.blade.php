@extends('layouts.admin')
@section('title', __('inventory.adjustments'))
@section('page-title', __('inventory.adjustments'))
@section('page-subtitle', __('inventory.adjustments_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.adjustments')" :subtitle="__('inventory.adjustments_subtitle')" icon="heroicon-o-adjustments-horizontal">
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
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab">{{ __('inventory.warehouses_view') }}</a>
    <a href="{{ route('admin.inventory.adjustments') }}" class="subnav-tab is-active">{{ __('inventory.adjustments') }}</a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="subnav-tab">{{ __('inventory.low_stock') }}</a>
    <a href="{{ route('admin.inventory.valuation') }}" class="subnav-tab">{{ __('inventory.valuation') }}</a>
</x-ui.subnav-tabs>

<x-ui.card class="mb-1">
    <x-slot:header>
        <h3 class="table-shell-title">{{ __('inventory.adjustments') }}</h3>
    </x-slot:header>
    <p class="field-help">{{ __('inventory.adjustment_ledger_notice') }}</p>
    <form method="POST" action="{{ route('admin.inventory.adjust') }}">
        @csrf
        <div class="row">
            <x-ui.select name="warehouse_id" :label="__('dashboard.warehouse')" required>
                <option value="">{{ __('dashboard.warehouse') }}</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select name="product_id" :label="__('dashboard.product')" required>
                <option value="">{{ __('dashboard.product') }}</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.input name="quantity_delta" :label="__('inventory.quantity_delta')" type="number" step="0.0001" required :help="__('inventory.quantity_delta_help')" />
            <x-ui.input name="reason" :label="__('inventory.reason')" required />
        </div>
        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.inventory.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button variant="warning" type="submit" icon="heroicon-o-adjustments-horizontal">{{ __('inventory.adjustments') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :placeholder="__('inventory.reason')" :value="request('q')" />
    <x-ui.select name="warehouse_id" :label="__('dashboard.warehouse')">
        <option value="">{{ __('inventory.all_warehouses') }}</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') === $warehouse->id)>{{ $warehouse->name }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.select name="product_id" :label="__('dashboard.product')">
        <option value="">{{ __('inventory.all_products') }}</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" @selected(request('product_id') === $product->id)>{{ $product->name }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('warehouse_id') || request()->filled('product_id'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.adjustments')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('inventory.adjustment_history')" :count="$adjustments->total()">
    <table>
        <thead>
        <tr>
            <th>{{ __('common.date') }}</th>
            <th>{{ __('dashboard.product') }}</th>
            <th>{{ __('dashboard.warehouse') }}</th>
            <th style="text-align:right;">{{ __('inventory.delta') }}</th>
            <th>{{ __('inventory.reason') }}</th>
            <th>{{ __('inventory.created_by') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($adjustments as $a)
            @php $delta = (float) $a->quantity_delta; @endphp
            <tr>
                <td>{{ $a->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $a->product?->name }}</td>
                <td>{{ $a->warehouse?->name }}</td>
                <td style="text-align:right;color:{{ $delta >= 0 ? '#047857' : '#b91c1c' }};">{{ number_format($delta,2) }}</td>
                <td>{{ $a->reason }}</td>
                <td>{{ $a->creator?->name ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">{{ __('inventory.no_adjustments') }}</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $adjustments->links() }}</div>
</x-ui.table-shell>
@endsection

