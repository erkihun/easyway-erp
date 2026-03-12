@extends('layouts.admin')
@section('title', __('warehouses.title'))
@section('page-title', __('warehouses.title'))
@section('page-subtitle', __('warehouses.subtitle'))
@section('content')
<x-ui.page-header :title="__('warehouses.title')" :subtitle="__('warehouses.index_subtitle')" icon="heroicon-o-building-storefront">
    <x-slot:actions>
        @can('manage_stock')
            <x-ui.page-actions>
                <x-ui.button size="sm" :href="route('admin.warehouses.create')">{{ __('warehouses.create') }}</x-ui.button>
            </x-ui.page-actions>
        @endcan
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        @if($warehouses->isEmpty())
            <x-ui.empty-state :title="__('warehouses.empty_title')" :description="__('warehouses.empty_description')" icon="heroicon-o-building-storefront" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('common.code') }}</th><th>{{ __('common.name') }}</th><th>{{ __('common.location') }}</th><th>{{ __('common.status') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($warehouses as $warehouse)
                        <tr>
                            <td>{{ $warehouse->code }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->location ?: __('common.none') }}</td>
                            <td><x-ui.status-badge :status="$warehouse->is_active ? 'active' : 'cancelled'" /></td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.warehouses.show',$warehouse)">{{ __('common.show') }}</x-ui.button>
                                    <x-ui.button variant="outline" size="sm" :href="route('admin.warehouses.edit',$warehouse)">{{ __('common.edit') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $warehouses->links() }}</div>
        @endif
    </div>
</div>
@endsection





