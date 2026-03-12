<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class SampleProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = ProductCategory::query()->updateOrCreate(
            ['code' => 'GENERAL'],
            ['name' => 'General Items']
        );

        $brand = ProductBrand::query()->updateOrCreate(
            ['code' => 'DEFAULT'],
            ['name' => 'Default Brand']
        );

        $uom = UnitOfMeasure::query()->updateOrCreate(
            ['symbol' => 'PCS'],
            ['name' => 'Pieces']
        );

        Product::query()->updateOrCreate(
            ['sku' => 'PRD-001'],
            [
                'name' => 'Sample Product A',
                'barcode' => '100000000001',
                'product_category_id' => $category->id,
                'product_brand_id' => $brand->id,
                'unit_of_measure_id' => $uom->id,
                'low_stock_threshold' => 10,
                'is_active' => true,
            ]
        );

        Product::query()->updateOrCreate(
            ['sku' => 'PRD-002'],
            [
                'name' => 'Sample Product B',
                'barcode' => '100000000002',
                'product_category_id' => $category->id,
                'product_brand_id' => $brand->id,
                'unit_of_measure_id' => $uom->id,
                'low_stock_threshold' => 5,
                'is_active' => true,
            ]
        );
    }
}
