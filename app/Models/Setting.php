<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(self::cacheKey($key), static function () use ($key, $default): mixed {
            $setting = self::query()->where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::decodeValue((string) $setting->value, (string) $setting->type);
        });
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        $setting = self::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => self::encodeValue($value, $type),
                'type' => $type,
                'group' => $group,
            ]
        );

        self::clearCache($key, $group);

        return $setting;
    }

    public static function clearCache(string $key, ?string $group = null): void
    {
        Cache::forget(self::cacheKey($key));

        if ($group !== null) {
            Cache::forget(self::groupCacheKey($group));
        }
    }

    public static function cacheKey(string $key): string
    {
        return "setting:{$key}";
    }

    public static function groupCacheKey(string $group): string
    {
        return "settings-group:{$group}";
    }

    private static function encodeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'json' => (string) json_encode($value, JSON_THROW_ON_ERROR),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    private static function decodeValue(string $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => $value === '1',
            'json' => json_decode($value, true, 512, JSON_THROW_ON_ERROR),
            default => $value,
        };
    }
}
