<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSalesOrderRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Services\ActivityLogService;
use App\Services\SalesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesOrderController extends Controller
{
    public function __construct(
        private readonly SalesService $salesService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(): View
    {
        $orders = SalesOrder::query()->with(['customer', 'items.product'])->latest()->paginate(20);

        return view('admin.sales.index', compact('orders'));
    }

    public function create(): View
    {
        return view('admin.sales.create', [
            'customers' => Customer::query()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreSalesOrderRequest $request): RedirectResponse
    {
        $order = $this->salesService->confirmOrder($request->validated());
        $this->activityLogService->log('create_sales_order', SalesOrder::class, $order->id, null, $order->toArray());

        return redirect()->route('admin.sales.show', $order)->with('status', __('messages.sales_order_created'));
    }

    public function show(SalesOrder $sale): View
    {
        $sale->load(['customer', 'items.product']);

        $invoices = \App\Models\Invoice::query()->where('sales_order_id', $sale->id)->latest()->get();

        return view('admin.sales.show', [
            'order' => $sale,
            'invoices' => $invoices,
        ]);
    }
}


