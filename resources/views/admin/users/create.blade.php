@extends('layouts.admin')
@section('title', __('users.create'))
@section('page-title', __('users.create'))
@section('page-subtitle', __('users.subtitle'))
@section('content')
<x-ui.page-header :title="__('users.create')" :subtitle="__('users.create_subtitle')" icon="heroicon-o-user-plus">
    <x-slot:actions>
        <x-ui.button variant="outline" size="sm" :href="route('admin.users.index')">{{ __('common.back') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    <x-ui.card class="mb-1">
        <x-slot:header><h3 class="table-shell-title">{{ __('users.basic_information') }}</h3></x-slot:header>
        <div class="row">
            <x-ui.input name="name" :label="__('common.name')" required />
            <x-ui.input name="email" :label="__('common.email')" type="email" required />
        </div>
    </x-ui.card>

    <x-ui.card class="mb-1">
        <x-slot:header><h3 class="table-shell-title">{{ __('users.security') }}</h3></x-slot:header>
        <div class="row">
            <x-ui.input name="password" :label="__('auth.password')" type="password" required />
            <x-ui.input name="password_confirmation" :label="__('users.password_confirm')" type="password" required />
        </div>
    </x-ui.card>

    @canany(['assign_roles','manage_users'])
        <x-ui.card>
            <x-slot:header><h3 class="table-shell-title">{{ __('users.roles') }}</h3></x-slot:header>
            <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
                @foreach($roles as $role)
                    <label class="smart-btn">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" style="width:auto;margin:0;" @checked(in_array($role->name, old('roles', []), true))>
                        <span class="smart-btn-copy">
                            <span class="smart-btn-value">{{ $role->name }}</span>
                            <span class="smart-btn-label">{{ __('common.role') }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('roles')
                <div class="field-error mt-1">{{ $message }}</div>
            @enderror
        </x-ui.card>
    @else
        <x-ui.card>
            <x-slot:header><h3 class="table-shell-title">{{ __('users.roles') }}</h3></x-slot:header>
            <p class="muted">{{ __('users.role_assign_forbidden') }}</p>
        </x-ui.card>
    @endcanany

    <div class="form-actions-sticky mt-1">
        <x-ui.button variant="ghost" :href="route('admin.users.index')">{{ __('common.cancel') }}</x-ui.button>
        <x-ui.button type="submit" icon="heroicon-o-check">{{ __('users.create') }}</x-ui.button>
    </div>
</form>
@endsection




