<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()->with('group')->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create', ['groups' => CustomerGroup::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'customer_group_id' => ['nullable', 'uuid', 'exists:customer_groups,id'],
        ]);

        $customer = Customer::create($data + ['is_active' => true]);

        return redirect()->route('admin.customers.edit', $customer)->with('status', __('messages.customer_created'));
    }

    public function edit(Customer $customer): View
    {
        $invoiceQuery = Invoice::query()
            ->whereHas('salesOrder', fn ($query) => $query->where('customer_id', $customer->id));
        $invoiceIds = (clone $invoiceQuery)->pluck('id');

        $totalInvoices = $invoiceIds->count();
        $totalPaid = (float) Payment::query()->whereIn('invoice_id', $invoiceIds)->sum('amount');
        $totalInvoiced = (float) (clone $invoiceQuery)->sum('total_amount');
        $totalCredits = (float) \App\Models\CreditNote::query()->whereIn('invoice_id', $invoiceIds)->sum('amount');
        $totalRefunds = (float) \App\Models\Refund::query()->whereIn('invoice_id', $invoiceIds)->sum('amount');
        $outstandingBalance = max(0, ($totalInvoiced - $totalCredits) - $totalPaid);
        $lastPayment = Payment::query()->whereIn('invoice_id', $invoiceIds)->latest('payment_date')->first();

        return view('admin.customers.edit', [
            'customer' => $customer,
            'groups' => CustomerGroup::query()->orderBy('name')->get(),
            'billingSummary' => [
                'total_invoices' => $totalInvoices,
                'total_paid' => $totalPaid,
                'outstanding_balance' => $outstandingBalance,
                'last_payment_date' => $lastPayment?->payment_date?->format('Y-m-d'),
                'total_refunds' => $totalRefunds,
            ],
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'customer_group_id' => ['nullable', 'uuid', 'exists:customer_groups,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customer->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.customers.index')->with('status', __('messages.customer_updated'));
    }
}


