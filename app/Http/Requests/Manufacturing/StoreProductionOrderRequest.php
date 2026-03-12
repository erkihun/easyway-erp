<?php
declare(strict_types=1);

namespace App\Http\Requests\Manufacturing;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_stock') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'bom_id' => ['required', 'uuid', 'exists:boms,id'],
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'planned_quantity' => ['required', 'numeric', 'min:0.0001'],
            'planned_date' => ['nullable', 'date'],
        ];
    }
}
