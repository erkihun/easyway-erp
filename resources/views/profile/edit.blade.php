@extends('layouts.admin')
@section('title', __('profile.my_profile'))
@section('page-title', __('profile.my_profile'))
@section('page-subtitle', __('profile.subtitle'))
@section('content')
@php
    $avatarUrl = $user?->profile_photo_url;
    $initials = collect(preg_split('/\s+/', trim((string) ($user?->name ?? 'U'))))
        ->filter()
        ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<x-ui.page-header :title="__('profile.my_profile')" :subtitle="__('profile.subtitle')" icon="heroicon-o-user-circle">
    <x-slot:actions>
        <x-ui.page-actions>
            <x-ui.button variant="ghost" :href="route('profile.show')">{{ __('profile.view_profile') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid gap-4 lg:grid-cols-[1.1fr_1.1fr_1fr]">
    <div class="panel lg:col-span-1">
        <div class="panel-body space-y-3">
            <div class="text-sm font-semibold text-slate-900">{{ __('profile.profile_summary') }}</div>
            <div class="flex items-center gap-3">
                <div class="sidebar-avatar" style="width:56px;height:56px;">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="avatar-image" />
                    @else
                        {{ $initials !== '' ? $initials : 'U' }}
                    @endif
                </div>
                <div>
                    <div class="font-semibold text-slate-900">{{ $user?->name }}</div>
                    <div class="text-sm text-slate-600">{{ $user?->email }}</div>
                </div>
            </div>
            <div class="text-xs text-slate-500">{{ __('profile.role') }}: {{ $primaryRole ?: __('profile.unassigned_role') }}</div>
            <div class="text-xs text-slate-500">{{ __('profile.member_since') }}: {{ $user?->created_at?->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="panel lg:col-span-1">
        <div class="panel-body">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3" x-data="{ preview: '{{ $avatarUrl ?? '' }}' }">
                @csrf
                @method('PUT')
                <div class="text-sm font-semibold text-slate-900">{{ __('profile.personal_information') }}</div>
                <x-ui.input name="name" :label="__('common.name')" :value="old('name', $user?->name)" required />
                <x-ui.input type="email" name="email" :label="__('common.email')" :value="old('email', $user?->email)" required />

                <div class="text-sm font-semibold text-slate-900 pt-1">{{ __('profile.profile_photo') }}</div>
                <div class="flex items-center gap-3">
                    <div class="sidebar-avatar" style="width:56px;height:56px;overflow:hidden;">
                        <template x-if="preview">
                            <img :src="preview" alt="{{ __('profile.profile_photo') }}" class="avatar-image" />
                        </template>
                        <template x-if="!preview">
                            <span>{{ $initials !== '' ? $initials : 'U' }}</span>
                        </template>
                    </div>
                    <div class="flex-1">
                        <x-ui.input type="file" name="profile_photo" :label="__('profile.photo_upload')" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            x-on:change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => preview = e.target?.result ?? ''; reader.readAsDataURL(file); }" />
                        <p class="text-xs text-slate-500">{{ __('profile.photo_help') }}</p>
                    </div>
                </div>

                @if($avatarUrl)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="remove_profile_photo" value="1">
                        <span>{{ __('profile.remove_photo') }}</span>
                    </label>
                @endif

                <div class="pt-2">
                    <x-ui.button type="submit" icon="heroicon-o-check">{{ __('profile.update_profile') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel lg:col-span-1">
        <div class="panel-body">
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <div class="text-sm font-semibold text-slate-900">{{ __('profile.change_password') }}</div>
                <x-ui.input type="password" name="current_password" :label="__('profile.current_password')" required />
                <x-ui.input type="password" name="password" :label="__('profile.new_password')" required />
                <x-ui.input type="password" name="password_confirmation" :label="__('profile.confirm_password')" required />
                <div class="pt-2">
                    <x-ui.button type="submit" variant="secondary" icon="heroicon-o-lock-closed">{{ __('profile.update_password') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
