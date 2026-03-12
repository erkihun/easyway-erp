@extends('layouts.admin')
@section('title','Invoices')
@section('page-title', __('sales.invoices'))
@section('page-subtitle', __('sales.invoices'))
@section('content')
<x-ui.page-header title="Invoices" subtitle="Track invoice status, paid amounts, and downloadable PDFs." icon="heroicon-o-document-text">
    <x-slot:actions>
        @canany(['manage_accounting','create_orders'])
            <x-ui.page-actions>
                <x-ui.button size="sm" :href="route('admin.invoices.create')">{{ __('sales.invoices') }}</x-ui.button>
                <x-ui.button variant="secondary" size="sm" :href="route('admin.sales.index')">{{ __('sales.title') }}</x-ui.button>
            </x-ui.page-actions>
        @endcanany
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.sales.index') }}" class="subnav-tab">{{ __('sales.title') }}</a>
    <a href="{{ route('admin.invoices.index') }}" class="subnav-tab is-active">{{ __('sales.invoices') }}</a>
</x-ui.subnav-tabs>

<div class="panel">
    <div class="panel-body">
        @if($invoices->isEmpty())
            <x-ui.empty-state title="No invoices found" description="Generated invoices will appear here with payment progress." icon="heroicon-o-document-text" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Invoice</th><th>Sales Order</th><th>{{ __('common.status') }}</th><th>{{ __('common.date') }}</th><th style="text-align:right;">{{ __('common.total') }}</th><th style="text-align:right;">Paid</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->salesOrder?->order_number ?? '—' }}</td>
                            <td><x-ui.status-badge :status="$invoice->status" /></td>
                            <td>{{ $invoice->invoice_date }}</td>
                            <td style="text-align:right;">{{ number_format((float)$invoice->total_amount,2) }}</td>
                            <td style="text-align:right;">{{ number_format((float)$invoice->paid_amount,2) }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.invoices.show',$invoice)">{{ __('common.show') }}</x-ui.button>
                                    <x-ui.button variant="outline" size="sm" :href="route('admin.invoices.pdf',$invoice)">{{ __('reports.export_pdf') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection







