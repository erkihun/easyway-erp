@extends('layouts.admin')
@section('title', __('sales.details'))
@section('page-title', __('sales.details'))
@section('content')
<x-ui.page-header :title="__('sales.details_title', ['number' => $order->order_number])" :subtitle="__('sales.details_subtitle', ['customer' => $order->customer?->name ?? __('sales.walk_in')])" icon="heroicon-o-banknotes">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.sales.index')">{{ __('common.back') }}</x-ui.button>
            @canany(['create_orders','manage_accounting'])
                <x-ui.button variant="secondary" icon="heroicon-o-document-text" :href="route('admin.invoices.create')">{{ __('sales.invoices') }}</x-ui.button>
            @endcanany
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid mb-1" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
    <x-ui.smart-button :label="__('common.status')" :value="strtoupper((string)($order->status->value ?? $order->status))" icon="heroicon-o-flag" />
    <x-ui.smart-button :label="__('common.items')" :value="$order->items->count()" icon="heroicon-o-list-bullet" />
    <x-ui.smart-button :label="__('sales.invoices')" :value="$invoices->count()" icon="heroicon-o-document-text" />
    <x-ui.smart-button :label="__('common.total')" :value="number_format((float)$order->total_amount,2)" icon="heroicon-o-banknotes" />
</div>

<div class="panel mb-1"><div class="panel-body"><h3 style="margin-top:0;">{{ __('common.items') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th><th style="text-align:right;">{{ __('common.unit_price') }}</th><th style="text-align:right;">{{ __('common.line_total') }}</th></tr></thead><tbody>@forelse($order->items as $item)<tr><td>{{ $item->product?->name }}</td><td style="text-align:right;">{{ number_format((float)$item->quantity,2) }}</td><td style="text-align:right;">{{ number_format((float)$item->unit_price,2) }}</td><td style="text-align:right;">{{ number_format((float)$item->line_total,2) }}</td></tr>@empty<tr><td colspan="4" class="muted">{{ __('common.no_items') }}</td></tr>@endforelse</tbody></table></div></div></div>
<div class="panel"><div class="panel-body"><h3 style="margin-top:0;">{{ __('sales.invoices') }}</h3><div class="table-wrap"><table><thead><tr><th>{{ __('sales.invoice') }}</th><th>{{ __('common.status') }}</th><th style="text-align:right;">{{ __('common.total') }}</th></tr></thead><tbody>@forelse($invoices as $inv)<tr><td><a class="link" href="{{ route('admin.invoices.show',$inv) }}">{{ $inv->invoice_number }}</a></td><td><x-ui.status-badge :status="$inv->status" /></td><td style="text-align:right;">{{ number_format((float)$inv->total_amount,2) }}</td></tr>@empty<tr><td colspan="3" class="muted">{{ __('sales.no_invoices_linked') }}</td></tr>@endforelse</tbody></table></div></div></div>
@endsection





