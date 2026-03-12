@extends('layouts.admin')
@section('title', __('purchases.goods_receipts'))
@section('page-title', __('purchases.goods_receipts'))
@section('page-subtitle', __('purchases.goods_receipts'))
@section('content')
<x-ui.page-header :title="__('purchases.goods_receipts')" :subtitle="__('purchases.goods_receipts_subtitle')" icon="heroicon-o-inbox-arrow-down">
    <x-slot:actions>
        @can('manage_purchases')
            <x-ui.page-actions>
                <x-ui.button size="sm" :href="route('admin.goods-receipts.create')">{{ __('purchases.create_goods_receipt') }}</x-ui.button>
                <x-ui.button variant="secondary" size="sm" :href="route('admin.purchases.index')">{{ __('purchases.title') }}</x-ui.button>
            </x-ui.page-actions>
        @endcan
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.purchases.index') }}" class="subnav-tab">{{ __('purchases.title') }}</a>
    <a href="{{ route('admin.goods-receipts.index') }}" class="subnav-tab is-active">{{ __('purchases.goods_receipts') }}</a>
</x-ui.subnav-tabs>

<div class="panel">
    <div class="panel-body">
        @if($receipts->isEmpty())
            <x-ui.empty-state :title="__('purchases.empty_receipts_title')" :description="__('purchases.empty_receipts_description')" icon="heroicon-o-inbox-arrow-down" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('dashboard.receipt') }}</th><th>{{ __('dashboard.purchase') }}</th><th>{{ __('dashboard.warehouse') }}</th><th>{{ __('common.date') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($receipts as $receipt)
                        <tr>
                            <td>{{ $receipt->receipt_number }}</td>
                            <td>{{ $receipt->purchaseOrder?->order_number }}</td>
                            <td>{{ $receipt->warehouse?->name }}</td>
                            <td>{{ $receipt->received_at }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.goods-receipts.show',$receipt)">{{ __('common.show') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $receipts->links() }}</div>
        @endif
    </div>
</div>
@endsection








