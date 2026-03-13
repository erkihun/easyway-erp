@extends('layouts.admin')
@section('title', __('credit_note.create_credit_note'))
@section('page-title', __('credit_note.create_credit_note'))
@section('page-subtitle', __('credit_note.subtitle'))
@section('content')
<x-ui.page-header :title="__('credit_note.create_credit_note')" :subtitle="__('credit_note.subtitle')" icon="heroicon-o-plus-circle" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.credit-notes.store') }}">
            @csrf
            <div class="row">
                <x-ui.select name="invoice_id" :label="__('credit_note.invoice_reference')" required>
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}" @selected($selectedInvoiceId === (string) $invoice->id)>{{ $invoice->invoice_number }} - {{ $invoice->salesOrder?->customer?->name ?? '-' }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.select name="customer_id" :label="__('credit_note.customer')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($invoices as $invoice)
                        @if($invoice->salesOrder?->customer)
                            <option value="{{ $invoice->salesOrder->customer->id }}">{{ $invoice->salesOrder->customer->name }}</option>
                        @endif
                    @endforeach
                </x-ui.select>
                <x-ui.input type="date" name="credit_date" :label="__('credit_note.credit_date')" :value="now()->toDateString()" required />
                <x-ui.input name="amount" :label="__('credit_note.amount')" type="number" step="0.0001" min="0.01" required />
            </div>
            <x-ui.textarea name="reason" :label="__('credit_note.reason')" rows="4" />
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.credit-notes.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit">{{ __('credit_note.create_credit_note') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

