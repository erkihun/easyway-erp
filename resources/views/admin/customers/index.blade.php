@extends('layouts.admin')
@section('title', __('customers.title'))
@section('page-title', __('customers.title'))
@section('page-subtitle', __('customers.subtitle'))
@section('content')
<x-ui.page-header :title="__('customers.title')" :subtitle="__('customers.index_subtitle')" icon="heroicon-o-users">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button size="sm" :href="route('admin.customers.create')">{{ __('customers.create') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        @if($customers->isEmpty())
            <x-ui.empty-state :title="__('customers.empty_title')" :description="__('customers.empty_description')" icon="heroicon-o-users" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('common.name') }}</th><th>{{ __('common.email') }}</th><th>{{ __('common.phone') }}</th><th>{{ __('common.group') }}</th><th>{{ __('common.status') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email ?: __('common.none') }}</td>
                            <td>{{ $customer->phone ?: __('common.none') }}</td>
                            <td>{{ $customer->group?->name ?: __('common.none') }}</td>
                            <td><x-ui.status-badge :status="$customer->is_active ? 'active' : 'cancelled'" /></td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.customers.edit',$customer)">{{ __('common.edit') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
@endsection






