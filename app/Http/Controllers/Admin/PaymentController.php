<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\ActivityLogService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with('invoice.salesOrder.customer')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = (string) $request->string('q');
                $query->where(function ($inner) use ($term): void {
                    $inner->where('payment_number', 'like', '%'.$term.'%')
                        ->orWhere('reference', 'like', '%'.$term.'%')
                        ->orWhereHas('invoice', fn ($invoiceQuery) => $invoiceQuery->where('invoice_number', 'like', '%'.$term.'%'));
                });
            })
            ->latest('payment_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
        ]);
    }

    public function create(Request $request): View
    {
        $invoices = Invoice::query()
            ->with('salesOrder.customer')
            ->latest('invoice_date')
            ->limit(100)
            ->get();

        return view('admin.payments.create', [
            'invoices' => $invoices,
            'selectedInvoiceId' => (string) $request->string('invoice_id'),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->paymentService->record($request->validated());
        $this->activityLogService->log('create_payment', Payment::class, $payment->id, null, $payment->toArray());

        return redirect()->route('admin.payments.show', $payment)->with('status', __('messages.payment_recorded'));
    }

    public function show(Payment $payment): View
    {
        return view('admin.payments.show', [
            'payment' => $payment->load('invoice.salesOrder.customer'),
        ]);
    }
}
