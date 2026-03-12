<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\ActivityLogService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(): View
    {
        $orders = PurchaseOrder::query()->with(['items.product'])->latest()->paginate(20);

        return view('admin.purchases.index', compact('orders'));
    }

    public function create(): View
    {
        return view('admin.purchases.create', [
            'suppliers' => Supplier::query()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $order = $this->purchaseService->createOrder($request->validated());
        $this->activityLogService->log('create_purchase_order', PurchaseOrder::class, $order->id, null, $order->toArray());

        return redirect()->route('admin.purchases.show', $order)->with('status', __('messages.purchase_order_created'));
    }

    public function show(PurchaseOrder $purchase): View
    {
        $purchase->load(['items.product']);

        $receipts = \App\Models\GoodsReceipt::query()
            ->with('warehouse')
            ->where('purchase_order_id', $purchase->id)
            ->latest('received_at')
            ->get();

        return view('admin.purchases.show', [
            'order' => $purchase,
            'receipts' => $receipts,
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }
}


