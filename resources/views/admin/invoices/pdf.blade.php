<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('invoice.invoice') }} {{ $invoice->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Abyssinica SIL';
            src: url('{{ 'file://'.str_replace("\\", "/", resource_path("fonts/AbyssinicaSIL-Regular.ttf")) }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Noto Sans Ethiopic';
            src: url('{{ 'file://'.str_replace("\\", "/", resource_path("fonts/NotoSansEthiopic-Regular.ttf")) }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Open Sans';
            src: url('{{ 'file://'.str_replace("\\", "/", resource_path("fonts/OpenSans-Regular.ttf")) }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Inter';
            src: url('{{ 'file://'.str_replace("\\", "/", resource_path("fonts/Inter-Regular.ttf")) }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        body {
            font-family: {{ app()->getLocale() === 'am' ? "'Abyssinica SIL', 'Noto Sans Ethiopic', serif" : "'Open Sans', sans-serif" }};
            color: #111827;
            font-size: 11px;
            direction: ltr;
            margin: 0;
        }
        .container { width: 100%; position: relative; }
        .section { margin-bottom: 24px; position: relative; z-index: 1; }
        .header { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brand-mark {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: #eef2ff;
            text-align: center;
            line-height: 50px;
            font-weight: 700;
            color: #4338ca;
        }
        .title {
            font-family: 'Inter', 'Open Sans', sans-serif;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }
        .muted { color: #6b7280; }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
        }
        .meta-table, .items-table, .tax-table, .payments-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td { padding: 4px 0; }
        .items-table th, .items-table td, .tax-table th, .tax-table td, .payments-table th, .payments-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
        }
        .items-table th, .tax-table th, .payments-table th {
            background: #f9fafb;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: .03em;
            color: #4b5563;
            text-align: left;
            font-weight: 600;
        }
        .right {
            text-align: right;
            font-family: 'Inter', 'Open Sans', sans-serif;
        }
        .totals-table td { padding: 7px 0; }
        .totals-table .label { color: #6b7280; }
        .totals-table .grand td {
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            font-weight: 700;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #3730a3;
            font-size: 9px;
        }
        .watermark {
            position: fixed;
            top: 24%;
            left: 22%;
            width: 56%;
            opacity: 0.06;
            text-align: center;
            z-index: 0;
        }
        .watermark img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .footer {
            margin-top: 8px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
@php
    $companyName = (string) ($appSettings['company_name'] ?? $appSettings['system_name'] ?? config('app.name'));
    $companyEmail = (string) ($appSettings['company_email'] ?? '');
    $companyPhone = (string) ($appSettings['company_phone'] ?? '');
    $companyAddress = trim((string) ($appSettings['company_address'] ?? ''));
    $companyVat = trim((string) ($appSettings['company_tax_id'] ?? $appSettings['company_vat'] ?? ''));
    $logoUrl = $appSettings['logo_url'] ?? $appSettings['system_logo_url'] ?? null;
    $logoPath = null;
    if ($logoUrl) {
        $logoParsedPath = parse_url((string) $logoUrl, PHP_URL_PATH);
        if (is_string($logoParsedPath) && $logoParsedPath !== '') {
            $candidate = public_path(ltrim($logoParsedPath, '/'));
            if (is_file($candidate)) {
                $logoPath = $candidate;
            }
        }
        if ($logoPath === null) {
            $logoPath = (string) $logoUrl;
        }
    }

    $status = (string) ($invoice->status->value ?? $invoice->status);
    $statusLabel = __('common.status_values.'.$status);
    if ($statusLabel === 'common.status_values.'.$status) {
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
    }

    $taxRate = $subTotal > 0 ? (($taxTotal / $subTotal) * 100) : 0;
    $taxBreakdown = collect([
        [
            'name' => __('invoice.standard_tax'),
            'rate' => $taxRate,
            'taxable_amount' => $subTotal,
            'tax_amount' => $taxTotal,
        ],
    ])->filter(fn (array $row): bool => (float) $row['tax_amount'] > 0)->values();

    $qrPayload = json_encode([
        'invoice_id' => (string) $invoice->id,
        'invoice_number' => (string) $invoice->invoice_number,
        'amount' => round((float) $balanceDue, 2),
        'company' => $companyName,
        'payment_contact' => trim($companyPhone.' '.$companyEmail),
    ], JSON_UNESCAPED_UNICODE);
    $qrCodeData = null;
    if (is_string($qrPayload) && class_exists(\DNS2D::class)) {
        try {
            $qrCodeData = 'data:image/png;base64,'.\DNS2D::getBarcodePNG($qrPayload, 'QRCODE', 4, 4);
        } catch (\Throwable $e) {
            $qrCodeData = null;
        }
    }
@endphp

@if($logoPath)
    <div class="watermark">
        <img src="{{ $logoPath }}" alt="{{ $companyName }}">
    </div>
@endif

<div class="container">
    <div class="section">
        <table class="header">
            <tr>
                <td style="width:58%;padding-right:16px;">
                    <table>
                        <tr>
                            <td style="width:56px;">
                                @if($logoPath)
                                    <img src="{{ $logoPath }}" alt="{{ $companyName }}" style="max-height:50px;width:auto;object-fit:contain;border-radius:8px;border:1px solid #d1d5db;padding:4px;">
                                @else
                                    <div class="brand-mark">{{ strtoupper(substr($companyName, 0, 1)) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="title">{{ $companyName }}</div>
                                <div class="muted">{{ $companyAddress !== '' ? $companyAddress : '-' }}</div>
                                <div class="muted">{{ $companyPhone !== '' ? $companyPhone : '-' }} | {{ $companyEmail !== '' ? $companyEmail : '-' }}</div>
                                <div class="muted">{{ __('invoice.tax_id') }}: {{ $companyVat !== '' ? $companyVat : '-' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:42%;">
                    <table class="meta-table">
                        <tr><td class="muted">{{ __('invoice.invoice_number') }}</td><td class="right"><strong>{{ $invoice->invoice_number }}</strong></td></tr>
                        <tr><td class="muted">{{ __('invoice.invoice_date') }}</td><td class="right">{{ $invoice->invoice_date?->format('Y-m-d') }}</td></tr>
                        <tr><td class="muted">{{ __('invoice.due_date') }}</td><td class="right">{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td></tr>
                        <tr><td class="muted">{{ __('common.status') }}</td><td class="right"><span class="badge">{{ $statusLabel }}</span></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section card">
        <strong>{{ __('invoice.customer_information') }}</strong>
        <table class="meta-table" style="margin-top:4px;">
            <tr>
                <td>{{ __('invoice.customer') }}: {{ $customer?->name ?? '-' }}</td>
                <td>{{ __('common.phone') }}: {{ $customer?->phone ?? '-' }}</td>
                <td>{{ __('common.email') }}: {{ $customer?->email ?? '-' }}</td>
                <td>{{ __('invoice.customer_tax_id') }}: {{ $customer?->tax_number ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="items-table">
            <thead>
            <tr>
                <th>{{ __('invoice.product') }}</th>
                <th>{{ __('common.description') }}</th>
                <th class="right">{{ __('invoice.quantity') }}</th>
                <th class="right">{{ __('invoice.unit_price') }}</th>
                <th class="right">{{ __('invoice.tax') }}</th>
                <th class="right">{{ __('invoice.discount') }}</th>
                <th class="right">{{ __('invoice.subtotal') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($lineItems as $item)
                <tr>
                    <td>{{ $item->product?->name ?? '-' }}</td>
                    <td>{{ $item->product?->description ?? '-' }}</td>
                    <td class="right">{{ number_format((float) $item->quantity, 2) }}</td>
                    <td class="right">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="right">{{ number_format((float) $item->tax_amount, 2) }}</td>
                    <td class="right">{{ number_format((float) $item->discount_amount, 2) }}</td>
                    <td class="right">{{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">{{ __('invoice.no_line_items') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="width:62%;vertical-align:top;padding-right:14px;">
                    <div class="card" style="margin-bottom:12px;">
                        <strong>{{ __('invoice.tax_breakdown') }}</strong>
                        <table class="tax-table" style="margin-top:8px;">
                            <thead>
                            <tr>
                                <th>{{ __('invoice.tax_name') }}</th>
                                <th class="right">{{ __('invoice.tax_rate') }}</th>
                                <th class="right">{{ __('invoice.taxable_amount') }}</th>
                                <th class="right">{{ __('invoice.tax_amount') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($taxBreakdown as $taxRow)
                                <tr>
                                    <td>{{ $taxRow['name'] }}</td>
                                    <td class="right">{{ number_format((float) $taxRow['rate'], 2) }}%</td>
                                    <td class="right">{{ number_format((float) $taxRow['taxable_amount'], 2) }}</td>
                                    <td class="right">{{ number_format((float) $taxRow['tax_amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td>{{ __('invoice.standard_tax') }}</td>
                                    <td class="right">0.00%</td>
                                    <td class="right">{{ number_format($subTotal, 2) }}</td>
                                    <td class="right">0.00</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card" style="margin-bottom:12px;">
                        <strong>{{ __('invoice.payment_information') }}</strong>
                        <table style="width:100%;margin-top:8px;border-collapse:collapse;">
                            <tr>
                                <td style="vertical-align:top;">
                                    <div>{{ __('invoice.payment_method') }}: {{ __('payment.bank_transfer') }}</div>
                                    <div style="margin-top:4px;">{{ __('invoice.payment_reference') }}: {{ $invoice->invoice_number }}</div>
                                    <div style="margin-top:4px;">{{ __('invoice.transaction_date') }}: {{ now()->format('Y-m-d') }}</div>
                                    <div class="muted" style="margin-top:8px;">{{ __('invoice.payment_instructions') }}</div>
                                </td>
                                <td style="width:132px;text-align:center;">
                                    @if($qrCodeData)
                                        <img src="{{ $qrCodeData }}" alt="{{ __('invoice.payment_qr') }}" style="width:118px;height:118px;border:1px solid #e5e7eb;border-radius:8px;padding:6px;background:#fff;">
                                    @endif
                                    <div class="muted" style="font-size:10px;margin-top:5px;">{{ __('invoice.payment_qr_hint') }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="card">
                        <strong>{{ __('invoice.notes_terms') }}</strong>
                        <p style="margin:8px 0 0 0;">{{ __('invoice.customer_notes') }}: {{ $invoice->salesOrder?->notes ?: '-' }}</p>
                        <p style="margin:6px 0 0 0;">{{ __('invoice.payment_terms') }}: {{ __('invoice.default_payment_terms') }}</p>
                    </div>
                </td>
                <td style="width:38%;vertical-align:top;">
                    <div class="card" style="padding:16px;">
                        <strong>{{ __('invoice.totals') }}</strong>
                        <table class="totals-table" style="margin-top:8px;">
                            <tr><td class="label">{{ __('invoice.subtotal') }}</td><td class="right">{{ number_format($subTotal, 2) }}</td></tr>
                            <tr><td class="label">{{ __('invoice.tax_total') }}</td><td class="right">{{ number_format($taxTotal, 2) }}</td></tr>
                            <tr><td class="label">{{ __('invoice.discount_total') }}</td><td class="right">{{ number_format($discountTotal, 2) }}</td></tr>
                            <tr><td class="label">{{ __('credit_note.title') }}</td><td class="right">{{ number_format($creditNoteTotal, 2) }}</td></tr>
                            <tr><td class="label">{{ __('credit_note.refunds') }}</td><td class="right">{{ number_format($refundTotal, 2) }}</td></tr>
                            <tr><td class="label">{{ __('invoice.paid_amount') }}</td><td class="right">{{ number_format($paidAmount, 2) }}</td></tr>
                            <tr><td class="label">{{ __('invoice.balance_due') }}</td><td class="right">{{ number_format($balanceDue, 2) }}</td></tr>
                            <tr class="grand"><td>{{ __('invoice.grand_total') }}</td><td class="right">{{ number_format($grandTotal, 2) }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="payments-table">
            <thead>
            <tr>
                <th>{{ __('payment.payment_number') }}</th>
                <th>{{ __('invoice.payment_method') }}</th>
                <th>{{ __('payment.reference') }}</th>
                <th>{{ __('common.status') }}</th>
                <th>{{ __('invoice.transaction_date') }}</th>
                <th class="right">{{ __('common.amount') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($invoice->payments as $p)
                <tr>
                    <td>{{ $p->payment_number }}</td>
                    <td>{{ $p->method ?: '-' }}</td>
                    <td>{{ $p->reference ?: '-' }}</td>
                    <td>{{ ucfirst((string) ($p->status->value ?? $p->status)) }}</td>
                    <td>{{ $p->payment_date?->format('Y-m-d') }}</td>
                    <td class="right">{{ number_format((float) $p->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">{{ __('invoice.no_payments') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        {{ $companyName }} | {{ __('invoice.generated_on') }} {{ now()->format('Y-m-d H:i') }} | {{ __('invoice.invoice_footer_note') }}
    </div>
</div>
</body>
</html>
