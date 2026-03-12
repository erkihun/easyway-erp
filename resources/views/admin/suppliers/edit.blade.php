@extends('layouts.admin')
@section('title', __('suppliers.edit'))
@section('page-title', __('suppliers.edit'))
@section('content')
<x-ui.page-header :title="__('suppliers.edit')" :subtitle="__('suppliers.subtitle')" icon="heroicon-o-pencil-square" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.suppliers.update',$supplier) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" :value="$supplier->name" required />
                <x-ui.input name="email" :label="__('common.email')" type="email" :value="$supplier->email" />
                <x-ui.input name="phone" :label="__('common.phone')" :value="$supplier->phone" />
                <x-ui.input name="tax_number" :label="__('suppliers.tax_number')" :value="$supplier->tax_number" />
            </div>
            <div class="row mt-1">
                <label class="field-label" style="display:flex;align-items:center;gap:.5rem;">
                    <input type="checkbox" name="is_active" value="1" @checked($supplier->is_active) style="width:auto;">
                    <span>{{ __('common.active') }}</span>
                </label>
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.suppliers.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

