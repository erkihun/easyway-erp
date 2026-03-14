@extends('layouts.admin')
@section('title', __('dashboard.title'))
@section('page-title', __('dashboard.title'))
@section('page-subtitle', __('dashboard.subtitle'))

@section('content')
<div class="dashboard-shell">
    <div class="row-grid row-actions">
        @can('create_products')
            <a href="{{ route('admin.products.create') }}" class="dashboard-card action-card">
                <span class="action-icon"><x-heroicon-o-cube class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_product') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_product_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('manage_purchases')
            <a href="{{ route('admin.purchases.create') }}" class="dashboard-card action-card">
                <span class="action-icon"><x-heroicon-o-clipboard-document-list class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_purchase_order') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_purchase_order_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('create_orders')
            <a href="{{ route('admin.sales.create') }}" class="dashboard-card action-card">
                <span class="action-icon"><x-heroicon-o-banknotes class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_sales_order') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_sales_order_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('manage_transfers')
            <a href="{{ route('admin.transfers.create') }}" class="dashboard-card action-card">
                <span class="action-icon"><x-heroicon-o-arrow-path class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.new_transfer') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.new_transfer_desc') }}</p>
                </div>
            </a>
        @endcan

        @can('manage_purchases')
            <a href="{{ route('admin.goods-receipts.create') }}" class="dashboard-card action-card">
                <span class="action-icon"><x-heroicon-o-inbox-arrow-down class="h-5 w-5" /></span>
                <div>
                    <p class="dashboard-card-title">{{ __('dashboard.record_goods_receipt') }}</p>
                    <p class="dashboard-card-subtitle">{{ __('dashboard.record_goods_receipt_desc') }}</p>
                </div>
            </a>
        @endcan
    </div>

    <div class="row-grid row-kpi">
        <div><x-ui.stat-card :label="__('dashboard.total_products')" :value="number_format((float) $totalProducts)" icon="heroicon-o-cube" :helper="__('dashboard.catalog_items')" /></div>
        <div><x-ui.stat-card :label="__('dashboard.total_warehouses')" :value="number_format((float) $totalWarehouses)" icon="heroicon-o-building-storefront" :helper="__('dashboard.active_sites')" /></div>
        <div><x-ui.stat-card :label="__('dashboard.low_stock')" :value="number_format((float) count($lowStockItems))" icon="heroicon-o-exclamation-triangle" tone="warning" :helper="__('dashboard.below_threshold')" /></div>
        <div><x-ui.stat-card :label="__('dashboard.out_of_stock')" :value="number_format((float) count($outOfStockItems))" icon="heroicon-o-no-symbol" tone="danger" :helper="__('dashboard.needs_replenishment')" /></div>
        <div><x-ui.stat-card :label="__('dashboard.today_sales')" :value="number_format($todaySales, 2)" icon="heroicon-o-banknotes" tone="success" :helper="__('dashboard.confirmed_totals')" /></div>
        <div><x-ui.stat-card :label="__('dashboard.monthly_revenue')" :value="number_format($monthlyRevenue, 2)" icon="heroicon-o-chart-bar" tone="info" :helper="__('dashboard.current_month')" /></div>
        <div><x-ui.stat-card :label="__('dashboard.invoices_this_month')" :value="number_format((float) $invoicesThisMonth)" icon="heroicon-o-document-text" tone="info" /></div>
        <div><x-ui.stat-card :label="__('dashboard.outstanding_payments')" :value="number_format((float) $outstandingPayments, 2)" icon="heroicon-o-exclamation-triangle" tone="warning" /></div>
        <div><x-ui.stat-card :label="__('dashboard.overdue_invoices')" :value="number_format((float) $overdueInvoices)" icon="heroicon-o-x-circle" tone="danger" /></div>
        <div><x-ui.stat-card :label="__('dashboard.total_revenue')" :value="number_format((float) $monthlyRevenue, 2)" icon="heroicon-o-currency-dollar" tone="success" /></div>
    </div>

    <div class="row-grid row-charts">
        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.sales_trend') }}</h3>
                <span class="section-meta">{{ __('dashboard.days_14') }}</span>
            </div>
            <div class="chart-canvas"><canvas id="salesTrends"></canvas></div>
        </div>

        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.inventory_value_trend') }}</h3>
                <span class="section-meta">{{ __('dashboard.valuation') }}</span>
            </div>
            <div class="chart-canvas"><canvas id="inventoryValue"></canvas></div>
        </div>

        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.warehouse_distribution') }}</h3>
                <span class="section-meta">{{ __('dashboard.by_qty') }}</span>
            </div>
            <div class="chart-canvas"><canvas id="warehouseDistribution"></canvas></div>
        </div>
    </div>

    <div class="row-grid row-ops">
        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.top_selling_products') }}</h3>
                <span class="section-meta">{{ $topSellingProducts->count() }}</span>
            </div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.product') }}</th>
                            <th style="text-align:right;">{{ __('dashboard.qty_sold') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSellingProducts as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td style="text-align:right;">{{ number_format((float) $row->qty, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="muted">{{ __('dashboard.no_sales') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.low_stock') }} / {{ __('dashboard.out_of_stock') }}</h3>
                <span class="section-meta">{{ __('dashboard.alerts') }}</span>
            </div>
            <div class="table-wrap" style="margin-bottom:.85rem;">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.low_stock') }} {{ __('dashboard.product') }}</th>
                            <th style="text-align:right;">{{ __('inventory.stock') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td style="text-align:right;">{{ number_format((float) $row->stock, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="muted">{{ __('dashboard.no_low_stock') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.out_of_stock') }} {{ __('dashboard.product') }}</th>
                            <th style="text-align:right;">{{ __('inventory.stock') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outOfStockItems as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td style="text-align:right;">{{ number_format((float) $row->stock, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="muted">{{ __('dashboard.no_out_of_stock') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.recent_sales_orders') }}</h3>
                <span class="section-meta">{{ $recentSalesOrders->count() }}</span>
            </div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.order') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('common.date') }}</th>
                            <th style="text-align:right;">{{ __('common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSalesOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td><x-ui.status-badge :status="$order->status" /></td>
                                <td>{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('Y-m-d') }}</td>
                                <td style="text-align:right;">{{ number_format((float) $order->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="muted">{{ __('dashboard.no_recent_sales_orders') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row-grid row-activity">
        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.recent_purchase_receipts') }}</h3>
                <span class="section-meta">{{ $recentGoodsReceipts->count() }}</span>
            </div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.receipt') }}</th>
                            <th>{{ __('dashboard.purchase') }}</th>
                            <th>{{ __('dashboard.warehouse') }}</th>
                            <th>{{ __('common.date') }}</th>
                        </tr>
                    </thead>
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

        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.recent_activity') }}</h3>
                <span class="section-meta">{{ __('dashboard.timeline') }}</span>
            </div>
            <div class="dashboard-timeline">
                @forelse($recentActivities as $log)
                    <div class="dashboard-timeline-item">
                        <span class="dashboard-timeline-dot" aria-hidden="true"></span>
                        <div>
                            <p class="dashboard-timeline-title">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                            <div class="dashboard-timeline-meta">{{ class_basename($log->model) }} · {{ \Illuminate\Support\Carbon::parse($log->timestamp)->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state :title="__('dashboard.no_activity_yet')" :description="__('dashboard.recent_activity_help')" icon="heroicon-o-clock" />
                @endforelse
            </div>
        </div>

        <div class="dashboard-card panel-fill">
            <div class="section-head">
                <h3 class="section-title">{{ __('dashboard.recent_stock_movements') }}</h3>
                <span class="section-meta">{{ $recentStockMovements->count() }}</span>
            </div>
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>{{ __('common.type') }}</th>
                            <th>{{ __('dashboard.product') }}</th>
                            <th>{{ __('dashboard.warehouse') }}</th>
                            <th style="text-align:right;">{{ __('dashboard.qty') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStockMovements as $movement)
                            <tr>
                                <td><x-ui.status-badge :status="$movement->movement_type" /></td>
                                <td>{{ $movement->product_name }}</td>
                                <td>{{ $movement->warehouse_name }}</td>
                                <td style="text-align:right;">{{ number_format((float) $movement->quantity, 2) }}</td>
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
    (() => {
        const chartRegistry = {};
        const labels = {
            sales: @json(__('navigation.sales')),
            inventory: @json(__('dashboard.inventory_value_trend')),
            quantity: @json(__('dashboard.qty')),
        };

        const datasets = {
            salesLabels: @json($salesTrends->pluck('day')),
            salesValues: @json($salesTrends->pluck('amount')),
            inventoryLabels: @json($inventoryValue->pluck('day')),
            inventoryValues: @json($inventoryValue->pluck('value')),
            warehouseLabels: @json($warehouseDistribution->pluck('name')),
            warehouseValues: @json($warehouseDistribution->pluck('qty')),
        };

        const readVar = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

        const baseOptions = () => ({
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    labels: {
                        color: readVar('--erp-text-soft'),
                        usePointStyle: true,
                        boxWidth: 10,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: readVar('--erp-muted') },
                    grid: { color: readVar('--erp-line') },
                },
                y: {
                    ticks: { color: readVar('--erp-muted') },
                    grid: { color: readVar('--erp-line') },
                },
            },
        });

        const makeCharts = () => {
            Object.values(chartRegistry).forEach((chart) => chart.destroy());

            chartRegistry.sales = new Chart(document.getElementById('salesTrends'), {
                type: 'line',
                data: {
                    labels: datasets.salesLabels,
                    datasets: [{
                        label: labels.sales,
                        data: datasets.salesValues,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.16)',
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#ffffff',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                    }],
                },
                options: baseOptions(),
            });

            chartRegistry.inventory = new Chart(document.getElementById('inventoryValue'), {
                type: 'line',
                data: {
                    labels: datasets.inventoryLabels,
                    datasets: [{
                        label: labels.inventory,
                        data: datasets.inventoryValues,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.14)',
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#ffffff',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                    }],
                },
                options: baseOptions(),
            });

            chartRegistry.warehouses = new Chart(document.getElementById('warehouseDistribution'), {
                type: 'bar',
                data: {
                    labels: datasets.warehouseLabels,
                    datasets: [{
                        label: labels.quantity,
                        data: datasets.warehouseValues,
                        backgroundColor: ['#4f46e5', '#2563eb', '#06b6d4', '#22c55e', '#f59e0b', '#ef4444'],
                        borderRadius: 12,
                        maxBarThickness: 38,
                    }],
                },
                options: {
                    ...baseOptions(),
                    plugins: {
                        legend: { display: false },
                    },
                },
            });
        };

        document.addEventListener('DOMContentLoaded', makeCharts);
        window.addEventListener('erp:theme-changed', makeCharts);
    })();
</script>
@endsection
