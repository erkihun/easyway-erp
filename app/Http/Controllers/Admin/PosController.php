<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PosSessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\ClosePosSessionRequest;
use App\Http\Requests\Pos\OpenPosSessionRequest;
use App\Http\Requests\Pos\StorePosOrderRequest;
use App\Models\Customer;
use App\Models\PosOrder;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\PosService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(private readonly PosService $posService)
    {
    }

    public function index(): View
    {
        return view('admin.pos.index', [
            'sessions' => PosSession::query()->with(['warehouse', 'user'])->latest()->paginate(20),
            'orders' => PosOrder::query()->latest()->paginate(20),
        ]);
    }

    public function sessionPage(): View
    {
        return view('admin.pos.session', [
            'sessions' => PosSession::query()->with('warehouse')->latest()->paginate(20),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    public function checkoutPage(): View
    {
        return view('admin.pos.checkout', [
            'sessions' => PosSession::query()->where('status', PosSessionStatus::Open)->get(),
            'products' => Product::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
            'customers' => Customer::query()->orderBy('name')->get(),
        ]);
    }

    public function openSession(OpenPosSessionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $session = PosSession::create([
            'warehouse_id' => $data['warehouse_id'],
            'user_id' => auth()->id(),
            'status' => PosSessionStatus::Open,
            'opening_amount' => $data['opening_amount'],
            'opened_at' => now(),
        ]);

        ActivityLogger::log('open_pos_session', PosSession::class, $session->id, null, $session->toArray());

        return redirect()->route('admin.pos.session')->with('status', __('messages.pos_session_opened'));
    }

    public function closeSession(ClosePosSessionRequest $request, PosSession $session): RedirectResponse
    {
        $session->update([
            'status' => PosSessionStatus::Closed,
            'closing_amount' => $session->orders()->sum('total_amount'),
            'closed_at' => now(),
        ]);

        ActivityLogger::log('close_pos_session', PosSession::class, $session->id, null, $session->toArray());

        return redirect()->route('admin.pos.session')->with('status', __('messages.pos_session_closed'));
    }

    public function checkout(StorePosOrderRequest $request): RedirectResponse
    {
        $order = $this->posService->checkout($request->validated());
        ActivityLogger::log('create_pos_order', PosOrder::class, $order->id, null, $order->toArray());

        return redirect()->route('admin.pos.index')->with('status', __('messages.pos_checkout_completed'));
    }
}


