@extends('layouts.admin')
@section('title', __('customers.create'))
@section('page-title', __('customers.create'))
@section('content')
<x-ui.page-header :title="__('customers.create')" :subtitle="__('customers.subtitle')" icon="heroicon-o-users" />

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" required />
                <x-ui.input name="email" :label="__('common.email')" type="email" />
                <x-ui.input name="phone" :label="__('common.phone')" />
                <x-ui.select name="customer_group_id" :label="__('common.group')">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.customers.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }} {{ __('customers.title') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

