@extends('layouts.admin')
@section('title', __('users.title'))
@section('page-title', __('users.title'))
@section('page-subtitle', __('users.subtitle'))
@section('content')
<x-ui.page-header :title="__('users.title')" :subtitle="__('users.subtitle')" icon="heroicon-o-user-group">
    <x-slot:actions>
        <x-ui.page-actions>
            @canany(['create_users','manage_users'])
                <x-ui.button size="sm" :href="route('admin.users.create')" icon="heroicon-o-plus">{{ __('users.create') }}</x-ui.button>
            @endcanany
            @canany(['view_roles','manage_users'])
                <x-ui.button variant="secondary" size="sm" :href="route('admin.roles.index')">{{ __('navigation.roles') }}</x-ui.button>
            @endcanany
            @canany(['view_permissions','manage_users'])
                <x-ui.button variant="secondary" size="sm" :href="route('admin.permissions.index')">{{ __('navigation.permissions') }}</x-ui.button>
            @endcanany
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.filter-bar>
    <x-ui.input name="q" :label="__('common.search')" :placeholder="__('common.search').' '.__('common.name').' / '.__('common.email')" :value="request('q')" />
    <x-ui.select name="role" :label="__('common.role')">
        <option value="">{{ __('common.all') }} {{ __('navigation.roles') }}</option>
        @foreach($roles as $role)
            <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
        @endforeach
    </x-ui.select>
    <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
    @if(request()->filled('q') || request()->filled('role'))
        <x-ui.button variant="ghost" size="sm" :href="route('admin.users.index')">{{ __('common.reset') }}</x-ui.button>
    @endif
</x-ui.filter-bar>

<x-ui.table-shell :title="__('users.directory')" :count="$users->total()">
    <table>
        <thead>
        <tr>
            <th>{{ __('common.name') }}</th>
            <th>{{ __('common.email') }}</th>
            <th>{{ __('users.roles') }}</th>
            <th>{{ __('common.created_at') }}</th>
            <th class="actions-col">{{ __('common.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <div class="table-actions">
                        @forelse($user->roles as $role)
                            @php
                                $roleTone = 'info';
                                if ($role->name === 'Admin') {
                                    $roleTone = 'danger';
                                } elseif (in_array($role->name, ['Warehouse User', 'Warehouse'], true)) {
                                    $roleTone = 'success';
                                }
                            @endphp
                            <span class="badge badge-{{ $roleTone }}">{{ $role->name }}</span>
                        @empty
                            <span class="badge badge-neutral">{{ __('common.none') }}</span>
                        @endforelse
                    </div>
                </td>
                <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                <td class="actions-col">
                    <x-ui.table-actions>
                        @canany(['view_users','manage_users'])
                            <x-ui.button variant="outline" size="sm" :href="route('admin.users.show',$user)">{{ __('common.view') }}</x-ui.button>
                        @endcanany
                        @canany(['update_users','manage_users'])
                            <x-ui.button variant="secondary" size="sm" :href="route('admin.users.edit',$user)">{{ __('common.edit') }}</x-ui.button>
                        @endcanany
                        @canany(['delete_users','manage_users'])
                            @if((string)auth()->id() !== (string)$user->id)
                                <form method="POST" action="{{ route('admin.users.destroy',$user) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button variant="danger" size="sm" type="submit">{{ __('common.delete') }}</x-ui.button>
                                </form>
                            @endif
                        @endcanany
                    </x-ui.table-actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">{{ __('common.no_records_found') }}</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-1">{{ $users->links() }}</div>
</x-ui.table-shell>
@endsection

