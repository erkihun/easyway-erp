<?php

declare(strict_types=1);

namespace App\Http\Requests\Accounting;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->can('manage_accounting') || $user->can('create_orders') || $user->can('update_invoices');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'sales_order_id' => ['nullable', 'uuid', 'exists:sales_orders,id'],
            'status' => ['required', 'string', Rule::in(array_map(static fn (InvoiceStatus $status): string => $status->value, InvoiceStatus::cases()))],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'currency' => ['nullable', 'string', 'max:10'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
