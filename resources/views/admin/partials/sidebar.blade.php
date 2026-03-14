@php
    $isMobile = $isMobile ?? false;
    $user = auth()->user();
    $isAdmin = (bool) $user?->hasRole('Admin');
    $systemName = (string) ($appSettings['system_name'] ?? __('navigation.erp_platform'));
    $systemLogoUrl = $appSettings['logo_url'] ?? $appSettings['system_logo_url'] ?? null;
    $roleName = $user?->roles()->pluck('name')->first() ?? __('common.user');
    $profilePhotoUrl = $user?->profile_photo_url;

    $nameParts = preg_split('/\s+/', trim((string) ($user?->name ?? __('common.user')))) ?: [];
    $initials = collect($nameParts)->filter()->map(fn(string $part): string => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
    if ($initials === '') {
        $initials = 'U';
    }

    $groups = [
        [
            'label' => __('common.main'),
            'items' => [
                ['label' => __('navigation.dashboard'), 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'heroicon-o-home', 'permissions' => null],
            ],
        ],
        [
            'label' => __('common.catalog'),
            'items' => [
                ['label' => __('navigation.products'), 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'heroicon-o-cube', 'permissions' => ['view_products', 'create_products', 'update_products', 'delete_products']],
                ['label' => __('navigation.product_categories'), 'route' => 'admin.product-categories.index', 'active' => 'admin.product-categories.*', 'icon' => 'heroicon-o-squares-2x2', 'permissions' => ['view_categories', 'create_categories', 'update_categories', 'delete_categories']],
                ['label' => __('navigation.product_brands'), 'route' => 'admin.product-brands.index', 'active' => 'admin.product-brands.*', 'icon' => 'heroicon-o-tag', 'permissions' => ['view_brands', 'create_brands', 'update_brands', 'delete_brands']],
                ['label' => __('navigation.inventory'), 'route' => 'admin.inventory.index', 'active' => 'admin.inventory.*', 'icon' => 'heroicon-o-archive-box', 'permissions' => ['manage_stock']],
                ['label' => __('inventory.ledger'), 'route' => 'admin.inventory.ledger', 'active' => 'admin.inventory.ledger', 'icon' => 'heroicon-o-book-open', 'permissions' => ['manage_stock']],
                ['label' => __('inventory.movements'), 'route' => 'admin.inventory.movements', 'active' => 'admin.inventory.movements', 'icon' => 'heroicon-o-arrow-path', 'permissions' => ['manage_stock']],
                ['label' => __('inventory.warehouses_view'), 'route' => 'admin.inventory.warehouses', 'active' => 'admin.inventory.warehouses', 'icon' => 'heroicon-o-building-storefront', 'permissions' => ['manage_stock']],
                ['label' => __('inventory.adjustments'), 'route' => 'admin.inventory.adjustments', 'active' => 'admin.inventory.adjustments', 'icon' => 'heroicon-o-adjustments-horizontal', 'permissions' => ['manage_stock']],
                ['label' => __('inventory.low_stock'), 'route' => 'admin.inventory.low-stock', 'active' => 'admin.inventory.low-stock', 'icon' => 'heroicon-o-exclamation-triangle', 'permissions' => ['manage_stock']],
                ['label' => __('inventory.valuation'), 'route' => 'admin.inventory.valuation', 'active' => 'admin.inventory.valuation', 'icon' => 'heroicon-o-banknotes', 'permissions' => ['manage_stock']],
                ['label' => __('navigation.warehouses'), 'route' => 'admin.warehouses.index', 'active' => 'admin.warehouses.*', 'icon' => 'heroicon-o-building-storefront', 'permissions' => ['view_warehouses', 'manage_stock']],
            ],
        ],
        [
            'label' => __('common.operations'),
            'items' => [
                ['label' => __('navigation.sales'), 'route' => 'admin.sales.index', 'active' => 'admin.sales.*', 'icon' => 'heroicon-o-banknotes', 'permissions' => ['create_orders']],
                ['label' => __('navigation.purchases'), 'route' => 'admin.purchases.index', 'active' => 'admin.purchases.*', 'icon' => 'heroicon-o-clipboard-document-list', 'permissions' => ['manage_purchases']],
                ['label' => __('navigation.transfers'), 'route' => 'admin.transfers.index', 'active' => 'admin.transfers.*', 'icon' => 'heroicon-o-arrow-path', 'permissions' => ['manage_transfers']],
                ['label' => __('navigation.manufacturing'), 'route' => 'admin.manufacturing.index', 'active' => 'admin.manufacturing.*', 'icon' => 'heroicon-o-wrench-screwdriver', 'permissions' => ['manage_stock']],
                ['label' => __('navigation.pos'), 'route' => 'admin.pos.index', 'active' => 'admin.pos.*', 'icon' => 'heroicon-o-shopping-cart', 'permissions' => ['operate_pos']],
            ],
        ],
        [
            'label' => __('common.crm'),
            'items' => [
                ['label' => __('navigation.customers'), 'route' => 'admin.customers.index', 'active' => 'admin.customers.*', 'icon' => 'heroicon-o-users', 'permissions' => ['create_orders', 'manage_users']],
                ['label' => __('navigation.suppliers'), 'route' => 'admin.suppliers.index', 'active' => 'admin.suppliers.*', 'icon' => 'heroicon-o-truck', 'permissions' => ['manage_purchases', 'manage_users']],
            ],
        ],
        [
            'label' => __('common.finance'),
            'items' => [
                ['label' => __('navigation.accounting'), 'route' => 'admin.accounting.index', 'active' => 'admin.accounting.*', 'icon' => 'heroicon-o-calculator', 'permissions' => ['manage_accounting']],
                ['label' => __('invoice.title'), 'route' => 'admin.invoices.index', 'active' => 'admin.invoices.*', 'icon' => 'heroicon-o-document-text', 'permissions' => ['manage_accounting', 'create_orders', 'view_invoices', 'create_invoices', 'update_invoices', 'delete_invoices']],
                ['label' => __('payment.title'), 'route' => 'admin.payments.index', 'active' => 'admin.payments.*', 'icon' => 'heroicon-o-credit-card', 'permissions' => ['manage_accounting', 'register_payments']],
                ['label' => __('credit_note.title'), 'route' => 'admin.credit-notes.index', 'active' => 'admin.credit-notes.*', 'icon' => 'heroicon-o-receipt-percent', 'permissions' => ['manage_accounting', 'manage_credit_notes']],
                ['label' => __('navigation.reports'), 'route' => 'admin.reports.index', 'active' => 'admin.reports.*', 'icon' => 'heroicon-o-chart-bar', 'permissions' => ['view_reports']],
            ],
        ],
        [
            'label' => __('common.administration'),
            'items' => [
                ['label' => __('navigation.users'), 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'icon' => 'heroicon-o-user-group', 'permissions' => ['view_users', 'create_users', 'update_users', 'delete_users', 'manage_users']],
                ['label' => __('navigation.my_profile'), 'route' => 'profile.show', 'active' => 'profile.*', 'icon' => 'heroicon-o-user-circle', 'permissions' => null],
                ['label' => __('navigation.roles'), 'route' => 'admin.roles.index', 'active' => 'admin.roles.*', 'icon' => 'heroicon-o-shield-check', 'permissions' => ['view_roles', 'create_roles', 'update_roles', 'delete_roles', 'manage_users']],
                ['label' => __('navigation.permissions'), 'route' => 'admin.permissions.index', 'active' => 'admin.permissions.*', 'icon' => 'heroicon-o-lock-closed', 'permissions' => ['view_permissions', 'manage_users']],
                ['label' => __('navigation.settings'), 'route' => 'admin.settings.index', 'active' => 'admin.settings.*', 'icon' => 'heroicon-o-cog-6-tooth', 'permissions' => ['manage_settings']],
            ],
        ],
    ];

    $visibleGroups = [];
    foreach ($groups as $group) {
        $visibleItems = [];
        foreach ($group['items'] as $item) {
            if (!\Illuminate\Support\Facades\Route::has($item['route'])) {
                continue;
            }

            $permissions = $item['permissions'];
            $canView = $permissions === null || $isAdmin || ($user && $user->hasAnyPermission($permissions));

            if ($canView) {
                $visibleItems[] = $item;
            }
        }

        if (count($visibleItems) > 0) {
            $group['items'] = $visibleItems;
            $visibleGroups[] = $group;
        }
    }

    $showLabelExpression = $isMobile ? 'true' : '!sidebarCollapsed';
@endphp

<div class="sidebar-shell {{ $isMobile ? 'sidebar-shell-mobile' : 'sidebar-shell-desktop' }}">
    <div class="sidebar-brand-row">
        <div class="sidebar-brand-mark">
            @if($systemLogoUrl)
                <img src="{{ $systemLogoUrl }}" alt="{{ $systemName }}" style="width:100%;height:100%;object-fit:contain;border-radius:11px;" />
            @else
                {{ strtoupper(substr($systemName, 0, 1)) }}
            @endif
        </div>
        <div class="sidebar-brand-copy" x-show="{{ $showLabelExpression }}" x-transition.opacity>
            <div class="sidebar-brand-title">{{ $systemName }}</div>
            <div class="sidebar-brand-subtitle">{{ __('navigation.operations_console') }}</div>
        </div>
        @if($isMobile)
            <button type="button" class="icon-btn mobile-only" @click="sidebarOpen = false" aria-label="{{ __('common.close') }}">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        @endif
    </div>

    <nav class="sidebar-nav" aria-label="{{ __('common.actions') }}">
        @foreach($visibleGroups as $group)
            <section class="sidebar-group">
                <h3 class="sidebar-group-label" x-show="{{ $showLabelExpression }}" x-transition.opacity>
                    {{ $group['label'] }}
                </h3>
                <div class="sidebar-group-items">
                    @foreach($group['items'] as $item)
                        <x-admin.sidebar-item
                            :label="$item['label']"
                            :route="$item['route']"
                            :icon="$item['icon']"
                            :active-pattern="$item['active']"
                            :is-mobile="$isMobile"
                        />
                    @endforeach
                </div>
            </section>
        @endforeach
    </nav>

    <div class="sidebar-user-panel" x-show="{{ $showLabelExpression }}" x-transition.opacity data-tooltip="{{ $user?->name }}">
        <div class="sidebar-avatar" aria-hidden="true">
            @if($profilePhotoUrl)
                <img src="{{ $profilePhotoUrl }}" alt="{{ $user?->name }}" class="avatar-image" />
            @else
                {{ $initials }}
            @endif
        </div>
        <div class="sidebar-user-meta">
            <div class="sidebar-user-name">{{ $user?->name }}</div>
            <div class="sidebar-user-role">{{ $roleName }}</div>
        </div>
    </div>
    <div class="sidebar-user-panel" x-show="{{ $isMobile ? 'false' : 'sidebarCollapsed' }}" x-transition.opacity data-tooltip="{{ $user?->name }}">
        <div class="sidebar-avatar" aria-hidden="true">
            @if($profilePhotoUrl)
                <img src="{{ $profilePhotoUrl }}" alt="{{ $user?->name }}" class="avatar-image" />
            @else
                {{ $initials }}
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('{{ __('common.logout_confirm') }}')">
        @csrf
        <button type="submit" class="sidebar-item sidebar-logout flex items-center gap-2" data-tooltip="{{ __('common.logout') }}">
            <span class="sidebar-item-icon-wrap" aria-hidden="true">
                <i data-lucide="log-out" class="sidebar-item-icon"></i>
            </span>
            <span class="sidebar-item-label" x-show="{{ $showLabelExpression }}" x-transition.opacity>{{ __('common.logout') }}</span>
        </button>
    </form>
</div>


