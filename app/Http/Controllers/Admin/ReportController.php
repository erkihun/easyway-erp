<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportExportService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportExportService $reportExportService)
    {
    }

    /** @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse */
    public function inventory(Request $request, string $format = 'html')
    {
        $warehouses = $this->warehouseOptions();

        $query = $this->inventoryQuery($request);

        return $this->renderReport($request, $format, [
            'key' => 'inventory',
            'title' => __('reports.inventory'),
            'subtitle' => __('reports.inventory_subtitle'),
            'filename' => 'inventory_report',
            'route' => 'admin.reports.inventory',
            'columns' => [
                ['key' => 'product', 'label' => __('common.name')],
                ['key' => 'sku', 'label' => __('products.sku')],
                ['key' => 'warehouse', 'label' => __('inventory.warehouse')],
                ['key' => 'quantity_on_hand', 'label' => __('inventory.quantity_on_hand'), 'numeric' => true],
                ['key' => 'reserved', 'label' => __('inventory.reserved'), 'numeric' => true],
                ['key' => 'available', 'label' => __('inventory.available'), 'numeric' => true],
                ['key' => 'unit_cost', 'label' => __('inventory.unit_cost'), 'numeric' => true],
                ['key' => 'total_value', 'label' => __('reports.total_value'), 'numeric' => true],
            ],
            'filters' => [
                ['type' => 'text', 'name' => 'q', 'label' => __('common.search'), 'placeholder' => __('reports.search_product_sku')],
                ['type' => 'select', 'name' => 'warehouse_id', 'label' => __('inventory.warehouse'), 'options' => $warehouses],
            ],
            'summaries' => static fn (Collection $rows): array => [
                ['label' => __('reports.total_products'), 'value' => (string) $rows->pluck('sku')->filter()->unique()->count(), 'icon' => 'heroicon-o-cube'],
                ['label' => __('reports.total_quantity'), 'value' => number_format((float) $rows->sum('quantity_on_hand'), 2), 'icon' => 'heroicon-o-calculator'],
                ['label' => __('reports.total_warehouses'), 'value' => (string) $rows->pluck('warehouse')->filter()->unique()->count(), 'icon' => 'heroicon-o-building-storefront'],
                ['label' => __('reports.total_stock_value'), 'value' => number_format((float) $rows->sum('total_value'), 2), 'icon' => 'heroicon-o-banknotes'],
            ],
        ], $query);
    }

    /** @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse */
    public function lowStock(Request $request, string $format = 'html')
    {
        $warehouses = $this->warehouseOptions();
        $query = $this->lowStockQuery($request);

        return $this->renderReport($request, $format, [
            'key' => 'low_stock',
            'title' => __('reports.low_stock'),
            'subtitle' => __('reports.low_stock_subtitle'),
            'filename' => 'low_stock_report',
            'route' => 'admin.reports.low-stock',
            'columns' => [
                ['key' => 'product', 'label' => __('common.name')],
                ['key' => 'sku', 'label' => __('products.sku')],
                ['key' => 'warehouse', 'label' => __('inventory.warehouse')],
                ['key' => 'current_stock', 'label' => __('reports.current_stock'), 'numeric' => true],
                ['key' => 'threshold', 'label' => __('inventory.low_stock_threshold'), 'numeric' => true],
                ['key' => 'shortage', 'label' => __('reports.shortage'), 'numeric' => true],
                ['key' => 'status', 'label' => __('common.status')],
            ],
            'filters' => [
                ['type' => 'text', 'name' => 'q', 'label' => __('common.search'), 'placeholder' => __('reports.search_product_sku')],
                ['type' => 'select', 'name' => 'warehouse_id', 'label' => __('inventory.warehouse'), 'options' => $warehouses],
                ['type' => 'select', 'name' => 'critical_only', 'label' => __('reports.stock_scope'), 'options' => [
                    ['value' => '', 'label' => __('reports.all_low_stock')],
                    ['value' => '1', 'label' => __('reports.out_of_stock_only')],
                ]],
            ],
            'summaries' => static fn (Collection $rows): array => [
                ['label' => __('reports.below_threshold'), 'value' => (string) $rows->count(), 'icon' => 'heroicon-o-exclamation-triangle', 'tone' => 'warning'],
                ['label' => __('reports.out_of_stock_count'), 'value' => (string) $rows->where('status', 'out_of_stock')->count(), 'icon' => 'heroicon-o-no-symbol', 'tone' => 'danger'],
            ],
        ], $query);
    }

    /** @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse */
    public function sales(Request $request, string $format = 'html')
    {
        $customers = $this->customerOptions();
        $statuses = $this->orderStatusOptions('sales_orders');
        $query = $this->salesQuery($request);

        return $this->renderReport($request, $format, [
            'key' => 'sales',
            'title' => __('reports.sales'),
            'subtitle' => __('reports.sales_subtitle'),
            'filename' => 'sales_report',
            'route' => 'admin.reports.sales',
            'columns' => [
                ['key' => 'order_number', 'label' => __('reports.order_number')],
                ['key' => 'customer', 'label' => __('common.customer')],
                ['key' => 'order_date', 'label' => __('common.date')],
                ['key' => 'status', 'label' => __('common.status')],
                ['key' => 'total', 'label' => __('common.total'), 'numeric' => true],
                ['key' => 'paid', 'label' => __('reports.total_paid'), 'numeric' => true],
                ['key' => 'balance', 'label' => __('reports.balance'), 'numeric' => true],
            ],
            'filters' => [
                ['type' => 'text', 'name' => 'q', 'label' => __('common.search'), 'placeholder' => __('reports.search_order')],
                ['type' => 'select', 'name' => 'customer_id', 'label' => __('common.customer'), 'options' => $customers],
                ['type' => 'select', 'name' => 'status', 'label' => __('common.status'), 'options' => $statuses],
                ['type' => 'date', 'name' => 'from_date', 'label' => __('reports.from_date')],
                ['type' => 'date', 'name' => 'to_date', 'label' => __('reports.to_date')],
            ],
            'summaries' => static fn (Collection $rows): array => [
                ['label' => __('reports.total_orders'), 'value' => (string) $rows->count(), 'icon' => 'heroicon-o-clipboard-document-list'],
                ['label' => __('reports.total_revenue'), 'value' => number_format((float) $rows->sum('total'), 2), 'icon' => 'heroicon-o-banknotes'],
                ['label' => __('reports.total_paid'), 'value' => number_format((float) $rows->sum('paid'), 2), 'icon' => 'heroicon-o-credit-card', 'tone' => 'success'],
                ['label' => __('reports.total_outstanding'), 'value' => number_format((float) $rows->sum('balance'), 2), 'icon' => 'heroicon-o-clock', 'tone' => 'warning'],
            ],
        ], $query);
    }

    /** @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse */
    public function purchase(Request $request, string $format = 'html')
    {
        $suppliers = $this->supplierOptions();
        $statuses = $this->orderStatusOptions('purchase_orders');
        $query = $this->purchaseQuery($request);

        return $this->renderReport($request, $format, [
            'key' => 'purchase',
            'title' => __('reports.purchase'),
            'subtitle' => __('reports.purchase_subtitle'),
            'filename' => 'purchase_report',
            'route' => 'admin.reports.purchase',
            'columns' => [
                ['key' => 'order_number', 'label' => __('reports.order_number')],
                ['key' => 'supplier', 'label' => __('reports.supplier')],
                ['key' => 'order_date', 'label' => __('common.date')],
                ['key' => 'status', 'label' => __('common.status')],
                ['key' => 'total', 'label' => __('common.total'), 'numeric' => true],
                ['key' => 'received', 'label' => __('reports.received_qty'), 'numeric' => true],
            ],
            'filters' => [
                ['type' => 'text', 'name' => 'q', 'label' => __('common.search'), 'placeholder' => __('reports.search_order')],
                ['type' => 'select', 'name' => 'supplier_id', 'label' => __('reports.supplier'), 'options' => $suppliers],
                ['type' => 'select', 'name' => 'status', 'label' => __('common.status'), 'options' => $statuses],
                ['type' => 'date', 'name' => 'from_date', 'label' => __('reports.from_date')],
                ['type' => 'date', 'name' => 'to_date', 'label' => __('reports.to_date')],
            ],
            'summaries' => static fn (Collection $rows): array => [
                ['label' => __('reports.total_purchase_orders'), 'value' => (string) $rows->count(), 'icon' => 'heroicon-o-clipboard-document-list'],
                ['label' => __('reports.total_supplier_spend'), 'value' => number_format((float) $rows->sum('total'), 2), 'icon' => 'heroicon-o-banknotes'],
                ['label' => __('reports.total_received_qty'), 'value' => number_format((float) $rows->sum('received'), 2), 'icon' => 'heroicon-o-inbox-stack', 'tone' => 'success'],
            ],
        ], $query);
    }

    /** @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse */
    public function profit(Request $request, string $format = 'html')
    {
        $statuses = $this->orderStatusOptions('sales_orders');
        $query = $this->profitQuery($request);

        return $this->renderReport($request, $format, [
            'key' => 'profit',
            'title' => __('reports.profit'),
            'subtitle' => __('reports.profit_subtitle'),
            'filename' => 'profit_report',
            'route' => 'admin.reports.profit',
            'columns' => [
                ['key' => 'order_number', 'label' => __('reports.order_number')],
                ['key' => 'order_date', 'label' => __('common.date')],
                ['key' => 'status', 'label' => __('common.status')],
                ['key' => 'revenue', 'label' => __('reports.revenue'), 'numeric' => true],
                ['key' => 'cost', 'label' => __('reports.cost'), 'numeric' => true],
                ['key' => 'profit', 'label' => __('reports.profit_value'), 'numeric' => true],
                ['key' => 'margin_percent', 'label' => __('reports.margin_percent'), 'numeric' => true],
            ],
            'filters' => [
                ['type' => 'text', 'name' => 'q', 'label' => __('common.search'), 'placeholder' => __('reports.search_order')],
                ['type' => 'select', 'name' => 'status', 'label' => __('common.status'), 'options' => $statuses],
                ['type' => 'date', 'name' => 'from_date', 'label' => __('reports.from_date')],
                ['type' => 'date', 'name' => 'to_date', 'label' => __('reports.to_date')],
            ],
            'summaries' => static fn (Collection $rows): array => [
                ['label' => __('reports.revenue'), 'value' => number_format((float) $rows->sum('revenue'), 2), 'icon' => 'heroicon-o-banknotes'],
                ['label' => __('reports.cost'), 'value' => number_format((float) $rows->sum('cost'), 2), 'icon' => 'heroicon-o-currency-dollar'],
                ['label' => __('reports.gross_profit'), 'value' => number_format((float) $rows->sum('profit'), 2), 'icon' => 'heroicon-o-chart-bar-square', 'tone' => 'success'],
            ],
        ], $query);
    }

    /** @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse */
    public function valuation(Request $request, string $format = 'html')
    {
        $warehouses = $this->warehouseOptions();
        $query = $this->valuationQuery($request);

        return $this->renderReport($request, $format, [
            'key' => 'valuation',
            'title' => __('reports.valuation'),
            'subtitle' => __('reports.valuation_subtitle'),
            'filename' => 'inventory_valuation_report',
            'route' => 'admin.reports.valuation',
            'columns' => [
                ['key' => 'product', 'label' => __('common.name')],
                ['key' => 'sku', 'label' => __('products.sku')],
                ['key' => 'warehouse', 'label' => __('inventory.warehouse')],
                ['key' => 'valuation_date', 'label' => __('common.date')],
                ['key' => 'quantity', 'label' => __('inventory.quantity_on_hand'), 'numeric' => true],
                ['key' => 'unit_cost', 'label' => __('inventory.unit_cost'), 'numeric' => true],
                ['key' => 'value', 'label' => __('reports.total_value'), 'numeric' => true],
            ],
            'filters' => [
                ['type' => 'text', 'name' => 'q', 'label' => __('common.search'), 'placeholder' => __('reports.search_product_sku')],
                ['type' => 'select', 'name' => 'warehouse_id', 'label' => __('inventory.warehouse'), 'options' => $warehouses],
                ['type' => 'date', 'name' => 'from_date', 'label' => __('reports.from_date')],
                ['type' => 'date', 'name' => 'to_date', 'label' => __('reports.to_date')],
            ],
            'summaries' => static fn (Collection $rows): array => [
                ['label' => __('reports.total_inventory_value'), 'value' => number_format((float) $rows->sum('value'), 2), 'icon' => 'heroicon-o-banknotes'],
                ['label' => __('reports.total_quantity'), 'value' => number_format((float) $rows->sum('quantity'), 2), 'icon' => 'heroicon-o-calculator'],
                ['label' => __('reports.highest_value_warehouse'), 'value' => (string) (($rows->groupBy('warehouse')->map->sum('value')->sortDesc()->keys()->first()) ?? __('common.none')), 'icon' => 'heroicon-o-building-storefront'],
            ],
        ], $query);
    }

    private function inventoryQuery(Request $request): Builder
    {
        $costLayers = DB::table('cost_layers')
            ->select('product_id', 'warehouse_id', DB::raw('AVG(unit_cost) as avg_cost'))
            ->groupBy('product_id', 'warehouse_id');

        return DB::table('product_stocks as ps')
            ->join('products as p', 'p.id', '=', 'ps.product_id')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->leftJoinSub($costLayers, 'cl', function ($join): void {
                $join->on('cl.product_id', '=', 'ps.product_id')
                    ->on('cl.warehouse_id', '=', 'ps.warehouse_id');
            })
            ->when((string) $request->query('q', '') !== '', function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(function (Builder $where) use ($term): void {
                    $where->where('p.name', 'like', $term)
                        ->orWhere('p.sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id', '') !== '', function (Builder $query) use ($request): void {
                $query->where('ps.warehouse_id', (string) $request->query('warehouse_id'));
            })
            ->select([
                'p.name as product',
                'p.sku',
                'w.name as warehouse',
                DB::raw('ps.cached_quantity as quantity_on_hand'),
                DB::raw('ps.reserved_quantity as reserved'),
                DB::raw('(ps.cached_quantity - ps.reserved_quantity) as available'),
                DB::raw('COALESCE(cl.avg_cost,0) as unit_cost'),
                DB::raw('(ps.cached_quantity * COALESCE(cl.avg_cost,0)) as total_value'),
            ])
            ->orderBy('p.name');
    }

    private function lowStockQuery(Request $request): Builder
    {
        return DB::table('product_stocks as ps')
            ->join('products as p', 'p.id', '=', 'ps.product_id')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->when((string) $request->query('q', '') !== '', function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(function (Builder $where) use ($term): void {
                    $where->where('p.name', 'like', $term)
                        ->orWhere('p.sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id', '') !== '', function (Builder $query) use ($request): void {
                $query->where('ps.warehouse_id', (string) $request->query('warehouse_id'));
            })
            ->whereRaw('ps.cached_quantity <= p.low_stock_threshold')
            ->when((string) $request->query('critical_only', '') === '1', function (Builder $query): void {
                $query->whereRaw('ps.cached_quantity <= 0');
            })
            ->select([
                'p.name as product',
                'p.sku',
                'w.name as warehouse',
                DB::raw('ps.cached_quantity as current_stock'),
                DB::raw('p.low_stock_threshold as threshold'),
                DB::raw('GREATEST(p.low_stock_threshold - ps.cached_quantity, 0) as shortage'),
                DB::raw("CASE WHEN ps.cached_quantity <= 0 THEN 'out_of_stock' ELSE 'low_stock' END as status"),
            ])
            ->orderByRaw('ps.cached_quantity asc')
            ->orderBy('p.name');
    }

    private function salesQuery(Request $request): Builder
    {
        $invoiceAgg = DB::table('invoices')
            ->select('sales_order_id', DB::raw('SUM(paid_amount) as paid_amount'))
            ->groupBy('sales_order_id');

        return DB::table('sales_orders as so')
            ->leftJoin('customers as c', 'c.id', '=', 'so.customer_id')
            ->leftJoinSub($invoiceAgg, 'ia', function ($join): void {
                $join->on('ia.sales_order_id', '=', 'so.id');
            })
            ->when((string) $request->query('q', '') !== '', function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where('so.order_number', 'like', $term);
            })
            ->when((string) $request->query('status', '') !== '', function (Builder $query) use ($request): void {
                $query->where('so.status', (string) $request->query('status'));
            })
            ->when((string) $request->query('customer_id', '') !== '', function (Builder $query) use ($request): void {
                $query->where('so.customer_id', (string) $request->query('customer_id'));
            })
            ->when((string) $request->query('from_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('so.order_date', '>=', (string) $request->query('from_date'));
            })
            ->when((string) $request->query('to_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('so.order_date', '<=', (string) $request->query('to_date'));
            })
            ->select([
                'so.order_number',
                DB::raw("COALESCE(c.name, '-') as customer"),
                'so.order_date',
                'so.status',
                DB::raw('so.total_amount as total'),
                DB::raw('COALESCE(ia.paid_amount, 0) as paid'),
                DB::raw('(so.total_amount - COALESCE(ia.paid_amount, 0)) as balance'),
            ])
            ->orderByDesc('so.order_date');
    }

    private function purchaseQuery(Request $request): Builder
    {
        $itemAgg = DB::table('purchase_order_items')
            ->select('purchase_order_id', DB::raw('SUM(received_quantity) as received_qty'))
            ->groupBy('purchase_order_id');

        return DB::table('purchase_orders as po')
            ->leftJoin('suppliers as s', 's.id', '=', 'po.supplier_id')
            ->leftJoinSub($itemAgg, 'ia', function ($join): void {
                $join->on('ia.purchase_order_id', '=', 'po.id');
            })
            ->when((string) $request->query('q', '') !== '', function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where('po.order_number', 'like', $term);
            })
            ->when((string) $request->query('status', '') !== '', function (Builder $query) use ($request): void {
                $query->where('po.status', (string) $request->query('status'));
            })
            ->when((string) $request->query('supplier_id', '') !== '', function (Builder $query) use ($request): void {
                $query->where('po.supplier_id', (string) $request->query('supplier_id'));
            })
            ->when((string) $request->query('from_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('po.order_date', '>=', (string) $request->query('from_date'));
            })
            ->when((string) $request->query('to_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('po.order_date', '<=', (string) $request->query('to_date'));
            })
            ->select([
                'po.order_number',
                DB::raw("COALESCE(s.name, '-') as supplier"),
                'po.order_date',
                'po.status',
                DB::raw('po.total_amount as total'),
                DB::raw('COALESCE(ia.received_qty, 0) as received'),
            ])
            ->orderByDesc('po.order_date');
    }

    private function profitQuery(Request $request): Builder
    {
        $costByProduct = DB::table('cost_layers')
            ->select('product_id', DB::raw('AVG(unit_cost) as avg_cost'))
            ->groupBy('product_id');

        return DB::table('sales_orders as so')
            ->leftJoin('sales_order_items as soi', 'soi.sales_order_id', '=', 'so.id')
            ->leftJoinSub($costByProduct, 'cp', function ($join): void {
                $join->on('cp.product_id', '=', 'soi.product_id');
            })
            ->when((string) $request->query('q', '') !== '', function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where('so.order_number', 'like', $term);
            })
            ->when((string) $request->query('status', '') !== '', function (Builder $query) use ($request): void {
                $query->where('so.status', (string) $request->query('status'));
            })
            ->when((string) $request->query('from_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('so.order_date', '>=', (string) $request->query('from_date'));
            })
            ->when((string) $request->query('to_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('so.order_date', '<=', (string) $request->query('to_date'));
            })
            ->select([
                'so.order_number',
                'so.order_date',
                'so.status',
                DB::raw('so.total_amount as revenue'),
                DB::raw('COALESCE(SUM(soi.quantity * COALESCE(cp.avg_cost, 0)),0) as cost'),
                DB::raw('(so.total_amount - COALESCE(SUM(soi.quantity * COALESCE(cp.avg_cost, 0)),0)) as profit'),
                DB::raw("CASE WHEN so.total_amount > 0 THEN ((so.total_amount - COALESCE(SUM(soi.quantity * COALESCE(cp.avg_cost, 0)),0)) / so.total_amount) * 100 ELSE 0 END as margin_percent"),
            ])
            ->groupBy('so.id', 'so.order_number', 'so.order_date', 'so.status', 'so.total_amount')
            ->orderByDesc('so.order_date');
    }

    private function valuationQuery(Request $request): Builder
    {
        return DB::table('inventory_valuations as iv')
            ->join('products as p', 'p.id', '=', 'iv.product_id')
            ->join('warehouses as w', 'w.id', '=', 'iv.warehouse_id')
            ->when((string) $request->query('q', '') !== '', function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(function (Builder $where) use ($term): void {
                    $where->where('p.name', 'like', $term)
                        ->orWhere('p.sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id', '') !== '', function (Builder $query) use ($request): void {
                $query->where('iv.warehouse_id', (string) $request->query('warehouse_id'));
            })
            ->when((string) $request->query('from_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('iv.valuation_date', '>=', (string) $request->query('from_date'));
            })
            ->when((string) $request->query('to_date', '') !== '', function (Builder $query) use ($request): void {
                $query->whereDate('iv.valuation_date', '<=', (string) $request->query('to_date'));
            })
            ->select([
                'p.name as product',
                'p.sku',
                'w.name as warehouse',
                DB::raw('DATE(iv.valuation_date) as valuation_date'),
                'iv.quantity',
                DB::raw('CASE WHEN iv.quantity > 0 THEN (iv.value / iv.quantity) ELSE 0 END as unit_cost'),
                'iv.value',
            ])
            ->orderByDesc('iv.valuation_date');
    }

    private function warehouseOptions(): array
    {
        return DB::table('warehouses')
            ->select('id as value', 'name as label')
            ->orderBy('name')
            ->get()
            ->map(static fn ($row): array => ['value' => (string) $row->value, 'label' => (string) $row->label])
            ->all();
    }

    private function customerOptions(): array
    {
        return DB::table('customers')
            ->select('id as value', 'name as label')
            ->orderBy('name')
            ->get()
            ->map(static fn ($row): array => ['value' => (string) $row->value, 'label' => (string) $row->label])
            ->all();
    }

    private function supplierOptions(): array
    {
        return DB::table('suppliers')
            ->select('id as value', 'name as label')
            ->orderBy('name')
            ->get()
            ->map(static fn ($row): array => ['value' => (string) $row->value, 'label' => (string) $row->label])
            ->all();
    }

    private function orderStatusOptions(string $table): array
    {
        return DB::table($table)
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->get()
            ->map(static fn ($row): array => ['value' => (string) $row->status, 'label' => __('common.status_values.'.(string) $row->status)])
            ->all();
    }

    /**
     * @param array{
     *   key:string,
     *   title:string,
     *   subtitle:string,
     *   filename:string,
     *   route:string,
     *   columns:array<int,array{key:string,label:string,numeric?:bool}>,
     *   filters:array<int,array<string,mixed>>,
     *   summaries:callable(Collection):array<int,array<string,mixed>>
     * } $meta
     * @return View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|BinaryFileResponse
     */
    private function renderReport(Request $request, string $format, array $meta, Builder $query)
    {
        $format = strtolower(trim($format));
        $isExportFormat = in_array($format, ['pdf', 'excel', 'csv', 'json'], true);

        if ($isExportFormat) {
            $rows = (clone $query)->get();
            $activeFilters = $this->activeFilters($request, $meta['filters']);

            return $this->reportExportService->export($meta['filename'], $rows, $format, [
                'title' => $meta['title'],
                'columns' => $meta['columns'],
                'filters' => $activeFilters,
                'generated_by' => (string) ($request->user()?->name ?? __('common.unknown')),
            ]);
        }

        $fullRows = (clone $query)->get();
        $paginatedRows = (clone $query)->paginate(20)->withQueryString();
        $summaries = ($meta['summaries'])($fullRows);

        $exportQuery = $request->query();
        $exportLinks = [
            'pdf' => route($meta['route'], array_merge(['format' => 'pdf'], $exportQuery)),
            'excel' => route($meta['route'], array_merge(['format' => 'excel'], $exportQuery)),
            'csv' => route($meta['route'], array_merge(['format' => 'csv'], $exportQuery)),
        ];

        return view('admin.reports.show', [
            'meta' => $meta,
            'rows' => $paginatedRows,
            'summaries' => $summaries,
            'filters' => $meta['filters'],
            'exportLinks' => $exportLinks,
            'hasRows' => $fullRows->isNotEmpty(),
            'resetUrl' => route($meta['route']),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $filters
     * @return array<string, string>
     */
    private function activeFilters(Request $request, array $filters): array
    {
        $active = [];

        foreach ($filters as $filter) {
            $name = (string) ($filter['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $raw = $request->query($name);
            if ($raw === null || trim((string) $raw) === '') {
                continue;
            }

            $label = (string) ($filter['label'] ?? $name);
            $value = (string) $raw;

            if (($filter['type'] ?? '') === 'select') {
                foreach (($filter['options'] ?? []) as $option) {
                    if ((string) ($option['value'] ?? '') === $value) {
                        $value = (string) ($option['label'] ?? $value);
                        break;
                    }
                }
            }

            $active[$label] = $value;
        }

        return $active;
    }
}
