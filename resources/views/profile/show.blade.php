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
            <x-ui.button :href="route('profile.edit')" icon="heroicon-o-pencil-square">{{ __('profile.edit_profile') }}</x-ui.button>
        </x-ui.page-actions>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid gap-4 lg:grid-cols-[1.2fr_1fr_1fr]">
    <div class="panel">
        <div class="panel-body flex items-center gap-4">
            <div class="sidebar-avatar" style="width:64px;height:64px;">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="avatar-image" />
                @else
                    {{ $initials !== '' ? $initials : 'U' }}
                @endif
            </div>
            <div>
                <div class="text-lg font-semibold text-slate-900">{{ $user?->name }}</div>
                <div class="text-sm text-slate-600">{{ $user?->email }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ __('profile.role') }}: {{ $primaryRole ?: __('profile.unassigned_role') }}</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="text-xs text-slate-500">{{ __('profile.account_status') }}</div>
            <div class="mt-2 text-base font-semibold text-emerald-700">{{ __('profile.active_account') }}</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="text-xs text-slate-500">{{ __('profile.member_since') }}</div>
            <div class="mt-2 text-base font-semibold text-slate-900">{{ $user?->created_at?->format('Y-m-d') }}</div>
        </div>
    </div>
</div>
@endsection
