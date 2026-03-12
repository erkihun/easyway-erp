<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::query()->latest()->paginate(20);

        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create(): View
    {
        return view('admin.warehouses.create');
    }

    public function store(StoreWarehouseRequest $request): RedirectResponse
    {
        $warehouse = Warehouse::create($request->validated());

        return redirect()->route('admin.warehouses.show', $warehouse)->with('status', __('messages.warehouse_created'));
    }

    public function show(Warehouse $warehouse): View
    {
        $movementSummary = StockMovement::query()
            ->where('warehouse_id', $warehouse->id)
            ->selectRaw('movement_type, SUM(quantity) as qty')
            ->groupBy('movement_type')
            ->get();

        $recentMovements = StockMovement::query()
            ->with('product')
            ->where('warehouse_id', $warehouse->id)
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('admin.warehouses.show', compact('warehouse', 'movementSummary', 'recentMovements'));
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $warehouse->update($request->validated());

        return redirect()->route('admin.warehouses.show', $warehouse)->with('status', __('messages.warehouse_updated'));
    }
}


