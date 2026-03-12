<?php

declare(strict_types=1);

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class OpenPosSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('operate_pos') ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'opening_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
