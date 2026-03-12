@extends('layouts.admin')
@section('title', __('users.details'))
@section('page-title', __('users.details'))
@section('page-subtitle', __('users.subtitle'))
@section('content')
<x-ui.page-header title="{{ $user->name }}" subtitle="{{ $user->email }}" icon="heroicon-o-user-circle">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="secondary" size="sm" :href="route('admin.users.edit',$user)">{{ __('common.edit') }}</x-ui.button>
            <x-ui.button variant="outline" size="sm" :href="route('admin.users.index')">{{ __('common.back') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
    <x-ui.card>
        <x-slot:header><h3 class="table-shell-title">{{ __('users.assigned_roles') }}</h3></x-slot:header>
        <div class="table-actions">
            @forelse($user->roles as $role)
                <span class="badge badge-info">{{ $role->name }}</span>
            @empty
                <span class="badge badge-neutral">{{ __('users.no_roles_assigned') }}</span>
            @endforelse
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header><h3 class="table-shell-title">{{ __('users.effective_permissions') }}</h3></x-slot:header>
        <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));">
            @forelse($user->getAllPermissions()->sortBy('name') as $permission)
                <span class="badge badge-neutral">{{ $permission->name }}</span>
            @empty
                <span class="muted">{{ __('users.no_effective_permissions') }}</span>
            @endforelse
        </div>
    </x-ui.card>
</div>
@endsection



