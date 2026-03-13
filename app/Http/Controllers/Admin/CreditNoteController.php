<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreCreditNoteRequest;
use App\Http\Requests\Accounting\StoreRefundRequest;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Refund;
use App\Services\ActivityLogService;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CreditNoteController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly BillingService $billingService,
    ) {
    }

    public function index(Request $request): View
    {
        $creditNotes = CreditNote::query()
            ->with(['invoice', 'customer'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = (string) $request->string('q');
                $query->where('credit_note_number', 'like', '%'.$term.'%')
                    ->orWhereHas('invoice', fn ($invoiceQuery) => $invoiceQuery->where('invoice_number', 'like', '%'.$term.'%'))
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', '%'.$term.'%'));
            })
            ->latest('credit_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.credit-notes.index', [
            'creditNotes' => $creditNotes,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.credit-notes.create', [
            'invoices' => Invoice::query()->with('salesOrder.customer')->latest('invoice_date')->limit(100)->get(),
            'selectedInvoiceId' => (string) $request->string('invoice_id'),
        ]);
    }

    public function store(StoreCreditNoteRequest $request): RedirectResponse
    {
        $creditNote = DB::transaction(function () use ($request): CreditNote {
            $invoice = Invoice::query()->findOrFail($request->validated('invoice_id'));

            $creditNote = CreditNote::create([
                'credit_note_number' => 'CRN-'.now()->format('YmdHis'),
                'invoice_id' => $invoice->id,
                'customer_id' => $request->validated('customer_id') ?: $invoice->salesOrder?->customer_id,
                'credit_date' => $request->validated('credit_date'),
                'amount' => $request->validated('amount'),
                'reason' => $request->validated('reason'),
                'status' => 'issued',
                'created_by' => auth()->id(),
            ]);

            $this->billingService->recalculateInvoiceStatus($invoice);

            return $creditNote;
        });

        $this->activityLogService->log('create_credit_note', CreditNote::class, $creditNote->id, null, $creditNote->toArray());

        return redirect()->route('admin.credit-notes.show', $creditNote)->with('status', __('messages.saved'));
    }

    public function show(CreditNote $creditNote): View
    {
        return view('admin.credit-notes.show', [
            'creditNote' => $creditNote->load(['invoice.salesOrder.customer', 'refunds']),
        ]);
    }

    public function storeRefund(StoreRefundRequest $request): RedirectResponse
    {
        $refund = DB::transaction(function () use ($request): Refund {
            $invoice = Invoice::query()->findOrFail($request->validated('invoice_id'));

            $refund = Refund::create([
                'refund_number' => 'RFD-'.now()->format('YmdHis'),
                'credit_note_id' => $request->validated('credit_note_id'),
                'invoice_id' => $invoice->id,
                'customer_id' => $request->validated('customer_id') ?: $invoice->salesOrder?->customer_id,
                'refund_date' => $request->validated('refund_date'),
                'amount' => $request->validated('amount'),
                'method' => $request->validated('method'),
                'reason' => $request->validated('reason'),
                'created_by' => auth()->id(),
            ]);

            $invoice->paid_amount = max(0, (float) $invoice->paid_amount - (float) $refund->amount);
            $invoice->save();
            $this->billingService->recalculateInvoiceStatus($invoice);

            return $refund;
        });

        $this->activityLogService->log('create_refund', Refund::class, $refund->id, null, $refund->toArray());

        if ($refund->credit_note_id) {
            return redirect()
                ->route('admin.credit-notes.show', $refund->credit_note_id)
                ->with('status', __('messages.saved'));
        }

        return redirect()->route('admin.invoices.show', $refund->invoice_id)->with('status', __('messages.saved'));
    }
}
