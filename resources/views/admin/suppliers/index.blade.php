@extends('layouts.admin')
@section('title', __('suppliers.title'))
@section('page-title', __('suppliers.title'))
@section('page-subtitle', __('suppliers.subtitle'))
@section('content')
<x-ui.page-header :title="__('suppliers.title')" :subtitle="__('suppliers.index_subtitle')" icon="heroicon-o-truck">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button size="sm" :href="route('admin.suppliers.create')">{{ __('suppliers.create') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        @if($suppliers->isEmpty())
            <x-ui.empty-state :title="__('suppliers.empty_title')" :description="__('suppliers.empty_description')" icon="heroicon-o-truck" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('common.name') }}</th><th>{{ __('common.email') }}</th><th>{{ __('common.phone') }}</th><th>{{ __('suppliers.tax_no') }}</th><th>{{ __('common.status') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email ?: __('common.none') }}</td>
                            <td>{{ $supplier->phone ?: __('common.none') }}</td>
                            <td>{{ $supplier->tax_number ?: __('common.none') }}</td>
                            <td><x-ui.status-badge :status="$supplier->is_active ? 'active' : 'cancelled'" /></td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.suppliers.edit',$supplier)">{{ __('common.edit') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $suppliers->links() }}</div>
        @endif
    </div>
</div>
@endsection





