@extends('layouts.admin')
@section('title','POS')
@section('page-title', __('pos.title'))
@section('page-subtitle', __('pos.title'))
@section('content')
<x-ui.page-header title="Point Of Sale" subtitle="Manage operator sessions and recent checkout transactions." icon="heroicon-o-shopping-cart">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" :href="route('admin.pos.session')" icon="heroicon-o-clock">{{ __('pos.sessions') }}</x-ui.button>
            <x-ui.button :href="route('admin.pos.checkout.page')" icon="heroicon-o-receipt-percent">{{ __('pos.checkout') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.table-shell title="Recent Sessions" :count="$sessions->total()" class="mb-1">
    <table>
        <thead><tr><th>{{ __('dashboard.warehouse') }}</th><th>User</th><th>{{ __('common.status') }}</th><th>Opened</th><th style="text-align:right;">Closing Amount</th></tr></thead>
        <tbody>
        @forelse($sessions as $session)
            <tr>
                <td>{{ $session->warehouse?->name }}</td>
                <td>{{ $session->user?->name }}</td>
                <td><x-ui.status-badge :status="$session->status" /></td>
                <td>{{ $session->opened_at }}</td>
                <td style="text-align:right;">{{ number_format((float)($session->closing_amount ?? 0),2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No sessions available.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $sessions->links() }}</div>
</x-ui.table-shell>

<x-ui.table-shell title="Recent POS Orders" :count="$orders->total()">
    <table>
        <thead><tr><th>No.</th><th>{{ __('common.status') }}</th><th style="text-align:right;">{{ __('common.total') }}</th><th>Ordered</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td><x-ui.status-badge :status="$order->status" /></td>
                <td style="text-align:right;">{{ number_format((float)$order->total_amount,2) }}</td>
                <td>{{ $order->ordered_at }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No POS orders posted.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $orders->links() }}</div>
</x-ui.table-shell>
@endsection





