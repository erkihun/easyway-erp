<?php
declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGeneralSettingsRequest extends FormRequest
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
            'system_name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'company_email' => ['nullable', 'email', 'max:190'],
            'company_phone' => ['nullable', 'string', 'max:40'],
            'default_currency' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'timezone'],
            'date_format' => ['required', Rule::in(['Y-m-d', 'd-m-Y', 'm/d/Y'])],
        ];
    }
}
