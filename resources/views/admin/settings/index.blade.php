@extends('layouts.admin')
@section('title', __('settings.title'))
@section('page-title', __('settings.title'))
@section('page-subtitle', __('settings.subtitle'))
@section('content')
<style>
    .settings-page { display: grid; gap: 1.25rem; }

    .settings-summary-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }
    .settings-summary-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
        padding: 1rem 1.1rem;
    }
    .settings-summary-label {
        font-size: .68rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 700;
    }
    .settings-summary-value {
        margin-top: .24rem;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .settings-main-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1.5rem;
    }
    .settings-main-col,
    .settings-side-col {
        display: grid;
        gap: 1.1rem;
        align-content: start;
    }
    .settings-form-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: .95rem;
    }

    .settings-card-title {
        margin: 0;
        font-size: .98rem;
        font-weight: 700;
        color: #0f2740;
    }
    .settings-card-subtitle {
        margin: .18rem 0 0;
        color: #64748b;
        font-size: .78rem;
    }

    .branding-upload-box {
        border: 2px dashed #d9e2ef;
        border-radius: 12px;
        padding: .9rem;
        text-align: center;
        background: #fbfcff;
        display: grid;
        gap: .35rem;
        cursor: pointer;
        transition: border-color .15s ease;
    }
    .branding-upload-box:hover { border-color: #a5b4fc; }

    .branding-upload-label {
        font-size: .82rem;
        font-weight: 700;
        color: #1f2937;
    }
    .branding-upload-help {
        font-size: .72rem;
        color: #64748b;
    }

    .branding-upload-preview {
        margin-top: .5rem;
        border: 1px dashed #dbe4f0;
        border-radius: 10px;
        background: #f9fbff;
        padding: .7rem;
        display: grid;
        gap: .5rem;
    }

    .branding-preview-logo-box,
    .branding-preview-favicon-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .branding-preview-logo-box { width: 100%; height: 7rem; }
    .branding-preview-favicon-box {
        width: 4rem;
        height: 4rem;
    }
    .branding-preview-logo-box img {
        max-height: 6rem;
        width: auto;
        object-fit: contain;
    }
    .branding-preview-favicon-box img {
        max-height: 2.4rem;
        width: auto;
        object-fit: contain;
    }
    .branding-preview-placeholder {
        color: #64748b;
        font-size: .74rem;
        font-weight: 600;
    }

    .branding-live-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .9rem;
    }
    .branding-live-left {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }
    .branding-live-logo {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .branding-live-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .branding-live-name {
        font-size: .88rem;
        font-weight: 700;
        color: #0f2740;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .settings-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: .45rem;
    }

    .future-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        padding: .55rem .75rem;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        color: #334155;
        font-size: .8rem;
        font-weight: 600;
        background: #fff;
    }
    .future-link:hover { background: #f8fafc; }

    .settings-info-list,
    .settings-notes-list {
        display: grid;
        gap: .5rem;
    }
    .settings-info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
        padding: .55rem .7rem;
    }
    .settings-info-label {
        font-size: .75rem;
        color: #64748b;
        font-weight: 600;
    }
    .settings-info-value {
        font-size: .8rem;
        color: #0f2740;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 65%;
    }
    .settings-note-item {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        padding: .45rem .1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .settings-note-item:last-child { border-bottom: 0; }
    .settings-note-copy {
        font-size: .78rem;
        color: #475569;
        line-height: 1.4;
    }

    @media (min-width: 768px) {
        .settings-summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .settings-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .settings-form-grid.preferences { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (min-width: 1280px) {
        .settings-main-grid {
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1.5rem;
        }
        .settings-main-col { grid-column: span 8 / span 8; }
        .settings-side-col { grid-column: span 4 / span 4; }
    }
</style>

<div class="settings-page" x-data="{
    logoPreview: @js(old('system_logo') ? '' : ($settings['system_logo_url'] ?? '')),
    faviconPreview: @js(old('system_favicon') ? '' : ($settings['system_favicon_url'] ?? '')),
    readFile(event, target) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => { this[target] = e.target?.result ?? ''; };
        reader.readAsDataURL(file);
    },
    clearBroken(target) {
        this[target] = '';
    }
}">
    <x-ui.page-header :title="__('settings.title')" :subtitle="__('settings.subtitle')" icon="heroicon-o-cog-6-tooth">
        <x-slot:actions>
            <x-ui.page-actions>
                <x-ui.button type="submit" form="general-settings-form" icon="heroicon-o-check">{{ __('settings.save_general_settings') }}</x-ui.button>
                <x-ui.button variant="secondary" type="submit" form="branding-settings-form" icon="heroicon-o-photo">{{ __('settings.save_branding') }}</x-ui.button>
            </x-ui.page-actions>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="settings-summary-grid">
        <div class="settings-summary-card">
            <div class="settings-summary-label">{{ __('settings.system_name') }}</div>
            <div class="settings-summary-value">{{ $settings['system_name'] }}</div>
        </div>
        <div class="settings-summary-card">
            <div class="settings-summary-label">{{ __('settings.default_currency') }}</div>
            <div class="settings-summary-value">{{ $settings['default_currency'] }}</div>
        </div>
        <div class="settings-summary-card">
            <div class="settings-summary-label">{{ __('settings.timezone') }}</div>
            <div class="settings-summary-value">{{ $settings['timezone'] }}</div>
        </div>
    </div>

    <div class="settings-main-grid">
        <div class="settings-main-col">
            <form id="general-settings-form" method="POST" action="{{ route('admin.settings.general') }}">
                @csrf
                <x-ui.card class="mb-1">
                    <x-slot:header>
                        <h3 class="settings-card-title">{{ __('settings.general_settings') }}</h3>
                        <p class="settings-card-subtitle">{{ __('settings.general_settings_help') }}</p>
                    </x-slot:header>
                    <div class="settings-form-grid">
                        <x-ui.input name="system_name" :label="__('settings.system_name')" :value="old('system_name', $settings['system_name'])" required :help="__('settings.system_name_help')" />
                        <x-ui.input name="company_name" :label="__('settings.company_name')" :value="old('company_name', $settings['company_name'])" :help="__('settings.company_name_help')" />
                        <x-ui.input name="company_email" :label="__('settings.company_email')" type="email" :value="old('company_email', $settings['company_email'])" />
                        <x-ui.input name="company_phone" :label="__('settings.company_phone')" :value="old('company_phone', $settings['company_phone'])" />
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <x-slot:header>
                        <h3 class="settings-card-title">{{ __('settings.system_preferences') }}</h3>
                        <p class="settings-card-subtitle">{{ __('settings.system_preferences_help') }}</p>
                    </x-slot:header>
                    <div class="settings-form-grid preferences">
                        <x-ui.select name="default_currency" :label="__('settings.default_currency')" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" @selected(old('default_currency', $settings['default_currency']) === $currency)>{{ $currency }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.select name="timezone" :label="__('settings.timezone')" required>
                            @foreach($timezones as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', $settings['timezone']) === $timezone)>{{ $timezone }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.select name="date_format" :label="__('settings.date_format')" required>
                            @foreach($dateFormats as $format)
                                <option value="{{ $format }}" @selected(old('date_format', $settings['date_format']) === $format)>{{ $format }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                </x-ui.card>
            </form>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="settings-card-title">{{ __('settings.live_branding_preview') }}</h3>
                    <p class="settings-card-subtitle">{{ __('settings.live_branding_preview_help') }}</p>
                </x-slot:header>
                <div class="branding-live-card">
                    <div class="branding-live-left">
                        <div class="branding-live-logo">
                            <template x-if="logoPreview">
                                <img :src="logoPreview" alt="{{ __('settings.system_logo') }}" x-on:error="clearBroken('logoPreview')">
                            </template>
                            <template x-if="!logoPreview">
                                <span>{{ strtoupper(substr((string) ($settings['system_name'] ?? 'E'), 0, 1)) }}</span>
                            </template>
                        </div>
                        <div style="min-width:0;">
                            <div class="branding-live-name">{{ $settings['system_name'] ?: __('navigation.erp_platform') }}</div>
                            <div class="muted" style="font-size:.76rem;">{{ $settings['company_email'] ?: __('settings.company_email') }}</div>
                        </div>
                    </div>
                    <div class="branding-preview-favicon-box">
                        <template x-if="faviconPreview">
                            <img :src="faviconPreview" alt="{{ __('settings.system_favicon') }}" x-on:error="clearBroken('faviconPreview')">
                        </template>
                        <template x-if="!faviconPreview">
                            <span style="font-size:.62rem;" class="branding-preview-placeholder">{{ strtoupper(substr((string) ($settings['system_name'] ?? 'F'), 0, 1)) }}</span>
                        </template>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="settings-card-title">{{ __('settings.general_settings') }}</h3>
                    <p class="settings-card-subtitle">{{ __('settings.general_settings_help') }}</p>
                </x-slot:header>
                <div class="settings-notes-list">
                    <div class="settings-note-item">
                        <x-heroicon-o-photo class="h-4 w-4 muted" />
                        <div class="settings-note-copy">{{ __('settings.system_logo') }} · {{ __('navigation.dashboard') }}</div>
                    </div>
                    <div class="settings-note-item">
                        <x-heroicon-o-globe-alt class="h-4 w-4 muted" />
                        <div class="settings-note-copy">{{ __('settings.system_favicon') }} · {{ __('common.system') }}</div>
                    </div>
                    <div class="settings-note-item">
                        <x-heroicon-o-clock class="h-4 w-4 muted" />
                        <div class="settings-note-copy">{{ __('settings.timezone') }} · {{ __('navigation.reports') }}</div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="settings-side-col">
            <form id="branding-settings-form" method="POST" action="{{ route('admin.settings.branding') }}" enctype="multipart/form-data">
                @csrf
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="settings-card-title">{{ __('settings.branding') }}</h3>
                        <p class="settings-card-subtitle">{{ __('settings.branding_help') }}</p>
                    </x-slot:header>
                    <div class="grid" style="gap:1rem;">
                        <div class="field">
                            <label class="field-label" for="system_logo">{{ __('settings.system_logo') }}</label>
                            <label class="branding-upload-box" for="system_logo">
                                <span class="branding-upload-label">{{ __('settings.upload_logo') }}</span>
                                <span class="branding-upload-help">{{ __('settings.drag_drop_or_browse') }}</span>
                                <span class="branding-upload-help">{{ __('settings.upload_logo_help') }}</span>
                            </label>
                            <input id="system_logo" type="file" name="system_logo" style="display:none;" accept=".png,.jpg,.jpeg,.svg,image/png,image/jpeg,image/svg+xml" @change="readFile($event, 'logoPreview')">
                            @error('system_logo')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                            <div class="branding-upload-preview">
                                <span class="muted" style="font-size:.76rem;">{{ __('settings.logo_preview') }}</span>
                                <div class="branding-preview-logo-box" style="height:7rem;background:#f8fafc;">
                                    <template x-if="logoPreview">
                                        <img :src="logoPreview" alt="{{ __('settings.logo_preview') }}" style="max-height:6rem;width:auto;object-fit:contain;" x-on:error="clearBroken('logoPreview')">
                                    </template>
                                    <template x-if="!logoPreview">
                                        <span class="branding-preview-placeholder">{{ __('settings.no_logo') }}</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label" for="system_favicon">{{ __('settings.system_favicon') }}</label>
                            <label class="branding-upload-box" for="system_favicon">
                                <span class="branding-upload-label">{{ __('settings.upload_favicon') }}</span>
                                <span class="branding-upload-help">{{ __('settings.drag_drop_or_browse') }}</span>
                                <span class="branding-upload-help">{{ __('settings.upload_favicon_help') }}</span>
                            </label>
                            <input id="system_favicon" type="file" name="system_favicon" style="display:none;" accept=".png,.svg,.ico,image/png,image/svg+xml,image/x-icon" @change="readFile($event, 'faviconPreview')">
                            @error('system_favicon')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                            <div class="branding-upload-preview">
                                <span class="muted" style="font-size:.76rem;">{{ __('settings.favicon_preview') }}</span>
                                <div class="branding-preview-favicon-box" style="height:4rem;width:4rem;background:#f8fafc;">
                                    <template x-if="faviconPreview">
                                        <img :src="faviconPreview" alt="{{ __('settings.favicon_preview') }}" x-on:error="clearBroken('faviconPreview')">
                                    </template>
                                    <template x-if="!faviconPreview">
                                        <span class="branding-preview-placeholder" style="font-size:.68rem;">{{ __('settings.no_favicon') }}</span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-slot:footer>
                        <div class="settings-actions">
                            <x-ui.button type="submit" variant="secondary" icon="heroicon-o-photo">{{ __('settings.save_branding') }}</x-ui.button>
                        </div>
                    </x-slot:footer>
                </x-ui.card>
            </form>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="settings-card-title">{{ __('common.system') }}</h3>
                    <p class="settings-card-subtitle">{{ __('settings.system_preferences_help') }}</p>
                </x-slot:header>
                <div class="settings-info-list">
                    <div class="settings-info-item">
                        <span class="settings-info-label">{{ __('common.language') }}</span>
                        <span class="settings-info-value">{{ strtoupper((string) app()->getLocale()) }}</span>
                    </div>
                    <div class="settings-info-item">
                        <span class="settings-info-label">{{ __('settings.timezone') }}</span>
                        <span class="settings-info-value">{{ $settings['timezone'] }}</span>
                    </div>
                    <div class="settings-info-item">
                        <span class="settings-info-label">{{ __('settings.default_currency') }}</span>
                        <span class="settings-info-value">{{ $settings['default_currency'] }}</span>
                    </div>
                    <div class="settings-info-item">
                        <span class="settings-info-label">{{ __('settings.date_format') }}</span>
                        <span class="settings-info-value">{{ $settings['date_format'] }}</span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="settings-card-title">{{ __('settings.future_sections') }}</h3>
                    <p class="settings-card-subtitle">{{ __('settings.prepared_for_expansion') }}</p>
                </x-slot:header>
                <div class="grid" style="gap:.45rem;">
                    <button type="button" class="future-link">{{ __('settings.email_configuration') }} <x-heroicon-o-chevron-right class="h-4 w-4" /></button>
                    <button type="button" class="future-link">{{ __('settings.invoice_branding') }} <x-heroicon-o-chevron-right class="h-4 w-4" /></button>
                    <button type="button" class="future-link">{{ __('settings.tax_defaults') }} <x-heroicon-o-chevron-right class="h-4 w-4" /></button>
                    <button type="button" class="future-link">{{ __('settings.pos_defaults') }} <x-heroicon-o-chevron-right class="h-4 w-4" /></button>
                    <button type="button" class="future-link">{{ __('settings.security_policies') }} <x-heroicon-o-chevron-right class="h-4 w-4" /></button>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection

