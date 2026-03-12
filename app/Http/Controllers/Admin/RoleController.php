<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $query = Role::query()->with(['permissions:id,name'])->withCount(['permissions', 'users']);

        if ($search = trim((string) request('q', ''))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissionGroups = $this->groupedPermissions();

        return view('admin.roles.create', compact('permissionGroups'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = Role::query()->create([
            'name' => (string) $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('status', __('messages.role_created'));
    }

    public function show(Role $role): View
    {
        $role->load('permissions', 'users');
        $groupedPermissions = $role->permissions
            ->sortBy('name')
            ->groupBy(function (Permission $permission): string {
                if (Str::contains($permission->name, '_')) {
                    return Str::of($permission->name)->after('_')->replace('_', ' ')->title()->toString();
                }

                return 'General';
            })
            ->sortKeys();

        return view('admin.roles.show', compact('role', 'groupedPermissions'));
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissionGroups = $this->groupedPermissions();

        return view('admin.roles.edit', compact('role', 'permissionGroups'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();

        if ($role->name === 'Admin' && strtolower((string) $data['name']) !== 'admin') {
            return back()->withErrors(['name' => __('messages.admin_role_name_locked')])->withInput();
        }

        $role->name = (string) $data['name'];
        $role->save();

        if ($role->name === 'Admin') {
            // Keep Admin as full-access by design.
            $role->syncPermissions(Permission::query()->pluck('name')->all());
        } else {
            $role->syncPermissions($data['permissions'] ?? []);
        }

        return redirect()->route('admin.roles.index')->with('status', __('messages.role_updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Admin') {
            return back()->withErrors(['role' => __('messages.admin_role_delete_forbidden')]);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors(['role' => __('messages.role_assigned_cannot_delete')]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', __('messages.role_deleted'));
    }

    /**
     * @return Collection<string, Collection<int, Permission>>
     */
    private function groupedPermissions(): Collection
    {
        $permissions = Permission::query()->orderBy('name')->get();

        return $permissions->groupBy(function (Permission $permission): string {
            $name = $permission->name;

            if (Str::contains($name, '_')) {
                $domain = Str::of($name)->after('_')->replace('_', ' ')->title()->toString();

                if ($domain !== '') {
                    return $domain;
                }
            }

            if ($name === 'manage_users') {
                return 'Users';
            }

            return 'General';
        })->sortKeys();
    }
}


