<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StoreGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Services\ActivityLogService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GoodsReceiptController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(): View
    {
        $receipts = GoodsReceipt::query()->with(['purchaseOrder', 'warehouse'])->latest('received_at')->paginate(20);

        return view('admin.goods-receipts.index', compact('receipts'));
    }

    public function create(): View
    {
        $orders = PurchaseOrder::query()->with('items.product')->latest()->get();

        return view('admin.goods-receipts.create', [
            'orders' => $orders,
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreGoodsReceiptRequest $request): RedirectResponse
    {
        $receipt = $this->purchaseService->receiveGoods($request->validated());
        $this->activityLogService->log('create_goods_receipt', GoodsReceipt::class, $receipt->id, null, $receipt->toArray());

        return redirect()->route('admin.goods-receipts.show', $receipt)->with('status', __('messages.goods_receipt_posted'));
    }

    public function show(GoodsReceipt $goodsReceipt): View
    {
        $goodsReceipt->load(['purchaseOrder.items.product', 'warehouse']);

        return view('admin.goods-receipts.show', ['receipt' => $goodsReceipt]);
    }
}


