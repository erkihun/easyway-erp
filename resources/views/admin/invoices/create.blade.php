@extends('layouts.admin')
@section('title', __('sales.invoices'))
@section('page-title', __('sales.invoices'))
@section('content')
<x-ui.page-header :title="__('sales.invoices')" :subtitle="__('sales.invoice_create_subtitle')" icon="heroicon-o-document-text" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.invoices.store') }}">
            @csrf
            <div class="row">
                <x-ui.select name="sales_order_id" :label="__('dashboard.order')">
                    <option value="">{{ __('sales.sales_order_optional') }}</option>
                    @foreach($salesOrders as $s)
                        <option value="{{ $s->id }}">{{ $s->order_number }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input type="date" name="invoice_date" :label="__('sales.invoice_date')" :value="now()->toDateString()" required />
                <x-ui.input type="date" name="due_date" :label="__('sales.due_date')" :value="now()->addDays(15)->toDateString()" />
                <x-ui.input name="total_amount" :label="__('common.amount')" type="number" step="0.0001" required />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.invoices.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('sales.invoices') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

