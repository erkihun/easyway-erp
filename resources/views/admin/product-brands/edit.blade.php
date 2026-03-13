@extends('layouts.admin')
@section('title', __('brands.edit'))
@section('page-title', __('brands.edit'))
@section('page-subtitle', __('brands.subtitle'))
@section('content')
<x-ui.page-header :title="__('brands.edit')" :subtitle="__('brands.subtitle')" icon="heroicon-o-pencil-square">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" size="sm" :href="route('admin.product-brands.index')">{{ __('common.back') }}</x-ui.button>
            @can('delete_brands')
                <form method="POST" action="{{ route('admin.product-brands.destroy', $brand) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">{{ __('common.delete') }}</x-ui.button>
                </form>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.product-brands.update', $brand) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="row">
                    <x-ui.input name="name" :label="__('common.name')" :value="$brand->name" required />
                    <x-ui.input name="slug" :label="__('brands.slug')" :value="$brand->slug" readonly disabled />
                </div>
                <x-ui.textarea name="description" :label="__('brands.description')">{{ old('description', $brand->description) }}</x-ui.textarea>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand->is_active))>
                    <span>{{ __('common.active') }}</span>
                </label>
                <p class="text-xs text-slate-500">{{ __('brands.status_help') }}</p>

                <div class="form-actions-sticky mt-1">
                    <x-ui.button variant="ghost" :href="route('admin.product-brands.index')">{{ __('common.cancel') }}</x-ui.button>
                    <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body space-y-2">
            <div class="text-xs text-slate-500">{{ __('brands.products_count') }}</div>
            <div class="text-2xl font-semibold text-slate-900">{{ number_format((int) $brand->products_count) }}</div>
            <div class="text-xs text-slate-500">{{ __('common.created_at') }}: {{ $brand->created_at?->format('Y-m-d') }}</div>
            <div class="text-xs text-slate-500">{{ __('common.updated_at') }}: {{ $brand->updated_at?->format('Y-m-d') }}</div>
        </div>
    </div>
</div>
@endsection
