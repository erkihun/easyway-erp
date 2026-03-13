@extends('layouts.admin')
@section('title', __('invoice.title'))
@section('page-title', __('invoice.title'))
@section('page-subtitle', __('invoice.subtitle'))
@section('content')
<x-ui.page-header :title="__('invoice.title')" :subtitle="__('invoice.subtitle')" icon="heroicon-o-document-text">
    <x-slot:actions>
        <x-ui.page-actions>
            @canany(['manage_accounting', 'create_orders'])
                <x-ui.button icon="heroicon-o-plus" :href="route('admin.invoices.create')">{{ __('invoice.create_invoice') }}</x-ui.button>
            @endcanany
            <x-ui.button variant="ghost" :href="route('admin.sales.index')">{{ __('sales.title') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="kpi-grid mb-1">
    <x-ui.stat-card :label="__('invoice.total_invoices')" :value="number_format((int) $stats['total'])" icon="heroicon-o-document-duplicate" />
    <x-ui.stat-card :label="__('invoice.draft')" :value="number_format((int) $stats['draft'])" icon="heroicon-o-pencil-square" tone="warning" />
    <x-ui.stat-card :label="__('invoice.paid')" :value="number_format((int) $stats['paid'])" icon="heroicon-o-check-circle" tone="success" />
    <x-ui.stat-card :label="__('invoice.overdue')" :value="number_format((int) $stats['overdue'])" icon="heroicon-o-exclamation-triangle" tone="danger" />
</div>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :value="request('q')" :placeholder="__('invoice.search_placeholder')" />
    <x-ui.select name="status" :label="__('common.status')">
        <option value="">{{ __('common.all') }}</option>
        <option value="draft" @selected(request('status') === 'draft')>{{ __('invoice.draft') }}</option>
        <option value="issued" @selected(request('status') === 'issued')>{{ __('invoice.sent') }}</option>
        <option value="partially_paid" @selected(request('status') === 'partially_paid')>{{ __('invoice.partially_paid') }}</option>
        <option value="paid" @selected(request('status') === 'paid')>{{ __('invoice.paid') }}</option>
        <option value="cancelled" @selected(request('status') === 'cancelled')>{{ __('invoice.cancelled') }}</option>
    </x-ui.select>
    <x-ui.input type="date" name="from_date" :label="__('invoice.from_date')" :value="request('from_date')" />
    <x-ui.input type="date" name="to_date" :label="__('invoice.to_date')" :value="request('to_date')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('status') || request()->filled('from_date') || request()->filled('to_date'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.invoices.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('invoice.directory')" :count="$invoices->total()">
    <x-ui.table compact>
        <thead>
            <tr>
                <th>{{ __('invoice.invoice_number') }}</th>
                <th>{{ __('invoice.customer') }}</th>
                <th>{{ __('invoice.invoice_date') }}</th>
                <th>{{ __('invoice.due_date') }}</th>
                <th>{{ __('common.status') }}</th>
                <th style="text-align:right;">{{ __('invoice.total') }}</th>
                <th style="text-align:right;">{{ __('invoice.paid_amount') }}</th>
                <th style="text-align:right;">{{ __('invoice.balance_due') }}</th>
                <th class="actions-col">{{ __('common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $status = (string) ($invoice->status->value ?? $invoice->status);
                    $isOverdue = $status === 'issued' && $invoice->due_date && $invoice->due_date->isPast();
                    $statusTone = match (true) {
                        $status === 'paid' => 'success',
                        $isOverdue => 'danger',
                        $status === 'draft' => 'warning',
                        $status === 'cancelled' => 'neutral',
                        default => 'info',
                    };
                    $statusLabel = $isOverdue ? __('invoice.overdue') : __('common.status_values.'.$status);
                    if ($statusLabel === 'common.status_values.'.$status) {
                        $statusLabel = ucfirst(str_replace('_', ' ', $status));
                    }
                    $total = max(0, (float) $invoice->total_amount - (float) ($invoice->credit_total ?? 0));
                    $paid = (float) $invoice->paid_amount;
                    $balance = max(0, $total - $paid);
                @endphp
                <tr>
                    <td class="font-semibold">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->salesOrder?->customer?->name ?? '-' }}</td>
                    <td>{{ $invoice->invoice_date?->format('Y-m-d') }}</td>
                    <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td>
                    <td><x-ui.badge :tone="$statusTone">{{ $statusLabel }}</x-ui.badge></td>
                    <td style="text-align:right;">{{ number_format($total, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($paid, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($balance, 2) }}</td>
                    <td class="actions-col">
                        <x-ui.table-actions>
                            <x-ui.button variant="outline" size="sm" :href="route('admin.invoices.show', $invoice)">{{ __('common.view') }}</x-ui.button>
                            <x-ui.button variant="secondary" size="sm" :href="route('admin.invoices.edit', $invoice)">{{ __('common.edit') }}</x-ui.button>
                            <x-ui.button variant="ghost" size="sm" :href="route('admin.invoices.pdf', $invoice)">{{ __('invoice.download_pdf') }}</x-ui.button>
                            <x-ui.button variant="ghost" size="sm" :href="route('admin.payments.create', ['invoice_id' => $invoice->id])">{{ __('invoice.register_payment') }}</x-ui.button>
                            <x-ui.button variant="ghost" size="sm" :href="route('admin.credit-notes.create', ['invoice_id' => $invoice->id])">{{ __('credit_note.create_credit_note') }}</x-ui.button>
                        </x-ui.table-actions>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="muted">{{ __('common.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
    <div class="mt-1">{{ $invoices->links() }}</div>
</x-ui.table-shell>
@endsection
