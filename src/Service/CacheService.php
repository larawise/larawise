<?php

namespace Larawise\Service;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use IntBackedEnum;
use Larawise\Support\Enums\TTL;
use RuntimeException;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
class CacheService
{
    use Macroable;

    /**
     * Create a new cache service instance.
     *
     * @param CacheManager $cache
     * @param array $config
     * @param string $group
     *
     * @return void
     */
    public function __construct(
        protected CacheManager $cache,
        protected $config,
        protected $group = 'default'
    ) { }

    /**
     * Set the compression status for cached values.
     *
     * @param bool $status
     *
     * @return static
     */
    public function compress($status = true)
    {
        $this->config['compress'] = $status;

        return $this;
    }

    /**
     * Set the encryption status for cached values.
     *
     * @param bool $status
     *
     * @return static
     */
    public function encrypt($status = true)
    {
        $this->config['encrypt'] = $status;

        return $this;
    }

    /**
     * Exclude exact keys from processing.
     *
     * @param array|string $keys
     *
     * @return static
     */
    public function except($keys)
    {
        $this->config['except'] = (array) $keys;

        return $this;
    }

    /**
     * Exclude keys matching a wildcard pattern.
     *
     * @param string $pattern
     *
     * @return static
     */
    public function exclude($pattern)
    {
        $this->config['exclude'] = $pattern;

        return $this;
    }

    /**
     * Flush all cache entries for the current group.
     *
     * @return bool
     */
    public function flush()
    {
        // Use native tag-based flush if enabled
        if ($this->config['tags']) {
            $this->cache->tags($this->group)->flush();
            return true;
        }

        // Retrieve tracked keys from the meta list
        $meta = $this->metaKey();
        $keys = $this->cache->get($meta, []);

        // Forget only keys that pass the filter
        foreach ($keys as $key) {
            if ($this->shouldProcess($key)) {
                $this->cache->forget($key);
            }
        }

        // Forget the meta key itself
        $this->cache->forget($meta);

        return true;
    }

    /**
     * Remove a cached item by key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function forget($key)
    {
        if (is_array($key)) {
            $this->forgetMany($key);

            return true;
        }

        $formatted = $this->formatKey($key);

        if (! $this->shouldProcess($formatted)) {
            return false;
        }

        return $this->store()->forget($formatted);
    }

    /**
     * Forget multiple keys from cache.
     *
     * @param array<int, string> $keys
     *
     * @return void
     */
    public function forgetMany($keys)
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }
    }

    /**
     * Store a value in cache permanently (without expiration).
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, TTL::FOREVER);
    }

    /**
     * Format a cache key using the configured prefix and group.
     *
     * @param string $key
     *
     * @return string
     */
    protected function formatKey($key)
    {
        return "{$this->config['prefix']}:{$this->group}:{$key}";
    }

    /**
     * Retrieve one or more values from cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        $formatted = $this->formatKey($key);

        if (! $this->shouldProcess($formatted))
            return null;

        $value = $this->store()->get($formatted);
        $restored = $this->restore($value);

        if ($this->config['sliding'] && $this->has($key)) {
            $this->put($key, $restored);
        }

        return $restored;
    }

    /**
     * Retrieve multiple values from cache.
     *
     * @param array<int, string> $keys
     *
     * @return array<string, mixed>
     */
    public function getMany($keys)
    {
        $results = [];

        foreach ($keys as $key) {
            $value = $this->get($key);

            if ($value !== null) {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Set the cache group name.
     *
     * @param string $group
     *
     * @return static
     */
    public function group($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Determine if one or more cache keys exist.
     *
     * @param string|array<int, string> $key
     *
     * @return bool|array<string, bool>
     */
    public function has($key)
    {
        if (is_array($key)) {
            return $this->hasMany($key);
        }

        $formatted = $this->formatKey($key);
        if (! $this->shouldProcess($formatted)) {
            return false;
        }

        return $this->store()->has($formatted);
    }

    /**
     * Check if at least one of the given cache keys exists.
     *
     * @param array<int, string> $keys
     *
     * @return bool
     */
    public function hasAny($keys)
    {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check existence of multiple cache keys individually.
     *
     * @param array<int, string> $keys
     *
     * @return array<string, bool>
     */
    public function hasMany($keys)
    {
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->has($key);
        }

        return $results;
    }

    /**
     * Inspect the current cache configuration.
     *
     * @param bool $verbose
     *
     * @return array<string, mixed>
     */
    public function inspect($verbose = false)
    {
        $basic = [
            'driver'   => get_class($this->cache),
            'group'    => $this->group,
            'prefix'   => $this->config['prefix'],
            'ttl'      => $this->config['ttl'],
            'tags'     => $this->config['tags'],
            'track'    => $this->config['track'],
            'compress' => $this->config['compress'],
            'encrypt'  => $this->config['encrypt'],
            'sliding'  => $this->config['sliding'],
            'match'    => $this->config['match'] ?? null,
            'exclude'  => $this->config['exclude'] ?? null,
            'only'     => $this->config['only'] ?? null,
            'except'   => $this->config['except'] ?? null,
        ];

        if (! $verbose)
            return $basic;

        return array_merge($basic, [
            'store_class'   => get_class($this->store()),
            'meta_key'      => $this->metaKey(),
            'tracked_keys'  => $this->cache->get($this->metaKey(), []),
        ]);
    }

    /**
     * Apply a key pattern filter to all cache operations.
     *
     * @param string $pattern
     *
     * @return static
     */
    public function match($pattern)
    {
        $this->config['match'] = $pattern;

        return $this;
    }

    /**
     * Generate the meta key used to track all cache keys in the current group.
     *
     * @return string
     */
    protected function metaKey()
    {
        // Example output: larawise:meta:users
        return "{$this->config['prefix']}:meta:{$this->group}";
    }

    /**
     * Allow only specific keys to be processed.
     *
     * @param array|string $keys
     *
     * @return static
     */
    public function only($keys)
    {
        $this->config['only'] = (array) $keys;

        return $this;
    }

    /**
     * Prepare a value for caching by applying compression and encryption.
     *
     * @param mixed $value
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function prepare($value)
    {
        // Compress if enabled
        if ($this->config['compress']) {
            if (! function_exists('gzencode')) {
                throw new RuntimeException('Gzip compression is not supported on this server.');
            }

            $value = gzencode(serialize($value));
        }

        // Encrypt if enabled
        if ($this->config['encrypt']) {
            $value = encrypt($value);
        }

        return $value;
    }

    /**
     * Set the prefix used for all cache keys.
     *
     * @param string $prefix
     *
     * @return static
     */
    public function prefix($prefix)
    {
        $this->config['prefix'] = rtrim($prefix, ':');

        return $this;
    }

    /**
     * Store one or more values in cache.
     *
     * @param string $key
     * @param mixed $value
     * @param Closure|DateTimeInterface|DateInterval|int|TTL|null $ttl
     *
     * @return bool
     */
    public function put($key, $value, $ttl = null)
    {
        if (is_array($key)) {
            $this->putMany($key, $ttl);

            return true;
        }

        // Resolve TTL: use default if not provided, null if -1
        $ttl = $ttl instanceof IntBackedEnum ? $ttl->value : $ttl;
        $ttl = $ttl === -1 ? null : ($ttl ?? $this->config['ttl']);

        // Format the key with prefix and group
        $formatted = $this->formatKey($key);

        // Skip if the key fails filter checks
        if (! $this->shouldProcess($formatted)) {
            return false;
        }

        // Prepare the value (compress + encrypt if enabled)
        $value = $this->prepare($value);

        // Resolve the correct cache store (tagged or default)
        $store = $this->store();

        // Track the key if tracking is enabled
        if ($this->config['track']) {
            $this->trackKey($formatted);
        }

        // Store the value with resolved TTL
        $store->put($formatted, $value, $ttl);

        return true;
    }

    /**
     * Store multiple key-value pairs in cache.
     *
     * @param array<string, mixed> $items
     * @param Closure|DateTimeInterface|DateInterval|int|TTL|null $ttl
     *
     * @return void
     */
    public function putMany($items, $ttl = null)
    {
        foreach ($items as $key => $value) {
            $this->put($key, $value, $ttl);
        }
    }

    /**
     * Retrieve a value from cache or store it if missing.
     *
     * @param string $key
     * @param Closure|DateTimeInterface|DateInterval|int|TTL|null $ttl
     * @param Closure $callback
     *
     * @return mixed
     */
    public function remember($key, $ttl, Closure $callback)
    {
        return $this->has($key)
            ? $this->get($key)
            : tap(value($callback), fn($v) => $this->put($key, $v, $ttl));
    }

    /**
     * Retrieve a value from cache or store it permanently if missing.
     *
     * @param string $key
     * @param Closure $callback
     *
     * @return mixed
     */
    public function rememberForever($key, Closure $callback)
    {
        return $this->remember($key, TTL::FOREVER, $callback);
    }

    /**
     * Restore a cached value to its original form.
     *
     * @param mixed $value
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function restore($value)
    {
        // Decrypt if enabled and value is a string
        if ($this->config['encrypt'] && is_string($value)) {
            try {
                $value = decrypt($value);
            } catch (DecryptException) {
                return null;
            }
        }

        // Decompress if enabled and value is a string
        if ($this->config['compress'] && is_string($value)) {
            if (! function_exists('gzdecode')) {
                throw new RuntimeException('Gzip compression is not supported on this server.');
            }

            $value = unserialize(gzdecode($value));
        }

        return $value;
    }

    /**
     * Determine whether a key should be processed based on filters.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function shouldProcess($key)
    {
        // Match pattern (wildcard)
        if (isset($this->config['match']) && ! Str::is($this->config['match'], $key)) {
            return false;
        }

        // Exclude pattern (wildcard)
        if (isset($this->config['exclude']) && Str::is($this->config['exclude'], $key)) {
            return false;
        }

        // Only exact keys
        if (isset($this->config['only']) && ! Arr::only($this->config['only'], $key)) {
            return false;
        }

        // Except exact keys
        if (isset($this->config['except']) && Arr::only($this->config['except'], $key)) {
            return false;
        }

        return true;
    }

    /**
     * Enable sliding expiration for cached items.
     *
     * @param bool $status
     *
     * @return static
     */
    public function sliding($status = true)
    {
        $this->config['sliding'] = $status;

        return $this;
    }

    /**
     * Resolve the active cache store for the current operation.
     *
     * @return mixed
     */
    protected function store()
    {
        // Use tagged cache if enabled, scoped to the current group
        return $this->config['tags']
            ? $this->cache->tags($this->group)
            : $this->cache;
    }

    /**
     * Set the tag-based caching status.
     *
     * @param bool $status
     *
     * @return static
     */
    public function tags($status = true)
    {
        $this->config['tags'] = $status;

        return $this;
    }

    /**
     * Set the key tracking status for manual flushing.
     *
     * @param bool $status
     *
     * @return static
     */
    public function track($status = true)
    {
        $this->config['track'] = $status;

        return $this;
    }

    /**
     * Track a cache key by adding it to the group's meta list.
     *
     * @param string $key
     *
     * @return void
     */
    protected function trackKey($key)
    {
        // Get the meta key for this group
        $meta = $this->metaKey();

        // Retrieve the current list of tracked keys (or an empty array)
        $keys = $this->cache->get($meta, []);

        // If the key is not already tracked, add it to the list
        if (! in_array($key, $keys)) {
            $keys[] = $key;

            // Store the updated list permanently
            $this->cache->forever($meta, $keys);
        }
    }

    /**
     * Set the default time-to-live (TTL) for cached items.
     *
     * @param Closure|DateTimeInterface|DateInterval|int|TTL $seconds
     *
     * @return static
     */
    public function ttl($seconds)
    {
        $this->config['ttl'] = $seconds instanceof \IntBackedEnum
            ? $seconds->value
            : $seconds;

        return $this;
    }

    /**
     * Set the cache driver to use for this instance.
     *
     * @param string $driver
     *
     * @return static
     */
    public function via($driver)
    {
        $this->cache = app(CacheManager::class)->driver($driver);

        return $this;
    }
}
