<?php

namespace Larawise\Facades\Service;

use Illuminate\Support\Facades\Facade;
use Larawise\Support\Enums\TTL;

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
 *
 * @method static \Larawise\Service\CacheService group(string $group)
 * @method static \Larawise\Service\CacheService match(string $pattern)
 * @method static \Larawise\Service\CacheService only(array|string $keys)
 * @method static \Larawise\Service\CacheService except(array|string $keys)
 * @method static \Larawise\Service\CacheService exclude(string $pattern)
 * @method static \Larawise\Service\CacheService prefix(string $prefix)
 * @method static \Larawise\Service\CacheService tags(bool $status = true)
 * @method static \Larawise\Service\CacheService track(bool $status = true)
 * @method static \Larawise\Service\CacheService sliding(bool $status = true)
 * @method static \Larawise\Service\CacheService compress(bool $status = true)
 * @method static \Larawise\Service\CacheService encrypt(bool $status = true)
 * @method static \Larawise\Service\CacheService ttl(int $seconds)
 * @method static \Larawise\Service\CacheService via(string $driver)
 * @method static bool put(string|array $key, mixed $value = null, \Closure|\DateTimeInterface|\DateInterval|int|TTL|null $ttl = null)
 * @method static mixed get(string|array $key)
 * @method static bool forget(string|array $key)
 * @method static bool has(string|array $key)
 * @method static array hasMany(array $keys)
 * @method static bool hasAny(array $keys)
 * @method static void putMany(array $items, \Closure|\DateTimeInterface|\DateInterval|int|TTL|null $ttl = null)
 * @method static array getMany(array $keys)
 * @method static void forgetMany(array $keys)
 * @method static mixed remember(string $key, \Closure|\DateTimeInterface|\DateInterval|int|TTL|null $ttl, \Closure $callback)
 * @method static mixed rememberForever(string $key, \Closure $callback)
 * @method static bool flush()
 * @method static array inspect(bool $verbose = false)

 * @see \Larawise\Service\CacheService
 */
class CacheService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larawise.cache';
    }
}
