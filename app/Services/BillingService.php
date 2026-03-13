<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;

class BillingService
{
    public function creditTotal(Invoice $invoice): float
    {
        return (float) $invoice->creditNotes()->sum('amount');
    }

    public function refundTotal(Invoice $invoice): float
    {
        return (float) $invoice->refunds()->sum('amount');
    }

    public function effectiveTotal(Invoice $invoice): float
    {
        return max(0, (float) $invoice->total_amount - $this->creditTotal($invoice));
    }

    public function remainingBalance(Invoice $invoice): float
    {
        return max(0, $this->effectiveTotal($invoice) - (float) $invoice->paid_amount);
    }

    public function recalculateInvoiceStatus(Invoice $invoice): void
    {
        $invoice->refresh();
        $balance = $this->remainingBalance($invoice);
        $status = (string) ($invoice->status->value ?? $invoice->status);

        if ((float) $invoice->paid_amount <= 0 && $balance > 0 && $status === InvoiceStatus::Draft->value) {
            return;
        }

        $invoice->status = $balance <= 0
            ? InvoiceStatus::Paid
            : ((float) $invoice->paid_amount > 0 ? InvoiceStatus::PartiallyPaid : InvoiceStatus::Issued);
        $invoice->save();
    }
}

