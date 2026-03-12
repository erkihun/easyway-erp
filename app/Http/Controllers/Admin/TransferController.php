<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\StoreTransferRequest;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\Warehouse;
use App\Services\ActivityLogService;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(): View
    {
        $transfers = Transfer::query()->with(['sourceWarehouse', 'destinationWarehouse', 'items.product'])->latest()->paginate(20);

        return view('admin.transfers.index', compact('transfers'));
    }

    public function create(): View
    {
        return view('admin.transfers.create', [
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTransferRequest $request): RedirectResponse
    {
        $transfer = $this->transferService->process($request->validated());
        $this->activityLogService->log('create_transfer', Transfer::class, $transfer->id, null, $transfer->toArray());

        return redirect()->route('admin.transfers.show', $transfer)->with('status', __('messages.transfer_completed'));
    }

    public function show(Transfer $transfer): View
    {
        $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'items.product']);

        $movements = \App\Models\StockMovement::query()
            ->where('reference_type', 'transfer')
            ->where('reference_id', $transfer->id)
            ->latest('created_at')
            ->get();

        return view('admin.transfers.show', compact('transfer', 'movements'));
    }
}


