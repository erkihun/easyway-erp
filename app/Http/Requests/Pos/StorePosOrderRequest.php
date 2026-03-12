<?php
declare(strict_types=1);

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StorePosOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('operate_pos') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'pos_session_id' => ['required', 'uuid', 'exists:pos_sessions,id'],
            'customer_id' => ['nullable', 'uuid', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
