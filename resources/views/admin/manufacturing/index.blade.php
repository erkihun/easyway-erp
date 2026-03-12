@extends('layouts.admin')
@section('title','Manufacturing')
@section('page-title', __('manufacturing.title'))
@section('page-subtitle', __('manufacturing.title'))
@section('content')
<x-ui.page-header title="Manufacturing" subtitle="Manage bill of materials and production execution." icon="heroicon-o-wrench-screwdriver">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.manufacturing.boms.index')">{{ __('manufacturing.boms') }}</x-ui.button>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.manufacturing.production-orders.index')">{{ __('manufacturing.production_orders') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.subnav-tabs>
    <a href="{{ route('admin.manufacturing.boms.index') }}" class="subnav-tab">{{ __('manufacturing.boms') }}</a>
    <a href="{{ route('admin.manufacturing.production-orders.index') }}" class="subnav-tab">{{ __('manufacturing.production_orders') }}</a>
</x-ui.subnav-tabs>

<div class="kpi-grid mb-1">
    <x-ui.kpi-card label="BOM Count" :value="$bomCount" icon="heroicon-o-clipboard-document-list" />
    <x-ui.kpi-card label="Production Orders" :value="$ordersCount" icon="heroicon-o-cog-8-tooth" />
</div>

<div class="panel">
    <div class="panel-body">
        <p class="muted" style="margin:0;">Core manufacturing workflows are active. Use BOM and production order modules for component planning and output posting through ledger-aware services.</p>
    </div>
</div>
@endsection





