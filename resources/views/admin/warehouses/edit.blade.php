@extends('layouts.admin')
@section('title', __('warehouses.edit'))
@section('page-title', __('warehouses.edit'))
@section('content')
<x-ui.page-header :title="__('warehouses.edit')" :subtitle="__('warehouses.subtitle')" icon="heroicon-o-pencil-square" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.warehouses.update',$warehouse) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" :value="$warehouse->name" required />
                <x-ui.input name="code" :label="__('common.code')" :value="$warehouse->code" required />
                <x-ui.input name="location" :label="__('common.location')" :value="$warehouse->location" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.warehouses.show',$warehouse)">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

