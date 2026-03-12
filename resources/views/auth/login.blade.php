<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $systemName = (string) ($appSettings['system_name'] ?? config('app.name', 'ERP Platform'));
        $companyName = trim((string) ($appSettings['company_name'] ?? ''));
        $logoUrl = $appSettings['logo_url'] ?? $appSettings['system_logo_url'] ?? null;
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
    <title>{{ __('auth.login') }} | {{ $systemName }}</title>
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Sans+Ethiopic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --auth-bg: #e2e8f0;
            --auth-card: #ffffff;
            --auth-ink: #0f172a;
            --auth-muted: #64748b;
            --auth-line: #dbe4ef;
            --auth-brand-a: #0b1323;
            --auth-brand-b: #152640;
            --auth-accent: #4f46e5;
            --auth-accent-hover: #4338ca;
            --auth-error: #b42318;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Noto Sans Ethiopic", "Noto Sans", "Segoe UI", Tahoma, system-ui, sans-serif;
            color: var(--auth-ink);
            background:
                radial-gradient(circle at 10% 15%, #dbeafe 0%, rgba(219,234,254,0) 45%),
                radial-gradient(circle at 90% 85%, #d1fae5 0%, rgba(209,250,229,0) 40%),
                linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.1rem;
        }

        .auth-grid {
            width: min(1180px, 100%);
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .auth-brand-panel,
        .auth-login-panel {
            border-radius: 22px;
            overflow: hidden;
        }

        .auth-brand-panel {
            order: 2;
            border: 1px solid #203451;
            background: linear-gradient(150deg, var(--auth-brand-a), var(--auth-brand-b));
            color: #e2e8f0;
            padding: 1.6rem;
            position: relative;
            min-height: 250px;
        }

        .auth-brand-panel::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            top: -80px;
            right: -100px;
            border-radius: 999px;
            background: rgba(99, 102, 241, .18);
            filter: blur(1px);
        }

        .auth-brand-inner {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 1rem;
            align-content: space-between;
            min-height: 100%;
        }

        .auth-brand-head {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .auth-brand-logo {
            width: 3rem;
            height: 3rem;
            border-radius: .9rem;
            background: rgba(255, 255, 255, .95);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .45);
            overflow: hidden;
            color: #0f172a;
            font-weight: 700;
            flex-shrink: 0;
        }

        .auth-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .auth-brand-title {
            margin: 0;
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1.2;
            color: #f8fafc;
        }

        .auth-brand-subtitle {
            margin: .2rem 0 0;
            color: #bfd0e3;
            font-size: .84rem;
        }

        .auth-brand-copy {
            margin: 0;
            max-width: 48ch;
            line-height: 1.55;
            color: #d7e5f6;
            font-size: .9rem;
        }

        .auth-brand-points {
            display: grid;
            gap: .55rem;
        }

        .auth-brand-point {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #d7e5f6;
            font-size: .84rem;
            font-weight: 500;
        }

        .auth-dot {
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
            background: #60a5fa;
            flex-shrink: 0;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, .18);
        }

        .auth-login-panel {
            order: 1;
            background: var(--auth-card);
            border: 1px solid var(--auth-line);
            box-shadow: 0 24px 56px rgba(15, 23, 42, .14);
            padding: 1.35rem;
            display: grid;
            gap: 1rem;
            align-content: start;
        }

        .auth-top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
        }

        .auth-lang-switch {
            display: inline-flex;
            gap: .28rem;
            border: 1px solid var(--auth-line);
            border-radius: .65rem;
            padding: .2rem;
            background: #f8fafc;
        }

        .auth-lang-switch form { margin: 0; }
        .auth-lang-switch button {
            border: 0;
            border-radius: .45rem;
            padding: .35rem .62rem;
            font-size: .72rem;
            font-weight: 700;
            color: #475569;
            background: transparent;
            cursor: pointer;
        }
        .auth-lang-switch button.active {
            background: #fff;
            color: #1e293b;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .16);
        }

        .auth-card-head {
            display: grid;
            gap: .35rem;
        }

        .auth-card-title {
            margin: 0;
            font-size: 1.42rem;
            line-height: 1.2;
            color: #0f172a;
            font-weight: 700;
        }

        .auth-card-subtitle {
            margin: 0;
            color: var(--auth-muted);
            font-size: .88rem;
        }

        .auth-alert {
            border-radius: .75rem;
            border: 1px solid #fecaca;
            background: #fff1f2;
            color: var(--auth-error);
            padding: .65rem .78rem;
            font-size: .82rem;
            font-weight: 600;
        }

        .auth-field {
            display: grid;
            gap: .3rem;
            margin-bottom: .8rem;
        }

        .auth-label {
            font-size: .84rem;
            font-weight: 600;
            color: #334155;
        }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input {
            width: 100%;
            height: 2.9rem;
            border-radius: .82rem;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #0f172a;
            font-size: .9rem;
            padding: 0 .85rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .auth-input:focus {
            outline: 0;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .18);
        }

        .auth-input.password {
            padding-right: 2.8rem;
        }

        .auth-toggle {
            position: absolute;
            right: .42rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #64748b;
            width: 2rem;
            height: 2rem;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .auth-toggle:hover { background: #eef2ff; color: #3730a3; }

        .auth-field-error {
            font-size: .76rem;
            color: var(--auth-error);
            font-weight: 600;
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            margin-top: .1rem;
            margin-bottom: .95rem;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #475569;
            font-size: .82rem;
            font-weight: 500;
        }
        .auth-check input { width: 1rem; height: 1rem; accent-color: #4f46e5; }

        .auth-link {
            color: #4338ca;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-link:hover { text-decoration: underline; }

        .auth-submit {
            width: 100%;
            border: 0;
            border-radius: .82rem;
            height: 2.9rem;
            background: var(--auth-accent);
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s ease;
        }
        .auth-submit:hover { background: var(--auth-accent-hover); }

        .auth-foot {
            display: grid;
            gap: .25rem;
            margin-top: .25rem;
            text-align: center;
            color: #64748b;
            font-size: .75rem;
        }

        @media (min-width: 992px) {
            .auth-grid { grid-template-columns: 1.2fr .95fr; gap: 1.15rem; }
            .auth-brand-panel { order: 1; min-height: 620px; padding: 2rem; }
            .auth-login-panel { order: 2; padding: 2rem; }
        }
    </style>
</head>
<body x-data="{ showPassword: false }">
    <div class="auth-shell">
        <div class="auth-grid">
            <section class="auth-brand-panel">
                <div class="auth-brand-inner">
                    <div class="auth-brand-head">
                        <div class="auth-brand-logo">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $systemName }}">
                            @else
                                {{ strtoupper(substr($systemName, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h2 class="auth-brand-title">{{ $systemName }}</h2>
                            <p class="auth-brand-subtitle">{{ $companyName !== '' ? $companyName : __('navigation.operations_console') }}</p>
                        </div>
                    </div>

                    <p class="auth-brand-copy">{{ __('auth.brand_message') }}</p>

                    <div class="auth-brand-points">
                        <div class="auth-brand-point"><span class="auth-dot"></span>{{ __('auth.secure_sign_in') }}</div>
                        <div class="auth-brand-point"><span class="auth-dot"></span>{{ __('auth.role_based_access') }}</div>
                        <div class="auth-brand-point"><span class="auth-dot"></span>{{ __('auth.managed_by_org') }}</div>
                    </div>
                </div>
            </section>

            <section class="auth-login-panel">
                <div class="auth-top-actions">
                    <div class="auth-brand-subtitle">{{ __('common.language') }}</div>
                    <div class="auth-lang-switch">
                        <form method="POST" action="{{ route('language.switch', 'en') }}">
                            @csrf
                            <button type="submit" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</button>
                        </form>
                        <form method="POST" action="{{ route('language.switch', 'am') }}">
                            @csrf
                            <button type="submit" class="{{ app()->getLocale() === 'am' ? 'active' : '' }}">AM</button>
                        </form>
                    </div>
                </div>

                <div class="auth-card-head">
                    <h1 class="auth-card-title">{{ __('auth.welcome_back') }}</h1>
                    <p class="auth-card-subtitle">{{ __('auth.workspace_subtitle') }}</p>
                </div>

                @if(session('status'))
                    <div class="auth-alert" style="border-color:#bfdbfe;background:#eff6ff;color:#1d4ed8;">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="auth-alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf

                    <div class="auth-field">
                        <label class="auth-label" for="email">{{ __('auth.email') }}</label>
                        <div class="auth-input-wrap">
                            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required>
                        </div>
                        @error('email')
                            <div class="auth-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password">{{ __('auth.password') }}</label>
                        <div class="auth-input-wrap">
                            <input id="password" class="auth-input password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="current-password" required>
                            <button class="auth-toggle" type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? @js(__('auth.hide_password')) : @js(__('auth.show_password'))">
                                <svg x-show="!showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:1.1rem;height:1.1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:1.1rem;height:1.1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.584 10.587A2.999 2.999 0 0012 15a2.99 2.99 0 002.413-1.216M9.88 5.09A9.953 9.953 0 0112 4.5c4.638 0 8.573 3.007 9.963 7.178a1.01 1.01 0 010 .639 10.007 10.007 0 01-4.293 5.21M6.228 6.226A9.956 9.956 0 002.037 11.68a1.01 1.01 0 000 .639c1.39 4.172 5.325 7.18 9.963 7.18 1.59 0 3.094-.352 4.443-.983" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="auth-field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="auth-row">
                        <label class="auth-check">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                            <span>{{ __('auth.remember_me') }}</span>
                        </label>
                        @if(\Illuminate\Support\Facades\Route::has('password.request'))
                            <a class="auth-link" href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
                        @endif
                    </div>

                    <button class="auth-submit" type="submit">{{ __('auth.sign_in') }}</button>
                </form>

                <div class="auth-foot">
                    <div>{{ __('auth.secure_sign_in') }}</div>
                    <div>{{ __('auth.managed_by_org') }}</div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

