@extends('layouts.admin')
@section('title', __('dashboard.title'))
@section('page-title', __('dashboard.title'))
@section('page-subtitle', __('dashboard.subtitle'))
@section('content')
<style>
.dashboard-shell { display: grid; gap: 1rem; }
.row-grid { display: grid; gap: .75rem; }
.row-actions { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.row-kpi { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.row-charts, .row-ops, .row-activity { grid-template-columns: repeat(1, minmax(0, 1fr)); }

.dashboard-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(15, 23, 42, .06);
    padding: .92rem;
}
.dashboard-card:hover { box-shadow: 0 8px 18px rgba(15, 23, 42, .08); }
.dashboard-card-title { margin: 0; font-size: .92rem; font-weight: 700; color: #1f2937; }
.dashboard-card-subtitle { margin: .1rem 0 0; font-size: .78rem; color: #6b7280; }

.action-card {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: .55rem;
    align-items: start;
    text-decoration: none;
    color: inherit;
}
.action-icon {
    width: 1.85rem;
    height: 1.85rem;
    display: grid;
    place-items: center;
    border-radius: 10px;
    background: #eef2ff;
    color: #4338ca;
}

.kpi-grid-item { min-width: 0; }

.section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .5rem;
    margin-bottom: .45rem;
}
.section-title { margin: 0; font-size: .94rem; font-weight: 700; color: #1f2937; }
.section-meta { font-size: .78rem; color: #6b7280; }

.table-compact th, .table-compact td { padding: .42rem .52rem; }

.panel-fill {
    display: flex;
    flex-direction: column;
    min-height: 100%;
}
.panel-fill .table-wrap { flex: 1; }

@media (min-width: 768px) {
    .row-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .row-kpi { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (min-width: 1280px) {
    .row-actions { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    .row-kpi { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .row-charts, .row-ops, .row-activity { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
</style>

<div class="dashboard-shell space-y-6">
    <div class="row-grid row-actions grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        @can('create_products')
            <a href="{{ route('admin.products.create') }}" class="dashboard-card action-card rounded-xl shadow-sm p-4 hover:shadow-md">
                <span class="action-icon"><x-heroicon-o-cube class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_product') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_product_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('manage_purchases')
            <a href="{{ route('admin.purchases.create') }}" class="dashboard-card action-card rounded-xl shadow-sm p-4 hover:shadow-md">
                <span class="action-icon"><x-heroicon-o-clipboard-document-list class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_purchase_order') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_purchase_order_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('create_orders')
            <a href="{{ route('admin.sales.create') }}" class="dashboard-card action-card rounded-xl shadow-sm p-4 hover:shadow-md">
                <span class="action-icon"><x-heroicon-o-banknotes class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_sales_order') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_sales_order_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('manage_transfers')
            <a href="{{ route('admin.transfers.create') }}" class="dashboard-card action-card rounded-xl shadow-sm p-4 hover:shadow-md">
                <span class="action-icon"><x-heroicon-o-arrow-path class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_transfer') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_transfer_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('manage_purchases')
            <a href="{{ route('admin.goods-receipts.create') }}" class="dashboard-card action-card rounded-xl shadow-sm p-4 hover:shadow-md">
                <span class="action-icon"><x-heroicon-o-inbox-arrow-down class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.record_goods_receipt') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.record_goods_receipt_desc') }}</p>
                </div>
            </a>
        @endcan
    </div>

    <div class="row-grid row-kpi grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="kpi-grid-item"><x-ui.stat-card :label="__('dashboard.total_products')" :value="number_format((float)$totalProducts)" icon="heroicon-o-cube" :helper="__('dashboard.catalog_items')" /></div>
        <div class="kpi-grid-item"><x-ui.stat-card :label="__('dashboard.total_warehouses')" :value="number_format((float)$totalWarehouses)" icon="heroicon-o-building-storefront" :helper="__('dashboard.active_sites')" /></div>
        <div class="kpi-grid-item"><x-ui.stat-card :label="__('dashboard.low_stock')" :value="number_format((float)count($lowStockItems))" icon="heroicon-o-exclamation-triangle" tone="warning" :helper="__('dashboard.below_threshold')" /></div>
        <div class="kpi-grid-item"><x-ui.stat-card :label="__('dashboard.out_of_stock')" :value="number_format((float)count($outOfStockItems))" icon="heroicon-o-no-symbol" tone="danger" :helper="__('dashboard.needs_replenishment')" /></div>
        <div class="kpi-grid-item"><x-ui.stat-card :label="__('dashboard.today_sales')" :value="number_format($todaySales,2)" icon="heroicon-o-banknotes" tone="success" :helper="__('dashboard.confirmed_totals')" /></div>
        <div class="kpi-grid-item"><x-ui.stat-card :label="__('dashboard.monthly_revenue')" :value="number_format($monthlyRevenue,2)" icon="heroicon-o-chart-bar" tone="info" :helper="__('dashboard.current_month')" /></div>
    </div>

    <div class="row-grid row-charts grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.sales_trend') }}</h3>
                <span class="section-meta">{{ __('dashboard.days_14') }}</span>
            </div>
            <div class="chart-canvas"><canvas id="salesTrends"></canvas></div>
        </div>

        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.inventory_value_trend') }}</h3>
                <span class="section-meta">{{ __('dashboard.valuation') }}</span>
            </div>
            <div class="chart-canvas"><canvas id="inventoryValue"></canvas></div>
        </div>

        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.warehouse_distribution') }}</h3>
                <span class="section-meta">{{ __('dashboard.by_qty') }}</span>
            </div>
            <div class="chart-canvas"><canvas id="warehouseDistribution"></canvas></div>
        </div>
    </div>

    <div class="row-grid row-ops grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head"><h3 class="section-title">{{ __('dashboard.top_selling_products') }}</h3><span class="section-meta">{{ $topSellingProducts->count() }}</span></div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead><tr><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('dashboard.qty_sold') }}</th></tr></thead>
                    <tbody>
                    @forelse($topSellingProducts as $row)
                        <tr><td>{{ $row->name }}</td><td style="text-align:right;">{{ number_format((float)$row->qty,2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="muted">{{ __('dashboard.no_sales') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head"><h3 class="section-title">{{ __('dashboard.low_stock') }} / {{ __('dashboard.out_of_stock') }}</h3><span class="section-meta">{{ __('dashboard.alerts') }}</span></div>
            <div class="table-wrap mb-1">
                <table class="table-compact">
                    <thead><tr><th>{{ __('dashboard.low_stock') }} {{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('inventory.stock') }}</th></tr></thead>
                    <tbody>
                    @forelse($lowStockItems as $row)
                        <tr><td>{{ $row->name }}</td><td style="text-align:right;">{{ number_format((float)$row->stock,2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="muted">{{ __('dashboard.no_low_stock') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead><tr><th>{{ __('dashboard.out_of_stock') }} {{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('inventory.stock') }}</th></tr></thead>
                    <tbody>
                    @forelse($outOfStockItems as $row)
                        <tr><td>{{ $row->name }}</td><td style="text-align:right;">{{ number_format((float)$row->stock,2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="muted">{{ __('dashboard.no_out_of_stock') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head"><h3 class="section-title">{{ __('dashboard.recent_sales_orders') }}</h3><span class="section-meta">{{ $recentSalesOrders->count() }}</span></div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead><tr><th>{{ __('dashboard.order') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.date') }}</th><th style="text-align:right;">{{ __('common.total') }}</th></tr></thead>
                    <tbody>
                    @forelse($recentSalesOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td><x-ui.status-badge :status="$order->status" /></td>
                            <td>{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('Y-m-d') }}</td>
                            <td style="text-align:right;">{{ number_format((float)$order->total_amount,2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">{{ __('dashboard.no_recent_sales_orders') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row-grid row-activity grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head"><h3 class="section-title">{{ __('dashboard.recent_purchase_receipts') }}</h3><span class="section-meta">{{ $recentGoodsReceipts->count() }}</span></div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead><tr><th>{{ __('dashboard.receipt') }}</th><th>{{ __('dashboard.purchase') }}</th><th>{{ __('dashboard.warehouse') }}</th><th>{{ __('common.date') }}</th></tr></thead>
                    <tbody>
                    @forelse($recentGoodsReceipts as $receipt)
                        <tr>
                            <td>{{ $receipt->receipt_number }}</td>
                            <td>{{ $receipt->purchase_order_number ?? __('common.none') }}</td>
                            <td>{{ $receipt->warehouse_name ?? __('common.none') }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($receipt->received_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">{{ __('dashboard.no_receipt_activity') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head"><h3 class="section-title">{{ __('dashboard.recent_activity') }}</h3><span class="section-meta">{{ __('dashboard.timeline') }}</span></div>
            <div class="dashboard-timeline">
                @forelse($recentActivities as $log)
                    <div class="dashboard-timeline-item">
                        <span class="dashboard-timeline-dot" aria-hidden="true"></span>
                        <div>
                            <p class="dashboard-timeline-title">{{ ucfirst(str_replace('_',' ', $log->action)) }}</p>
                            <div class="dashboard-timeline-meta">{{ class_basename($log->model) }} · {{ \Illuminate\Support\Carbon::parse($log->timestamp)->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state :title="__('dashboard.no_activity_yet')" :description="__('dashboard.recent_activity_help')" icon="heroicon-o-clock" />
                @endforelse
            </div>
        </div>

        <div class="dashboard-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 panel-fill">
            <div class="section-head"><h3 class="section-title">{{ __('dashboard.recent_stock_movements') }}</h3><span class="section-meta">{{ $recentStockMovements->count() }}</span></div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead><tr><th>{{ __('common.type') }}</th><th>{{ __('dashboard.product') }}</th><th>{{ __('dashboard.warehouse') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th></tr></thead>
                    <tbody>
                    @forelse($recentStockMovements as $m)
                        <tr>
                            <td><x-ui.status-badge :status="$m->movement_type" /></td>
                            <td>{{ $m->product_name }}</td>
                            <td>{{ $m->warehouse_name }}</td>
                            <td style="text-align:right;">{{ number_format((float)$m->quantity,2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">{{ __('dashboard.no_movement_activity') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('salesTrends'), {
    type: 'line',
    data: {
        labels: @json($salesTrends->pluck('day')),
        datasets: [{
            label: @json(__('navigation.sales')),
            data: @json($salesTrends->pluck('amount')),
            borderColor: '#0f766e',
            backgroundColor: 'rgba(15,118,110,.12)',
            tension: .3,
            fill: true
        }]
    },
    options: { maintainAspectRatio: false, plugins: { legend: { display: true } } }
});

new Chart(document.getElementById('inventoryValue'), {
    type: 'line',
    data: {
        labels: @json($inventoryValue->pluck('day')),
        datasets: [{
            label: @json(__('dashboard.inventory_value_trend')),
            data: @json($inventoryValue->pluck('value')),
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,.12)',
            tension: .3,
            fill: true
        }]
    },
    options: { maintainAspectRatio: false, plugins: { legend: { display: true } } }
});

new Chart(document.getElementById('warehouseDistribution'), {
    type: 'bar',
    data: {
        labels: @json($warehouseDistribution->pluck('name')),
        datasets: [{
            label: @json(__('dashboard.qty')),
            data: @json($warehouseDistribution->pluck('qty')),
            backgroundColor: '#3b82f6'
        }]
    },
    options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
});
</script>
@endsection

