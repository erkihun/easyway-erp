<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::query()->updateOrCreate(
            ['code' => 'MAIN'],
            ['name' => 'Main Warehouse', 'location' => 'HQ', 'is_default' => true]
        );
    }
}
