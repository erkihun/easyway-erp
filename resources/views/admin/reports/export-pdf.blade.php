<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $ethiopicRegular = str_replace('\\', '/', resource_path('fonts/NotoSansEthiopic-Regular.ttf'));
        $ethiopicBold = str_replace('\\', '/', resource_path('fonts/NotoSansEthiopic-Bold.ttf'));
    @endphp
    <meta charset="UTF-8">
    <title>{{ $metadata['title'] }}</title>
    <style>
        @font-face {
            font-family: 'NotoSansEthiopicLocal';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $ethiopicRegular }}') format('truetype');
        }

        @font-face {
            font-family: 'NotoSansEthiopicLocal';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $ethiopicBold }}') format('truetype');
        }

        @page {
            margin: 24px 26px 34px;
        }

        body {
            font-family: 'NotoSansEthiopicLocal', 'DejaVu Sans', sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.45;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 34%;
            left: 10%;
            width: 80%;
            text-align: center;
            opacity: .05;
            z-index: -1;
        }

        .watermark img {
            max-width: 420px;
            max-height: 420px;
            object-fit: contain;
        }

        .report-header {
            border: 1px solid #dbe4ef;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 14px;
            background: #f8fafc;
        }

        .report-brand-row {
            display: table;
            width: 100%;
        }

        .brand-logo,
        .brand-copy {
            display: table-cell;
            vertical-align: middle;
        }

        .brand-logo {
            width: 72px;
        }

        .brand-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .brand-mark {
            width: 60px;
            height: 60px;
            line-height: 60px;
            text-align: center;
            border-radius: 10px;
            font-weight: 700;
            color: #0f172a;
            background: #e2e8f0;
            display: inline-block;
        }

        .system-name {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .company-name {
            color: #475569;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 700;
            margin: 4px 0 0;
        }

        .report-meta {
            margin-top: 8px;
            border-top: 1px solid #dbe4ef;
            padding-top: 7px;
            color: #334155;
            font-size: 11px;
            line-height: 1.55;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 11px;
            line-height: 1.42;
        }

        thead th {
            background: #e2e8f0;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 7px 6px;
            text-align: left;
            font-weight: 700;
        }

        tbody td {
            border: 1px solid #e2e8f0;
            padding: 6px;
            color: #1e293b;
            word-wrap: break-word;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .is-numeric {
            text-align: right;
            white-space: nowrap;
        }

        .report-footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
    @if(!empty($branding['watermark_data_uri']))
        <div class="watermark">
            <img src="{{ $branding['watermark_data_uri'] }}" alt="watermark">
        </div>
    @endif

    <section class="report-header">
        <div class="report-brand-row">
            <div class="brand-logo">
                @if(!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_data_uri'] ?? $branding['logo_url'] }}" alt="logo">
                @else
                    <span class="brand-mark">{{ strtoupper(substr((string) ($branding['system_name'] ?? 'E'), 0, 1)) }}</span>
                @endif
            </div>
            <div class="brand-copy">
                <div class="system-name">{{ $branding['system_name'] }}</div>
                <div class="company-name">{{ $branding['company_name'] }}</div>
                <div class="report-title">{{ $metadata['title'] }}</div>
            </div>
        </div>
        <div class="report-meta">
            <div>{{ __('reports.exported_at') }}: {{ $metadata['exported_at'] }}</div>
            <div>{{ __('reports.generated_by') }}: {{ $metadata['generated_by'] }}</div>
            <div>{{ __('reports.applied_filters') }}: {{ $metadata['filters_text'] }}</div>
            <div>{{ __('common.currency') }}: {{ $metadata['currency'] }}</div>
            @if(!empty($branding['company_email']) || !empty($branding['company_phone']))
                <div>{{ $branding['company_email'] }} @if(!empty($branding['company_email']) && !empty($branding['company_phone'])) | @endif {{ $branding['company_phone'] }}</div>
            @endif
        </div>
    </section>

    <table>
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th @if($column['numeric'] ?? false) class="is-numeric" @endif>{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($columns as $column)
                        @php($value = $row->{$column['key']} ?? '')
                        <td @if($column['numeric'] ?? false) class="is-numeric" @endif>
                            @if(($column['numeric'] ?? false) && is_numeric($value))
                                {{ number_format((float) $value, 2) }}
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" style="text-align:center;">{{ __('reports.no_data_title') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-footer">
        {{ $branding['system_name'] }} | {{ $metadata['title'] }}
    </div>
</body>
</html>
