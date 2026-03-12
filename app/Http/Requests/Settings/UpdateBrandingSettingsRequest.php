<?php
declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('manage_settings'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'system_logo' => ['nullable', 'file', 'max:2048', 'mimes:png,jpg,jpeg,svg'],
            'system_favicon' => ['nullable', 'file', 'max:2048', 'mimes:png,ico,svg'],
        ];
    }
}
