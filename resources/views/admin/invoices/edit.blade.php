@extends('layouts.admin')
@section('title', __('invoice.edit_invoice'))
@section('page-title', __('invoice.edit_invoice'))
@section('page-subtitle', __('invoice.subtitle'))
@section('content')
<x-ui.page-header :title="__('invoice.edit_invoice')" :subtitle="__('invoice.edit_subtitle')" icon="heroicon-o-pencil-square">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="outline" :href="route('admin.invoices.show', $invoice)">{{ __('common.view') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<form method="POST" action="{{ route('admin.invoices.update', $invoice) }}"
      x-data="invoiceForm({
        salesOrders: @js($salesOrdersPayload),
        selectedSalesOrderId: @js((string) old('sales_order_id', (string) ($invoice->sales_order_id ?? ''))),
        initialLines: @js(($invoice->salesOrder?->items ?? collect())->map(fn($item) => [
            'product' => (string) ($item->product?->name ?? ''),
            'description' => (string) ($item->product?->description ?? ''),
            'qty' => (float) $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'tax' => (float) $item->tax_amount,
            'discount' => (float) $item->discount_amount,
            'subtotal' => (float) $item->line_total,
        ])->values()),
      })">
    @csrf
    @method('PUT')
    <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
        <div class="space-y-4">
            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.invoice_information') }}</h3>
                    <div class="row">
                        <x-ui.input name="invoice_number" :label="__('invoice.invoice_number')" :value="$invoice->invoice_number" readonly disabled />
                        <x-ui.select name="sales_order_id" :label="__('invoice.sales_order')" x-model="selectedSalesOrderId" x-on:change="applySalesOrder()">
                            <option value="">{{ __('invoice.standalone_invoice') }}</option>
                            @foreach($salesOrders as $s)
                                <option value="{{ $s->id }}" @selected((string) old('sales_order_id', (string) ($invoice->sales_order_id ?? '')) === (string) $s->id)>{{ $s->order_number }}{{ $s->customer ? ' - '.$s->customer->name : '' }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.select name="status" :label="__('common.status')" required>
                            @php $currentStatus = (string) old('status', (string) ($invoice->status->value ?? $invoice->status)); @endphp
                            <option value="draft" @selected($currentStatus === 'draft')>{{ __('invoice.draft') }}</option>
                            <option value="issued" @selected($currentStatus === 'issued')>{{ __('invoice.sent') }}</option>
                            <option value="partially_paid" @selected($currentStatus === 'partially_paid')>{{ __('invoice.partially_paid') }}</option>
                            <option value="paid" @selected($currentStatus === 'paid')>{{ __('invoice.paid') }}</option>
                            <option value="cancelled" @selected($currentStatus === 'cancelled')>{{ __('invoice.cancelled') }}</option>
                        </x-ui.select>
                        <x-ui.input type="date" name="invoice_date" :label="__('invoice.invoice_date')" :value="old('invoice_date', $invoice->invoice_date?->toDateString())" required />
                        <x-ui.input type="date" name="due_date" :label="__('invoice.due_date')" :value="old('due_date', $invoice->due_date?->toDateString())" />
                        <x-ui.select name="currency" :label="__('invoice.currency')" x-model="currency">
                            <option value="ETB">ETB</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </x-ui.select>
                    </div>
                    <x-ui.textarea name="notes" :label="__('invoice.customer_notes')" rows="2">{{ old('notes', $invoice->notes) }}</x-ui.textarea>
                </div>
            </div>

            <div class="panel">
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
                        <template x-for="(line, idx) in lines" :key="idx">
                            <tr>
                                <td x-text="line.product || '-'"></td>
                                <td x-text="line.description || '-'"></td>
                                <td style="text-align:right;" x-text="formatNumber(line.qty)"></td>
                                <td style="text-align:right;" x-text="formatMoney(line.unit_price)"></td>
                                <td style="text-align:right;" x-text="formatMoney(line.tax)"></td>
                                <td style="text-align:right;" x-text="formatMoney(line.discount)"></td>
                                <td style="text-align:right;font-weight:600;" x-text="formatMoney(line.subtotal)"></td>
                            </tr>
                        </template>
                        <tr x-show="lines.length === 0">
                            <td colspan="7" class="muted">{{ __('invoice.no_line_items') }}</td>
                        </tr>
                        </tbody>
                    </x-ui.table>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.customer_information') }}</h3>
                    <div class="space-y-2">
                        <div><span class="muted">{{ __('invoice.customer') }}:</span> <strong x-text="customerName || '-'"></strong></div>
                        <div><span class="muted">{{ __('common.email') }}:</span> <span x-text="customerEmail || '-'"></span></div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <h3 style="margin:0 0 .75rem 0;">{{ __('invoice.totals') }}</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between"><span class="muted">{{ __('invoice.subtotal') }}</span><strong x-text="formatMoney(subtotal)"></strong></div>
                        <div class="flex items-center justify-between"><span class="muted">{{ __('invoice.tax_total') }}</span><strong x-text="formatMoney(taxTotal)"></strong></div>
                        <div class="flex items-center justify-between"><span class="muted">{{ __('invoice.discount_total') }}</span><strong x-text="formatMoney(discountTotal)"></strong></div>
                        <div class="flex items-center justify-between" style="padding-top:.4rem;border-top:1px solid #e5e7eb;">
                            <span class="font-semibold">{{ __('invoice.grand_total') }}</span>
                            <strong x-text="formatMoney(grandTotal)"></strong>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="total_amount" :value="grandTotal.toFixed(4)">
        </div>
    </div>

    <div class="form-actions-sticky mt-1">
        <x-ui.button variant="ghost" :href="route('admin.invoices.show', $invoice)">{{ __('common.cancel') }}</x-ui.button>
        <x-ui.button type="submit" icon="heroicon-o-check">{{ __('common.save_changes') }}</x-ui.button>
    </div>
</form>
@endsection

@once
<script>
    function invoiceForm(config) {
        return {
            salesOrders: Array.isArray(config.salesOrders) ? config.salesOrders : [],
            selectedSalesOrderId: config.selectedSalesOrderId ?? '',
            lines: Array.isArray(config.initialLines) ? config.initialLines : [],
            customerName: '',
            customerEmail: '',
            currency: 'ETB',
            subtotal: 0,
            taxTotal: 0,
            discountTotal: 0,
            grandTotal: 0,
            init() {
                if (this.selectedSalesOrderId) {
                    this.applySalesOrder();
                } else {
                    this.recalculateFromLines();
                }
            },
            applySalesOrder() {
                const selected = this.salesOrders.find((item) => String(item.id) === String(this.selectedSalesOrderId));
                if (!selected) {
                    this.recalculateFromLines();
                    return;
                }
                this.lines = Array.isArray(selected.items) ? selected.items : [];
                this.customerName = selected.customer_name ?? '';
                this.customerEmail = selected.customer_email ?? '';
                this.currency = selected.currency ?? 'ETB';
                this.subtotal = Number(selected.subtotal ?? 0);
                this.taxTotal = Number(selected.tax_total ?? 0);
                this.discountTotal = Number(selected.discount_total ?? 0);
                this.grandTotal = Number(selected.total ?? 0);
                if (this.grandTotal <= 0) {
                    this.recalculateFromLines();
                }
            },
            recalculateFromLines() {
                const totals = this.lines.reduce((acc, line) => {
                    acc.subtotal += Number(line.subtotal ?? 0);
                    acc.tax += Number(line.tax ?? 0);
                    acc.discount += Number(line.discount ?? 0);
                    return acc;
                }, { subtotal: 0, tax: 0, discount: 0 });
                this.subtotal = totals.subtotal;
                this.taxTotal = totals.tax;
                this.discountTotal = totals.discount;
                this.grandTotal = this.subtotal + this.taxTotal - this.discountTotal;
            },
            formatMoney(value) {
                const number = Number(value ?? 0);
                return number.toFixed(2);
            },
            formatNumber(value) {
                const number = Number(value ?? 0);
                return number.toFixed(2);
            },
        };
    }
</script>
@endonce
