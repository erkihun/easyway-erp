<?php

declare(strict_types=1);

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    private array $permissions = [
        'view_invoices',
        'create_invoices',
        'update_invoices',
        'delete_invoices',
        'register_payments',
        'manage_credit_notes',
    ];

    public function up(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }

    public function down(): void
    {
        Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $this->permissions)
            ->delete();
    }
};
