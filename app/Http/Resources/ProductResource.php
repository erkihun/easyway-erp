<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category?->name,
            'brand' => $this->brand?->name,
            'unit' => $this->unit?->symbol,
            'low_stock_threshold' => $this->low_stock_threshold,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
