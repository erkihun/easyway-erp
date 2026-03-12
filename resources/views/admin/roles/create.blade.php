@extends('layouts.admin')
@section('title', __('roles.create'))
@section('page-title', __('roles.create'))
@section('page-subtitle', __('roles.subtitle'))
@section('content')
<x-ui.page-header :title="__('roles.create')" :subtitle="__('roles.subtitle')" icon="heroicon-o-plus-circle">
    <x-slot:actions>
        <x-ui.button variant="outline" size="sm" :href="route('admin.roles.index')">{{ __('common.back') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf
    @include('admin.roles._form', [
        'permissionGroups' => $permissionGroups,
        'selectedPermissions' => old('permissions', []),
        'submitLabel' => __('roles.create'),
    ])
</form>
@endsection



