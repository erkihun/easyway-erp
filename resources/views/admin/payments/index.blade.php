@extends('layouts.admin')
@section('title', __('payment.title'))
@section('page-title', __('payment.title'))
@section('page-subtitle', __('payment.subtitle'))
@section('content')
<x-ui.page-header :title="__('payment.title')" :subtitle="__('payment.subtitle')" icon="heroicon-o-credit-card">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button :href="route('admin.payments.create')">{{ __('payment.create_payment') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :value="request('q')" :placeholder="__('payment.search_placeholder')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.payments.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('payment.directory')" :count="$payments->total()">
    <x-ui.table compact>
        <thead>
            <tr>
                <th>{{ __('payment.payment_number') }}</th>
                <th>{{ __('payment.invoice') }}</th>
                <th>{{ __('payment.customer') }}</th>
                <th>{{ __('payment.payment_method') }}</th>
                <th>{{ __('payment.reference') }}</th>
                <th>{{ __('payment.payment_date') }}</th>
                <th style="text-align:right;">{{ __('payment.payment_amount') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_number }}</td>
                    <td>{{ $payment->invoice?->invoice_number ?? '-' }}</td>
                    <td>{{ $payment->invoice?->salesOrder?->customer?->name ?? '-' }}</td>
                    <td>{{ __('payment.'.$payment->method) !== 'payment.'.$payment->method ? __('payment.'.$payment->method) : ucfirst(str_replace('_', ' ', (string) $payment->method)) }}</td>
                    <td>{{ $payment->reference ?: '-' }}</td>
                    <td>{{ $payment->payment_date?->format('Y-m-d') }}</td>
                    <td style="text-align:right;">{{ number_format((float) $payment->amount, 2) }}</td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            <x-ui.button variant="outline" size="sm" :href="route('admin.payments.show', $payment)">{{ __('common.view') }}</x-ui.button>
                        </x-ui.table-actions>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">{{ __('common.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
    <div class="mt-1">{{ $payments->links() }}</div>
</x-ui.table-shell>
@endsection

