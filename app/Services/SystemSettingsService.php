<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SystemSettingsService
{
    private const UI_CACHE_KEY = 'app-settings-ui-payload';
    private const BRANDING_KEYS = ['system_logo', 'system_favicon'];

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = Setting::get($key, $default);

        if (in_array($key, self::BRANDING_KEYS, true)) {
            return $this->normalizeFilePath((string) ($value ?? ''));
        }

        return $value;
    }

    public function set(string $key, mixed $value, ?string $group = null): void
    {
        if (in_array($key, self::BRANDING_KEYS, true)) {
            $value = $this->normalizeFilePath((string) $value);
        }

        $this->setSetting($key, $value, $group);
    }

    public function getSetting(string $key): mixed
    {
        return $this->get($key);
    }

    public function setSetting(string $key, mixed $value, ?string $group = null): void
    {
        $detectedType = $this->detectType($value);

        Setting::set(
            key: $key,
            value: $value,
            type: $detectedType,
            group: $group ?? 'general'
        );

        $this->forgetUiCache();
    }

    /**
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        return Cache::rememberForever(Setting::groupCacheKey($group), static function () use ($group): array {
            return Setting::query()
                ->where('group', $group)
                ->get(['key', 'value', 'type'])
                ->mapWithKeys(static fn (Setting $setting): array => [
                    $setting->key => Setting::get($setting->key),
                ])
                ->toArray();
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set((string) $key, $value, $group);
        }

        $this->forgetUiCache();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateGeneralSettings(array $data): void
    {
        $this->updateGroup('general', [
            'system_name' => (string) ($data['system_name'] ?? ''),
            'company_name' => (string) ($data['company_name'] ?? ''),
            'company_email' => (string) ($data['company_email'] ?? ''),
            'company_phone' => (string) ($data['company_phone'] ?? ''),
        ]);

        $this->updateGroup('preferences', [
            'default_currency' => (string) ($data['default_currency'] ?? 'USD'),
            'timezone' => (string) ($data['timezone'] ?? config('app.timezone')),
            'date_format' => (string) ($data['date_format'] ?? 'Y-m-d'),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateGeneral(array $data): void
    {
        $this->updateGeneralSettings($data);
    }

    /**
     * @param array<string, UploadedFile|null> $files
     */
    public function updateBrandingSettings(array $files): void
    {
        $updates = [];

        if (($files['system_logo'] ?? null) instanceof UploadedFile) {
            $updates['system_logo'] = $this->storeBrandingFile(
                file: $files['system_logo'],
                key: 'system_logo',
                prefix: 'logo'
            );
        }

        if (($files['system_favicon'] ?? null) instanceof UploadedFile) {
            $updates['system_favicon'] = $this->storeBrandingFile(
                file: $files['system_favicon'],
                key: 'system_favicon',
                prefix: 'favicon'
            );
        }

        if ($updates !== []) {
            $this->updateGroup('branding', $updates);
            $this->forgetUiCache();
        }
    }

    /**
     * @param array<string, UploadedFile|null> $files
     */
    public function updateBranding(array $files): void
    {
        $this->updateBrandingSettings($files);
    }

    public function getLogoPath(): ?string
    {
        $path = (string) ($this->get('system_logo', '') ?? '');

        return $path !== '' ? $path : null;
    }

    public function getFaviconPath(): ?string
    {
        $path = (string) ($this->get('system_favicon', '') ?? '');

        return $path !== '' ? $path : null;
    }

    public function getLogoUrl(): ?string
    {
        return $this->getFileUrl('system_logo');
    }

    public function getFaviconUrl(): ?string
    {
        return $this->getFileUrl('system_favicon');
    }

    public function getFileUrl(string $key): ?string
    {
        $path = (string) ($this->get($key, '') ?? '');
        if ($path === '') {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return $this->toRelativePublicUrl((string) Storage::disk('public')->url($path));
    }

    /**
     * @return array<string, mixed>
     */
    public function getBrandingPreviewData(): array
    {
        return [
            'system_logo' => (string) ($this->getLogoPath() ?? ''),
            'system_favicon' => (string) ($this->getFaviconPath() ?? ''),
            'system_logo_url' => $this->getLogoUrl(),
            'system_favicon_url' => $this->getFaviconUrl(),
            'logo_url' => $this->getLogoUrl(),
            'favicon_url' => $this->getFaviconUrl(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getBrandingData(): array
    {
        return $this->getBrandingPreviewData();
    }

    /**
     * @return array<string, mixed>
     */
    public function getUiPayload(): array
    {
        return Cache::rememberForever(self::UI_CACHE_KEY, function (): array {
            $general = $this->getGroup('general');
            $branding = $this->getBrandingPreviewData();
            $preferences = $this->getGroup('preferences');

            return array_merge($general, $branding, $preferences, [
                'system_name' => $general['system_name'] ?? (string) config('app.name'),
                'company_name' => $general['company_name'] ?? '',
                'company_email' => $general['company_email'] ?? '',
                'company_phone' => $general['company_phone'] ?? '',
                'default_currency' => $preferences['default_currency'] ?? 'USD',
                'timezone' => $preferences['timezone'] ?? (string) config('app.timezone'),
                'date_format' => $preferences['date_format'] ?? 'Y-m-d',
                'system_logo' => $branding['system_logo'] ?? '',
                'system_favicon' => $branding['system_favicon'] ?? '',
                'system_logo_url' => $branding['system_logo_url'] ?? null,
                'system_favicon_url' => $branding['system_favicon_url'] ?? null,
                'logo_url' => $branding['system_logo_url'] ?? null,
                'favicon_url' => $branding['system_favicon_url'] ?? null,
            ]);
        });
    }

    public function forgetUiCache(): void
    {
        Cache::forget(self::UI_CACHE_KEY);
    }

    public function normalizeStoredBrandingPaths(): void
    {
        foreach (self::BRANDING_KEYS as $key) {
            $setting = Setting::query()
                ->where('key', $key)
                ->first(['id', 'value', 'group']);

            if (!$setting) {
                continue;
            }

            $current = (string) $setting->value;
            $normalized = $this->normalizeFilePath($current);

            if ($normalized === $current) {
                continue;
            }

            $this->setSetting(
                key: $key,
                value: $normalized,
                group: (string) ($setting->group ?: 'branding')
            );
        }
    }

    private function detectType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }

    private function storeBrandingFile(UploadedFile $file, string $key, string $prefix): string
    {
        $oldPath = $this->normalizeFilePath((string) ($this->get($key, '') ?? ''));
        $directory = $prefix === 'favicon' ? 'system/favicon' : 'system/logo';
        $newPath = $file->storeAs(
            $directory,
            $prefix . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        if ($oldPath !== '' && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $newPath;
    }

    private function normalizeFilePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsedPath = (string) parse_url($path, PHP_URL_PATH);
            $path = $parsedPath !== '' ? $parsedPath : $path;
        }

        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        while (str_starts_with($path, 'storage/')) {
            $path = (string) Str::after($path, 'storage/');
        }

        while (str_starts_with($path, 'public/')) {
            $path = (string) Str::after($path, 'public/');
        }

        return $path;
    }

    private function toRelativePublicUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            return str_starts_with($url, '/') ? $url : '/'.$url;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        $query = (string) parse_url($url, PHP_URL_QUERY);

        if ($path === '') {
            return $url;
        }

        return $query !== '' ? $path.'?'.$query : $path;
    }
}
