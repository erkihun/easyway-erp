<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private readonly JournalService $journalService)
    {
    }

    /** @param array<string,mixed> $payload */
    public function record(array $payload): Payment
    {
        return DB::transaction(function () use ($payload): Payment {
            $invoice = \App\Models\Invoice::query()->findOrFail($payload['invoice_id']);

            $payment = Payment::create([
                'invoice_id' => $payload['invoice_id'],
                'payment_number' => $payload['payment_number'] ?? 'PAY-'.now()->format('YmdHis'),
                'status' => PaymentStatus::Completed,
                'method' => $payload['method'] ?? 'cash',
                'amount' => $payload['amount'],
                'payment_date' => $payload['payment_date'] ?? now()->toDateString(),
            ]);

            $invoice->paid_amount = (float) $invoice->paid_amount + (float) $payment->amount;
            $invoice->status = ((float) $invoice->paid_amount >= (float) $invoice->total_amount)
                ? InvoiceStatus::Paid
                : InvoiceStatus::PartiallyPaid;
            $invoice->save();

            $this->journalService->postSimpleEntry(
                'PAY-'.now()->format('YmdHisv'),
                'invoice_payment',
                $payment->id,
                'Invoice payment recorded',
                (float) $payment->amount
            );

            return $payment;
        });
    }
}
