<?php

declare(strict_types=1);

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'image' => 'The :attribute must be an image.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'array' => 'The :attribute must be an array.',
    'exists' => 'The selected :attribute is invalid.',
    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'role' => 'role',
        'permission' => 'permission',
        'system_name' => 'system name',
        'company_email' => 'company email',
        'system_logo' => 'system logo',
        'system_favicon' => 'system favicon',
    ],
];

