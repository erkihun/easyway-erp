@extends('layouts.admin')
@section('title', __('warehouses.create'))
@section('page-title', __('warehouses.create'))
@section('content')
<x-ui.page-header :title="__('warehouses.create')" :subtitle="__('warehouses.subtitle')" icon="heroicon-o-building-storefront" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.warehouses.store') }}">
            @csrf
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" required />
                <x-ui.input name="code" :label="__('common.code')" required />
                <x-ui.input name="location" :label="__('common.location')" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.warehouses.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }} {{ __('warehouses.title') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

