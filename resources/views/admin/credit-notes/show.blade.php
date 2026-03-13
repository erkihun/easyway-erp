@extends('layouts.admin')
@section('title', __('credit_note.credit_note_details'))
@section('page-title', __('credit_note.credit_note_details'))
@section('page-subtitle', __('credit_note.subtitle'))
@section('content')
<x-ui.page-header :title="__('credit_note.credit_note_number').': '.$creditNote->credit_note_number" :subtitle="__('credit_note.subtitle')" icon="heroicon-o-receipt-percent">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.credit-notes.index')">{{ __('common.back') }}</x-ui.button>
            @if($creditNote->invoice)
                <x-ui.button variant="secondary" :href="route('admin.invoices.show', $creditNote->invoice)">{{ __('invoice.invoice_details') }}</x-ui.button>
            @endif
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid gap-4 lg:grid-cols-2 mb-1">
    <div class="panel">
        <div class="panel-body">
            <div class="grid gap-2 md:grid-cols-2">
                <div><span class="muted">{{ __('credit_note.customer') }}:</span> {{ $creditNote->customer?->name ?? '-' }}</div>
                <div><span class="muted">{{ __('credit_note.invoice_reference') }}:</span> {{ $creditNote->invoice?->invoice_number ?? '-' }}</div>
                <div><span class="muted">{{ __('credit_note.credit_date') }}:</span> {{ $creditNote->credit_date?->format('Y-m-d') }}</div>
                <div><span class="muted">{{ __('credit_note.amount') }}:</span> {{ number_format((float) $creditNote->amount, 2) }}</div>
                <div class="md:col-span-2"><span class="muted">{{ __('credit_note.reason') }}:</span> {{ $creditNote->reason ?: '-' }}</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <h3 style="margin:0 0 .75rem 0;">{{ __('credit_note.create_refund') }}</h3>
            <form method="POST" action="{{ route('admin.refunds.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="credit_note_id" value="{{ $creditNote->id }}">
                <input type="hidden" name="invoice_id" value="{{ $creditNote->invoice_id }}">
                <input type="hidden" name="customer_id" value="{{ $creditNote->customer_id }}">
                <div class="row">
                    <x-ui.input type="date" name="refund_date" :label="__('refund.date')" :value="now()->toDateString()" required />
                    <x-ui.input type="number" step="0.0001" min="0.01" name="amount" :label="__('refund.amount')" :value="$creditNote->amount" required />
                    <x-ui.select name="method" :label="__('refund.method')" required>
                        <option value="cash">{{ __('payment.cash') }}</option>
                        <option value="bank_transfer">{{ __('payment.bank_transfer') }}</option>
                        <option value="credit_card">{{ __('payment.credit_card') }}</option>
                        <option value="mobile_payment">{{ __('payment.mobile_payment') }}</option>
                    </x-ui.select>
                </div>
                <x-ui.textarea name="reason" :label="__('refund.reason')" rows="2" />
                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="danger">{{ __('credit_note.create_refund') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-body">
        <h3 style="margin:0 0 .75rem 0;">{{ __('credit_note.refunds') }}</h3>
        <x-ui.table compact>
            <thead>
                <tr>
                    <th>{{ __('credit_note.refund_number') }}</th>
                    <th>{{ __('refund.date') }}</th>
                    <th>{{ __('refund.method') }}</th>
                    <th>{{ __('refund.reason') }}</th>
                    <th style="text-align:right;">{{ __('refund.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($creditNote->refunds as $refund)
                    <tr>
                        <td>{{ $refund->refund_number }}</td>
                        <td>{{ $refund->refund_date?->format('Y-m-d') }}</td>
                        <td>{{ __('payment.'.$refund->method) !== 'payment.'.$refund->method ? __('payment.'.$refund->method) : ucfirst(str_replace('_', ' ', (string) $refund->method)) }}</td>
                        <td>{{ $refund->reason ?: '-' }}</td>
                        <td style="text-align:right;">{{ number_format((float) $refund->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">{{ __('common.no_records_found') }}</td></tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>
</div>
@endsection
