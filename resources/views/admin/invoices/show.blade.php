@extends('layouts.admin')
@section('title', __('invoice.invoice_details'))
@section('page-title', __('invoice.invoice_details'))
@section('page-subtitle', __('invoice.subtitle'))
@section('content')
@php
    $companyName = (string) ($appSettings['company_name'] ?? $appSettings['system_name'] ?? config('app.name'));
    $companyEmail = (string) ($appSettings['company_email'] ?? '');
    $companyPhone = (string) ($appSettings['company_phone'] ?? '');
    $companyAddress = trim((string) ($appSettings['company_address'] ?? ''));
    $companyVat = trim((string) ($appSettings['company_tax_id'] ?? $appSettings['company_vat'] ?? ''));
    $logoUrl = $appSettings['logo_url'] ?? $appSettings['system_logo_url'] ?? null;
    $status = (string) ($invoice->status->value ?? $invoice->status);
    $isOverdue = $status === 'issued' && $invoice->due_date && $invoice->due_date->isPast();
    $statusTone = match (true) {
        $status === 'paid' => 'success',
        $isOverdue => 'danger',
        $status === 'draft' => 'warning',
        $status === 'cancelled' => 'neutral',
        default => 'info',
    };
    $statusLabel = $isOverdue ? __('invoice.overdue') : __('common.status_values.'.$status);
    if ($statusLabel === 'common.status_values.'.$status) {
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
    }

    $customerAddress = '-';
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

<style>
    .invoice-document {
        position: relative;
    }
    .invoice-watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
    }
    .invoice-watermark img {
        width: 46%;
        max-width: 440px;
        opacity: .06;
        object-fit: contain;
    }
    .invoice-section {
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }
    .invoice-meta-grid {
        display: grid;
        gap: .5rem .75rem;
        grid-template-columns: auto 1fr;
    }
    .invoice-totals td,
    .invoice-tax td {
        padding: .45rem .25rem;
    }
    .invoice-totals .grand td {
        border-top: 1px solid #e5e7eb;
        padding-top: .65rem;
        font-weight: 700;
    }
    .invoice-tax th,
    .invoice-tax td {
        padding: .5rem .625rem;
    }
    @media print {
        header, nav, aside, .sidebar, .topbar, .page-actions, .no-print {
            display: none !important;
        }
        main, .content, .invoice-document {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }
        .panel {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
            break-inside: avoid;
        }
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
    }
</style>

<x-ui.page-header :title="__('invoice.invoice_number').': '.$invoice->invoice_number" :subtitle="__('invoice.details_subtitle')" icon="heroicon-o-document-text" class="no-print">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.invoices.index')">{{ __('common.back') }}</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.invoices.edit', $invoice)">{{ __('common.edit') }}</x-ui.button>
            <x-ui.button variant="ghost" onclick="window.print(); return false;">{{ __('invoice.print_invoice') }}</x-ui.button>
            <x-ui.button variant="outline" :href="route('admin.invoices.pdf', $invoice)">{{ __('invoice.download_pdf') }}</x-ui.button>
            @canany(['manage_accounting', 'manage_credit_notes'])
                <x-ui.button variant="secondary" :href="route('admin.credit-notes.create', ['invoice_id' => $invoice->id])">{{ __('credit_note.create_credit_note') }}</x-ui.button>
            @endcanany
            @if($customer?->email)
                <x-ui.button
                    variant="ghost"
                    :href="'mailto:'.$customer->email.'?subject='.rawurlencode(__('invoice.email_subject', ['number' => $invoice->invoice_number]))">
                    {{ __('invoice.send_email') }}
                </x-ui.button>
            @endif
            @can('manage_accounting')
                <x-ui.button variant="success" :href="route('admin.payments.create', ['invoice_id' => $invoice->id])">{{ __('invoice.register_payment') }}</x-ui.button>
            @endcan
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="invoice-document">
    @if($logoUrl)
        <div class="invoice-watermark">
            <img src="{{ $logoUrl }}" alt="{{ $companyName }}">
        </div>
    @endif

    <div class="panel invoice-section">
        <div class="panel-body" style="padding:20px;">
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <div class="flex items-center gap-3">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $companyName }}" style="max-height:50px;width:auto;object-fit:contain;border-radius:8px;border:1px solid #e5e7eb;background:#fff;padding:4px;" />
                        @else
                            <div style="width:50px;height:50px;border-radius:8px;border:1px solid #e5e7eb;background:#eef2ff;display:grid;place-items:center;font-weight:700;color:#4338ca;">{{ strtoupper(substr($companyName, 0, 1)) }}</div>
                        @endif
                        <div>
                            <div class="text-lg font-semibold">{{ $companyName }}</div>
                            <div class="muted">{{ $companyAddress !== '' ? $companyAddress : '-' }}</div>
                            <div class="muted">{{ $companyPhone !== '' ? $companyPhone : '-' }} | {{ $companyEmail !== '' ? $companyEmail : '-' }}</div>
                            <div class="muted">{{ __('invoice.tax_id') }}: {{ $companyVat !== '' ? $companyVat : '-' }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="invoice-meta-grid">
                        <div class="muted">{{ __('invoice.invoice_number') }}</div><div class="font-semibold">{{ $invoice->invoice_number }}</div>
                        <div class="muted">{{ __('invoice.invoice_date') }}</div><div>{{ $invoice->invoice_date?->format('Y-m-d') }}</div>
                        <div class="muted">{{ __('invoice.due_date') }}</div><div>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</div>
                        <div class="muted">{{ __('common.status') }}</div><div><x-ui.badge :tone="$statusTone">{{ $statusLabel }}</x-ui.badge></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel invoice-section">
        <div class="panel-body">
            <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.customer_information') }}</h3>
            <div class="grid gap-2 md:grid-cols-2">
                <div><span class="muted">{{ __('invoice.customer') }}:</span> {{ $customer?->name ?? '-' }}</div>
                <div><span class="muted">{{ __('common.phone') }}:</span> {{ $customer?->phone ?? '-' }}</div>
                <div><span class="muted">{{ __('common.email') }}:</span> {{ $customer?->email ?? '-' }}</div>
                <div><span class="muted">{{ __('invoice.customer_tax_id') }}:</span> {{ $customer?->tax_number ?? '-' }}</div>
                <div class="md:col-span-2"><span class="muted">{{ __('invoice.customer_address') }}:</span> {{ $customerAddress }}</div>
            </div>
        </div>
    </div>

    <div class="panel invoice-section">
        <div class="panel-body">
            <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.line_items') }}</h3>
            <x-ui.table compact>
                <thead>
                    <tr>
                        <th>{{ __('invoice.product') }}</th>
                        <th>{{ __('common.description') }}</th>
                        <th style="text-align:right;">{{ __('invoice.quantity') }}</th>
                        <th style="text-align:right;">{{ __('invoice.unit_price') }}</th>
                        <th style="text-align:right;">{{ __('invoice.tax') }}</th>
                        <th style="text-align:right;">{{ __('invoice.discount') }}</th>
                        <th style="text-align:right;">{{ __('invoice.subtotal') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lineItems as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? '-' }}</td>
                            <td>{{ $item->product?->description ?? '-' }}</td>
                            <td style="text-align:right;">{{ number_format((float) $item->quantity, 2) }}</td>
                            <td style="text-align:right;">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td style="text-align:right;">{{ number_format((float) $item->tax_amount, 2) }}</td>
                            <td style="text-align:right;">{{ number_format((float) $item->discount_amount, 2) }}</td>
                            <td style="text-align:right;">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="muted">{{ __('invoice.no_line_items') }}</td></tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.55fr_1fr] invoice-section">
        <div class="space-y-4">
            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.tax_breakdown') }}</h3>
                    <x-ui.table compact class="invoice-tax">
                        <thead>
                        <tr>
                            <th>{{ __('invoice.tax_name') }}</th>
                            <th style="text-align:right;">{{ __('invoice.tax_rate') }}</th>
                            <th style="text-align:right;">{{ __('invoice.taxable_amount') }}</th>
                            <th style="text-align:right;">{{ __('invoice.tax_amount') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($taxBreakdown as $taxRow)
                            <tr>
                                <td>{{ $taxRow['name'] }}</td>
                                <td style="text-align:right;">{{ number_format((float) $taxRow['rate'], 2) }}%</td>
                                <td style="text-align:right;">{{ number_format((float) $taxRow['taxable_amount'], 2) }}</td>
                                <td style="text-align:right;">{{ number_format((float) $taxRow['tax_amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td>{{ __('invoice.standard_tax') }}</td>
                                <td style="text-align:right;">0.00%</td>
                                <td style="text-align:right;">{{ number_format($subTotal, 2) }}</td>
                                <td style="text-align:right;">0.00</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </x-ui.table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.payment_information') }}</h3>
                    <div class="grid gap-4 md:grid-cols-[1fr_auto]">
                        <div class="space-y-2">
                            <div><span class="muted">{{ __('invoice.payment_method') }}:</span> {{ __('payment.bank_transfer') }}</div>
                            <div><span class="muted">{{ __('invoice.payment_reference') }}:</span> {{ $invoice->invoice_number }}</div>
                            <div><span class="muted">{{ __('invoice.transaction_date') }}:</span> {{ now()->format('Y-m-d') }}</div>
                            <div class="muted">{{ __('invoice.payment_instructions') }}</div>
                        </div>
                        <div class="text-center">
                            @if($qrCodeData)
                                <img src="{{ $qrCodeData }}" alt="{{ __('invoice.payment_qr') }}" style="width:118px;height:118px;border:1px solid #e5e7eb;border-radius:10px;padding:6px;background:#fff;" />
                            @endif
                            <div class="muted" style="font-size:.78rem;margin-top:.35rem;">{{ __('invoice.payment_qr_hint') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.notes_terms') }}</h3>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <div class="muted">{{ __('invoice.customer_notes') }}</div>
                            <p style="margin:.35rem 0 0;">{{ $invoice->salesOrder?->notes ?: '-' }}</p>
                        </div>
                        <div>
                            <div class="muted">{{ __('invoice.payment_terms') }}</div>
                            <p style="margin:.35rem 0 0;">{{ __('invoice.default_payment_terms') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body" style="padding:16px;">
                <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.totals') }}</h3>
                <table style="width:100%;" class="invoice-totals">
                    <tr><td class="muted">{{ __('invoice.subtotal') }}</td><td style="text-align:right;">{{ number_format($subTotal, 2) }}</td></tr>
                    <tr><td class="muted">{{ __('invoice.tax_total') }}</td><td style="text-align:right;">{{ number_format($taxTotal, 2) }}</td></tr>
                    <tr><td class="muted">{{ __('invoice.discount_total') }}</td><td style="text-align:right;">{{ number_format($discountTotal, 2) }}</td></tr>
                    <tr><td class="muted">{{ __('credit_note.title') }}</td><td style="text-align:right;">{{ number_format($creditNoteTotal, 2) }}</td></tr>
                    <tr><td class="muted">{{ __('credit_note.refunds') }}</td><td style="text-align:right;">{{ number_format($refundTotal, 2) }}</td></tr>
                    <tr><td class="muted">{{ __('invoice.paid_amount') }}</td><td style="text-align:right;">{{ number_format($paidAmount, 2) }}</td></tr>
                    <tr><td class="muted">{{ __('invoice.balance_due') }}</td><td style="text-align:right;">{{ number_format($balanceDue, 2) }}</td></tr>
                    <tr class="grand"><td>{{ __('invoice.grand_total') }}</td><td style="text-align:right;">{{ number_format($grandTotal, 2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="panel invoice-section">
        <div class="panel-body">
            <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.payment_history') }}</h3>
            <x-ui.table compact>
                <thead>
                    <tr>
                        <th>{{ __('payment.payment_number') }}</th>
                        <th>{{ __('invoice.payment_method') }}</th>
                        <th>{{ __('payment.reference') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('invoice.transaction_date') }}</th>
                        <th style="text-align:right;">{{ __('common.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->payments as $p)
                        <tr>
                            <td>{{ $p->payment_number }}</td>
                            <td>{{ $p->method ?: '-' }}</td>
                            <td>{{ $p->reference ?: '-' }}</td>
                            <td><x-ui.status-badge :status="$p->status" /></td>
                            <td>{{ $p->payment_date?->format('Y-m-d') }}</td>
                            <td style="text-align:right;">{{ number_format((float) $p->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">{{ __('invoice.no_payments') }}</td></tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
    </div>

    @can('manage_accounting')
        <div class="panel invoice-section no-print" id="payment-form">
            <div class="panel-body">
                <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.register_payment') }}</h3>
                <form method="POST" action="{{ route('admin.invoices.payments') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <div class="row">
                        <x-ui.input type="date" name="payment_date" :label="__('invoice.transaction_date')" :value="now()->toDateString()" required />
                        <x-ui.input name="amount" :label="__('common.amount')" type="number" step="0.0001" min="0.01" required />
                        <x-ui.select name="method" :label="__('invoice.payment_method')" required>
                            <option value="cash">{{ __('payment.cash') }}</option>
                            <option value="bank_transfer">{{ __('payment.bank_transfer') }}</option>
                            <option value="credit_card">{{ __('payment.credit_card') }}</option>
                            <option value="mobile_payment">{{ __('payment.mobile_payment') }}</option>
                        </x-ui.select>
                        <x-ui.input name="reference" :label="__('payment.reference')" />
                    </div>
                    <x-ui.textarea name="notes" :label="__('payment.notes')" rows="2" />
                    <div class="flex justify-end gap-2">
                        <x-ui.button type="submit" variant="success" icon="heroicon-o-credit-card">{{ __('invoice.register_payment') }}</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="panel invoice-section">
        <div class="panel-body">
            <div class="muted text-sm">{{ __('invoice.invoice_footer_note') }}</div>
        </div>
    </div>
</div>
@endsection
