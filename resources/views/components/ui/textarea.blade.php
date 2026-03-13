@props([
    'name',
    'label' => null,
    'rows' => 3,
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
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @if($required) required @endif {{ $attributes->merge(['class' => 'form-control']) }}>{{ old($name, $slot) }}</textarea>
    @if($help)
        <div class="field-help">{{ $help }}</div>
    @endif
    @error($lookup)
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

