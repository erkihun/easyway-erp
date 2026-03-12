@extends('layouts.admin')
@section('title', __('purchases.title'))
@section('page-title', __('purchases.title'))
@section('page-subtitle', __('purchases.subtitle'))
@section('content')
<x-ui.page-header :title="__('purchases.index_title')" :subtitle="__('purchases.index_subtitle')" icon="heroicon-o-clipboard-document-list">
    <x-slot:actions>
        @can('manage_purchases')
            <x-ui.page-actions>
                <x-ui.button size="sm" :href="route('admin.purchases.create')">{{ __('purchases.create') }}</x-ui.button>
                <x-ui.button variant="secondary" size="sm" :href="route('admin.goods-receipts.index')">{{ __('purchases.goods_receipts') }}</x-ui.button>
            </x-ui.page-actions>
        @endcan
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.purchases.index') }}" class="subnav-tab is-active">{{ __('purchases.title') }}</a>
    <a href="{{ route('admin.goods-receipts.index') }}" class="subnav-tab">{{ __('purchases.goods_receipts') }}</a>
</x-ui.subnav-tabs>

<div class="panel">
    <div class="panel-body">
        @if($orders->isEmpty())
            <x-ui.empty-state :title="__('purchases.empty_title')" :description="__('purchases.empty_description')" icon="heroicon-o-clipboard-document-list" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('common.code') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.date') }}</th><th style="text-align:right;">{{ __('common.total') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td><x-ui.status-badge :status="$order->status" /></td>
                            <td>{{ $order->order_date }}</td>
                            <td style="text-align:right;">{{ number_format((float)$order->total_amount,2) }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.purchases.show',$order)">{{ __('common.show') }}</x-ui.button>
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





