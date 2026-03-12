@extends('layouts.admin')
@section('title', __('suppliers.create'))
@section('page-title', __('suppliers.create'))
@section('content')
<x-ui.page-header :title="__('suppliers.create')" :subtitle="__('suppliers.subtitle')" icon="heroicon-o-truck" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" required />
                <x-ui.input name="email" :label="__('common.email')" type="email" />
                <x-ui.input name="phone" :label="__('common.phone')" />
                <x-ui.input name="tax_number" :label="__('suppliers.tax_number')" />
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.suppliers.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }} {{ __('suppliers.title') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

