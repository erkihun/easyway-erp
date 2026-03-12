<?php
declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode'],
            'description' => ['nullable', 'string'],
            'product_category_id' => ['nullable', 'uuid', 'exists:product_categories,id'],
            'product_brand_id' => ['nullable', 'uuid', 'exists:product_brands,id'],
            'unit_of_measure_id' => ['nullable', 'uuid', 'exists:units_of_measure,id'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
