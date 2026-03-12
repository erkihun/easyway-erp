<div class="topbar panel mb-1">
    @php
        $systemName = (string) ($appSettings['system_name'] ?? __('navigation.erp_platform'));
        $companyName = trim((string) ($appSettings['company_name'] ?? ''));
        $identityLabel = $companyName !== '' ? $companyName : $systemName;
    @endphp
    <div class="panel-body topbar-body">
        <div class="topbar-left">
            <button type="button" class="icon-btn mobile-only" @click="sidebarOpen = true" aria-label="{{ __('common.open_navigation') }}">
                <x-heroicon-o-bars-3 class="h-5 w-5" />
            </button>
            <button type="button" class="icon-btn desktop-only" @click="toggleCollapsed()" :aria-label="sidebarCollapsed ? '{{ __('common.expand_sidebar') }}' : '{{ __('common.collapse_sidebar') }}'">
                <x-heroicon-o-chevron-double-right class="h-5 w-5" x-show="sidebarCollapsed" x-cloak />
                <x-heroicon-o-chevron-double-left class="h-5 w-5" x-show="!sidebarCollapsed" x-cloak />
            </button>

            <div>
                <h1 class="topbar-title">@yield('page-title', __('common.dashboard'))</h1>
                <div class="muted">@yield('page-subtitle', __('navigation.operations_console'))</div>
            </div>
        </div>

        <div class="topbar-right">
            <form method="GET" action="{{ url()->current() }}" class="desktop-only" style="margin-right:.25rem;">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('common.quick_search') }}" style="width:190px;">
            </form>
            <form method="POST" action="{{ route('language.switch', 'en') }}" style="display:inline-flex;">
                @csrf
                <button type="submit" class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-outline' : 'btn-ghost' }}">EN</button>
            </form>
            <form method="POST" action="{{ route('language.switch', 'am') }}" style="display:inline-flex;">
                @csrf
                <button type="submit" class="btn btn-sm {{ app()->getLocale() === 'am' ? 'btn-outline' : 'btn-ghost' }}">AM</button>
            </form>
            <div class="topbar-user-dot" aria-hidden="true">{{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) }}</div>
            <div class="topbar-user-copy">
                <div><strong>{{ auth()->user()?->name }}</strong></div>
                <div class="muted">{{ auth()->user()?->email }} · {{ $identityLabel }}</div>
            </div>
        </div>
    </div>
</div>

