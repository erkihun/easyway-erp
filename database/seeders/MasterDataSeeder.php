<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\TaxRate;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        UnitOfMeasure::query()->updateOrCreate(['symbol' => 'PCS'], ['name' => 'Pieces']);
        UnitOfMeasure::query()->updateOrCreate(['symbol' => 'BOX'], ['name' => 'Box']);

        ProductCategory::query()->updateOrCreate(['code' => 'GENERAL'], ['name' => 'General Items']);
        ProductCategory::query()->updateOrCreate(['code' => 'RAW'], ['name' => 'Raw Materials']);
        ProductCategory::query()->updateOrCreate(['code' => 'FG'], ['name' => 'Finished Goods']);

        ProductBrand::query()->updateOrCreate(['code' => 'DEFAULT'], ['name' => 'Default Brand']);
        ProductBrand::query()->updateOrCreate(['code' => 'ACME'], ['name' => 'ACME']);

        Supplier::query()->updateOrCreate(['email' => 'supplier@erp.local'], [
            'name' => 'Main Supplier Ltd',
            'phone' => '+1-202-555-0142',
            'tax_number' => 'SUP-TAX-001',
            'is_active' => true,
        ]);

        TaxRate::query()->updateOrCreate(['name' => 'VAT 15%'], ['rate' => 15, 'is_active' => true]);

        Currency::query()->updateOrCreate(['code' => 'USD'], [
            'name' => 'US Dollar',
            'exchange_rate' => 1,
            'is_base' => true,
        ]);
    }
}
