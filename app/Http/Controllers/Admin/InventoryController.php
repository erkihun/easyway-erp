<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $stockRows = $this->baseStockQuery($request)
            ->paginate(30);
        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name']);

        $warehouseDistribution = DB::table('product_stocks as ps')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->select('w.id', 'w.name', DB::raw('SUM(ps.cached_quantity) as qty'))
            ->groupBy('w.id', 'w.name')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        $highValueProducts = DB::table('product_stocks as ps')
            ->join('products as p', 'p.id', '=', 'ps.product_id')
            ->leftJoinSub(
                DB::table('cost_layers')
                    ->select('product_id', 'warehouse_id', DB::raw('AVG(unit_cost) as avg_cost'))
                    ->groupBy('product_id', 'warehouse_id'),
                'cl',
                static fn ($join) => $join
                    ->on('cl.product_id', '=', 'ps.product_id')
                    ->on('cl.warehouse_id', '=', 'ps.warehouse_id')
            )
            ->select('p.id', 'p.name', 'p.sku', DB::raw('SUM(ps.cached_quantity * COALESCE(cl.avg_cost,0)) as value'))
            ->groupBy('p.id', 'p.name', 'p.sku')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        $recentMovements = StockMovement::query()
            ->with(['product', 'warehouse'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        $recentAdjustments = StockAdjustment::query()
            ->with(['product', 'warehouse'])
            ->latest()
            ->limit(8)
            ->get();

        $lowStockItems = DB::table('product_stocks as ps')
            ->join('products as p', 'p.id', '=', 'ps.product_id')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->select(
                'p.id',
                'p.name',
                'p.sku',
                'w.name as warehouse_name',
                'p.low_stock_threshold',
                'ps.cached_quantity',
                DB::raw('(p.low_stock_threshold - ps.cached_quantity) as shortage')
            )
            ->whereColumn('ps.cached_quantity', '<=', 'p.low_stock_threshold')
            ->orderByDesc('shortage')
            ->limit(10)
            ->get();

        $kpis = [
            'total_stock_items' => (int) DB::table('product_stocks')->where('cached_quantity', '>', 0)->count(),
            'total_quantity_on_hand' => (float) DB::table('stock_movements')->sum('quantity'),
            'low_stock_products' => (int) DB::table('product_stocks as ps')
                ->join('products as p', 'p.id', '=', 'ps.product_id')
                ->whereColumn('ps.cached_quantity', '<=', 'p.low_stock_threshold')
                ->where('ps.cached_quantity', '>', 0)
                ->distinct('ps.product_id')
                ->count('ps.product_id'),
            'out_of_stock_products' => (int) DB::table('product_stocks as ps')
                ->where('ps.cached_quantity', '<=', 0)
                ->distinct('ps.product_id')
                ->count('ps.product_id'),
            'warehouses_with_stock' => (int) DB::table('product_stocks')
                ->where('cached_quantity', '>', 0)
                ->distinct('warehouse_id')
                ->count('warehouse_id'),
            'inventory_value' => (float) DB::table('product_stocks as ps')
                ->leftJoinSub(
                    DB::table('cost_layers')
                        ->select('product_id', 'warehouse_id', DB::raw('AVG(unit_cost) as avg_cost'))
                        ->groupBy('product_id', 'warehouse_id'),
                    'cl',
                    static fn ($join) => $join
                        ->on('cl.product_id', '=', 'ps.product_id')
                        ->on('cl.warehouse_id', '=', 'ps.warehouse_id')
                )
                ->selectRaw('SUM(ps.cached_quantity * COALESCE(cl.avg_cost,0)) as value')
                ->value('value') ?? 0.0,
        ];

        return view('admin.inventory.index', compact(
            'stockRows',
            'warehouses',
            'warehouseDistribution',
            'highValueProducts',
            'recentMovements',
            'recentAdjustments',
            'lowStockItems',
            'kpis'
        ));
    }

    public function ledger(Request $request): View
    {
        $movements = $this->baseLedgerQuery($request)->paginate(40)->withQueryString();
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'sku']);
        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.inventory.ledger', compact('movements', 'products', 'warehouses'));
    }

    public function movements(Request $request): View
    {
        $movements = $this->baseLedgerQuery($request)->paginate(30)->withQueryString();
        $movementTotals = DB::table('stock_movements')
            ->select('movement_type', DB::raw('SUM(quantity) as qty'))
            ->groupBy('movement_type')
            ->orderByDesc(DB::raw('ABS(SUM(quantity))'))
            ->limit(8)
            ->get();

        return view('admin.inventory.movements', compact('movements', 'movementTotals'));
    }

    public function warehouses(Request $request): View
    {
        $rows = $this->baseStockQuery($request)
            ->orderBy('warehouses.name')
            ->orderBy('products.name')
            ->paginate(40)
            ->withQueryString();

        $warehouseSummary = DB::table('product_stocks as ps')
            ->join('warehouses', 'warehouses.id', '=', 'ps.warehouse_id')
            ->select(
                'warehouses.id',
                'warehouses.name',
                DB::raw('COUNT(ps.id) as item_count'),
                DB::raw('SUM(ps.cached_quantity) as on_hand'),
                DB::raw('SUM(ps.reserved_quantity) as reserved'),
                DB::raw('SUM(ps.cached_quantity - ps.reserved_quantity) as available')
            )
            ->groupBy('warehouses.id', 'warehouses.name')
            ->orderBy('warehouses.name')
            ->get();

        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.inventory.warehouses', compact('rows', 'warehouseSummary', 'warehouses'));
    }

    public function lowStock(Request $request): View
    {
        $rows = DB::table('product_stocks as ps')
            ->join('products as p', 'p.id', '=', 'ps.product_id')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->select(
                'p.id',
                'p.sku',
                'p.name',
                'p.low_stock_threshold',
                'w.name as warehouse_name',
                'ps.cached_quantity as stock',
                DB::raw('GREATEST(p.low_stock_threshold - ps.cached_quantity, 0) as shortage')
            )
            ->whereColumn('ps.cached_quantity', '<=', 'p.low_stock_threshold')
            ->when((string) $request->query('q') !== '', static function ($query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(static function ($sub) use ($term): void {
                    $sub->where('p.name', 'like', $term)
                        ->orWhere('p.sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id') !== '', static function ($query) use ($request): void {
                $query->where('w.id', (string) $request->query('warehouse_id'));
            })
            ->orderByDesc('shortage')
            ->orderBy('p.name')
            ->paginate(40)
            ->withQueryString();

        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.inventory.low-stock', compact('rows', 'warehouses'));
    }

    public function adjustments(Request $request): View
    {
        $adjustments = StockAdjustment::query()
            ->with(['product', 'warehouse', 'creator'])
            ->when((string) $request->query('q') !== '', static function ($query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where('reason', 'like', $term);
            })
            ->when((string) $request->query('product_id') !== '', static function ($query) use ($request): void {
                $query->where('product_id', (string) $request->query('product_id'));
            })
            ->when((string) $request->query('warehouse_id') !== '', static function ($query) use ($request): void {
                $query->where('warehouse_id', (string) $request->query('warehouse_id'));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();
        $products = Product::query()->orderBy('name')->get();
        $warehouses = Warehouse::query()->orderBy('name')->get();

        return view('admin.inventory.adjustments', compact('adjustments', 'products', 'warehouses'));
    }

    public function valuation(Request $request): View
    {
        $base = DB::table('product_stocks as ps')
            ->join('products as p', 'p.id', '=', 'ps.product_id')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->leftJoinSub(
                DB::table('cost_layers')
                    ->select('product_id', 'warehouse_id', DB::raw('AVG(unit_cost) as avg_cost'))
                    ->groupBy('product_id', 'warehouse_id'),
                'cl',
                static fn ($join) => $join
                    ->on('cl.product_id', '=', 'ps.product_id')
                    ->on('cl.warehouse_id', '=', 'ps.warehouse_id')
            )
            ->select(
                'ps.product_id',
                'ps.warehouse_id',
                'p.name as product_name',
                'p.sku',
                'w.name as warehouse_name',
                'ps.cached_quantity as quantity',
                DB::raw('COALESCE(cl.avg_cost, 0) as unit_cost'),
                DB::raw('(ps.cached_quantity * COALESCE(cl.avg_cost, 0)) as value')
            )
            ->when((string) $request->query('q') !== '', static function ($query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(static function ($sub) use ($term): void {
                    $sub->where('p.name', 'like', $term)
                        ->orWhere('p.sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id') !== '', static function ($query) use ($request): void {
                $query->where('w.id', (string) $request->query('warehouse_id'));
            });

        $rows = (clone $base)
            ->orderByDesc('value')
            ->paginate(40)
            ->withQueryString();

        $warehouseValues = (clone $base)
            ->select(
                'ps.warehouse_id as warehouse_id',
                'w.name as warehouse_name',
                DB::raw('SUM(ps.cached_quantity * COALESCE(cl.avg_cost, 0)) as total_value'),
                DB::raw('SUM(ps.cached_quantity) as total_qty')
            )
            ->groupBy('ps.warehouse_id', 'w.name')
            ->orderByDesc('total_value')
            ->get();

        $productValues = (clone $base)
            ->select(
                'ps.product_id as product_id',
                'p.name as product_name',
                'p.sku as sku',
                DB::raw('SUM(ps.cached_quantity * COALESCE(cl.avg_cost, 0)) as total_value'),
                DB::raw('SUM(ps.cached_quantity) as total_qty')
            )
            ->groupBy('ps.product_id', 'p.name', 'p.sku')
            ->orderByDesc('total_value')
            ->limit(20)
            ->get();

        $totalValue = (float) $warehouseValues->sum('total_value');
        $totalQty = (float) $warehouseValues->sum('total_qty');
        $avgUnitCost = $totalQty > 0 ? $totalValue / $totalQty : 0.0;
        $topWarehouse = $warehouseValues->first();
        $topProduct = $productValues->first();

        $kpis = [
            'total_value' => $totalValue,
            'avg_unit_cost' => $avgUnitCost,
            'top_warehouse_name' => (string) ($topWarehouse->warehouse_name ?? '-'),
            'top_warehouse_value' => (float) ($topWarehouse->total_value ?? 0),
            'top_product_name' => (string) ($topProduct->product_name ?? '-'),
            'top_product_value' => (float) ($topProduct->total_value ?? 0),
        ];

        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.inventory.valuation', compact('rows', 'warehouseValues', 'productValues', 'kpis', 'warehouses'));
    }

    public function adjust(Request $request, InventoryService $inventoryService): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'quantity_delta' => ['required', 'numeric', 'not_in:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $adjustment = StockAdjustment::create([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'quantity_delta' => $data['quantity_delta'],
            'reason' => $data['reason'],
            'created_by' => auth()->id(),
        ]);

        $inventoryService->adjustStock(
            $data['product_id'],
            $data['warehouse_id'],
            (float) $data['quantity_delta'],
            $data['reason'],
            $adjustment->id
        );

        ActivityLogger::log('adjust_stock', StockAdjustment::class, $adjustment->id, null, $adjustment->toArray());

        return redirect()->route('admin.inventory.adjustments')->with('status', __('messages.stock_adjusted'));
    }

    private function baseStockQuery(Request $request)
    {
        return DB::table('product_stocks')
            ->join('products', 'products.id', '=', 'product_stocks.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'product_stocks.warehouse_id')
            ->select(
                'product_stocks.*',
                'products.name as product_name',
                'products.sku',
                'products.low_stock_threshold',
                'warehouses.name as warehouse_name'
            )
            ->when((string) $request->query('q') !== '', static function ($query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(static function ($sub) use ($term): void {
                    $sub->where('products.name', 'like', $term)
                        ->orWhere('products.sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id') !== '', static function ($query) use ($request): void {
                $query->where('warehouses.id', (string) $request->query('warehouse_id'));
            })
            ->orderBy('products.name');
    }

    private function baseLedgerQuery(Request $request)
    {
        return StockMovement::query()
            ->with(['product', 'warehouse', 'creator'])
            ->when((string) $request->query('q') !== '', static function ($query) use ($request): void {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->whereHas('product', static function ($productQuery) use ($term): void {
                    $productQuery->where('name', 'like', $term)->orWhere('sku', 'like', $term);
                });
            })
            ->when((string) $request->query('warehouse_id') !== '', static function ($query) use ($request): void {
                $query->where('warehouse_id', (string) $request->query('warehouse_id'));
            })
            ->when((string) $request->query('product_id') !== '', static function ($query) use ($request): void {
                $query->where('product_id', (string) $request->query('product_id'));
            })
            ->when((string) $request->query('movement_type') !== '', static function ($query) use ($request): void {
                $query->where('movement_type', (string) $request->query('movement_type'));
            })
            ->when((string) $request->query('date_from') !== '', static function ($query) use ($request): void {
                $query->whereDate('created_at', '>=', (string) $request->query('date_from'));
            })
            ->when((string) $request->query('date_to') !== '', static function ($query) use ($request): void {
                $query->whereDate('created_at', '<=', (string) $request->query('date_to'));
            })
            ->latest('created_at');
    }
}


