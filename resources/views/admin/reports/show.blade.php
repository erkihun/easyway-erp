@extends('layouts.admin')

@section('title', $meta['title'])
@section('page-title', $meta['title'])
@section('page-subtitle', $meta['subtitle'])

@section('content')
    <x-ui.page-header :title="$meta['title']" :subtitle="$meta['subtitle']" icon="heroicon-o-chart-bar">
        <x-slot:actions>
            <x-ui.page-actions>
                @if($hasRows)
                    <x-ui.button size="sm" variant="secondary" :href="$exportLinks['pdf']">{{ __('reports.export_pdf') }}</x-ui.button>
                    <x-ui.button size="sm" variant="secondary" :href="$exportLinks['excel']">{{ __('reports.export_excel') }}</x-ui.button>
                    <x-ui.button size="sm" variant="secondary" :href="$exportLinks['csv']">{{ __('reports.export_csv') }}</x-ui.button>
                @endif
            </x-ui.page-actions>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.filter-bar :action="route($meta['route'])" method="GET">
        @foreach($filters as $filter)
            @if(($filter['type'] ?? '') === 'text')
                <x-ui.input
                    :name="$filter['name']"
                    :label="$filter['label']"
                    :value="request($filter['name'])"
                    :placeholder="$filter['placeholder'] ?? ''"
                    style="min-width: 220px;"
                />
            @elseif(($filter['type'] ?? '') === 'select')
                <x-ui.select :name="$filter['name']" :label="$filter['label']" style="min-width: 190px;">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach(($filter['options'] ?? []) as $option)
                        <option value="{{ $option['value'] }}" @selected((string) request($filter['name']) === (string) $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </x-ui.select>
            @elseif(($filter['type'] ?? '') === 'date')
                <x-ui.input :name="$filter['name']" :label="$filter['label']" :value="request($filter['name'])" type="date" style="min-width: 170px;" />
            @endif
        @endforeach

        <x-ui.button type="submit" size="sm">{{ __('reports.run_report') }}</x-ui.button>
        <x-ui.button variant="outline" size="sm" :href="$resetUrl">{{ __('common.reset') }}</x-ui.button>
    </x-ui.filter-bar>

    @if(count($summaries) > 0)
        <div class="kpi-grid mb-1">
            @foreach($summaries as $summary)
                <x-ui.stat-card
                    :label="$summary['label']"
                    :value="$summary['value']"
                    :icon="$summary['icon'] ?? null"
                    :tone="$summary['tone'] ?? 'default'"
                />
            @endforeach
        </div>
    @endif

    <x-ui.table-shell :title="$meta['title']" :count="$rows->total()">
        @if($rows->count() > 0)
            <table>
                <thead>
                    <tr>
                        @foreach($meta['columns'] as $column)
                            <th @if($column['numeric'] ?? false) style="text-align:right;" @endif>{{ $column['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            @foreach($meta['columns'] as $column)
                                @php($value = $row->{$column['key']} ?? null)
                                <td @if($column['numeric'] ?? false) style="text-align:right;" @endif>
                                    @if(($column['numeric'] ?? false) && is_numeric($value))
                                        {{ number_format((float) $value, 2) }}
                                    @elseif($column['key'] === 'status' && is_string($value))
                                        {{ __('common.status_values.'.$value) }}
                                    @else
                                        {{ $value ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding:.8rem;">
                <x-ui.empty-state
                    icon="heroicon-o-inbox"
                    :title="__('reports.no_data_title')"
                    :description="__('reports.no_data_description')"
                />
            </div>
        @endif
    </x-ui.table-shell>

    @if($rows->hasPages())
        <div class="panel mt-1">
            <div class="panel-body">
                {{ $rows->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
@endsection
