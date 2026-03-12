<?php
declare(strict_types=1);

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update_roles') || $this->user()?->can('manage_users') || false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $roleId = (string) ($this->route('role')?->id ?? '');

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('roles', 'name')->ignore($roleId, 'id')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
