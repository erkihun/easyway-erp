@extends('layouts.admin')
@section('title', __('roles.edit'))
@section('page-title', __('roles.edit'))
@section('page-subtitle', __('roles.subtitle'))
@section('content')
<x-ui.page-header :title="__('roles.edit')" :subtitle="__('roles.details_subtitle')" icon="heroicon-o-pencil-square">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" size="sm" :href="route('admin.roles.show', $role)">{{ __('common.view') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.roles.index')">{{ __('common.back') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf
    @method('PUT')
    @include('admin.roles._form', [
        'role' => $role,
        'permissionGroups' => $permissionGroups,
        'selectedPermissions' => old('permissions', $role->permissions->pluck('name')->all()),
        'submitLabel' => __('common.save_changes'),
    ])
</form>
@endsection



