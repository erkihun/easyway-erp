<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\ProductCategory\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_categories')->only(['index']);
        $this->middleware('permission:create_categories')->only(['create', 'store']);
        $this->middleware('permission:update_categories')->only(['edit', 'update']);
        $this->middleware('permission:delete_categories')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $categoriesQuery = ProductCategory::query()
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

        $categories = (clone $categoriesQuery)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $statsBase = ProductCategory::query();

        return view('admin.product-categories.index', [
            'categories' => $categories,
            'stats' => [
                'total' => (clone $statsBase)->count(),
                'active' => (clone $statsBase)->where('is_active', true)->count(),
                'inactive' => (clone $statsBase)->where('is_active', false)->count(),
                'linked_products' => (int) ProductCategory::query()->withCount('products')->get()->sum('products_count'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.product-categories.create');
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $category = ProductCategory::query()->create([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
            'slug' => $this->uniqueSlug((string) $payload['name']),
            'code' => $this->uniqueCode((string) $payload['name']),
        ]);

        ActivityLogger::log('create_category', ProductCategory::class, $category->id, null, $category->toArray());

        if ($request->boolean('quick_add')) {
            return redirect()->route('admin.product-categories.index', $request->only(['q', 'status']))
                ->with('status', __('messages.category_created'));
        }

        return redirect()->route('admin.product-categories.edit', $category)
            ->with('status', __('messages.category_created'));
    }

    public function edit(ProductCategory $productCategory): View
    {
        $productCategory->loadCount('products');

        return view('admin.product-categories.edit', ['category' => $productCategory]);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $old = $productCategory->toArray();
        $payload = $request->validated();

        $productCategory->update([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
            'slug' => $this->uniqueSlug((string) $payload['name'], (string) $productCategory->id),
            'code' => $this->uniqueCode((string) $payload['name'], (string) $productCategory->id),
        ]);

        ActivityLogger::log('update_category', ProductCategory::class, $productCategory->id, $old, $productCategory->fresh()?->toArray());

        return redirect()->route('admin.product-categories.edit', $productCategory)
            ->with('status', __('messages.category_updated'));
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        $old = $productCategory->toArray();
        $productCategory->delete();

        ActivityLogger::log('delete_category', ProductCategory::class, $old['id'], $old, null);

        return redirect()->route('admin.product-categories.index')
            ->with('status', __('messages.category_deleted'));
    }

    private function uniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'category';
        }

        $slug = $base;
        $counter = 1;
        while (ProductCategory::query()
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
            $base = 'CATEGORY';
        }

        $code = $base;
        $counter = 1;
        while (ProductCategory::query()
            ->where('code', $code)
            ->when($ignoreId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $code = "{$base}_{$counter}";
            $counter++;
        }

        return $code;
    }
}
