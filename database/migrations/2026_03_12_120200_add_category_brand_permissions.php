<?php
declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $names = [
            'view_categories',
            'create_categories',
            'update_categories',
            'delete_categories',
            'view_brands',
            'create_brands',
            'update_brands',
            'delete_brands',
        ];

        foreach ($names as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $adminRole = Role::query()->where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::query()->pluck('name')->all());
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::query()->whereIn('name', [
            'view_categories',
            'create_categories',
            'update_categories',
            'delete_categories',
            'view_brands',
            'create_brands',
            'update_brands',
            'delete_brands',
        ])->where('guard_name', 'web')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
