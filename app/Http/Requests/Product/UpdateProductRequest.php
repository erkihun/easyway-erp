<?php
declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update_products') || false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $productId = (string) ($this->route('product')?->id ?? '');

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId, 'id')],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($productId, 'id')],
            'description' => ['nullable', 'string'],
            'product_category_id' => ['nullable', 'uuid', 'exists:product_categories,id'],
            'product_brand_id' => ['nullable', 'uuid', 'exists:product_brands,id'],
            'unit_of_measure_id' => ['nullable', 'uuid', 'exists:units_of_measure,id'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
