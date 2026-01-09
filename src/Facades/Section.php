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
 * @method static static withGroup(string $groupId)
 * @method static string group()
 * @method static static withGroupName(string $name)
 * @method static string groupName()
 * @method static static default()
 * @method static static moveGroup(string $from, string $to)
 * @method static static register(array|string|\Closure $sections)
 * @method static static registerItem(string $section, \Closure $items)
 * @method static array all()
 * @method static array sections()
 * @method static array items(string $section)
 * @method static static removeItem(string $section, string $id)
 * @method static static ignoreItem(string|array $id)
 * @method static bool isIgnoredItem(string $id)
 * @method static static beforeRendering(\Closure|callable $callback, int $priority = 100)
 * @method static static afterRendering(\Closure|callable $callback, int $priority = 100)
 * @method static string render()
 * @method static string toHtml()
 * @method static array toArray()
 *
 * @see \Larawise\Dashboard\Section\SectionManager
 */
class Section extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'section';
    }
}
