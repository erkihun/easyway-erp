@extends('layouts.admin')

@section('title', __('reports.title'))
@section('page-title', __('reports.title'))
@section('page-subtitle', __('reports.subtitle'))

@section('content')
    <x-ui.page-header :title="__('reports.title')" :subtitle="__('reports.subtitle')" icon="heroicon-o-chart-bar" />

    @php
        $reports = [
            ['route' => 'admin.reports.inventory', 'label' => __('reports.inventory'), 'desc' => __('reports.inventory_subtitle')],
            ['route' => 'admin.reports.low-stock', 'label' => __('reports.low_stock'), 'desc' => __('reports.low_stock_subtitle')],
            ['route' => 'admin.reports.sales', 'label' => __('reports.sales'), 'desc' => __('reports.sales_subtitle')],
            ['route' => 'admin.reports.purchase', 'label' => __('reports.purchase'), 'desc' => __('reports.purchase_subtitle')],
            ['route' => 'admin.reports.profit', 'label' => __('reports.profit'), 'desc' => __('reports.profit_subtitle')],
            ['route' => 'admin.reports.valuation', 'label' => __('reports.valuation'), 'desc' => __('reports.valuation_subtitle')],
        ];
    @endphp

    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
        @foreach($reports as $report)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="table-shell-title">{{ $report['label'] }}</h3>
                </x-slot:header>
                <p class="muted" style="margin:.1rem 0 .7rem;">{{ $report['desc'] }}</p>
                <div class="actions" style="margin-bottom:.55rem;">
                    <span class="badge badge-neutral">{{ __('reports.export_pdf') }}</span>
                    <span class="badge badge-neutral">{{ __('reports.export_excel') }}</span>
                    <span class="badge badge-neutral">{{ __('reports.export_csv') }}</span>
                </div>
                <x-ui.button :href="route($report['route'])">{{ __('reports.view_report') }}</x-ui.button>
            </x-ui.card>
        @endforeach
    </div>
@endsection



