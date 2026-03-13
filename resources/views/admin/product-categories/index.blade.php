@extends('layouts.admin')
@section('title', __('categories.title'))
@section('page-title', __('categories.title'))
@section('page-subtitle', __('categories.subtitle'))
@section('content')
<div x-data="{ quickAddOpen: false }" class="space-y-4">
    <x-ui.page-header :title="__('categories.title')" :subtitle="__('categories.subtitle')" icon="heroicon-o-squares-2x2">
        <x-slot:actions>
            <x-ui.page-actions>
                @can('create_categories')
                    <x-ui.button size="sm" icon="heroicon-o-plus" @click.prevent="quickAddOpen = true">{{ __('categories.quick_add') }}</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" :href="route('admin.product-categories.create')">{{ __('categories.create') }}</x-ui.button>
                @endcan
            </x-ui.page-actions>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="kpi-grid">
        <x-ui.stat-card :label="__('categories.total')" :value="number_format($stats['total'])" icon="heroicon-o-squares-2x2" />
        <x-ui.stat-card :label="__('categories.active')" :value="number_format($stats['active'])" icon="heroicon-o-check-circle" tone="success" />
        <x-ui.stat-card :label="__('categories.inactive')" :value="number_format($stats['inactive'])" icon="heroicon-o-pause-circle" tone="warning" />
        <x-ui.stat-card :label="__('categories.linked_products')" :value="number_format($stats['linked_products'])" icon="heroicon-o-cube" tone="info" />
    </div>

    <x-ui.filter-bar>
        <x-ui.input name="q" :label="__('common.search')" :placeholder="__('categories.name_placeholder')" :value="request('q')" />
        <x-ui.select name="status" :label="__('common.status')">
            <option value="">{{ __('categories.status_all') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('common.status_values.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('common.status_values.inactive') }}</option>
        </x-ui.select>
        <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
        @if(request()->filled('q') || request()->filled('status'))
            <x-ui.button variant="ghost" size="sm" :href="route('admin.product-categories.index')">{{ __('common.reset') }}</x-ui.button>
        @endif
        @can('create_categories')
            <x-ui.button variant="outline" size="sm" icon="heroicon-o-plus" @click.prevent="quickAddOpen = true">{{ __('categories.quick_add') }}</x-ui.button>
        @endcan
    </x-ui.filter-bar>

    <x-ui.table-shell :title="__('categories.directory')" :count="$categories->total()">
        <x-ui.table compact>
            <thead>
                <tr>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('categories.slug') }}</th>
                    <th>{{ __('common.status') }}</th>
                    <th>{{ __('categories.products_count') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    <th class="actions-col">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td class="font-medium">{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>
                            <x-ui.badge :tone="$category->is_active ? 'success' : 'neutral'">
                                {{ $category->is_active ? __('common.status_values.active') : __('common.status_values.inactive') }}
                            </x-ui.badge>
                        </td>
                        <td>{{ number_format((int) $category->products_count) }}</td>
                        <td>{{ $category->created_at?->format('Y-m-d') }}</td>
                        <td class="actions-col">
                            <x-ui.table-actions>
                                @can('update_categories')
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.product-categories.edit', $category)">{{ __('common.edit') }}</x-ui.button>
                                @endcan
                                @can('delete_categories')
                                    <form method="POST" action="{{ route('admin.product-categories.destroy', $category) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button variant="danger" size="sm" type="submit">{{ __('common.delete') }}</x-ui.button>
                                    </form>
                                @endcan
                            </x-ui.table-actions>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">{{ __('common.no_records_found') }}</td></tr>
                @endforelse
            </tbody>
        </x-ui.table>
        <div class="mt-1">{{ $categories->links() }}</div>
    </x-ui.table-shell>

    @can('create_categories')
        <x-ui.modal :title="__('categories.quick_add_title')" :subtitle="__('categories.quick_add_hint')" show="quickAddOpen" max-width="560px">
            <form method="POST" action="{{ route('admin.product-categories.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="quick_add" value="1">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <x-ui.input name="name" :label="__('common.name')" :placeholder="__('categories.name_placeholder')" required />
                <x-ui.textarea name="description" :label="__('categories.description')" />
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>{{ __('common.active') }}</span>
                </label>
                <div class="flex justify-end gap-2">
                    <x-ui.button type="button" variant="ghost" @click.prevent="quickAddOpen = false">{{ __('common.cancel') }}</x-ui.button>
                    <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    @endcan
</div>
@endsection
