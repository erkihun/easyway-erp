@props([
    'name',
    'label' => null,
    'required' => false,
    'help' => null,
    'errorKey' => null,
])

@php
    $lookup = $errorKey ?? preg_replace('/\]/', '', str_replace('[', '.', $name));
@endphp

<div class="field">
    @if($label)
        <label for="{{ $name }}" class="field-label">
            {{ $label }}
            @if($required)
                <span style="color:#dc2626;" aria-hidden="true">*</span>
            @endif
        </label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" @if($required) required @endif {{ $attributes->merge(['class' => 'form-control']) }}>
        {{ $slot }}
    </select>
    @if($help)
        <div class="field-help">{{ $help }}</div>
    @endif
    @error($lookup)
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

