@extends('layouts.admin')
@section('title', __('sales.title'))
@section('page-title', __('sales.title'))
@section('page-subtitle', __('sales.subtitle'))
@section('content')
<x-ui.page-header :title="__('sales.index_title')" :subtitle="__('sales.index_subtitle')" icon="heroicon-o-banknotes">
    <x-slot:actions>
        @can('create_orders')
            <x-ui.page-actions>
                <x-ui.button size="sm" :href="route('admin.sales.create')">{{ __('sales.create') }}</x-ui.button>
                <x-ui.button variant="secondary" size="sm" :href="route('admin.invoices.index')">{{ __('sales.invoices') }}</x-ui.button>
            </x-ui.page-actions>
        @endcan
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.sales.index') }}" class="subnav-tab is-active">{{ __('sales.title') }}</a>
    <a href="{{ route('admin.invoices.index') }}" class="subnav-tab">{{ __('sales.invoices') }}</a>
</x-ui.subnav-tabs>

<div class="panel">
    <div class="panel-body">
        @if($orders->isEmpty())
            <x-ui.empty-state :title="__('sales.empty_title')" :description="__('sales.empty_description')" icon="heroicon-o-banknotes" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('common.code') }}</th><th>{{ __('common.customer') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.date') }}</th><th style="text-align:right;">{{ __('common.total') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer?->name ?? __('sales.walk_in') }}</td>
                            <td><x-ui.status-badge :status="$order->status" /></td>
                            <td>{{ $order->order_date }}</td>
                            <td style="text-align:right;">{{ number_format((float)$order->total_amount,2) }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.sales.show',$order)">{{ __('common.show') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection






