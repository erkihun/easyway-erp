<?php

declare(strict_types=1);

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->can('create_orders') || $user->can('manage_accounting');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'sales_order_id' => ['nullable', 'uuid', 'exists:sales_orders,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
