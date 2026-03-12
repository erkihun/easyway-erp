@extends('layouts.admin')
@section('title', __('permissions.title'))
@section('page-title', __('permissions.title'))
@section('page-subtitle', __('permissions.subtitle'))
@section('content')
<x-ui.page-header :title="__('permissions.title')" :subtitle="__('permissions.subtitle')" icon="heroicon-o-lock-closed">
    <x-slot:actions>
        <x-ui.page-actions>
            @canany(['view_roles','manage_users'])
                <x-ui.button variant="secondary" size="sm" :href="route('admin.roles.index')">{{ __('navigation.roles') }}</x-ui.button>
            @endcanany
            @canany(['view_users','manage_users'])
                <x-ui.button variant="outline" size="sm" :href="route('admin.users.index')">{{ __('navigation.users') }}</x-ui.button>
            @endcanany
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :placeholder="__('common.search').' '.__('permissions.name')" :value="request('q')" />
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.permissions.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<div class="dashboard-kpi-grid mb-1">
    <x-ui.stat-card :label="__('permissions.title')" :value="$permissions->total()" :helper="__('common.total_permission_records')" icon="heroicon-o-lock-closed" />
    <x-ui.stat-card :label="__('common.groups')" :value="$grouped->count()" :helper="__('common.module_domain_groupings')" icon="heroicon-o-chart-bar" />
</div>

<x-ui.table-shell :title="__('permissions.title')" :count="$permissions->total()">
    <table>
        <thead>
        <tr>
            <th>{{ __('permissions.name') }}</th>
            <th>{{ __('permissions.group') }}</th>
            <th>{{ __('common.guard') }}</th>
            <th style="text-align:right;">{{ __('permissions.roles_using') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($permissions as $permission)
            @php
                $groupName = __('common.system');
                if (str_contains($permission->name, '_')) {
                    $groupName = \Illuminate\Support\Str::of($permission->name)->after('_')->replace('_', ' ')->title()->toString();
                }
            @endphp
            <tr>
                <td><strong>{{ $permission->name }}</strong></td>
                <td><span class="badge badge-neutral">{{ $groupName }}</span></td>
                <td>{{ $permission->guard_name }}</td>
                <td style="text-align:right;">{{ number_format((int) $permission->roles_count) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <x-ui.empty-state :title="__('common.no_records_found')" :description="__('permissions.subtitle')" icon="heroicon-o-lock-closed" />
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $permissions->links() }}</div>
</x-ui.table-shell>

<x-ui.card>
    <x-slot:header><h3 class="table-shell-title">{{ __('permissions.group') }}</h3></x-slot:header>
    @if($grouped->isEmpty())
        <x-ui.empty-state :title="__('common.no_records_found')" :description="__('permissions.subtitle')" icon="heroicon-o-archive-box" />
    @else
        <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
            @foreach($grouped as $group => $items)
                <div class="panel">
                    <div class="panel-body" style="border-bottom:1px solid var(--line);padding-bottom:.45rem;">
                        <strong style="font-size:.88rem;">{{ $group }}</strong>
                        <div class="muted" style="font-size:.75rem;">{{ __('permissions.permissions_count', ['count' => $items->count()]) }}</div>
                    </div>
                    <div class="panel-body" style="display:grid;gap:.35rem;">
                        @foreach($items as $permission)
                            <div style="display:flex;justify-content:space-between;gap:.5rem;align-items:center;">
                                <span style="font-size:.8rem;color:#334155;">{{ $permission->name }}</span>
                                <span class="badge badge-info">{{ $permission->roles_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-ui.card>
@endsection

