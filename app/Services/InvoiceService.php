<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;

class InvoiceService
{
    /** @param array<string,mixed> $payload */
    public function generate(array $payload): Invoice
    {
        return Invoice::create([
            'invoice_number' => $payload['invoice_number'] ?? 'INV-'.now()->format('YmdHis'),
            'sales_order_id' => $payload['sales_order_id'] ?? null,
            'status' => $payload['status'] ?? InvoiceStatus::Draft,
            'invoice_date' => $payload['invoice_date'] ?? now()->toDateString(),
            'due_date' => $payload['due_date'] ?? null,
            'currency' => $payload['currency'] ?? 'ETB',
            'total_amount' => $payload['total_amount'] ?? 0,
            'notes' => $payload['notes'] ?? null,
            'created_by' => $payload['created_by'] ?? auth()->id(),
        ]);
    }
}
