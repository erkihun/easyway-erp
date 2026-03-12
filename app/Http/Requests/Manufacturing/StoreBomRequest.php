<?php

declare(strict_types=1);

namespace App\Http\Requests\Manufacturing;

use Illuminate\Foundation\Http\FormRequest;

class StoreBomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_stock') ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'code' => ['required', 'string', 'max:100', 'unique:boms,code'],
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.component_product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}
