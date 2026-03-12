<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $query = Permission::query()->withCount('roles');

        if ($search = trim((string) request('q', ''))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $permissions = $query->orderBy('name')->paginate(40)->withQueryString();

        $grouped = collect($permissions->items())->groupBy(function (Permission $permission): string {
            if (Str::contains($permission->name, '_')) {
                return (string) Str::of($permission->name)->after('_')->replace('_', ' ')->title();
            }

            return 'General';
        })->sortKeys();

        return view('admin.permissions.index', [
            'permissions' => $permissions,
            'grouped' => $grouped,
        ]);
    }
}

