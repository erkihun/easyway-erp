<div class="topbar panel mb-1">
    @php
    $systemName = (string) ($appSettings['system_name'] ?? __('navigation.erp_platform'));
    $companyName = trim((string) ($appSettings['company_name'] ?? ''));
    $identityLabel = $companyName !== '' ? $companyName : $systemName;
    $profilePhotoUrl = auth()->user()?->profile_photo_url;
    $profileInitial = strtoupper(substr((string) auth()->user()?->name, 0, 1));
    $roleName = auth()->user()?->roles()->pluck('name')->first() ?? __('common.user');
    @endphp

    <div class="topbar-body">
        <div class="topbar-left">
            <button type="button" class="icon-btn mobile-only" @click="sidebarOpen = true"
                aria-label="{{ __('common.open_navigation') }}">
                <x-heroicon-o-bars-3 class="h-5 w-5" />
            </button>

            <button type="button" class="icon-btn desktop-only" @click="toggleCollapsed()"
                :aria-label="sidebarCollapsed ? '{{ __('common.expand_sidebar') }}' : '{{ __('common.collapse_sidebar') }}'">
                <x-heroicon-o-chevron-double-right class="h-5 w-5" x-show="sidebarCollapsed" x-cloak />
                <x-heroicon-o-chevron-double-left class="h-5 w-5" x-show="!sidebarCollapsed" x-cloak />
            </button>

            <div>
                <h1 class="topbar-title">@yield('page-title', __('dashboard.title'))</h1>
                <p class="topbar-subtitle">{{ $identityLabel }} / @yield('page-subtitle', __('dashboard.subtitle'))</p>
            </div>
        </div>

        <div class="topbar-right">
            <form method="GET" action="{{ url()->current() }}" class="topbar-search" role="search">
                <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('common.search') }}"
                    aria-label="{{ __('common.search') }}">
            </form>

            <button type="button" class="theme-toggle" @click="toggleTheme()"
                :aria-label="isDark ? '{{ __('common.light_mode') }}' : '{{ __('common.dark_mode') }}'"
                :title="isDark ? '{{ __('common.light_mode') }}' : '{{ __('common.dark_mode') }}'">
                <x-heroicon-o-sun class="h-5 w-5" x-show="isDark" x-cloak />
                <x-heroicon-o-moon class="h-5 w-5" x-show="!isDark" x-cloak />
            </button>

            <div class="topbar-control-group" aria-label="{{ __('common.language') }}">
                <form method="POST" action="{{ route('language.switch', 'en') }}" style="display:inline-flex;">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-ghost' }}">EN</button>
                </form>
                <form method="POST" action="{{ route('language.switch', 'am') }}" style="display:inline-flex;">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm {{ app()->getLocale() === 'am' ? 'btn-primary' : 'btn-ghost' }}">AM</button>
                </form>
            </div>

            <a href="{{ route('profile.show') }}" class="topbar-user-link">
                <div class="topbar-user-dot" aria-hidden="true">
                    @if($profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}" alt="{{ auth()->user()?->name }}" class="avatar-image" />
                    @else
                    {{ $profileInitial }}
                    @endif
                </div>

            </a>
        </div>
    </div>
</div>