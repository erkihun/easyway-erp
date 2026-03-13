@extends('layouts.admin')
@section('title', __('products.create_product'))
@section('page-title', __('products.create_product'))
@section('page-subtitle', __('products.subtitle'))
@section('content')
<x-ui.page-header :title="__('products.create_product')" :subtitle="__('products.subtitle')" icon="heroicon-o-plus-circle" />

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" x-data="{ preview: '' }">
    @csrf
    <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
        <div class="space-y-4">
            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('products.basic_information') }}</h3>
                    <div class="row">
                        <x-ui.input name="name" :label="__('common.name')" required />
                        <x-ui.input name="sku" :label="__('products.sku')" :help="__('products.form_hint_sku')" />
                        <x-ui.input name="barcode" :label="__('products.barcode')" :help="__('products.form_hint_barcode')" />
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('products.pricing_inventory') }}</h3>
                    <div class="row">
                        <x-ui.input type="number" step="0.0001" min="0" name="selling_price" :label="__('products.selling_price')" value="0" required />
                        <x-ui.input type="number" step="0.0001" min="0" name="cost_price" :label="__('products.cost_price')" value="0" required />
                        <x-ui.input type="number" step="0.0001" min="0" name="low_stock_threshold" :label="__('products.low_stock_threshold')" value="0" required />
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('products.description_card') }}</h3>
                    <x-ui.textarea name="description" :label="__('common.description')" rows="4" />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('products.image') }}</h3>
                    <div style="width:100%;height:220px;border-radius:12px;border:1px dashed #cbd5e1;background:#f8fafc;display:grid;place-items:center;overflow:hidden;">
                        <template x-if="preview">
                            <img :src="preview" alt="preview" style="width:100%;height:100%;object-fit:cover;" />
                        </template>
                        <template x-if="!preview">
                            <span class="muted">{{ __('products.no_image') }}</span>
                        </template>
                    </div>
                    <div class="mt-1">
                        <x-ui.input type="file" name="image" :label="__('products.image')" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" :help="__('products.image_help')"
                            x-on:change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => preview = e.target?.result ?? ''; reader.readAsDataURL(file); }" />
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('products.classification') }}</h3>
                    <x-ui.select name="product_category_id" :label="__('products.category')">
                        <option value="">{{ __('common.none') }}</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.select class="mt-1" name="product_brand_id" :label="__('products.brand')">
                        <option value="">{{ __('common.none') }}</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.select class="mt-1" name="unit_of_measure_id" :label="__('products.unit')">
                        <option value="">{{ __('common.none') }}</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->symbol }})</option>
                        @endforeach
                    </x-ui.select>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('products.status_summary') }}</h3>
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>{{ __('common.status_values.active') }}</span>
                    </label>
                    <p class="muted mt-1" style="font-size:.78rem;">{{ __('products.status_active_help') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions-sticky mt-1">
        <x-ui.button variant="ghost" :href="route('admin.products.index')">{{ __('common.cancel') }}</x-ui.button>
        <x-ui.button variant="secondary" type="submit" name="stay" value="1">{{ __('common.save') }} &amp; {{ __('common.edit') }}</x-ui.button>
        <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save') }}</x-ui.button>
    </div>
</form>
@endsection
