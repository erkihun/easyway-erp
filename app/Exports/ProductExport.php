<?php
declare(strict_types=1);

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductExport implements FromCollection
{
    public function collection(): Collection
    {
        return Product::query()
            ->select(['id', 'sku', 'barcode', 'name', 'description', 'low_stock_threshold', 'is_active', 'created_at'])
            ->latest()
            ->get();
    }
}
