<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacturing\StoreBomRequest;
use App\Http\Requests\Manufacturing\StoreProductionOrderRequest;
use App\Models\Bom;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use App\Services\ProductionService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ManufacturingController extends Controller
{
    public function __construct(private readonly ProductionService $productionService)
    {
    }

    public function index(): View
    {
        return view('admin.manufacturing.index', [
            'ordersCount' => ProductionOrder::query()->count(),
            'bomCount' => Bom::query()->count(),
        ]);
    }

    public function bomsIndex(): View
    {
        $boms = Bom::query()->with(['product', 'items.component'])->latest()->paginate(20);

        return view('admin.manufacturing.boms.index', compact('boms'));
    }

    public function bomsCreate(): View
    {
        return view('admin.manufacturing.boms.create', [
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function bomsStore(StoreBomRequest $request): RedirectResponse
    {
        $bom = Bom::create($request->safe()->only(['product_id', 'code', 'name']));
        foreach ($request->validated('items') as $item) {
            $bom->items()->create($item);
        }

        return redirect()->route('admin.manufacturing.boms.index')->with('status', __('messages.bom_created'));
    }

    public function productionOrdersIndex(): View
    {
        $orders = ProductionOrder::query()->with(['bom', 'product', 'warehouse'])->latest()->paginate(20);

        return view('admin.manufacturing.production-orders.index', compact('orders'));
    }

    public function productionOrdersCreate(): View
    {
        return view('admin.manufacturing.production-orders.create', [
            'boms' => Bom::query()->with('product')->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    public function productionOrdersStore(StoreProductionOrderRequest $request): RedirectResponse
    {
        $order = $this->productionService->createOrder($request->validated());
        ActivityLogger::log('create_production_order', ProductionOrder::class, $order->id, null, $order->toArray());

        return redirect()->route('admin.manufacturing.production-orders.show', $order)->with('status', __('messages.production_order_created'));
    }

    public function productionOrdersShow(ProductionOrder $productionOrder): View
    {
        $productionOrder->load(['bom.items.component', 'product', 'warehouse']);
        return view('admin.manufacturing.production-orders.show', ['order' => $productionOrder]);
    }

    public function complete(ProductionOrder $productionOrder): RedirectResponse
    {
        $order = $this->productionService->completeOrder($productionOrder);
        ActivityLogger::log('complete_production_order', ProductionOrder::class, $order->id, null, $order->toArray());

        return redirect()->route('admin.manufacturing.production-orders.show', $order)->with('status', __('messages.production_completed'));
    }
}


