<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $erpSystemName = (string) ($appSettings['system_name'] ?? config('app.name', 'ERP Platform'));
        $pageTitle = trim((string) $__env->yieldContent('title'));
        $documentTitle = $pageTitle !== '' ? $pageTitle.' | '.$erpSystemName : $erpSystemName;
        $faviconUrl = $appSettings['favicon_url'] ?? $appSettings['system_favicon_url'] ?? asset('favicon.ico');
        $faviconExt = strtolower(pathinfo(parse_url((string) $faviconUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $faviconType = match ($faviconExt) {
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            default => 'image/x-icon',
        };
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle }}</title>
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&family=Noto+Sans+Ethiopic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (() => {
            const theme = localStorage.getItem('erp_theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
            document.documentElement.dataset.theme = theme;
        })();
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            color-scheme: light;
            --erp-bg: #f3f7fb;
            --erp-bg-accent: rgba(99, 102, 241, 0.16);
            --erp-surface: rgba(255, 255, 255, 0.78);
            --erp-surface-strong: rgba(255, 255, 255, 0.88);
            --erp-card: rgba(255, 255, 255, 0.85);
            --erp-card-solid: #ffffff;
            --erp-card-muted: #f8fafc;
            --erp-text: #0f172a;
            --erp-text-soft: #475569;
            --erp-muted: #64748b;
            --erp-line: rgba(148, 163, 184, 0.22);
            --erp-line-strong: rgba(148, 163, 184, 0.32);
            --erp-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
            --erp-shadow-soft: 0 12px 30px rgba(15, 23, 42, 0.06);
            --erp-primary: #4f46e5;
            --erp-primary-strong: #4338ca;
            --erp-primary-soft: rgba(79, 70, 229, 0.12);
            --erp-success: #059669;
            --erp-warning: #d97706;
            --erp-danger: #dc2626;
            --erp-info: #2563eb;
            --erp-topbar-gradient: linear-gradient(135deg, #1e3a8a, #4f46e5, #6366f1);
            --erp-sidebar-bg: #020617;
            --erp-sidebar-panel: rgba(15, 23, 42, 0.86);
            --erp-sidebar-item: rgba(148, 163, 184, 0.08);
            --erp-sidebar-hover: rgba(99, 102, 241, 0.16);
            --erp-sidebar-active: #4f46e5;
            --erp-sidebar-text: #e2e8f0;
            --erp-sidebar-muted: #94a3b8;
            --erp-sidebar-line: rgba(148, 163, 184, 0.14);
            --erp-table-head: rgba(248, 250, 252, 0.92);
            --erp-table-row-hover: rgba(99, 102, 241, 0.08);
            --erp-input-bg: rgba(255, 255, 255, 0.88);
            --erp-overlay: rgba(2, 8, 23, 0.6);
        }

        .dark {
            color-scheme: dark;
            --erp-bg: #020617;
            --erp-bg-accent: rgba(99, 102, 241, 0.18);
            --erp-surface: rgba(15, 23, 42, 0.72);
            --erp-surface-strong: rgba(15, 23, 42, 0.86);
            --erp-card: rgba(15, 23, 42, 0.7);
            --erp-card-solid: #0f172a;
            --erp-card-muted: #111c33;
            --erp-text: #e2e8f0;
            --erp-text-soft: #cbd5e1;
            --erp-muted: #94a3b8;
            --erp-line: rgba(148, 163, 184, 0.18);
            --erp-line-strong: rgba(148, 163, 184, 0.26);
            --erp-shadow: 0 28px 60px rgba(2, 8, 23, 0.45);
            --erp-shadow-soft: 0 18px 36px rgba(2, 8, 23, 0.28);
            --erp-primary-soft: rgba(99, 102, 241, 0.2);
            --erp-topbar-gradient: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca);
            --erp-sidebar-bg: #020617;
            --erp-sidebar-panel: rgba(15, 23, 42, 0.96);
            --erp-sidebar-item: rgba(30, 41, 59, 0.9);
            --erp-sidebar-hover: rgba(79, 70, 229, 0.24);
            --erp-sidebar-active: #4f46e5;
            --erp-sidebar-text: #e2e8f0;
            --erp-sidebar-muted: #94a3b8;
            --erp-sidebar-line: rgba(71, 85, 105, 0.4);
            --erp-table-head: rgba(30, 41, 59, 0.92);
            --erp-table-row-hover: rgba(51, 65, 85, 0.65);
            --erp-input-bg: rgba(15, 23, 42, 0.78);
            --erp-overlay: rgba(2, 8, 23, 0.75);
        }

        * { box-sizing: border-box; }
        [x-cloak] { display: none !important; }
        html, body { min-height: 100%; }

        body {
            margin: 0;
            font-family: "Manrope", system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 28%),
                radial-gradient(circle at top right, var(--erp-bg-accent), transparent 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0)),
                var(--erp-bg);
            color: var(--erp-text);
            transition: background-color .18s ease, color .18s ease;
        }

        html[lang^="am"] body {
            font-family: "Noto Sans Ethiopic", "Manrope", sans-serif;
        }

        .print-layout {
            font-family: "Manrope", sans-serif;
        }

        a, button, input, select, textarea {
            transition:
                background-color .18s ease,
                color .18s ease,
                border-color .18s ease,
                box-shadow .18s ease,
                transform .18s ease,
                opacity .18s ease;
        }

        .erp-shell { min-height: 100vh; }
        .sidebar {
            background:
                radial-gradient(circle at top, rgba(99, 102, 241, 0.18), transparent 32%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(2, 8, 23, 0.98)),
                var(--erp-sidebar-bg);
            color: var(--erp-sidebar-text);
            border-right: 1px solid var(--erp-sidebar-line);
            box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.02);
        }

        .sidebar-desktop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 30;
            width: 288px;
            height: 100vh;
            transition: width .18s ease;
            overflow: hidden;
        }

        .sidebar-desktop.is-collapsed { width: 96px; }

        .sidebar-mobile {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 60;
            width: min(88vw, 290px);
            height: 100vh;
            transform: translateX(-100%);
            transition: transform .18s ease;
            box-shadow: 0 24px 48px rgba(2, 8, 23, 0.5);
        }

        .sidebar-mobile.is-open { transform: translateX(0); }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 55;
            background: var(--erp-overlay);
            backdrop-filter: blur(4px);
        }

        .sidebar-shell {
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem .85rem .9rem;
        }

        .sidebar-brand-row {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .35rem .4rem .9rem;
            border-bottom: 1px solid var(--erp-sidebar-line);
        }

        .sidebar-brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-weight: 800;
            font-family: "Space Grotesk", "Manrope", sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #38bdf8, #4f46e5 60%, #818cf8);
            box-shadow: 0 14px 30px rgba(79, 70, 229, 0.3);
        }

        .sidebar-brand-title {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -.02em;
            color: #f8fafc;
            font-family: "Space Grotesk", "Manrope", sans-serif;
        }

        .sidebar-brand-subtitle {
            margin-top: .16rem;
            font-size: .78rem;
            color: var(--erp-sidebar-muted);
        }

        .sidebar-nav {
            overflow-y: auto;
            flex: 1;
            padding-right: .15rem;
        }

        .sidebar-nav::-webkit-scrollbar { width: 8px; }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.26);
            border-radius: 999px;
        }

        .sidebar-group {
            padding-top: .8rem;
            margin-top: .8rem;
            border-top: 1px solid var(--erp-sidebar-line);
        }

        .sidebar-group:first-child {
            margin-top: 0;
            padding-top: 0;
            border-top: 0;
        }

        .sidebar-group-label {
            margin: 0 0 .45rem .6rem;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: var(--erp-sidebar-muted);
        }

        .sidebar-group-items {
            display: grid;
            gap: .34rem;
        }

        .sidebar-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: .8rem;
            width: 100%;
            padding: .8rem .85rem;
            border: 1px solid transparent;
            border-radius: 16px;
            color: var(--erp-sidebar-text);
            background: transparent;
            text-decoration: none;
            cursor: pointer;
            text-align: left;
        }

        .sidebar-item:hover {
            background: var(--erp-sidebar-hover);
            border-color: rgba(129, 140, 248, 0.18);
            transform: translateX(2px);
        }

        .sidebar-item.is-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.96), rgba(79, 70, 229, 0.92));
            color: #fff;
            box-shadow: 0 16px 30px rgba(79, 70, 229, 0.28);
        }

        .sidebar-item.is-active .sidebar-item-icon-wrap {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .sidebar-item-icon-wrap {
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--erp-sidebar-item);
            color: #cbd5e1;
        }

        .sidebar-item-icon {
            width: 24px;
            height: 24px;
            stroke-width: 2;
        }

        .sidebar-item-label {
            font-size: .92rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .sidebar-user-panel {
            position: relative;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .78rem;
            border-radius: 18px;
            border: 1px solid var(--erp-sidebar-line);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.9));
        }

        .sidebar-user-panel:hover {
            border-color: rgba(129, 140, 248, 0.26);
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.86), rgba(15, 23, 42, 0.96));
        }

        .sidebar-avatar {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #22c55e, #3b82f6);
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.22);
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 999px;
            object-fit: cover;
        }

        .sidebar-user-name {
            font-size: .92rem;
            font-weight: 800;
            color: #f8fafc;
            line-height: 1.2;
        }

        .sidebar-user-role {
            margin-top: .15rem;
            font-size: .76rem;
            color: var(--erp-sidebar-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-logout {
            margin-top: auto;
            background: rgba(220, 38, 38, 0.12);
            color: #fecaca;
        }

        .sidebar-logout:hover {
            background: rgba(220, 38, 38, 0.18);
            border-color: rgba(248, 113, 113, 0.2);
        }

        .sidebar-desktop.is-collapsed .sidebar-item,
        .sidebar-desktop.is-collapsed .sidebar-user-panel {
            justify-content: center;
            padding-left: .7rem;
            padding-right: .7rem;
        }

        .sidebar-desktop.is-collapsed .sidebar-item[data-tooltip]:hover::after,
        .sidebar-desktop.is-collapsed .sidebar-user-panel[data-tooltip]:hover::after,
        .sidebar-desktop.is-collapsed .sidebar-logout[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            padding: .45rem .6rem;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.96);
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: #f8fafc;
            font-size: .76rem;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 16px 30px rgba(2, 8, 23, 0.34);
            pointer-events: none;
            z-index: 70;
        }

        .content-wrap {
            margin-left: 288px;
            min-height: 100vh;
            transition: margin-left .18s ease;
        }

        .content-wrap.is-collapsed { margin-left: 96px; }
        .content { padding: 1rem; }
        .content-inner { max-width: 1700px; margin: 0 auto; }

        .panel,
        .glass-card {
            background: var(--erp-card);
            border: 1px solid var(--erp-line);
            border-radius: 24px;
            box-shadow: var(--erp-shadow-soft);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .panel:hover,
        .glass-card:hover {
            border-color: var(--erp-line-strong);
            box-shadow: var(--erp-shadow);
        }

        .panel-body { padding: 1.25rem; }
        .panel-head { border-bottom: 1px solid var(--erp-line); }
        .panel-foot { border-top: 1px solid var(--erp-line); }

        .topbar {
            position: sticky;
            top: .9rem;
            z-index: 20;
            overflow: hidden;
            background: var(--erp-topbar-gradient);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 0 24px 44px rgba(37, 99, 235, 0.22);
        }

        .topbar::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.28), transparent 26%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .topbar-body {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.3rem 1.4rem;
            flex-wrap: nowrap;
        }

        .topbar-left,
        .topbar-right,
        .flex,
        .items-center {
            display: flex;
            align-items: center;
        }

        .topbar-left {
            gap: .9rem;
            min-width: 0;
            flex: 1 1 auto;
        }
        .topbar-right {
            gap: .65rem;
            flex-wrap: nowrap;
            justify-content: flex-end;
            flex: 0 0 auto;
            min-width: 0;
        }
        .gap-2 { gap: .5rem; }
        .gap-3 { gap: .75rem; }

        .topbar-title {
            margin: 0;
            color: #fff;
            font-size: 1.7rem;
            line-height: 1;
            letter-spacing: -.03em;
            font-family: "Space Grotesk", "Manrope", sans-serif;
        }

        .topbar-subtitle {
            margin: .35rem 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: .92rem;
        }

        .topbar-search {
            position: relative;
            display: flex;
            align-items: center;
            gap: .55rem;
            width: 320px;
            min-width: 320px;
            padding: .78rem 1rem;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .topbar-search input {
            width: 100%;
            min-width: 0;
            padding: 0;
            border: 0;
            background: transparent;
            color: #fff;
            font-size: .92rem;
        }

        .topbar-search input::placeholder { color: rgba(255, 255, 255, 0.72); }
        .topbar-search input:focus { outline: 0; box-shadow: none; }
        .icon-btn,
        .theme-toggle {
            width: 2.75rem;
            height: 2.75rem;
            padding: 0;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .icon-btn:hover,
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-1px);
        }

        .topbar-control-group {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .3rem;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .topbar-user-link {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            padding: .34rem .42rem .34rem .34rem;
            border-radius: 20px;
            text-decoration: none;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            flex-shrink: 0;
        }

        .topbar-user-link:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-1px);
        }

        .topbar-user-dot {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 999px;
            display: grid;
            place-items: center;
            overflow: hidden;
            flex-shrink: 0;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.9), rgba(59, 130, 246, 0.85));
        }

        .topbar-user-copy {
            min-width: 0;
            text-align: left;
        }

        .topbar-user-name {
            font-size: .88rem;
            font-weight: 800;
            line-height: 1.1;
            white-space: nowrap;
        }

        .topbar-user-role {
            margin-top: .12rem;
            font-size: .74rem;
            color: rgba(255, 255, 255, 0.76);
            white-space: nowrap;
        }

        .page-header,
        .dashboard-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .page-header-title,
        .dashboard-head-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--erp-text);
            font-family: "Space Grotesk", "Manrope", sans-serif;
        }

        .page-header-subtitle,
        .dashboard-head-subtitle,
        .muted {
            color: var(--erp-muted);
        }

        .page-header-subtitle,
        .dashboard-head-subtitle {
            margin: .28rem 0 0;
            font-size: .92rem;
        }

        .page-header-actions,
        .page-actions,
        .dashboard-actions,
        .actions,
        .btn-group {
            display: flex;
            gap: .45rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .dashboard-page,
        .dashboard-shell,
        .grid {
            display: grid;
            gap: 1rem;
        }

        .panel-fill {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .dashboard-quick-grid,
        .erp-action-grid,
        .row,
        .row-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .dashboard-card,
        .erp-action-card,
        .dashboard-quick-item,
        .kpi-card {
            position: relative;
            overflow: hidden;
            background: var(--erp-card);
            border: 1px solid var(--erp-line);
            border-radius: 24px;
            box-shadow: var(--erp-shadow-soft);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .dashboard-card,
        .erp-action-card,
        .dashboard-quick-item { padding: 1rem; }

        .dashboard-card:hover,
        .erp-action-card:hover,
        .dashboard-quick-item:hover,
        .kpi-card:hover {
            border-color: var(--erp-line-strong);
            box-shadow: var(--erp-shadow);
            transform: translateY(-2px);
        }

        .action-card,
        .dashboard-quick-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .85rem;
            align-items: start;
            color: inherit;
            text-decoration: none;
        }

        .action-icon,
        .dashboard-quick-icon,
        .erp-action-icon,
        .kpi-card-icon-wrap {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 16px;
            display: grid;
            place-items: center;
            color: var(--erp-primary);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.18), rgba(59, 130, 246, 0.12));
        }

        .dashboard-card-title,
        .dashboard-quick-title,
        .erp-action-title {
            margin: 0;
            font-size: .96rem;
            font-weight: 800;
            color: var(--erp-text);
        }

        .dashboard-card-subtitle,
        .dashboard-quick-desc,
        .erp-action-description {
            margin: .18rem 0 0;
            font-size: .8rem;
            color: var(--erp-muted);
            line-height: 1.45;
        }

        .kpi-grid,
        .dashboard-kpi-grid,
        .row-kpi {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .kpi-card { padding: 1rem; }

        .kpi-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .8rem;
        }

        .kpi-card-label {
            color: var(--erp-muted);
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .kpi-card-value {
            margin-top: .7rem;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1;
            color: var(--erp-text);
            letter-spacing: -.04em;
        }

        .kpi-card-helper {
            margin-top: .32rem;
            font-size: .8rem;
            color: var(--erp-muted);
        }

        .tone-warning .kpi-card-icon,
        .smart-btn-warning .smart-btn-icon { color: var(--erp-warning); }

        .tone-danger .kpi-card-icon,
        .smart-btn-danger .smart-btn-icon { color: var(--erp-danger); }

        .tone-success .kpi-card-icon,
        .smart-btn-success .smart-btn-icon { color: var(--erp-success); }

        .tone-info .kpi-card-icon { color: var(--erp-info); }

        .table-shell-head,
        .section-head,
        .chart-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            margin-bottom: .9rem;
        }

        .table-shell-title,
        .section-title,
        .chart-card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: var(--erp-text);
        }

        .section-meta,
        .chart-card-subtitle {
            font-size: .78rem;
            color: var(--erp-muted);
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--erp-line);
            border-radius: 18px;
            background: var(--erp-surface-strong);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: .92rem 1rem;
            border-bottom: 1px solid var(--erp-line);
            text-align: left;
            vertical-align: middle;
            font-size: .88rem;
            color: var(--erp-text-soft);
        }

        th {
            background: var(--erp-table-head);
            color: var(--erp-muted);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tbody tr:hover { background: var(--erp-table-row-hover); }
        tbody tr:last-child td { border-bottom: 0; }

        .erp-table-compact th,
        .erp-table-compact td,
        .table-compact th,
        .table-compact td {
            padding: .72rem .84rem;
        }

        input,
        select,
        textarea,
        button {
            width: 100%;
            border-radius: 14px;
            border: 1px solid var(--erp-line);
            background: var(--erp-input-bg);
            color: var(--erp-text);
            padding: .72rem .88rem;
        }

        input::placeholder,
        textarea::placeholder { color: var(--erp-muted); }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 0;
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .form-control {
            font-size: .92rem;
            color: var(--erp-text);
        }

        .field { display: grid; gap: .35rem; }
        .field-label {
            color: var(--erp-text);
            font-size: .86rem;
            font-weight: 700;
        }
        .field-help,
        .field-error {
            font-size: .78rem;
        }
        .field-help { color: var(--erp-muted); }
        .field-error { color: #f87171; font-weight: 700; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            width: auto;
            min-width: fit-content;
            padding: .74rem 1rem;
            border-radius: 14px;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn:disabled,
        .btn[aria-disabled="true"] {
            opacity: .55;
            pointer-events: none;
        }

        .btn-sm { min-height: 2.35rem; padding: .62rem .8rem; font-size: .8rem; }
        .btn-md { min-height: 2.6rem; }
        .btn-lg { min-height: 2.9rem; padding: .82rem 1.08rem; }

        .btn-primary {
            background: var(--erp-primary);
            border-color: var(--erp-primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--erp-primary-strong);
            border-color: var(--erp-primary-strong);
        }

        .btn-secondary {
            background: rgba(148, 163, 184, 0.14);
            border-color: rgba(148, 163, 184, 0.18);
            color: var(--erp-text);
        }

        .btn-secondary:hover,
        .btn-outline:hover {
            background: rgba(148, 163, 184, 0.2);
        }

        .btn-outline {
            background: transparent;
            border-color: var(--erp-line-strong);
            color: var(--erp-text);
        }

        .btn-ghost {
            background: transparent;
            border-color: transparent;
            color: var(--erp-muted);
        }

        .btn-ghost:hover {
            background: rgba(148, 163, 184, 0.12);
            color: var(--erp-text);
        }

        .btn-danger {
            background: #dc2626;
            border-color: #dc2626;
            color: #fff;
        }

        .btn-danger:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .btn-success {
            background: #059669;
            border-color: #059669;
            color: #fff;
        }

        .btn-success:hover {
            background: #047857;
            border-color: #047857;
        }

        .btn-warning {
            background: #d97706;
            border-color: #d97706;
            color: #fff;
        }

        .btn-warning:hover {
            background: #b45309;
            border-color: #b45309;
        }

        .btn-icon { width: 2.75rem; min-width: 2.75rem; padding: 0; }

        .subnav-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-bottom: .9rem;
        }

        .subnav-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .6rem .85rem;
            border-radius: 14px;
            border: 1px solid var(--erp-line);
            background: var(--erp-input-bg);
            color: var(--erp-text-soft);
            text-decoration: none;
            font-size: .82rem;
            font-weight: 700;
        }

        .subnav-tab:hover { background: var(--erp-card); }

        .subnav-tab.is-active {
            background: var(--erp-primary);
            border-color: var(--erp-primary);
            color: #fff;
        }

        .dropdown { position: relative; display: inline-block; }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            z-index: 40;
            min-width: 180px;
            padding: .38rem;
            border-radius: 18px;
            border: 1px solid var(--erp-line);
            background: var(--erp-surface-strong);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            box-shadow: var(--erp-shadow);
            display: grid;
            gap: .25rem;
        }

        .dropdown-menu .btn {
            justify-content: flex-start;
            width: 100%;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 70;
            display: grid;
            place-items: center;
            padding: 1rem;
        }

        .modal-backdrop {
            position: absolute;
            inset: 0;
            background: var(--erp-overlay);
            backdrop-filter: blur(4px);
        }

        .modal-shell {
            position: relative;
            z-index: 1;
            width: min(100%, 640px);
        }

        .modal-panel {
            border-radius: 28px;
            box-shadow: var(--erp-shadow);
        }

        .modal-head,
        .modal-foot {
            border-color: var(--erp-line);
        }

        .modal-title {
            margin: 0;
            font-size: 1.08rem;
            font-weight: 800;
            color: var(--erp-text);
        }

        .modal-subtitle {
            margin: .3rem 0 0;
            color: var(--erp-muted);
            font-size: .86rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .34rem .66rem;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 800;
            border: 1px solid transparent;
            line-height: 1.2;
        }

        .badge-neutral { background: rgba(148, 163, 184, 0.14); color: var(--erp-text-soft); border-color: rgba(148, 163, 184, 0.16); }
        .badge-success, .badge-good { background: rgba(34, 197, 94, 0.14); color: #15803d; border-color: rgba(34, 197, 94, 0.2); }
        .badge-info { background: rgba(59, 130, 246, 0.14); color: #2563eb; border-color: rgba(59, 130, 246, 0.2); }
        .badge-warning, .badge-warn { background: rgba(245, 158, 11, 0.14); color: #b45309; border-color: rgba(245, 158, 11, 0.2); }
        .badge-danger, .badge-bad { background: rgba(239, 68, 68, 0.14); color: #b91c1c; border-color: rgba(239, 68, 68, 0.2); }

        .flash-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
        }

        .flash-copy {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-weight: 700;
        }

        .flash-success { color: #16a34a; }
        .flash-error { color: #dc2626; }
        .flash-info { color: #2563eb; }
        .flash-warning { color: #d97706; }

        .empty-state-body {
            text-align: center;
            display: grid;
            justify-items: center;
            gap: .4rem;
            padding: 1.1rem;
            color: var(--erp-muted);
        }

        .empty-state-title {
            font-weight: 800;
            color: var(--erp-text);
        }

        .empty-state-description {
            max-width: 520px;
            font-size: .9rem;
        }

        .dashboard-timeline {
            display: grid;
            gap: .55rem;
        }

        .dashboard-timeline-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .7rem;
            align-items: start;
            padding: .55rem 0;
            border-bottom: 1px solid var(--erp-line);
        }

        .dashboard-timeline-item:last-child { border-bottom: 0; }

        .dashboard-timeline-dot {
            width: .72rem;
            height: .72rem;
            margin-top: .32rem;
            border-radius: 999px;
            background: var(--erp-primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.14);
        }

        .dashboard-timeline-title {
            margin: 0;
            font-size: .88rem;
            font-weight: 800;
            color: var(--erp-text);
        }

        .dashboard-timeline-meta {
            margin-top: .14rem;
            font-size: .8rem;
            color: var(--erp-muted);
        }

        .smart-btn {
            display: flex;
            align-items: center;
            gap: .6rem;
            min-width: 170px;
            padding: .72rem .85rem;
            border-radius: 16px;
            text-decoration: none;
            background: var(--erp-card);
            border: 1px solid var(--erp-line);
            color: var(--erp-text);
            box-shadow: var(--erp-shadow-soft);
        }

        .smart-btn:hover {
            border-color: var(--erp-line-strong);
            box-shadow: var(--erp-shadow);
            transform: translateY(-1px);
        }

        .smart-btn-copy { display: grid; gap: .1rem; }
        .smart-btn-label {
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--erp-muted);
        }
        .smart-btn-value {
            font-size: .94rem;
            font-weight: 800;
            color: var(--erp-text);
        }

        .form-actions-sticky {
            position: sticky;
            bottom: 0;
            z-index: 5;
            display: flex;
            justify-content: flex-end;
            gap: .45rem;
            padding: .8rem 0 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), var(--erp-bg) 45%);
        }

        .link {
            color: var(--erp-primary);
            text-decoration: none;
        }

        .link:hover { text-decoration: underline; }

        .mb-1 { margin-bottom: .9rem; }
        .mt-1 { margin-top: .9rem; }

        .desktop-only { display: inline-flex; }
        .mobile-only { display: none; }

        .h-4 { width: 1rem; height: 1rem; }
        .w-4 { width: 1rem; }
        .h-5 { width: 1.25rem; height: 1.25rem; }
        .w-5 { width: 1.25rem; }
        .h-8 { width: 2rem; height: 2rem; }
        .w-8 { width: 2rem; }

        .chart-canvas {
            position: relative;
            width: 100%;
            height: 260px;
        }

        @media (max-width: 1024px) {
            .sidebar-desktop { display: none; }
            .content-wrap,
            .content-wrap.is-collapsed { margin-left: 0; }
            .desktop-only { display: none; }
            .mobile-only { display: inline-flex; }
            .content { padding: .8rem; }
            .topbar { top: .45rem; }
            .topbar-body {
                padding: 1rem;
                flex-wrap: wrap;
                align-items: stretch;
            }
            .topbar-title { font-size: 1.35rem; }
            .topbar-user-copy { display: none; }
            .topbar-right {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            .topbar-search {
                width: 100%;
                min-width: 100%;
                order: 3;
            }
            .dropdown-menu { right: auto; left: 0; }
            .smart-btn { min-width: 100%; }
            .chart-canvas { height: 220px; }
        }

        @media (min-width: 768px) {
            .erp-action-grid,
            .dashboard-quick-grid,
            .row-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-kpi-grid,
            .row-kpi {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .erp-action-grid,
            .dashboard-quick-grid,
            .row-actions {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }

            .dashboard-kpi-grid,
            .row-kpi {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }

            .dashboard-chart-grid,
            .dashboard-operational-grid,
            .dashboard-ops-grid,
            .dashboard-activity-grid,
            .row-charts,
            .row-ops,
            .row-activity {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1025px) {
            .sidebar-mobile,
            .sidebar-backdrop { display: none !important; }
        }
    </style>
</head>
<body x-data="adminLayout()" x-init="initLayout()">
<div class="erp-shell">
    <aside class="sidebar sidebar-desktop" :class="{ 'is-collapsed': sidebarCollapsed }">
        @include('admin.partials.sidebar', ['isMobile' => false])
    </aside>

    <div class="sidebar-backdrop" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>

    <aside class="sidebar sidebar-mobile" :class="{ 'is-open': sidebarOpen }" @keydown.escape.window="sidebarOpen = false" x-cloak>
        @include('admin.partials.sidebar', ['isMobile' => true])
    </aside>

    <main class="content-wrap" :class="{ 'is-collapsed': sidebarCollapsed }">
        <div class="content">
            <div class="content-inner">
                @include('admin.partials.topbar')
                @include('admin.partials.alerts')
                @include('admin.partials.breadcrumbs')
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script>
    function adminLayout() {
        return {
            sidebarOpen: false,
            sidebarCollapsed: false,
            theme: 'light',
            initLayout() {
                const storedSidebar = localStorage.getItem('erp_sidebar_collapsed');
                this.sidebarCollapsed = storedSidebar === '1';
                this.theme = localStorage.getItem('erp_theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                this.applyTheme(this.theme, false);
                this.refreshIcons();
                window.addEventListener('load', () => this.refreshIcons(), { once: true });
                this.$watch('sidebarCollapsed', () => this.$nextTick(() => this.refreshIcons()));
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1025) {
                        this.sidebarOpen = false;
                    }
                });
            },
            get isDark() {
                return this.theme === 'dark';
            },
            toggleCollapsed() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('erp_sidebar_collapsed', this.sidebarCollapsed ? '1' : '0');
            },
            toggleTheme() {
                this.applyTheme(this.isDark ? 'light' : 'dark');
            },
            applyTheme(theme, persist = true) {
                this.theme = theme;
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.dataset.theme = theme;
                if (persist) {
                    localStorage.setItem('erp_theme', theme);
                }
                window.dispatchEvent(new CustomEvent('erp:theme-changed', { detail: { theme } }));
            },
            refreshIcons() {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }
        };
    }
</script>
</body>
</html>
