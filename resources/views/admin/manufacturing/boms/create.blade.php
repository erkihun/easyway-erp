@extends('layouts.admin')
@section('title','Create BOM')
@section('page-title', __('manufacturing.boms'))
@section('content')
<x-ui.page-header title="Create BOM" subtitle="Create a bill of materials for a finished product." icon="heroicon-o-plus-circle" />

<x-ui.card>
    <form method="POST" action="{{ route('admin.manufacturing.boms.store') }}">
        @csrf
        <div class="row">
            <x-ui.select name="product_id" label="Finished Product" required>
                @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </x-ui.select>
            <x-ui.input name="code" label="BOM Code" required />
            <x-ui.input name="name" label="BOM Name" required />
        </div>
        <div class="row mt-1">
            <x-ui.select name="items[0][component_product_id]" label="Component" required>
                @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </x-ui.select>
            <x-ui.input name="items[0][quantity]" label="Quantity Per Unit" type="number" step="0.0001" required />
        </div>
        <div class="form-actions-sticky mt-1">
            <x-ui.button variant="ghost" :href="route('admin.manufacturing.boms.index')">{{ __('common.cancel') }}</x-ui.button>
            <x-ui.button type="submit" icon="heroicon-o-check">{{ __('manufacturing.boms') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection





