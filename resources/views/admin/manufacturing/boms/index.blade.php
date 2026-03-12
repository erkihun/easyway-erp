@extends('layouts.admin')
@section('title','BOMs')
@section('page-title', __('manufacturing.boms'))
@section('content')
<x-ui.page-header title="Bills of Materials" subtitle="Define finished product component requirements." icon="heroicon-o-clipboard-document-list">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button size="sm" :href="route('admin.manufacturing.boms.create')" icon="heroicon-o-plus">{{ __('manufacturing.boms') }}</x-ui.button>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.manufacturing.production-orders.index')">{{ __('manufacturing.production_orders') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.manufacturing.boms.index') }}" class="subnav-tab is-active">{{ __('manufacturing.boms') }}</a>
    <a href="{{ route('admin.manufacturing.production-orders.index') }}" class="subnav-tab">{{ __('manufacturing.production_orders') }}</a>
</x-ui.subnav-tabs>

<x-ui.table-shell title="BOM Records" :count="$boms->total()">
    <table>
        <thead><tr><th>Code</th><th>{{ __('common.name') }}</th><th>{{ __('dashboard.product') }}</th><th style="text-align:right;">Components</th></tr></thead>
        <tbody>
        @forelse($boms as $bom)
            <tr><td>{{ $bom->code }}</td><td>{{ $bom->name }}</td><td>{{ $bom->product?->name }}</td><td style="text-align:right;">{{ $bom->items->count() }}</td></tr>
        @empty
            <tr><td colspan="4" class="muted">No BOMs.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $boms->links() }}</div>
</x-ui.table-shell>
@endsection






