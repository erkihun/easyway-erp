<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreInvoiceRequest;
use App\Http\Requests\Accounting\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\ActivityLogService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly PaymentService $paymentService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(): View
    {
        $invoices = Invoice::query()->with('salesOrder')->latest()->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        return view('admin.invoices.create', [
            'salesOrders' => SalesOrder::query()->latest('order_date')->limit(100)->get(),
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->generate($request->validated());
        $this->activityLogService->log('create_invoice', Invoice::class, $invoice->id, null, $invoice->toArray());

        return redirect()->route('admin.invoices.show', $invoice)->with('status', __('messages.invoice_created'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['salesOrder', 'payments']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function recordPayment(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->paymentService->record($request->validated());
        $this->activityLogService->log('record_payment', Payment::class, $payment->id, null, $payment->toArray());

        return redirect()->route('admin.invoices.show', $payment->invoice_id)->with('status', __('messages.payment_recorded'));
    }

    public function pdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}


