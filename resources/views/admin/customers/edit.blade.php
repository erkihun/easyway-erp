@extends('layouts.admin')
@section('title', __('customers.edit'))
@section('page-title', __('customers.edit'))
@section('content')
<x-ui.page-header :title="__('customers.edit')" :subtitle="__('customers.subtitle')" icon="heroicon-o-pencil-square" />

<div class="kpi-grid mb-1">
    <x-ui.stat-card :label="__('invoice.total_invoices')" :value="number_format((int) ($billingSummary['total_invoices'] ?? 0))" icon="heroicon-o-document-text" />
    <x-ui.stat-card :label="__('invoice.paid_amount')" :value="number_format((float) ($billingSummary['total_paid'] ?? 0), 2)" icon="heroicon-o-check-circle" tone="success" />
    <x-ui.stat-card :label="__('invoice.balance_due')" :value="number_format((float) ($billingSummary['outstanding_balance'] ?? 0), 2)" icon="heroicon-o-exclamation-triangle" tone="warning" />
    <x-ui.stat-card :label="__('payment.payment_date')" :value="(string) ($billingSummary['last_payment_date'] ?? '-')" icon="heroicon-o-calendar-days" tone="info" />
</div>

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.customers.update',$customer) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" :value="$customer->name" required />
                <x-ui.input name="email" :label="__('common.email')" type="email" :value="$customer->email" />
                <x-ui.input name="phone" :label="__('common.phone')" :value="$customer->phone" />
                <x-ui.select name="customer_group_id" :label="__('common.group')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" @selected($customer->customer_group_id===$g->id)>{{ $g->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="row mt-1">
                <label class="field-label" style="display:flex;align-items:center;gap:.5rem;">
                    <input type="checkbox" name="is_active" value="1" @checked($customer->is_active) style="width:auto;">
                    <span>{{ __('common.active') }}</span>
                </label>
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.customers.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
