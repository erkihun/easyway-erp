@extends('layouts.admin')
@section('title', __('inventory.movements'))
@section('page-title', __('inventory.movements'))
@section('page-subtitle', __('inventory.movements_subtitle'))
@section('content')
<x-ui.page-header :title="__('inventory.movements')" :subtitle="__('inventory.movements_subtitle')" icon="heroicon-o-arrow-path">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.ledger')">{{ __('inventory.ledger') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.inventory.adjustments')">{{ __('inventory.adjustments') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.inventory.index') }}" class="subnav-tab">{{ __('inventory.title') }}</a>
    <a href="{{ route('admin.inventory.ledger') }}" class="subnav-tab">{{ __('inventory.ledger') }}</a>
    <a href="{{ route('admin.inventory.movements') }}" class="subnav-tab is-active">{{ __('inventory.movements') }}</a>
    <a href="{{ route('admin.inventory.warehouses') }}" class="subnav-tab">{{ __('inventory.warehouses_view') }}</a>
    <a href="{{ route('admin.inventory.adjustments') }}" class="subnav-tab">{{ __('inventory.adjustments') }}</a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="subnav-tab">{{ __('inventory.low_stock') }}</a>
    <a href="{{ route('admin.inventory.valuation') }}" class="subnav-tab">{{ __('inventory.valuation') }}</a>
</x-ui.subnav-tabs>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :placeholder="__('inventory.search_reference')" :value="request('q')" />
    <x-ui.select name="movement_type" :label="__('inventory.movement_type')">
        <option value="">{{ __('inventory.all_movement_types') }}</option>
        @foreach(\App\Enums\StockMovementType::cases() as $type)
            <option value="{{ $type->value }}" @selected(request('movement_type') === $type->value)>{{ __('common.status_values.'.$type->value) }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.input type="date" name="date_from" :label="__('inventory.date_from')" :value="request('date_from')" />
    <x-ui.input type="date" name="date_to" :label="__('inventory.date_to')" :value="request('date_to')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('movement_type') || request()->filled('date_from') || request()->filled('date_to'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.movements')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
    @foreach($movementTotals as $row)
        <x-ui.stat-card :label="__('common.status_values.'.strtolower((string)$row->movement_type))" :value="number_format((float)$row->qty,2)" icon="heroicon-o-arrows-right-left" />
    @endforeach
</div>

<x-ui.table-shell :title="__('dashboard.recent_stock_movements')" :count="$movements->total()">
    @if($movements->isEmpty())
        <x-ui.empty-state :title="__('inventory.no_movement_records')" :description="__('inventory.no_movement_records_help')" icon="heroicon-o-arrow-path" />
    @else
        <table>
            <thead>
            <tr>
                <th>{{ __('common.date') }}</th>
                <th>{{ __('inventory.movement_type') }}</th>
                <th>{{ __('dashboard.product') }}</th>
                <th>{{ __('dashboard.warehouse') }}</th>
                <th style="text-align:right;">{{ __('dashboard.qty') }}</th>
                <th>{{ __('inventory.reference') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($movements as $m)
                @php $qty = (float) $m->quantity; @endphp
                <tr>
                    <td>{{ $m->created_at?->format('Y-m-d H:i') }}</td>
                    <td><x-ui.status-badge :status="$m->movement_type" /></td>
                    <td>{{ $m->product?->name }}</td>
                    <td>{{ $m->warehouse?->name }}</td>
                    <td style="text-align:right;color:{{ $qty >= 0 ? '#047857' : '#b91c1c' }};">{{ number_format($qty,2) }}</td>
                    <td>{{ trim(($m->reference_type ?? '-').' '.($m->reference_id ?? '')) }}</td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            @if($m->product)
                                <x-ui.button size="sm" variant="outline" :href="route('admin.products.show', $m->product)">{{ __('common.view') }}</x-ui.button>
                            @endif
                            <x-ui.button size="sm" variant="ghost" :href="route('admin.inventory.ledger', ['q' => $m->product?->sku, 'movement_type' => $m->movement_type])">{{ __('inventory.ledger') }}</x-ui.button>
                        </x-ui.table-actions>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-1">{{ $movements->links() }}</div>
    @endif
</x-ui.table-shell>
@endsection
