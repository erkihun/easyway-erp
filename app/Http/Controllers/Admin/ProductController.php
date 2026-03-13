<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\UnitOfMeasure;
use App\Services\ProductService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
        $this->middleware('permission:view_products|create_products|update_products|delete_products')->only(['index', 'show', 'barcode']);
        $this->middleware('permission:create_products')->only(['create', 'store', 'import']);
        $this->middleware('permission:update_products')->only(['edit', 'update']);
        $this->middleware('permission:delete_products')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $productsQuery = Product::query()
            ->with([
                'category',
                'brand',
                'unit',
                'images' => static fn ($query) => $query->orderByDesc('is_primary')->latest(),
            ])
            ->withSum('productStocks as total_stock', 'cached_quantity')
            ->withSum('productStocks as total_reserved', 'reserved_quantity')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = (string) $request->string('q');
                $query->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%"));
            })
            ->when($request->filled('category_id'), function ($query) use ($request): void {
                $query->where('product_category_id', (string) $request->string('category_id'));
            })
            ->when($request->filled('brand_id'), function ($query) use ($request): void {
                $query->where('product_brand_id', (string) $request->string('brand_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $status = (string) $request->string('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                }
                if ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($request->filled('stock_status'), function ($query) use ($request): void {
                $stockStatus = (string) $request->string('stock_status');
                if ($stockStatus === 'in_stock') {
                    $query->havingRaw('COALESCE(total_stock, 0) > low_stock_threshold');
                }
                if ($stockStatus === 'low_stock') {
                    $query->havingRaw('COALESCE(total_stock, 0) > 0 AND COALESCE(total_stock, 0) <= low_stock_threshold');
                }
                if ($stockStatus === 'out_of_stock') {
                    $query->havingRaw('COALESCE(total_stock, 0) <= 0');
                }
            });

        $products = (clone $productsQuery)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stockAggregate = DB::table('product_stocks')
            ->select('product_id', DB::raw('SUM(cached_quantity) as total_stock'))
            ->groupBy('product_id');

        $stats = [
            'total' => Product::query()->count(),
            'active' => Product::query()->where('is_active', true)->count(),
            'low_stock' => (int) DB::table('products as p')
                ->leftJoinSub($stockAggregate, 's', static fn ($join) => $join->on('s.product_id', '=', 'p.id'))
                ->whereRaw('COALESCE(s.total_stock, 0) > 0 AND COALESCE(s.total_stock, 0) <= p.low_stock_threshold')
                ->count(),
            'out_of_stock' => (int) DB::table('products as p')
                ->leftJoinSub($stockAggregate, 's', static fn ($join) => $join->on('s.product_id', '=', 'p.id'))
                ->whereRaw('COALESCE(s.total_stock, 0) <= 0')
                ->count(),
        ];

        if ($request->wantsJson()) {
            return ProductResource::collection($products);
        }

        return view('admin.products.index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => ProductCategory::query()->orderBy('name')->get(['id', 'name']),
            'brands' => ProductBrand::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => ProductCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'brands' => ProductBrand::query()->where('is_active', true)->orderBy('name')->get(),
            'units' => UnitOfMeasure::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->create($request->validated());
        ActivityLogger::log('create_product', Product::class, $product->id, null, $product->toArray());

        if ($request->boolean('stay')) {
            return redirect()->route('admin.products.edit', $product)->with('status', __('messages.product_created'));
        }

        return redirect()->route('admin.products.show', $product)->with('status', __('messages.product_created'));
    }

    public function show(Product $product): View
    {
        $product->load([
            'category',
            'brand',
            'unit',
            'variants',
            'images' => static fn ($query) => $query->orderByDesc('is_primary')->latest(),
        ]);

        $stockByWarehouse = DB::table('product_stocks as ps')
            ->join('warehouses as w', 'w.id', '=', 'ps.warehouse_id')
            ->where('ps.product_id', $product->id)
            ->select(
                'ps.warehouse_id',
                'w.name as warehouse_name',
                'ps.cached_quantity',
                'ps.reserved_quantity',
                DB::raw('(ps.cached_quantity - ps.reserved_quantity) as available_quantity')
            )
            ->orderByDesc('ps.cached_quantity')
            ->orderBy('w.name')
            ->get();

        $recentMovements = StockMovement::query()
            ->with('warehouse')
            ->where('product_id', $product->id)
            ->latest('created_at')
            ->limit(12)
            ->get();

        $avgCostByWarehouse = DB::table('cost_layers')
            ->select('warehouse_id', DB::raw('AVG(unit_cost) as avg_cost'))
            ->where('product_id', $product->id)
            ->groupBy('warehouse_id');

        $inventoryValue = (float) DB::table('product_stocks as ps')
            ->leftJoinSub($avgCostByWarehouse, 'c', static fn ($join) => $join->on('c.warehouse_id', '=', 'ps.warehouse_id'))
            ->where('ps.product_id', $product->id)
            ->selectRaw('SUM(ps.cached_quantity * COALESCE(c.avg_cost, ?, 0)) as total', [(float) $product->cost_price])
            ->value('total');

        $metrics = [
            'total_stock' => (float) $stockByWarehouse->sum('cached_quantity'),
            'warehouses_holding' => (int) $stockByWarehouse->where('cached_quantity', '>', 0)->count(),
            'low_stock_threshold' => (float) $product->low_stock_threshold,
            'inventory_value' => $inventoryValue,
        ];

        $barcodeHtml = null;
        if ((string) $product->barcode !== '' || (string) $product->sku !== '') {
            $barcodeHtml = \DNS1D::getBarcodeHTML($product->barcode ?: $product->sku, 'C128');
        }

        return view('admin.products.show', compact('product', 'stockByWarehouse', 'recentMovements', 'metrics', 'barcodeHtml'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => ProductCategory::query()
                ->where(function ($query) use ($product): void {
                    $query->where('is_active', true);
                    if ($product->product_category_id) {
                        $query->orWhere('id', $product->product_category_id);
                    }
                })
                ->orderBy('name')
                ->get(),
            'brands' => ProductBrand::query()
                ->where(function ($query) use ($product): void {
                    $query->where('is_active', true);
                    if ($product->product_brand_id) {
                        $query->orWhere('id', $product->product_brand_id);
                    }
                })
                ->orderBy('name')
                ->get(),
            'units' => UnitOfMeasure::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $old = $product->toArray();
        $product = $this->productService->update($product, $request->validated());
        ActivityLogger::log('update_product', Product::class, $product->id, $old, $product->toArray());

        if ($request->boolean('stay')) {
            return redirect()->route('admin.products.edit', $product)->with('status', __('messages.product_updated'));
        }

        return redirect()->route('admin.products.show', $product)->with('status', __('messages.product_updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $old = $product->toArray();
        $product->delete();
        ActivityLogger::log('delete_product', Product::class, $old['id'], $old, null);

        return redirect()->route('admin.products.index')->with('status', __('messages.product_deleted'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls'],
        ]);

        Excel::import(app(ProductImport::class), $request->file('file'));

        return redirect()->route('admin.products.index')->with('status', __('messages.products_imported'));
    }

    public function export()
    {
        return Excel::download(new ProductExport(), 'products.xlsx');
    }

    public function barcode(Product $product): View
    {
        $barcode = \DNS1D::getBarcodeHTML($product->barcode ?: $product->sku, 'C128');

        return view('admin.products.barcode', compact('product', 'barcode'));
    }
}


