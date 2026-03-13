@extends('layouts.admin')
@section('title', __('payment.payment_details'))
@section('page-title', __('payment.payment_details'))
@section('page-subtitle', __('payment.subtitle'))
@section('content')
<x-ui.page-header :title="__('payment.payment_number').': '.$payment->payment_number" :subtitle="__('payment.subtitle')" icon="heroicon-o-credit-card">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.payments.index')">{{ __('common.back') }}</x-ui.button>
            @if($payment->invoice)
                <x-ui.button variant="secondary" :href="route('admin.invoices.show', $payment->invoice)">{{ __('invoice.invoice_details') }}</x-ui.button>
            @endif
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        <div class="grid gap-3 md:grid-cols-2">
            <div><span class="muted">{{ __('payment.invoice') }}:</span> {{ $payment->invoice?->invoice_number ?? '-' }}</div>
            <div><span class="muted">{{ __('payment.customer') }}:</span> {{ $payment->invoice?->salesOrder?->customer?->name ?? '-' }}</div>
            <div><span class="muted">{{ __('payment.payment_method') }}:</span> {{ __('payment.'.$payment->method) !== 'payment.'.$payment->method ? __('payment.'.$payment->method) : ucfirst(str_replace('_', ' ', (string) $payment->method)) }}</div>
            <div><span class="muted">{{ __('payment.payment_date') }}:</span> {{ $payment->payment_date?->format('Y-m-d') }}</div>
            <div><span class="muted">{{ __('payment.reference') }}:</span> {{ $payment->reference ?: '-' }}</div>
            <div><span class="muted">{{ __('payment.payment_amount') }}:</span> {{ number_format((float) $payment->amount, 2) }}</div>
            <div class="md:col-span-2"><span class="muted">{{ __('payment.notes') }}:</span> {{ $payment->notes ?: '-' }}</div>
        </div>
    </div>
</div>
@endsection

