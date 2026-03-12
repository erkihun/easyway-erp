@extends('layouts.admin')
@section('title','Invoice Details')
@section('page-title', __('sales.invoice_details'))
@section('content')
<x-ui.page-header title="Invoice {{ $invoice->invoice_number }}" subtitle="Track payment and export actions." icon="heroicon-o-document-text">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.invoices.index')">{{ __('common.back') }}</x-ui.button>
            <x-ui.button variant="secondary" icon="heroicon-o-arrow-down-tray" :href="route('admin.invoices.pdf',$invoice)">{{ __('reports.export_pdf') }}</x-ui.button>
            @if($invoice->salesOrder)
                <x-ui.button variant="ghost" icon="heroicon-o-banknotes" :href="route('admin.sales.show',$invoice->salesOrder)">{{ __('dashboard.order') }}</x-ui.button>
            @endif
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">
    <x-ui.smart-button label="Status" :value="strtoupper((string)($invoice->status->value ?? $invoice->status))" icon="heroicon-o-flag" />
    <x-ui.smart-button label="Total" :value="number_format((float)$invoice->total_amount,2)" icon="heroicon-o-banknotes" />
    <x-ui.smart-button label="Paid" :value="number_format((float)$invoice->paid_amount,2)" icon="heroicon-o-check-badge" variant="success" />
    <x-ui.smart-button label="Payments" :value="$invoice->payments->count()" icon="heroicon-o-credit-card" />
</div>

<div class="panel mb-1"><div class="panel-body"><h3 style="margin-top:0;">{{ __('sales.record_payment') }}</h3><form method="POST" action="{{ route('admin.invoices.payments') }}">@csrf<input type="hidden" name="invoice_id" value="{{ $invoice->id }}"><div class="row"><x-ui.input type="date" name="payment_date" :label="__('common.date')" :value="now()->toDateString()" required /><x-ui.input name="amount" :label="__('common.amount')" type="number" step="0.0001" required /><x-ui.input name="method" :label="__('sales.payment_method')" value="cash" required /></div><div class="form-actions-sticky mt-1"><x-ui.button variant="ghost" :href="route('admin.invoices.index')">{{ __('common.cancel') }}</x-ui.button><x-ui.button variant="success" type="submit" icon="heroicon-o-credit-card">{{ __('sales.record_payment') }}</x-ui.button></div></form></div></div>

<div class="panel"><div class="panel-body"><h3 style="margin-top:0;">{{ __('sales.payments') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('common.no') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.date') }}</th><th style="text-align:right;">{{ __('common.amount') }}</th></tr></thead><tbody>@forelse($invoice->payments as $p)<tr><td>{{ $p->payment_number }}</td><td><x-ui.status-badge :status="$p->status" /></td><td>{{ $p->payment_date }}</td><td style="text-align:right;">{{ number_format((float)$p->amount,2) }}</td></tr>@empty<tr><td colspan="4" class="muted">{{ __('sales.no_payments') }}</td></tr>@endforelse</tbody></table></div></div></div>
@endsection





