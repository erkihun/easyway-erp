@extends('layouts.admin')
@section('title', __('roles.details'))
@section('page-title', __('roles.details'))
@section('page-subtitle', __('roles.details_subtitle'))
@section('content')
<style>
    .role-show-page { display: grid; gap: 1rem; }
    .role-summary-grid,
    .role-main-grid,
    .role-permission-grid { display: grid; gap: 1rem; }
    .role-summary-grid { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .role-main-grid { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .role-permission-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .role-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
        padding: 1rem;
        height: 100%;
    }
    .role-card-title {
        margin: 0 0 .75rem;
        font-weight: 700;
        color: #374151;
        font-size: .92rem;
    }
    .role-stat-label { color: #6b7280; font-size: .8rem; margin-bottom: .35rem; }
    .role-stat-value { color: #111827; font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
    .role-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .25rem .6rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        white-space: nowrap;
    }
    .role-table table th,
    .role-table table td { padding: .42rem .5rem; }
    .role-table table tbody tr:hover { background: #f9fafb; }
    @media (min-width: 768px) {
        .role-summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .role-permission-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .role-permission-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (min-width: 1280px) {
        .role-main-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .role-main-span-2 { grid-column: span 2 / span 2; }
    }
</style>

<div class="role-show-page">
<x-ui.page-header :title="__('roles.details')" :subtitle="__('roles.details_subtitle')" icon="heroicon-o-shield-check">
    <x-slot:actions>
        <x-ui.page-actions>
            @canany(['update_roles','manage_users'])
                <x-ui.button variant="secondary" size="sm" :href="route('admin.roles.edit', $role)">{{ __('roles.edit') }}</x-ui.button>
            @endcanany
            <x-ui.button variant="outline" size="sm" :href="route('admin.roles.index')">{{ __('common.back') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.card>
    <div style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;">
        <div>
            <div class="muted" style="font-size:.76rem;">{{ __('roles.title') }}</div>
            <div style="font-size:1.05rem;font-weight:700;color:#0f2740;">{{ $role->name }}</div>
        </div>
        @if($role->name === 'Admin')
            <span class="badge badge-info">{{ __('roles.full_system_access') }}</span>
        @elseif(in_array($role->name, ['Warehouse User', 'Warehouse'], true))
            <span class="badge badge-success">{{ __('roles.warehouse_operations_access') }}</span>
        @endif
    </div>
</x-ui.card>

<div class="role-summary-grid">
    <div class="role-card">
        <div class="role-stat-label">{{ __('roles.permissions') }}</div>
        <div class="role-stat-value">{{ number_format($role->permissions->count()) }}</div>
        <div class="muted" style="font-size:.76rem;margin-top:.25rem;">{{ __('roles.total_permissions_assigned') }}</div>
    </div>
    <div class="role-card">
        <div class="role-stat-label">{{ __('roles.assigned_users') }}</div>
        <div class="role-stat-value">{{ number_format($role->users->count()) }}</div>
        <div class="muted" style="font-size:.76rem;margin-top:.25rem;">{{ __('roles.users_using_this_role') }}</div>
    </div>
    <div class="role-card">
        <div class="role-stat-label">{{ __('common.guard') }}</div>
        <div class="role-stat-value">{{ $role->guard_name }}</div>
        <div class="muted" style="font-size:.76rem;margin-top:.25rem;">{{ __('roles.authorization_guard_context') }}</div>
    </div>
</div>

<div class="role-main-grid">
    <div class="role-card role-main-span-2">
        <h3 class="role-card-title">{{ __('roles.permissions') }}</h3>
        @if($role->permissions->isEmpty())
            <x-ui.empty-state :title="__('common.no_records_found')" :description="__('permissions.subtitle')" icon="heroicon-o-lock-closed" />
        @else
            <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.75rem;">
                @foreach($groupedPermissions as $group => $permissions)
                    <div class="panel">
                        <div class="panel-body" style="border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:.4rem;">
                            <strong style="font-size:.86rem;color:#1f2937;">{{ $group }}</strong>
                            <span class="badge badge-neutral">{{ $permissions->count() }}</span>
                        </div>
                        <div class="panel-body role-permission-grid" style="gap:.4rem;">
                            @foreach($permissions as $permission)
                                <span class="role-chip">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="role-card role-table">
        <h3 class="role-card-title">{{ __('navigation.users') }}</h3>
        @if($role->users->isEmpty())
            <x-ui.empty-state :title="__('common.no_records_found')" :description="__('users.subtitle')" icon="heroicon-o-user-group" />
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.email') }}</th>
                        <th class="actions-col">{{ __('common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($role->users->sortBy('name') as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="actions-col">
                                <x-ui.table-actions>
                                    @canany(['view_users','manage_users'])
                                        <x-ui.button variant="outline" size="sm" :href="route('admin.users.show', $user)">{{ __('common.view') }}</x-ui.button>
                                    @endcanany
                                    @canany(['update_users','manage_users'])
                                        <x-ui.button variant="ghost" size="sm" :href="route('admin.users.edit', $user)">{{ __('common.edit') }}</x-ui.button>
                                    @endcanany
                                </x-ui.table-actions>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</div>
@endsection

