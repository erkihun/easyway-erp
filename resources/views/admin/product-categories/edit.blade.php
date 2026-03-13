@extends('layouts.admin')
@section('title', __('categories.edit'))
@section('page-title', __('categories.edit'))
@section('page-subtitle', __('categories.subtitle'))
@section('content')
<x-ui.page-header :title="__('categories.edit')" :subtitle="__('categories.subtitle')" icon="heroicon-o-pencil-square">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" size="sm" :href="route('admin.product-categories.index')">{{ __('common.back') }}</x-ui.button>
            @can('delete_categories')
                <form method="POST" action="{{ route('admin.product-categories.destroy', $category) }}" onsubmit="return confirm('{{ __('common.delete') }}?')">
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
            <form method="POST" action="{{ route('admin.product-categories.update', $category) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="row">
                    <x-ui.input name="name" :label="__('common.name')" :value="$category->name" required />
                    <x-ui.input name="slug" :label="__('categories.slug')" :value="$category->slug" readonly disabled />
                </div>
                <x-ui.textarea name="description" :label="__('categories.description')">{{ old('description', $category->description) }}</x-ui.textarea>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                    <span>{{ __('common.active') }}</span>
                </label>
                <p class="text-xs text-slate-500">{{ __('categories.status_help') }}</p>

                <div class="form-actions-sticky mt-1">
                    <x-ui.button variant="ghost" :href="route('admin.product-categories.index')">{{ __('common.cancel') }}</x-ui.button>
                    <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body space-y-2">
            <div class="text-xs text-slate-500">{{ __('categories.products_count') }}</div>
            <div class="text-2xl font-semibold text-slate-900">{{ number_format((int) $category->products_count) }}</div>
            <div class="text-xs text-slate-500">{{ __('common.created_at') }}: {{ $category->created_at?->format('Y-m-d') }}</div>
            <div class="text-xs text-slate-500">{{ __('common.updated_at') }}: {{ $category->updated_at?->format('Y-m-d') }}</div>
        </div>
    </div>
</div>
@endsection
