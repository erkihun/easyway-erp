@extends('layouts.admin')
@section('title', __('brands.title'))
@section('page-title', __('brands.title'))
@section('page-subtitle', __('brands.subtitle'))
@section('content')
<div x-data="{ quickAddOpen: false }" class="space-y-4">
    <x-ui.page-header :title="__('brands.title')" :subtitle="__('brands.subtitle')" icon="heroicon-o-tag">
        <x-slot:actions>
            <x-ui.page-actions>
                @can('create_brands')
                    <x-ui.button size="sm" icon="heroicon-o-plus" @click.prevent="quickAddOpen = true">{{ __('brands.quick_add') }}</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" :href="route('admin.product-brands.create')">{{ __('brands.create') }}</x-ui.button>
                @endcan
            </x-ui.page-actions>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="kpi-grid">
        <x-ui.stat-card :label="__('brands.total')" :value="number_format($stats['total'])" icon="heroicon-o-tag" />
        <x-ui.stat-card :label="__('brands.active')" :value="number_format($stats['active'])" icon="heroicon-o-check-circle" tone="success" />
        <x-ui.stat-card :label="__('brands.inactive')" :value="number_format($stats['inactive'])" icon="heroicon-o-pause-circle" tone="warning" />
        <x-ui.stat-card :label="__('brands.linked_products')" :value="number_format($stats['linked_products'])" icon="heroicon-o-cube" tone="info" />
    </div>

    <x-ui.filter-bar>
        <x-ui.input name="q" :label="__('common.search')" :placeholder="__('brands.name_placeholder')" :value="request('q')" />
        <x-ui.select name="status" :label="__('common.status')">
            <option value="">{{ __('brands.status_all') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('common.status_values.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('common.status_values.inactive') }}</option>
        </x-ui.select>
        <x-ui.button type="submit" size="sm" icon="heroicon-o-funnel">{{ __('common.filter') }}</x-ui.button>
        @if(request()->filled('q') || request()->filled('status'))
            <x-ui.button variant="ghost" size="sm" :href="route('admin.product-brands.index')">{{ __('common.reset') }}</x-ui.button>
        @endif
        @can('create_brands')
            <x-ui.button variant="outline" size="sm" icon="heroicon-o-plus" @click.prevent="quickAddOpen = true">{{ __('brands.quick_add') }}</x-ui.button>
        @endcan
    </x-ui.filter-bar>

    <x-ui.table-shell :title="__('brands.directory')" :count="$brands->total()">
        <x-ui.table compact>
            <thead>
                <tr>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('brands.slug') }}</th>
                    <th>{{ __('common.status') }}</th>
                    <th>{{ __('brands.products_count') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    <th class="actions-col">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                    <tr>
                        <td class="font-medium">{{ $brand->name }}</td>
                        <td>{{ $brand->slug }}</td>
                        <td>
                            <x-ui.badge :tone="$brand->is_active ? 'success' : 'neutral'">
                                {{ $brand->is_active ? __('common.status_values.active') : __('common.status_values.inactive') }}
                            </x-ui.badge>
                        </td>
                        <td>{{ number_format((int) $brand->products_count) }}</td>
                        <td>{{ $brand->created_at?->format('Y-m-d') }}</td>
                        <td class="actions-col">
                            <x-ui.table-actions>
                                @can('update_brands')
                                    <x-ui.button variant="secondary" size="sm" :href="route('admin.product-brands.edit', $brand)">{{ __('common.edit') }}</x-ui.button>
                                @endcan
                                @can('delete_brands')
                                    <form method="POST" action="{{ route('admin.product-brands.destroy', $brand) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
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
        <div class="mt-1">{{ $brands->links() }}</div>
    </x-ui.table-shell>

    @can('create_brands')
        <x-ui.modal :title="__('brands.quick_add_title')" :subtitle="__('brands.quick_add_hint')" show="quickAddOpen" max-width="560px">
            <form method="POST" action="{{ route('admin.product-brands.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="quick_add" value="1">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <x-ui.input name="name" :label="__('common.name')" :placeholder="__('brands.name_placeholder')" required />
                <x-ui.textarea name="description" :label="__('brands.description')" />
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
