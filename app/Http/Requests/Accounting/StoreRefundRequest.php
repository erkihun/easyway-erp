<?php

declare(strict_types=1);

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->can('manage_accounting') || $user->can('manage_credit_notes');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'credit_note_id' => ['nullable', 'uuid', 'exists:credit_notes,id'],
            'invoice_id' => ['required', 'uuid', 'exists:invoices,id'],
            'customer_id' => ['nullable', 'uuid', 'exists:customers,id'],
            'refund_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', Rule::in(['cash', 'bank_transfer', 'credit_card', 'mobile_payment'])],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

