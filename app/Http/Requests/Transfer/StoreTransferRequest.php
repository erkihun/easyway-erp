<?php
declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_transfers') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'source_warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'destination_warehouse_id' => ['required', 'uuid', 'different:source_warehouse_id', 'exists:warehouses,id'],
            'transfer_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}
