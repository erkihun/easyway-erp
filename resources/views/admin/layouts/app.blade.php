<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ERP System' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #eef3f8;
            --panel: #ffffff;
            --ink: #102a43;
            --accent: #0f766e;
            --muted: #5e6c84;
            --line: #d9e2ec;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Tahoma, sans-serif; background: radial-gradient(circle at top, #e1f5ef, #eaf1f8 45%, #f8fbff); color: var(--ink); }
        .app { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .sidebar { background: #102a43; color: #f0f4f8; padding: 1rem; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .sidebar h2 { margin-top: 0; }
        .sidebar a { display: block; color: #d9e2ec; text-decoration: none; padding: .5rem .75rem; border-radius: .5rem; margin-bottom: .25rem; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,.16); }
        .content { padding: 1.25rem; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: .75rem; padding: 1rem; box-shadow: 0 8px 24px rgba(16,42,67,.08); }
        .grid { display: grid; gap: 1rem; }
        .grid.kpi { grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); }
        table { width: 100%; border-collapse: collapse; font-size: .93rem; }
        th, td { text-align: left; padding: .5rem; border-bottom: 1px solid var(--line); vertical-align: top; }
        input, select, button, textarea { width: 100%; padding: .5rem .6rem; border-radius: .5rem; border: 1px solid #c3ced9; background: #fff; }
        button { cursor: pointer; background: var(--accent); color: white; border-color: var(--accent); }
        .row { display: grid; gap: .75rem; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); }
        .status { color: #0f766e; margin-bottom: .75rem; font-weight: 600; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .muted { color: var(--muted); }
        @media (max-width: 900px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <h2>{{ __('navigation.erp_platform') }}</h2>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">{{ __('navigation.dashboard') }}</a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">{{ __('navigation.products') }}</a>
        <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">{{ __('inventory.title') }}</a>
        <a href="{{ route('admin.warehouses.index') }}" class="{{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">{{ __('navigation.warehouses') }}</a>
        <a href="{{ route('admin.manufacturing.index') }}" class="{{ request()->routeIs('admin.manufacturing.*') ? 'active' : '' }}">{{ __('navigation.manufacturing') }}</a>
        <a href="{{ route('admin.pos.index') }}" class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">POS</a>
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">{{ __('navigation.customers') }}</a>
        <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">{{ __('navigation.suppliers') }}</a>
        <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">{{ __('navigation.sales') }}</a>
        <a href="{{ route('admin.purchases.index') }}" class="{{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">{{ __('navigation.purchases') }}</a>
        <a href="{{ route('admin.transfers.index') }}" class="{{ request()->routeIs('admin.transfers.*') ? 'active' : '' }}">{{ __('navigation.transfers') }}</a>
        <a href="{{ route('admin.accounting.index') }}" class="{{ request()->routeIs('admin.accounting.*') ? 'active' : '' }}">{{ __('navigation.accounting') }}</a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">{{ __('navigation.reports') }}</a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">{{ __('navigation.users') }}</a>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">{{ __('navigation.settings') }}</a>
    </aside>
    <main class="content">
        <div class="topbar">
            <div>
                <strong>{{ auth()->user()?->name }}</strong>
                <div class="muted">{{ auth()->user()?->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">{{ __('common.logout') }}</button></form>
        </div>
        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="card" style="margin-bottom:1rem;color:#b42318;">
                {{ $errors->first() }}
            </div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>







