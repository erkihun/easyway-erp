@extends('layouts.admin')
@section('title', __('roles.title'))
@section('page-title', __('roles.title'))
@section('page-subtitle', __('roles.subtitle'))
@section('content')
<style>
    .roles-page { display: grid; gap: 1rem; }
    .roles-filter-card,
    .roles-directory-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
    }
    .roles-filter-card { padding: .8rem; }
    .roles-filter-row { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
    .roles-directory-head {
        padding: .8rem .9rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .6rem;
    }
    .roles-directory-title { margin: 0; font-size: 1rem; font-weight: 700; color: #111827; }
    .roles-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: .15rem .5rem;
        font-size: .72rem;
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #dbe5f0;
    }
    .roles-table-wrap { overflow: auto; }
    .roles-table { width: 100%; border-collapse: collapse; }
    .roles-table th,
    .roles-table td {
        padding: .6rem .75rem;
        border-bottom: 1px solid #edf2f7;
        vertical-align: top;
    }
    .roles-table th {
        background: #f8fafc;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        text-align: left;
    }
    .roles-table tbody tr:hover { background: #f9fafb; }
    .roles-table tbody tr:last-child td { border-bottom: 0; }
    .role-name { font-weight: 700; color: #0f172a; line-height: 1.2; }
    .role-system-badge {
        margin-top: .25rem;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .15rem .5rem;
        font-size: .7rem;
        font-weight: 700;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .role-ops-badge {
        margin-top: .25rem;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .15rem .5rem;
        font-size: .7rem;
        font-weight: 700;
        background: #ecfdf3;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .permission-preview {
        display: flex;
        gap: .3rem;
        flex-wrap: wrap;
        max-width: 460px;
    }
    .permission-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: .12rem .45rem;
        font-size: .7rem;
        font-weight: 600;
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        white-space: nowrap;
    }
    .user-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: .15rem .5rem;
        font-size: .72rem;
        font-weight: 700;
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }
    .created-col { font-size: .82rem; color: #6b7280; white-space: nowrap; }
    .roles-actions { display: flex; gap: .35rem; align-items: center; justify-content: flex-end; flex-wrap: nowrap; white-space: nowrap; }
    .roles-actions > * { display: inline-flex; align-items: center; margin: 0; flex-shrink: 0; }
    .roles-actions form { display: inline-flex; margin: 0; flex-shrink: 0; }
    .roles-actions-col { min-width: 220px; width: 1%; white-space: nowrap; text-align: right; }
</style>

<div class="roles-page">
<x-ui.page-header :title="__('roles.title')" :subtitle="__('roles.subtitle')" icon="heroicon-o-shield-check">
    <x-slot:actions>
        <x-ui.page-actions>
            @canany(['create_roles','manage_users'])
                <x-ui.button size="sm" :href="route('admin.roles.create')" icon="heroicon-o-plus">{{ __('roles.create') }}</x-ui.button>
            @endcanany
            @canany(['view_permissions','manage_users'])
                <x-ui.button variant="secondary" size="sm" :href="route('admin.permissions.index')">{{ __('navigation.permissions') }}</x-ui.button>
            @endcanany
            @canany(['view_users','manage_users'])
                <x-ui.button variant="outline" size="sm" :href="route('admin.users.index')">{{ __('navigation.users') }}</x-ui.button>
            @endcanany
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<form method="GET" action="{{ route('admin.roles.index') }}" class="roles-filter-card">
    <div class="roles-filter-row">
        <x-ui.input name="q" :label="__('common.search')" :placeholder="__('common.search').' '.__('roles.title')" :value="request('q')" style="min-width:220px;" />
        <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
        @if(request()->filled('q'))
            <x-ui.button variant="ghost" size="sm" :href="route('admin.roles.index')">{{ __('common.reset') }}</x-ui.button>
        @endif
    </div>
</form>

<section class="roles-directory-card">
    <div class="roles-directory-head">
        <h3 class="roles-directory-title">{{ __('roles.directory') }}</h3>
        <span class="roles-count-badge">{{ $roles->total() }}</span>
    </div>

    <div class="roles-table-wrap">
    <table class="roles-table">
        <thead>
        <tr>
            <th>{{ __('roles.title') }}</th>
            <th>{{ __('roles.permissions') }}</th>
            <th>{{ __('navigation.users') }}</th>
            <th>{{ __('common.created_at') }}</th>
            <th class="roles-actions-col">{{ __('common.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($roles as $role)
            @php
                $permissionNames = $role->permissions->pluck('name')->sort()->values();
                $permissionPreview = $permissionNames->take(3);
                $remainingPermissions = max(0, $permissionNames->count() - $permissionPreview->count());
                $groupPreview = $permissionNames
                    ->map(static fn (string $name): string => str_contains($name, '_') ? ucwords(str_replace('_', ' ', explode('_', $name, 2)[1])) : 'General')
                    ->unique()
                    ->take(2)
                    ->values();
                $isAdminRole = $role->name === 'Admin';
                $isWarehouseRole = in_array($role->name, ['Warehouse User', 'Warehouse'], true);
            @endphp
            <tr>
                <td>
                    <div class="role-name">{{ $role->name }}</div>
                    @if($isAdminRole)
                        <span class="role-system-badge">{{ __('roles.system_role') }}</span>
                        <span class="role-system-badge">{{ __('roles.full_system_access') }}</span>
                    @elseif($isWarehouseRole)
                        <span class="role-ops-badge">{{ __('roles.warehouse_operations_access') }}</span>
                    @endif
                </td>
                <td>
                    <div class="permission-preview">
                        @foreach($groupPreview as $group)
                            <span class="permission-chip">{{ $group }}</span>
                        @endforeach
                        @foreach($permissionPreview as $permissionName)
                            <span class="permission-chip">{{ $permissionName }}</span>
                        @endforeach
                        @if($remainingPermissions > 0)
                            <span class="permission-chip">+{{ $remainingPermissions }} {{ __('common.more') }}</span>
                        @endif
                        @if($permissionPreview->isEmpty())
                            <span class="permission-chip">{{ __('common.no_records_found') }}</span>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="user-count-badge">{{ number_format((int) $role->users_count) }}</span>
                </td>
                <td class="created-col">{{ $role->created_at?->format('Y-m-d') ?? '-' }}</td>
                <td class="roles-actions-col">
                    <div class="roles-actions">
                        @canany(['view_roles','manage_users'])
                            <x-ui.button variant="outline" size="sm" :href="route('admin.roles.show', $role)">{{ __('common.view') }}</x-ui.button>
                        @endcanany
                        @canany(['update_roles','manage_users'])
                            <x-ui.button variant="secondary" size="sm" :href="route('admin.roles.edit', $role)">{{ __('common.edit') }}</x-ui.button>
                        @endcanany
                        @canany(['delete_roles','manage_users'])
                            @if($role->name !== 'Admin')
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button variant="danger" size="sm" type="submit">{{ __('common.delete') }}</x-ui.button>
                                </form>
                            @endif
                        @endcanany
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <x-ui.empty-state :title="__('common.no_records_found')" :description="__('roles.subtitle')" icon="heroicon-o-shield-check" />
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-1">{{ $roles->links() }}</div>
</section>
</div>
@endsection

