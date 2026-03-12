<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class SampleCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $group = CustomerGroup::query()->updateOrCreate(
            ['code' => 'RETAIL'],
            ['name' => 'Retail']
        );

        Customer::query()->updateOrCreate(
            ['email' => 'customer@erp.local'],
            [
                'name' => 'Sample Customer',
                'phone' => '+1-202-555-0111',
                'customer_group_id' => $group->id,
                'is_active' => true,
            ]
        );
    }
}
