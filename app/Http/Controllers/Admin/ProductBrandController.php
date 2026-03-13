<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductBrand\StoreProductBrandRequest;
use App\Http\Requests\ProductBrand\UpdateProductBrandRequest;
use App\Models\ProductBrand;
use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_brands')->only(['index']);
        $this->middleware('permission:create_brands')->only(['create', 'store']);
        $this->middleware('permission:update_brands')->only(['edit', 'update']);
        $this->middleware('permission:delete_brands')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $brandsQuery = ProductBrand::query()
            ->withCount('products')
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = (string) $request->string('q');
                $query->where(function (Builder $inner) use ($term): void {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), function (Builder $query) use ($request): void {
                $status = (string) $request->string('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                }
                if ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            });

        $brands = (clone $brandsQuery)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $statsBase = ProductBrand::query();

        return view('admin.product-brands.index', [
            'brands' => $brands,
            'stats' => [
                'total' => (clone $statsBase)->count(),
                'active' => (clone $statsBase)->where('is_active', true)->count(),
                'inactive' => (clone $statsBase)->where('is_active', false)->count(),
                'linked_products' => (int) ProductBrand::query()->withCount('products')->get()->sum('products_count'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.product-brands.create');
    }

    public function store(StoreProductBrandRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $brand = ProductBrand::query()->create([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
            'slug' => $this->uniqueSlug((string) $payload['name']),
            'code' => $this->uniqueCode((string) $payload['name']),
        ]);

        ActivityLogger::log('create_brand', ProductBrand::class, $brand->id, null, $brand->toArray());

        if ($request->boolean('quick_add')) {
            return redirect()->route('admin.product-brands.index', $request->only(['q', 'status']))
                ->with('status', __('messages.brand_created'));
        }

        return redirect()->route('admin.product-brands.edit', $brand)
            ->with('status', __('messages.brand_created'));
    }

    public function edit(ProductBrand $productBrand): View
    {
        $productBrand->loadCount('products');

        return view('admin.product-brands.edit', ['brand' => $productBrand]);
    }

    public function update(UpdateProductBrandRequest $request, ProductBrand $productBrand): RedirectResponse
    {
        $old = $productBrand->toArray();
        $payload = $request->validated();

        $productBrand->update([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
            'slug' => $this->uniqueSlug((string) $payload['name'], (string) $productBrand->id),
            'code' => $this->uniqueCode((string) $payload['name'], (string) $productBrand->id),
        ]);

        ActivityLogger::log('update_brand', ProductBrand::class, $productBrand->id, $old, $productBrand->fresh()?->toArray());

        return redirect()->route('admin.product-brands.edit', $productBrand)
            ->with('status', __('messages.brand_updated'));
    }

    public function destroy(ProductBrand $productBrand): RedirectResponse
    {
        $old = $productBrand->toArray();
        $productBrand->delete();

        ActivityLogger::log('delete_brand', ProductBrand::class, $old['id'], $old, null);

        return redirect()->route('admin.product-brands.index')
            ->with('status', __('messages.brand_deleted'));
    }

    private function uniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'brand';
        }

        $slug = $base;
        $counter = 1;
        while (ProductBrand::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function uniqueCode(string $name, ?string $ignoreId = null): string
    {
        $base = Str::upper(Str::snake(Str::slug($name, ' ')));
        if ($base === '') {
            $base = 'BRAND';
        }

        $code = $base;
        $counter = 1;
        while (ProductBrand::query()
            ->where('code', $code)
            ->when($ignoreId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $code = "{$base}_{$counter}";
            $counter++;
        }

        return $code;
    }
}
