<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreInvoiceRequest;
use App\Http\Requests\Accounting\StorePaymentRequest;
use App\Http\Requests\Accounting\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\ActivityLogService;
use App\Services\BillingService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly PaymentService $paymentService,
        private readonly ActivityLogService $activityLogService,
        private readonly BillingService $billingService,
    ) {
    }

    public function index(Request $request): View
    {
        $invoicesQuery = Invoice::query()
            ->with(['salesOrder.customer'])
            ->withSum('creditNotes as credit_total', 'amount')
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = (string) $request->string('q');
                $query->where(function (Builder $inner) use ($term): void {
                    $inner->where('invoice_number', 'like', '%'.$term.'%')
                        ->orWhereHas('salesOrder', function (Builder $salesOrderQuery) use ($term): void {
                            $salesOrderQuery->where('order_number', 'like', '%'.$term.'%')
                                ->orWhereHas('customer', function (Builder $customerQuery) use ($term): void {
                                    $customerQuery->where('name', 'like', '%'.$term.'%')
                                        ->orWhere('email', 'like', '%'.$term.'%');
                                });
                        });
                });
            })
            ->when($request->filled('status'), function (Builder $query) use ($request): void {
                $query->where('status', (string) $request->string('status'));
            })
            ->when($request->filled('from_date'), fn (Builder $query): Builder => $query->whereDate('invoice_date', '>=', (string) $request->string('from_date')))
            ->when($request->filled('to_date'), fn (Builder $query): Builder => $query->whereDate('invoice_date', '<=', (string) $request->string('to_date')));

        $invoices = $invoicesQuery->latest('invoice_date')->paginate(20)->withQueryString();

        $statsBase = Invoice::query()
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = (string) $request->string('q');
                $query->where('invoice_number', 'like', '%'.$term.'%');
            })
            ->when($request->filled('from_date'), fn (Builder $query): Builder => $query->whereDate('invoice_date', '>=', (string) $request->string('from_date')))
            ->when($request->filled('to_date'), fn (Builder $query): Builder => $query->whereDate('invoice_date', '<=', (string) $request->string('to_date')));

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'stats' => [
                'total' => (clone $statsBase)->count(),
                'draft' => (clone $statsBase)->where('status', 'draft')->count(),
                'paid' => (clone $statsBase)->where('status', 'paid')->count(),
                'overdue' => (clone $statsBase)->where('status', 'issued')->whereDate('due_date', '<', now()->toDateString())->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $salesOrders = SalesOrder::query()
            ->with(['customer', 'items.product'])
            ->latest('order_date')
            ->limit(100)
            ->get();

        return view('admin.invoices.create', [
            'salesOrders' => $salesOrders,
            'salesOrdersPayload' => $salesOrders->map(function (SalesOrder $order): array {
                return [
                    'id' => (string) $order->id,
                    'order_number' => (string) $order->order_number,
                    'customer_name' => (string) ($order->customer?->name ?? ''),
                    'customer_email' => (string) ($order->customer?->email ?? ''),
                    'currency' => 'ETB',
                    'subtotal' => (float) $order->subtotal,
                    'tax_total' => (float) $order->tax_amount,
                    'discount_total' => (float) $order->discount_amount,
                    'total' => (float) $order->total_amount,
                    'items' => $order->items->map(fn ($item): array => [
                        'product' => (string) ($item->product?->name ?? ''),
                        'description' => (string) ($item->product?->description ?? ''),
                        'qty' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'tax' => (float) $item->tax_amount,
                        'discount' => (float) $item->discount_amount,
                        'subtotal' => (float) $item->line_total,
                    ])->values()->all(),
                ];
            })->values(),
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
        $invoice->load(['salesOrder.customer', 'salesOrder.items.product', 'payments', 'creditNotes', 'refunds']);

        $customer = $invoice->salesOrder?->customer;
        $lineItems = $invoice->salesOrder?->items ?? collect();
        $subTotal = (float) ($invoice->salesOrder?->subtotal ?? $invoice->total_amount);
        $taxTotal = (float) ($invoice->salesOrder?->tax_amount ?? 0);
        $discountTotal = (float) ($invoice->salesOrder?->discount_amount ?? 0);
        $creditNoteTotal = $this->billingService->creditTotal($invoice);
        $refundTotal = $this->billingService->refundTotal($invoice);
        $grandTotal = $this->billingService->effectiveTotal($invoice);
        $paidAmount = (float) $invoice->paid_amount;
        $balanceDue = $this->billingService->remainingBalance($invoice);

        return view('admin.invoices.show', compact(
            'invoice',
            'customer',
            'lineItems',
            'subTotal',
            'taxTotal',
            'discountTotal',
            'grandTotal',
            'paidAmount',
            'balanceDue',
            'creditNoteTotal',
            'refundTotal',
        ));
    }

    public function edit(Invoice $invoice): View
    {
        $salesOrders = SalesOrder::query()
            ->with(['customer', 'items.product'])
            ->latest('order_date')
            ->limit(100)
            ->get();

        return view('admin.invoices.edit', [
            'invoice' => $invoice->load('salesOrder.customer', 'salesOrder.items.product'),
            'salesOrders' => $salesOrders,
            'salesOrdersPayload' => $salesOrders->map(function (SalesOrder $order): array {
                return [
                    'id' => (string) $order->id,
                    'order_number' => (string) $order->order_number,
                    'customer_name' => (string) ($order->customer?->name ?? ''),
                    'customer_email' => (string) ($order->customer?->email ?? ''),
                    'currency' => 'ETB',
                    'subtotal' => (float) $order->subtotal,
                    'tax_total' => (float) $order->tax_amount,
                    'discount_total' => (float) $order->discount_amount,
                    'total' => (float) $order->total_amount,
                    'items' => $order->items->map(fn ($item): array => [
                        'product' => (string) ($item->product?->name ?? ''),
                        'description' => (string) ($item->product?->description ?? ''),
                        'qty' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'tax' => (float) $item->tax_amount,
                        'discount' => (float) $item->discount_amount,
                        'subtotal' => (float) $item->line_total,
                    ])->values()->all(),
                ];
            })->values(),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $before = $invoice->toArray();
        $data = $request->validated();

        $invoice->update($data);
        $this->billingService->recalculateInvoiceStatus($invoice);

        $this->activityLogService->log('update_invoice', Invoice::class, $invoice->id, $before, $invoice->fresh()?->toArray() ?? []);

        return redirect()->route('admin.invoices.show', $invoice)->with('status', __('messages.updated'));
    }

    public function recordPayment(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->paymentService->record($request->validated());
        $this->activityLogService->log('record_payment', Payment::class, $payment->id, null, $payment->toArray());

        return redirect()->route('admin.invoices.show', $payment->invoice_id)->with('status', __('messages.payment_recorded'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['salesOrder.customer', 'salesOrder.items.product', 'payments', 'creditNotes', 'refunds']);

        $customer = $invoice->salesOrder?->customer;
        $lineItems = $invoice->salesOrder?->items ?? collect();
        $subTotal = (float) ($invoice->salesOrder?->subtotal ?? $invoice->total_amount);
        $taxTotal = (float) ($invoice->salesOrder?->tax_amount ?? 0);
        $discountTotal = (float) ($invoice->salesOrder?->discount_amount ?? 0);
        $creditNoteTotal = $this->billingService->creditTotal($invoice);
        $refundTotal = $this->billingService->refundTotal($invoice);
        $grandTotal = $this->billingService->effectiveTotal($invoice);
        $paidAmount = (float) $invoice->paid_amount;
        $balanceDue = $this->billingService->remainingBalance($invoice);

        $pdf = Pdf::loadView('admin.invoices.pdf', compact(
            'invoice',
            'customer',
            'lineItems',
            'subTotal',
            'taxTotal',
            'discountTotal',
            'grandTotal',
            'paidAmount',
            'balanceDue',
            'creditNoteTotal',
            'refundTotal',
        ))->setPaper('a4', 'landscape');

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}


