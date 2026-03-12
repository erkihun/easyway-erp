<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'system_name', 'value' => 'ERP Platform', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_name', 'value' => 'ERP Company', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_email', 'value' => 'admin@erp.local', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => '+1-555-0100', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'group' => 'preferences'],
            ['key' => 'timezone', 'value' => 'Africa/Addis_Ababa', 'type' => 'string', 'group' => 'preferences'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'group' => 'preferences'],
            ['key' => 'system_logo', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'system_favicon', 'value' => '', 'type' => 'string', 'group' => 'branding'],
        ];

        foreach ($defaults as $item) {
            Setting::query()->updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
