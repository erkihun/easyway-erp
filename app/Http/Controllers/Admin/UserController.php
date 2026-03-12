<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::query()->with('roles');

        if ($search = trim((string) request('q', ''))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter = trim((string) request('role', ''))) {
            $query->role($roleFilter);
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => (string) $data['name'],
            'email' => (string) $data['email'],
            'password' => (string) $data['password'],
        ]);

        if (array_key_exists('roles', $data)) {
            $user->syncRoles($data['roles'] ?? []);
        }

        return redirect()->route('admin.users.index')->with('status', __('messages.user_created'));
    }

    public function show(User $user): View
    {
        $user->load('roles', 'permissions');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $roles = Role::query()->orderBy('name')->get();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (array_key_exists('roles', $data)) {
            $incomingRoles = collect($data['roles'] ?? [])->map(static fn (mixed $value): string => (string) $value);
            $wouldLoseAdmin = $user->hasRole('Admin') && !$incomingRoles->contains('Admin');

            if ($wouldLoseAdmin && User::role('Admin')->count() <= 1) {
                return back()->withErrors(['roles' => __('messages.last_admin_role_remove_forbidden')])->withInput();
            }
        }

        $user->fill([
            'name' => (string) $data['name'],
            'email' => (string) $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->password = (string) $data['password'];
        }

        $user->save();
        if (array_key_exists('roles', $data)) {
            $user->syncRoles($data['roles'] ?? []);
        }

        return redirect()->route('admin.users.index')->with('status', __('messages.user_updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((string) auth()->id() === (string) $user->id) {
            return back()->withErrors(['user' => __('messages.delete_own_account_forbidden')]);
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() <= 1) {
            return back()->withErrors(['user' => __('messages.last_admin_delete_forbidden')]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', __('messages.user_deleted'));
    }
}


