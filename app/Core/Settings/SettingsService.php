<?php

namespace App\Core\Settings;

use App\Models\Settings as SettingsModel;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

/**
 * Global settings accessor (foundation).
 *
 * Values are stored as JSON in the `settings` table and cached. Later phases
 * (Blue Control + public rendering) read site settings through this service so
 * domain code never touches the settings table directly.
 */
class SettingsService
{
    public const CACHE_KEY = 'blue.settings';

    public function __construct(
        private readonly Cache $cache,
        private readonly SettingsModel $model,
    ) {
    }

    /**
     * Get a setting value or return the default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()->get($key, $default);
    }

    /**
     * Get a boolean setting.
     */
    public function getBool(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default);
    }

    /**
     * Persist a setting (upsert) and invalidate the cache.
     */
    public function set(string $key, mixed $value, string $type = 'string', bool $isPublic = false): void
    {
        $this->model->query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'is_public' => $isPublic,
            ],
        );

        $this->cache->forget(self::CACHE_KEY);
    }

    /**
     * All settings as a key => value collection.
     */
    public function all(): Collection
    {
        return $this->cache->rememberForever(self::CACHE_KEY, function (): Collection {
            return $this->model->query()
                ->orderBy('key')
                ->pluck('value', 'key');
        });
    }

    /**
     * Forget the cached settings (used by tests/observers).
     */
    public function flush(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }
}
