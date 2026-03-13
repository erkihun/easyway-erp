@extends('layouts.admin')
@section('title', __('products.product_details'))
@section('page-title', __('products.product_details'))
@section('page-subtitle', __('products.product_profile'))
@section('content')
@php
    $imageUrl = $product->primary_image_url;
@endphp

<x-ui.page-header :title="__('products.product_details')" :subtitle="__('products.product_profile')" icon="heroicon-o-cube">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="ghost" :href="route('admin.products.index')">{{ __('products.back_to_products') }}</x-ui.button>
            @can('update_products')
                <x-ui.button icon="heroicon-o-pencil-square" :href="route('admin.products.edit',$product)">{{ __('products.edit_product') }}</x-ui.button>
            @endcan
            @can('manage_stock')
                <x-ui.button variant="secondary" icon="heroicon-o-adjustments-horizontal" :href="route('admin.inventory.adjustments', ['product_id' => $product->id])">{{ __('products.adjust_stock') }}</x-ui.button>
            @endcan
            @can('manage_transfers')
                <x-ui.button variant="secondary" icon="heroicon-o-arrow-path" :href="route('admin.transfers.create')">{{ __('products.transfer_stock') }}</x-ui.button>
            @endcan
            @can('manage_stock')
                <x-ui.button variant="outline" icon="heroicon-o-book-open" :href="route('admin.inventory.ledger', ['product_id' => $product->id])">{{ __('products.view_full_ledger') }}</x-ui.button>
            @endcan
            <x-ui.button variant="outline" icon="heroicon-o-qr-code" :href="route('admin.products.barcode',$product)">{{ __('products.print_barcode') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="panel mb-1">
    <div class="panel-body">
        <div class="grid gap-4 lg:grid-cols-[1.3fr_1fr] items-start">
            <div class="flex items-start gap-4">
                <div style="width:92px;height:92px;border-radius:14px;border:1px solid #dbe3ef;background:#f8fafc;overflow:hidden;display:grid;place-items:center;flex-shrink:0;">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;" />
                    @else
                        <span class="muted">{{ __('products.no_image') }}</span>
                    @endif
                </div>
                <div>
                    <h2 style="margin:0;font-size:1.25rem;">{{ $product->name }}</h2>
                    <div class="muted" style="margin-top:.25rem;">{{ __('products.sku') }}: {{ $product->sku }}</div>
                    <div class="muted" style="margin-top:.1rem;">{{ __('products.barcode') }}: {{ $product->barcode ?: '-' }}</div>
                    <div class="table-actions mt-1">
                        <x-ui.status-badge :status="$product->is_active ? 'active' : 'inactive'" />
                        <x-ui.badge tone="neutral">{{ $product->category?->name ?? __('common.none') }}</x-ui.badge>
                        <x-ui.badge tone="neutral">{{ $product->brand?->name ?? __('common.none') }}</x-ui.badge>
                    </div>
                </div>
            </div>
            <div>
                @if($barcodeHtml)
                    <div class="panel" style="background:#f8fafc;">
                        <div class="panel-body" style="padding:.6rem;">
                            <div class="muted" style="font-size:.75rem;">{{ __('products.barcode_label') }}</div>
                            <div class="mt-1">{!! $barcodeHtml !!}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="kpi-grid mb-1">
    <x-ui.stat-card :label="__('products.total_stock')" :value="number_format((float)$metrics['total_stock'], 2)" icon="heroicon-o-archive-box" />
    <x-ui.stat-card :label="__('products.warehouses_holding')" :value="number_format((int)$metrics['warehouses_holding'])" icon="heroicon-o-building-storefront" tone="info" />
    <x-ui.stat-card :label="__('products.low_stock_threshold')" :value="number_format((float)$metrics['low_stock_threshold'], 2)" icon="heroicon-o-exclamation-triangle" tone="warning" />
    <x-ui.stat-card :label="__('products.inventory_value')" :value="number_format((float)$metrics['inventory_value'], 2)" icon="heroicon-o-banknotes" tone="success" />
</div>

<div class="grid gap-4 lg:grid-cols-[2fr_1fr] mb-1">
    <div class="space-y-4">
        <div class="panel">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('products.basic_information') }}</h3>
                <div class="row">
                    <div><div class="muted">{{ __('common.name') }}</div><strong>{{ $product->name }}</strong></div>
                    <div><div class="muted">{{ __('products.sku') }}</div><strong>{{ $product->sku }}</strong></div>
                    <div><div class="muted">{{ __('products.barcode') }}</div><strong>{{ $product->barcode ?: '-' }}</strong></div>
                    <div><div class="muted">{{ __('products.unit') }}</div><strong>{{ $product->unit?->name ?? __('common.none') }}</strong></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('products.pricing_inventory') }}</h3>
                <div class="row">
                    <div><div class="muted">{{ __('products.selling_price') }}</div><strong>{{ number_format((float)$product->selling_price, 2) }}</strong></div>
                    <div><div class="muted">{{ __('products.cost_price') }}</div><strong>{{ number_format((float)$product->cost_price, 2) }}</strong></div>
                    <div><div class="muted">{{ __('products.low_stock_threshold') }}</div><strong>{{ number_format((float)$product->low_stock_threshold, 2) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('products.description_card') }}</h3>
                <p style="margin:0;white-space:pre-wrap;">{{ $product->description ?: '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="panel">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('products.image') }}</h3>
                <div style="width:100%;height:220px;border-radius:12px;border:1px dashed #cbd5e1;background:#f8fafc;overflow:hidden;display:grid;place-items:center;">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;" />
                    @else
                        <span class="muted">{{ __('products.no_image') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('products.classification') }}</h3>
                <div class="space-y-2">
                    <div><div class="muted">{{ __('products.category') }}</div><strong>{{ $product->category?->name ?? __('common.none') }}</strong></div>
                    <div><div class="muted">{{ __('products.brand') }}</div><strong>{{ $product->brand?->name ?? __('common.none') }}</strong></div>
                    <div><div class="muted">{{ __('products.unit') }}</div><strong>{{ $product->unit?->name ?? __('common.none') }}</strong></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('products.status_summary') }}</h3>
                <x-ui.status-badge :status="$product->is_active ? 'active' : 'inactive'" />
                <div class="mt-1 text-xs text-slate-500">{{ __('products.meta_created') }}: {{ $product->created_at?->format('Y-m-d H:i') }}</div>
                <div class="text-xs text-slate-500">{{ __('products.meta_updated') }}: {{ $product->updated_at?->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="panel mb-1">
    <div class="panel-body">
        <div class="flex items-center justify-between mb-1">
            <h3 style="margin:0;">{{ __('products.stock_by_warehouse') }}</h3>
            @can('manage_stock')
                <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.warehouses', ['q' => $product->sku])">{{ __('products.warehouse_stock') }}</x-ui.button>
            @endcan
        </div>

        <x-ui.table compact>
            <thead>
                <tr>
                    <th>{{ __('navigation.warehouses') }}</th>
                    <th style="text-align:right;">{{ __('products.quantity_on_hand') }}</th>
                    <th style="text-align:right;">{{ __('products.reserved_quantity') }}</th>
                    <th style="text-align:right;">{{ __('products.available_quantity') }}</th>
                    <th>{{ __('products.stock_status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockByWarehouse as $row)
                    @php
                        $available = (float) $row->available_quantity;
                        $tone = 'success';
                        $status = __('products.in_stock');
                        if ($available <= 0) {
                            $tone = 'danger';
                            $status = __('products.out_of_stock');
                        } elseif ($available <= (float) $product->low_stock_threshold) {
                            $tone = 'warning';
                            $status = __('products.low_stock');
                        }
                    @endphp
                    <tr>
                        <td>{{ $row->warehouse_name }}</td>
                        <td style="text-align:right;">{{ number_format((float) $row->cached_quantity, 2) }}</td>
                        <td style="text-align:right;">{{ number_format((float) $row->reserved_quantity, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($available, 2) }}</td>
                        <td><x-ui.badge :tone="$tone">{{ $status }}</x-ui.badge></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">{{ __('common.no_records_found') }}</td></tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>
</div>

<div class="panel mb-1">
    <div class="panel-body">
        <div class="flex items-center justify-between mb-1">
            <h3 style="margin:0;">{{ __('products.recent_stock_movements') }}</h3>
            @can('manage_stock')
                <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.ledger', ['product_id' => $product->id])">{{ __('products.view_full_ledger') }}</x-ui.button>
            @endcan
        </div>
        <x-ui.table compact>
            <thead>
                <tr>
                    <th>{{ __('common.date') }}</th>
                    <th>{{ __('navigation.warehouses') }}</th>
                    <th>{{ __('products.movement_type') }}</th>
                    <th style="text-align:right;">{{ __('products.quantity_change') }}</th>
                    <th>{{ __('common.reference') }}</th>
                    <th>{{ __('common.reason') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMovements as $movement)
                    @php $qty = (float) $movement->quantity; @endphp
                    <tr>
                        <td>{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $movement->warehouse?->name ?? '-' }}</td>
                        <td><x-ui.status-badge :status="$movement->movement_type" /></td>
                        <td style="text-align:right;font-weight:700;color:{{ $qty >= 0 ? '#15803d' : '#b91c1c' }};">{{ $qty >= 0 ? '+' : '' }}{{ number_format($qty, 2) }}</td>
                        <td>{{ $movement->reference_type && $movement->reference_id ? $movement->reference_type.':'.$movement->reference_id : '-' }}</td>
                        <td>{{ $movement->reason ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">{{ __('common.no_records_found') }}</td></tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>
</div>

<div class="panel">
    <div class="panel-body">
        <h3 style="margin:0 0 .75rem 0;">{{ __('products.quick_actions') }}</h3>
        <x-ui.table-actions>
            @can('update_products')
                <x-ui.button size="sm" :href="route('admin.products.edit', $product)">{{ __('products.edit_product') }}</x-ui.button>
            @endcan
            @can('manage_stock')
                <x-ui.button variant="secondary" size="sm" :href="route('admin.inventory.adjustments', ['product_id' => $product->id])">{{ __('products.adjust_stock') }}</x-ui.button>
                <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.ledger', ['product_id' => $product->id])">{{ __('products.view_full_ledger') }}</x-ui.button>
                <x-ui.button variant="ghost" size="sm" :href="route('admin.inventory.warehouses', ['q' => $product->sku])">{{ __('products.warehouse_stock') }}</x-ui.button>
            @endcan
            @can('manage_transfers')
                <x-ui.button variant="secondary" size="sm" :href="route('admin.transfers.create')">{{ __('products.transfer_stock') }}</x-ui.button>
            @endcan
            <x-ui.button variant="outline" size="sm" :href="route('admin.products.barcode',$product)">{{ __('products.print_barcode') }}</x-ui.button>
        </x-ui.table-actions>
    </div>
</div>
@endsection

