@php
    $role = $role ?? null;
    $selectedPermissions = $selectedPermissions ?? [];
@endphp

<x-ui.card class="mb-1">
    <x-slot:header><h3 class="table-shell-title">{{ __('roles.basic_information') }}</h3></x-slot:header>
    <div class="row">
        <x-ui.input name="name" :label="__('roles.role_name')" :value="$role?->name" required :help="__('roles.role_name_help')" />
    </div>
</x-ui.card>

<x-ui.card>
    <x-slot:header><h3 class="table-shell-title">{{ __('roles.permission_groups') }}</h3></x-slot:header>
    @if($permissionGroups->isEmpty())
        <x-ui.empty-state :title="__('roles.no_permissions')" :description="__('roles.no_permissions_help')" icon="heroicon-o-lock-closed" />
    @else
        <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            @foreach($permissionGroups as $group => $permissions)
                <div class="panel" x-data="{
                        toggleAll(state) {
                            $refs.boxes.querySelectorAll('input[type=checkbox]').forEach((el) => {
                                el.checked = state;
                            });
                        }
                    }">
                    <div class="panel-body" style="border-bottom:1px solid var(--line);display:flex;justify-content:space-between;gap:.5rem;align-items:center;padding-bottom:.45rem;">
                        <div>
                            <strong style="font-size:.9rem;">{{ $group }}</strong>
                            <div class="muted" style="font-size:.76rem;">{{ __('roles.permissions_count', ['count' => $permissions->count()]) }}</div>
                        </div>
                        <x-ui.button-group>
                            <x-ui.button type="button" variant="ghost" size="sm" @click.prevent="toggleAll(true)">{{ __('common.all') }}</x-ui.button>
                            <x-ui.button type="button" variant="ghost" size="sm" @click.prevent="toggleAll(false)">{{ __('common.reset') }}</x-ui.button>
                        </x-ui.button-group>
                    </div>
                    <div class="panel-body" x-ref="boxes" style="display:grid;gap:.45rem;">
                        @foreach($permissions as $permission)
                            <label style="display:flex;align-items:flex-start;gap:.45rem;padding:.28rem .15rem;border-radius:8px;">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    style="width:auto;margin-top:.12rem;"
                                    @checked(in_array($permission->name, $selectedPermissions, true))
                                >
                                <span>
                                    <span style="font-weight:600;color:#1e293b;font-size:.83rem;">{{ $permission->name }}</span>
                                    <span class="muted" style="display:block;font-size:.74rem;">{{ __('roles.guard_label', ['guard' => $permission->guard_name]) }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @error('permissions')
            <div class="field-error mt-1">{{ $message }}</div>
        @enderror
    @endif
</x-ui.card>

<div class="form-actions-sticky mt-1">
    <x-ui.button variant="ghost" :href="route('admin.roles.index')">{{ __('common.cancel') }}</x-ui.button>
    <x-ui.button type="submit" icon="heroicon-o-check">{{ $submitLabel ?? __('roles.save_role') }}</x-ui.button>
</div>




