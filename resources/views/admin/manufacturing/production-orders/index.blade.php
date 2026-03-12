@extends('layouts.admin')
@section('title','Production Orders')
@section('page-title', __('manufacturing.production_orders'))
@section('content')
<x-ui.page-header title="Production Orders" subtitle="Plan and track manufacturing output." icon="heroicon-o-cog-8-tooth">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button size="sm" :href="route('admin.manufacturing.production-orders.create')" icon="heroicon-o-plus">{{ __('manufacturing.production_orders') }}</x-ui.button>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.manufacturing.boms.index')">{{ __('manufacturing.boms') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.manufacturing.boms.index') }}" class="subnav-tab">{{ __('manufacturing.boms') }}</a>
    <a href="{{ route('admin.manufacturing.production-orders.index') }}" class="subnav-tab is-active">{{ __('manufacturing.production_orders') }}</a>
</x-ui.subnav-tabs>

<x-ui.table-shell title="Production Orders" :count="$orders->total()">
    <table>
        <thead><tr><th>No.</th><th>{{ __('dashboard.product') }}</th><th>{{ __('common.status') }}</th><th style="text-align:right;">{{ __('dashboard.qty') }}</th><th class="actions-col">Action</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->product?->name }}</td>
                <td><x-ui.status-badge :status="$order->status" /></td>
                <td style="text-align:right;">{{ number_format((float)$order->planned_quantity,2) }}</td>
                <td class="actions-col">
                    <x-ui.table-actions>
                        <x-ui.button variant="outline" size="sm" :href="route('admin.manufacturing.production-orders.show',$order)">{{ __('common.show') }}</x-ui.button>
                    </x-ui.table-actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No production orders.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $orders->links() }}</div>
</x-ui.table-shell>
@endsection






