@extends('layouts.admin')
@section('title', __('payment.create_payment'))
@section('page-title', __('payment.create_payment'))
@section('page-subtitle', __('payment.subtitle'))
@section('content')
<x-ui.page-header :title="__('payment.create_payment')" :subtitle="__('payment.subtitle')" icon="heroicon-o-plus-circle" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf
            <div class="row">
                <x-ui.select name="invoice_id" :label="__('payment.invoice')" required>
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}" @selected($selectedInvoiceId === (string) $invoice->id)>{{ $invoice->invoice_number }} - {{ $invoice->salesOrder?->customer?->name ?? '-' }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input type="date" name="payment_date" :label="__('payment.payment_date')" :value="now()->toDateString()" required />
                <x-ui.input name="amount" :label="__('payment.payment_amount')" type="number" step="0.0001" min="0.01" required />
                <x-ui.select name="method" :label="__('payment.payment_method')" required>
                    <option value="cash">{{ __('payment.cash') }}</option>
                    <option value="bank_transfer">{{ __('payment.bank_transfer') }}</option>
                    <option value="credit_card">{{ __('payment.credit_card') }}</option>
                    <option value="mobile_payment">{{ __('payment.mobile_payment') }}</option>
                </x-ui.select>
                <x-ui.input name="reference" :label="__('payment.reference')" />
                <x-ui.textarea name="notes" :label="__('payment.notes')" rows="3" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.payments.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit">{{ __('payment.create_payment') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
