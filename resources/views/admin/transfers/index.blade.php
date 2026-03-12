@extends('layouts.admin')
@section('title', __('transfers.title'))
@section('page-title', __('transfers.title'))
@section('page-subtitle', __('transfers.subtitle'))
@section('content')
<x-ui.page-header :title="__('transfers.index_title')" :subtitle="__('transfers.index_subtitle')" icon="heroicon-o-arrow-path">
    <x-slot:actions>
        @can('manage_transfers')
            <x-ui.page-actions>
                <x-ui.button size="sm" :href="route('admin.transfers.create')">{{ __('transfers.create') }}</x-ui.button>
            </x-ui.page-actions>
        @endcan
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        @if($transfers->isEmpty())
            <x-ui.empty-state :title="__('transfers.empty_title')" :description="__('transfers.empty_description')" icon="heroicon-o-arrow-path" />
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('common.code') }}</th><th>{{ __('common.source') }}</th><th>{{ __('common.destination') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.date') }}</th><th class="actions-col">{{ __('common.actions') }}</th></tr></thead>
                    <tbody>
                    @foreach($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->transfer_number }}</td>
                            <td>{{ $transfer->sourceWarehouse?->name }}</td>
                            <td>{{ $transfer->destinationWarehouse?->name }}</td>
                            <td><x-ui.status-badge :status="$transfer->status" /></td>
                            <td>{{ $transfer->transfer_date }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.transfers.show',$transfer)">{{ __('common.show') }}</x-ui.button>
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-1">{{ $transfers->links() }}</div>
        @endif
    </div>
</div>
@endsection





