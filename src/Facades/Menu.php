<?php

namespace Larawise\Facades;

use Illuminate\Support\Facades\Facade;

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
 * @method static static make()
 * @method static static setGroupId(string $id)
 * @method static static for(string $id)
 * @method static static default()
 * @method static static withGroup(string $id, \Closure $callback)
 * @method static string getGroupId()
 * @method static static registerItem(\Illuminate\Contracts\Support\Arrayable|array $options)
 * @method static static removeItem(array|string $id)
 * @method static bool hasItem(string $id)
 * @method static \Illuminate\Support\Collection all(string|null $id = null)
 * @method static array|null getItemById(string $itemId)
 * @method static \Illuminate\Support\Collection|null getItemsByParentId(string $parentId)
 * @method static static beforeRetrieving(\Closure $callback)
 * @method static static afterRetrieved(\Closure $callback)
 * @method static void clearCachesForCurrentUser()
 * @method static void clearCaches()
 * @method static bool hasCache()
 * @method static \Larawise\Dashboard\Menu\Menu|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Larawise\Dashboard\Menu\Menu|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Larawise\Dashboard\Menu\Menu|\Illuminate\Support\HigherOrderTapProxy tap(callable|null $callback = null)
 *
 * @see \Larawise\Dashboard\Menu\Menu
 */
class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'menu';
    }
}
