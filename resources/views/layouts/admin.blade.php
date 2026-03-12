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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Sans+Ethiopic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #f1f5f9;
            --panel: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --danger: #e11d48;
            --success: #059669;
            --warning: #d97706;
            --side-bg: #0b1220;
            --side-muted: #9caec5;
            --side-text: #e2e8f0;
            --side-border: #1f2a44;
            --side-hover: #15213a;
            --side-active: #1e2d52;
            --side-accent: #818cf8;
        }

        * { box-sizing: border-box; }
        [x-cloak] { display: none !important; }

        body {
            margin: 0;
            font-family: "Noto Sans Ethiopic", "Noto Sans", "Segoe UI", Tahoma, system-ui, sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        html[lang^="am"] th {
            text-transform: none;
            letter-spacing: 0;
            font-size: .75rem;
        }

        html[lang^="am"] .sidebar-item-label,
        html[lang^="am"] .sidebar-user-role,
        html[lang^="am"] .page-header-title,
        html[lang^="am"] .page-header-subtitle,
        html[lang^="am"] .table-shell-title,
        html[lang^="am"] .btn,
        html[lang^="am"] .subnav-tab {
            letter-spacing: 0;
        }

        .erp-shell { min-height: 100vh; }

        .sidebar {
            background: linear-gradient(180deg, #0a1220 0%, #0b1322 100%);
            color: var(--side-text);
            border-right: 1px solid var(--side-border);
        }

        .sidebar-desktop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 30;
            width: 260px;
            height: 100vh;
            transition: width .2s ease;
            overflow: hidden;
        }

        .sidebar-desktop.is-collapsed { width: 88px; }

        .sidebar-mobile {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            width: 260px;
            height: 100vh;
            transform: translateX(-100%);
            transition: transform .2s ease;
            border-right: 1px solid var(--side-border);
            box-shadow: 0 16px 40px rgba(2, 8, 23, .5);
        }

        .sidebar-mobile.is-open { transform: translateX(0); }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 45;
            background: rgba(2, 8, 23, .55);
        }

        .sidebar-shell {
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: .65rem;
            padding: .75rem .6rem;
        }

        .sidebar-brand-row {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .15rem .3rem .55rem;
            border-bottom: 1px solid rgba(154, 168, 189, .15);
        }

        .sidebar-brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            font-weight: 700;
            letter-spacing: .02em;
            color: #0f172a;
            background: linear-gradient(135deg, #67e8f9, #22d3ee);
        }

        .sidebar-brand-title {
            font-size: .95rem;
            font-weight: 700;
            color: #f8fbff;
            letter-spacing: .01em;
        }

        .sidebar-brand-subtitle {
            font-size: .75rem;
            color: var(--side-muted);
            margin-top: .1rem;
        }

        .sidebar-nav {
            overflow-y: auto;
            flex: 1;
            padding-right: .2rem;
        }
        .sidebar-nav::-webkit-scrollbar { width: 8px; }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(143, 167, 197, .4);
            border-radius: 999px;
        }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }

        .sidebar-group {
            padding-top: .35rem;
            margin-top: .35rem;
            border-top: 1px solid rgba(154, 168, 189, .13);
        }

        .sidebar-group:first-child {
            margin-top: 0;
            border-top: 0;
            padding-top: 0;
        }

        .sidebar-group-label {
            color: #8ea0b9;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .67rem;
            font-weight: 700;
            margin: 0 0 .25rem .45rem;
        }

        .sidebar-group-items {
            display: grid;
            gap: .15rem;
        }

        .sidebar-item {
            position: relative;
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: .45rem .58rem;
            color: var(--side-muted);
            background: transparent;
            text-decoration: none;
            transition: background .15s ease, color .15s ease, transform .15s ease;
            cursor: pointer;
            text-align: left;
        }

        .sidebar-item:hover {
            background: var(--side-hover);
            color: var(--side-text);
        }

        .sidebar-item.is-active {
            background: linear-gradient(180deg, rgba(56, 189, 248, .22), rgba(30, 58, 95, .9));
            color: #ecfeff;
            box-shadow: inset 0 0 0 1px rgba(56, 189, 248, .35);
        }

        .sidebar-item.is-active::before {
            content: "";
            position: absolute;
            left: -2px;
            top: 8px;
            bottom: 8px;
            width: 3px;
            border-radius: 999px;
            background: #67e8f9;
        }

        .sidebar-item.is-active .sidebar-item-icon {
            color: var(--side-accent);
        }

        .sidebar-item-icon {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }

        .sidebar-item-label {
            font-size: .85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .sidebar-user-panel {
            display: flex;
            align-items: center;
            gap: .55rem;
            margin-top: .1rem;
            padding: .5rem;
            border-radius: 10px;
            background: rgba(20, 35, 58, .65);
            border: 1px solid rgba(154, 168, 189, .16);
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #0f172a;
            background: linear-gradient(135deg, #e2e8f0, #93c5fd);
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: .88rem;
            font-weight: 700;
            color: #e8f0fb;
            line-height: 1.15;
        }

        .sidebar-user-role {
            margin-top: .12rem;
            font-size: .75rem;
            color: #9fb0c7;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-logout { margin-top: .2rem; }

        .sidebar-desktop.is-collapsed .sidebar-item {
            justify-content: center;
            padding: .45rem;
        }

        .sidebar-desktop.is-collapsed .sidebar-user-panel {
            justify-content: center;
            padding: .38rem;
        }

        .sidebar-desktop.is-collapsed .sidebar-item[data-tooltip]:hover::after,
        .sidebar-desktop.is-collapsed .sidebar-logout[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%);
            background: #111b2f;
            color: #eff6ff;
            border: 1px solid #2a3a54;
            border-radius: 8px;
            padding: .32rem .48rem;
            font-size: .74rem;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            box-shadow: 0 8px 16px rgba(2, 8, 23, .45);
            z-index: 60;
        }

        .content-wrap {
            margin-left: 260px;
            transition: margin-left .2s ease;
            min-height: 100vh;
        }

        .content-wrap.is-collapsed { margin-left: 88px; }

        .content { padding: .8rem .95rem; }
        .content-inner { max-width: 1680px; margin: 0 auto; }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .045);
            transition: box-shadow .18s ease;
        }
        .panel:hover { box-shadow: 0 6px 18px rgba(15, 23, 42, .065); }

        .panel-body { padding: .78rem; }

        .topbar-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .55rem;
        }

        .topbar {
            position: sticky;
            top: .55rem;
            z-index: 20;
            backdrop-filter: blur(6px);
        }

        .topbar-left,
        .topbar-right,
        .flex,
        .items-center {
            display: flex;
            align-items: center;
        }

        .gap-3 { gap: .75rem; }
        .gap-2 { gap: .5rem; }

        .topbar-left { gap: .45rem; min-width: 0; }
        .topbar-right { gap: .45rem; }

        .topbar-title {
            margin: .1rem 0;
            font-size: 1.16rem;
            line-height: 1.2;
            color: #0f2740;
        }

        .icon-btn {
            width: 1.95rem;
            height: 1.95rem;
            border-radius: .58rem;
            border: 1px solid #d6dfeb;
            background: #fff;
            color: #334e68;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .icon-btn:hover { background: #f3f8ff; }

        .topbar-user-dot {
            width: 1.85rem;
            height: 1.85rem;
            border-radius: 999px;
            background: #dbeafe;
            color: #0f2740;
            font-weight: 700;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .topbar-user-copy { text-align: right; font-size: .86rem; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .page-header-title-row {
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .page-header-title {
            margin: 0;
            font-size: 1.08rem;
            font-weight: 700;
            letter-spacing: .01em;
            color: #0f2740;
        }

        .page-header-subtitle {
            margin: .15rem 0 0;
            color: var(--muted);
            font-size: .84rem;
        }

        .page-header-icon { color: #0f4b68; }
        .page-header-actions { display: flex; gap: .4rem; flex-wrap: wrap; }

        .kpi-grid {
            display: grid;
            gap: .6rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .kpi-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: .62rem .68rem;
            background: #fff;
            box-shadow: 0 1px 6px rgba(2, 8, 23, .04);
        }

        .kpi-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .55rem;
        }

        .kpi-card-label {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .kpi-card-value {
            margin-top: .22rem;
            font-size: 1.18rem;
            font-weight: 700;
            color: #0f2740;
        }

        .kpi-card-icon { color: #236293; }
        .kpi-card.tone-danger .kpi-card-icon { color: #b42318; }
        .kpi-card.tone-success .kpi-card-icon { color: #047857; }
        .kpi-card.tone-warning .kpi-card-icon { color: #b45309; }

        .filter-bar-body {
            display: flex;
            gap: .42rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-bar-body > * { margin: 0; }
        .filter-bar-body input,
        .filter-bar-body select { min-width: 140px; }

        .table-wrap {
            overflow: auto;
            border-radius: 8px;
            border: 1px solid #e3eaf2;
        }

        .grid { display: grid; gap: .75rem; }
        .grid-kpi { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        .row { display: grid; gap: .6rem; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .44rem .55rem; border-bottom: 1px solid #e8eef5; text-align: left; vertical-align: top; font-size: .83rem; }
        th { background: #f8fafc; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: #5c7189; position: sticky; top: 0; z-index: 1; }
        tbody tr:hover { background: #f8fbff; }

        input, select, textarea, button {
            width: 100%;
            padding: .46rem .56rem;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #fff;
        }
        input:focus, select:focus, textarea:focus {
            outline: 0;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(79,70,229,.18);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .38rem;
            width: auto;
            text-decoration: none;
            border-radius: 9px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: fit-content;
            border: 1px solid var(--primary);
            line-height: 1;
            background: var(--primary);
            color: #fff;
        }

        .btn:disabled,
        .btn[aria-disabled="true"] {
            opacity: .5;
            pointer-events: none;
        }

        .btn-sm { padding: .34rem .54rem; font-size: .76rem; min-height: 1.82rem; }
        .btn-md { padding: .48rem .68rem; font-size: .82rem; min-height: 2.02rem; }
        .btn-lg { padding: .58rem .86rem; font-size: .88rem; min-height: 2.24rem; }

        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }

        .btn-secondary { background: #eef2ff; color: #3730a3; border-color: #c7d2fe; }
        .btn-secondary:hover { background: #e0e7ff; }

        .btn-outline { background: #fff; color: #334155; border-color: #cbd5e1; }
        .btn-outline:hover { background: #f8fbff; }

        .btn-ghost { background: transparent; color: #475569; border-color: transparent; }
        .btn-ghost:hover { background: #eef2ff; color: #3730a3; }

        .btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); }
        .btn-danger:hover { background: #be123c; }

        .btn-success { background: var(--success); color: #fff; border-color: var(--success); }
        .btn-success:hover { background: #047857; }

        .btn-warning { background: var(--warning); color: #fff; border-color: var(--warning); }
        .btn-warning:hover { background: #b45309; }

        .btn-icon { width: 2.1rem; min-width: 2.1rem; padding: 0; }

        .actions { display: flex; gap: .28rem; flex-wrap: wrap; align-items: center; }
        .page-actions { display: flex; gap: .32rem; flex-wrap: wrap; align-items: center; justify-content: flex-end; }
        .table-actions {
            display: inline-flex;
            gap: .35rem;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            white-space: nowrap;
        }
        .table-actions > * {
            display: inline-flex;
            align-items: center;
            margin: 0;
            flex-shrink: 0;
        }
        .table-actions form {
            display: inline-flex;
            margin: 0;
            flex-shrink: 0;
        }
        .table-actions .dropdown { display: inline-flex; }
        .table-actions .btn.btn-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .34rem .58rem;
            font-size: .74rem;
            border-radius: .42rem;
            white-space: nowrap;
        }
        .btn-group { display: inline-flex; gap: .28rem; flex-wrap: wrap; }
        .actions-col {
            width: 1%;
            min-width: 220px;
            white-space: nowrap;
            text-align: right;
        }
        th.actions-col, td.actions-col { text-align: right; }
        .actions-col .table-actions {
            justify-content: flex-end;
            flex-wrap: nowrap;
            width: 100%;
        }
        .btn-label { white-space: nowrap; }

        .subnav-tabs {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .35rem;
            margin-bottom: .75rem;
        }
        .subnav-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            padding: .38rem .62rem;
            border-radius: 9px;
            border: 1px solid #d7e0eb;
            background: #fff;
            color: #334155;
            text-decoration: none;
            font-size: .78rem;
            font-weight: 600;
        }
        .subnav-tab:hover { background: #f8fbff; }
        .subnav-tab.is-active {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
        }

        .dropdown { position: relative; display: inline-block; }
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            background: #fff;
            border: 1px solid #d9e4f0;
            border-radius: 9px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
            min-width: 170px;
            z-index: 40;
            padding: .3rem;
            display: grid;
            gap: .2rem;
        }

        .dropdown-menu .btn {
            justify-content: flex-start;
            width: 100%;
        }

        .badge {
            padding: .14rem .42rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            display: inline-block;
            border: 1px solid transparent;
        }

        .badge-neutral { background: #eef2f7; color: #3e556e; border-color: #d6dfea; }
        .badge-success { background: #dcfce7; color: #166534; border-color: #86efac; }
        .badge-info { background: #e0e7ff; color: #3730a3; border-color: #c7d2fe; }
        .badge-warning { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .badge-danger { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        .badge-good { background: #dcfce7; color: #166534; border-color: #86efac; }
        .badge-warn { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .badge-bad { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }

        .muted { color: var(--muted); }
        .mb-1 { margin-bottom: .75rem; }
        .mt-1 { margin-top: .75rem; }
        .link { color: #0b5cab; text-decoration: none; }
        .link:hover { text-decoration: underline; }

        .desktop-only { display: inline-flex; }
        .mobile-only { display: none; }

        .h-4 { width: 1rem; height: 1rem; }
        .w-4 { width: 1rem; }
        .h-5 { width: 1.25rem; height: 1.25rem; }
        .w-5 { width: 1.25rem; }
        .h-8 { width: 2rem; height: 2rem; }
        .w-8 { width: 2rem; }

        .flash-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .55rem;
        }

        .flash-copy {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-weight: 600;
        }

        .flash-success { color: #047857; }
        .flash-error { color: #be123c; }
        .flash-info { color: #1d4ed8; }
        .flash-warning { color: #b45309; }

        .field { display: grid; gap: .3rem; }
        .field-label {
            color: #475569;
            font-size: .82rem;
            font-weight: 600;
            line-height: 1.25;
        }
        .field-help {
            color: #64748b;
            font-size: .76rem;
        }
        .field-error {
            color: #b42318;
            font-size: .76rem;
            font-weight: 600;
        }

        .table-shell-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
        }
        .table-shell-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f2740;
        }

        .erp-action-grid {
            display: grid;
            gap: .9rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        .erp-action-card {
            display: flex;
            flex-direction: column;
            gap: .45rem;
            padding: .95rem;
            border-radius: 14px;
            border: 1px solid #e1e9f3;
            background: #fff;
            text-decoration: none;
            color: #0f2740;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
            transition: box-shadow .15s ease, border-color .15s ease, transform .15s ease;
        }
        .erp-action-card:hover {
            border-color: #c7d2fe;
            box-shadow: 0 10px 22px rgba(79, 70, 229, .11);
            transform: translateY(-1px);
        }
        .erp-action-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #3730a3;
            background: #eef2ff;
        }
        .erp-action-title {
            font-size: .92rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .erp-action-description {
            color: #64748b;
            font-size: .8rem;
            line-height: 1.35;
        }

        .dashboard-kpi-grid {
            display: grid;
            gap: .75rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .dashboard-page { display: grid; gap: .75rem; }
        .dashboard-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .55rem;
            flex-wrap: wrap;
        }
        .dashboard-head-title { margin: 0; font-size: 1.15rem; font-weight: 700; color: #0f2740; }
        .dashboard-head-subtitle { margin: .2rem 0 0; color: #64748b; font-size: .9rem; }
        .dashboard-actions { display: flex; gap: .45rem; flex-wrap: wrap; }
        .dashboard-quick-grid {
            display: grid;
            gap: .55rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        .dashboard-quick-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .65rem;
            padding: .95rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            text-decoration: none;
            color: #0f2740;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .dashboard-quick-item:hover { border-color: #c7d2fe; box-shadow: 0 10px 22px rgba(79, 70, 229, .1); }
        .dashboard-quick-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: #eef2ff;
            color: #4338ca;
        }
        .dashboard-quick-title { font-size: .9rem; font-weight: 700; }
        .dashboard-quick-desc { margin-top: .1rem; color: #64748b; font-size: .8rem; }
        .dashboard-chart-grid,
        .dashboard-operational-grid,
        .dashboard-ops-grid,
        .dashboard-activity-grid {
            display: grid;
            gap: .72rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        .dashboard-timeline {
            display: grid;
            gap: .4rem;
        }
        .dashboard-timeline-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .6rem;
            align-items: start;
            padding: .42rem .1rem;
            border-bottom: 1px solid #edf2f7;
        }
        .dashboard-timeline-item:last-child { border-bottom: 0; }
        .dashboard-timeline-dot {
            width: .6rem;
            height: .6rem;
            margin-top: .4rem;
            border-radius: 999px;
            background: #4f46e5;
            box-shadow: 0 0 0 3px #e0e7ff;
        }
        .dashboard-timeline-title {
            margin: 0;
            font-size: .87rem;
            font-weight: 700;
            color: #1e293b;
        }
        .dashboard-timeline-meta {
            margin-top: .12rem;
            font-size: .78rem;
            color: #64748b;
        }

        .empty-state-body {
            text-align: center;
            padding: .92rem;
            color: var(--muted);
            display: grid;
            justify-items: center;
            gap: .34rem;
        }

        .empty-state-icon { color: #7b93ad; }
        .empty-state-title { font-weight: 700; color: #30475f; }
        .empty-state-description { max-width: 520px; font-size: .9rem; }

        .smart-btn {
            display: flex;
            align-items: center;
            gap: .45rem;
            text-decoration: none;
            border: 1px solid #d8e2ef;
            background: #fff;
            border-radius: 10px;
            padding: .44rem .6rem;
            color: #1f3a56;
            min-width: 150px;
            transition: all .15s ease;
        }

        .smart-btn:hover { background: #f8fbff; border-color: #bfd6f0; }
        .smart-btn-copy { display: grid; gap: .08rem; }
        .smart-btn-label { font-size: .76rem; color: #5c7189; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
        .smart-btn-value { font-size: .96rem; font-weight: 700; color: #1a3855; }
        .smart-btn-icon { color: #2563eb; }
        .smart-btn-warning .smart-btn-icon { color: #b45309; }
        .smart-btn-danger .smart-btn-icon { color: #b42318; }
        .smart-btn-success .smart-btn-icon { color: #047857; }

        .form-actions-sticky {
            position: sticky;
            bottom: 0;
            background: rgba(255,255,255,.97);
            border-top: 1px solid #d9e4ef;
            padding: .56rem;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            display: flex;
            justify-content: flex-end;
            gap: .34rem;
            z-index: 5;
        }

        .quick-action-card-body {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: .55rem;
            flex-wrap: wrap;
        }
        .quick-action-card-body > div:first-child { min-width: 180px; }
        .quick-action-card-body .btn-group { row-gap: .35rem; }

        .chart-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .42rem;
            margin-bottom: .5rem;
        }

        .chart-card-title {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-weight: 700;
            color: #19324d;
        }

        .chart-card-subtitle {
            font-size: .76rem;
            color: #6c8299;
        }
        .chart-canvas {
            position: relative;
            height: 190px;
            width: 100%;
        }

        @media (max-width: 1024px) {
            .sidebar-desktop { display: none; }
            .content-wrap,
            .content-wrap.is-collapsed { margin-left: 0; }
            .desktop-only { display: none; }
            .mobile-only { display: inline-flex; }
            .content { padding: .7rem; }
            .topbar { top: 0; }
            .topbar-user-copy { display: none; }
            .dropdown-menu { right: auto; left: 0; }
            .smart-btn { min-width: 100%; }
            .chart-canvas { height: 175px; }
        }

        @media (min-width: 768px) {
            .erp-action-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dashboard-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .dashboard-quick-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (min-width: 1280px) {
            .erp-action-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .dashboard-kpi-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); }
            .dashboard-quick-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        @media (min-width: 1024px) {
            .dashboard-chart-grid,
            .dashboard-operational-grid,
            .dashboard-ops-grid,
            .dashboard-activity-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1025px) {
            .sidebar-mobile,
            .sidebar-backdrop { display: none !important; }
        }
    </style>
</head>
<body x-data="adminLayout()" x-init="initSidebar()">
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
            initSidebar() {
                const stored = localStorage.getItem('erp_sidebar_collapsed');
                this.sidebarCollapsed = stored === '1';
            },
            toggleCollapsed() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('erp_sidebar_collapsed', this.sidebarCollapsed ? '1' : '0');
            }
        };
    }
</script>
</body>
</html>


