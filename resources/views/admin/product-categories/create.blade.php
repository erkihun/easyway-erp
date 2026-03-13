@extends('layouts.admin')
@section('title', __('categories.create'))
@section('page-title', __('categories.create'))
@section('page-subtitle', __('categories.subtitle'))
@section('content')
<x-ui.page-header :title="__('categories.create')" :subtitle="__('categories.subtitle')" icon="heroicon-o-plus-circle">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="ghost" size="sm" :href="route('admin.product-categories.index')">{{ __('common.back') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.product-categories.store') }}" class="space-y-4">
            @csrf
            <div class="row">
                <x-ui.input name="name" :label="__('common.name')" :placeholder="__('categories.name_placeholder')" required />
                <x-ui.input name="slug_preview" :label="__('categories.slug')" :value="__('common.optional')" readonly disabled />
            </div>

            <x-ui.textarea name="description" :label="__('categories.description')" />

            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" checked>
                <span>{{ __('common.active') }}</span>
            </label>
            <p class="text-xs text-slate-500">{{ __('categories.status_help') }}</p>

            <div class="form-actions-sticky mt-1">
                <x-ui.button variant="ghost" :href="route('admin.product-categories.index')">{{ __('common.cancel') }}</x-ui.button>
                <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }}</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
