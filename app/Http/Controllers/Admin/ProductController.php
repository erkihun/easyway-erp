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
use App\Models\UnitOfMeasure;
use App\Services\ProductService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'brand', 'unit'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = (string) $request->string('q');
                $query->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        if ($request->wantsJson()) {
            return ProductResource::collection($products);
        }

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => ProductCategory::query()->orderBy('name')->get(),
            'brands' => ProductBrand::query()->orderBy('name')->get(),
            'units' => UnitOfMeasure::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->create($request->validated());
        ActivityLogger::log('create_product', Product::class, $product->id, null, $product->toArray());

        return redirect()->route('admin.products.show', $product)->with('status', __('messages.product_created'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'unit', 'variants', 'images']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => ProductCategory::query()->orderBy('name')->get(),
            'brands' => ProductBrand::query()->orderBy('name')->get(),
            'units' => UnitOfMeasure::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $old = $product->toArray();
        $product = $this->productService->update($product, $request->validated());
        ActivityLogger::log('update_product', Product::class, $product->id, $old, $product->toArray());

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


