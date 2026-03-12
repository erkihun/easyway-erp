<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view_dashboard',
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'view_roles',
            'create_roles',
            'update_roles',
            'delete_roles',
            'view_permissions',
            'assign_roles',
            'assign_permissions',
            'manage_access',
            'manage_settings',
            'create_products',
            'update_products',
            'delete_products',
            'view_products',
            'view_inventory',
            'view_warehouses',
            'manage_stock',
            'view_stock_movements',
            'view_stock_by_warehouse',
            'view_low_stock',
            'create_stock_adjustments',
            'update_stock_adjustments',
            'view_inventory_valuation',
            'view_inventory_snapshots',
            'manage_bins',
            'create_orders',
            'manage_purchases',
            'manage_transfers',
            'view_transfers',
            'create_transfers',
            'update_transfers',
            'receive_goods',
            'process_goods_receipts',
            'view_purchase_receipts',
            'manage_accounting',
            'view_reports',
            'operate_pos',
            'manage_users',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $roles = [
            'Manager' => [
                'view_dashboard', 'create_products', 'update_products', 'view_products', 'view_inventory', 'view_warehouses',
                'manage_stock', 'view_stock_movements', 'view_stock_by_warehouse', 'view_low_stock', 'view_inventory_valuation',
                'create_orders', 'manage_purchases', 'manage_transfers', 'view_transfers', 'create_transfers',
                'view_reports', 'view_users', 'view_roles', 'view_permissions', 'manage_settings',
            ],
            'Sales' => ['view_dashboard', 'create_orders', 'view_reports'],
            'Accountant' => ['manage_accounting', 'view_reports'],
            'POS Operator' => ['operate_pos', 'create_orders'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($rolePermissions);
        }

        $warehouseUserPermissions = [
            'view_dashboard',
            'view_products',
            'view_inventory',
            'view_warehouses',
            'manage_stock',
            'view_stock_movements',
            'view_stock_by_warehouse',
            'view_low_stock',
            'create_stock_adjustments',
            'update_stock_adjustments',
            'view_transfers',
            'create_transfers',
            'update_transfers',
            'manage_transfers',
            'view_inventory_valuation',
            'receive_goods',
            'process_goods_receipts',
            'view_purchase_receipts',
        ];

        $warehouseUserRole = Role::findOrCreate('Warehouse User', 'web');
        $warehouseUserRole->syncPermissions($warehouseUserPermissions);

        $legacyWarehouseRole = Role::query()->where('name', 'Warehouse')->where('guard_name', 'web')->first();
        if ($legacyWarehouseRole !== null && $legacyWarehouseRole->id !== $warehouseUserRole->id) {
            foreach ($legacyWarehouseRole->users as $user) {
                $user->assignRole($warehouseUserRole);
            }
            $legacyWarehouseRole->delete();
        }

        // Admin must always retain full access, including newly-added permissions.
        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->syncPermissions(Permission::query()->pluck('name')->all());
    }
}
