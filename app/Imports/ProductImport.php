<?php
declare(strict_types=1);

namespace App\Imports;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $payload = Arr::only($row->toArray(), [
                'name',
                'sku',
                'barcode',
                'description',
                'product_category_id',
                'product_brand_id',
                'unit_of_measure_id',
                'low_stock_threshold',
                'is_active',
            ]);

            if (! empty($payload['name'])) {
                Product::query()->updateOrCreate(
                    ['sku' => $payload['sku'] ?? $this->productService->generateSku((string) $payload['name'])],
                    $payload
                );
            }
        }
    }
}
