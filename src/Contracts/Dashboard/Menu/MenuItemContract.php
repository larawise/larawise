<?php

namespace Larawise\Contracts\Dashboard\Menu;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

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
interface MenuItemContract extends Arrayable
{
    /**
     * Get the unique ID of the menu item.
     *
     * @return string
     */
    public function identifier();

    /**
     * Set the unique ID of the menu item.
     *
     * @param string $id
     *
     * @return static
     */
    public function withIdentifier($id);

    /**
     * Get the display priority of the menu item.
     *
     * @return int
     */
    public function priority();

    /**
     * Set the display priority of the menu item.
     *
     * @param int $priority
     *
     * @return static
     */
    public function withPriority($priority);

    /**
     * Get the parent ID for nested menu structures.
     *
     * @return string|null
     */
    public function parent();

    /**
     * Set the parent ID for nested menu structures.
     *
     * @param string|null $parent
     *
     * @return static
     */
    public function withParent($parent);

    /**
     * Get the translated name of the menu item.
     *
     * @return string
     */
    public function name();

    /**
     * Set the name of the menu item (automatically translated).
     *
     * @param string $name
     *
     * @return static
     */
    public function withName($name);

    /**
     * Get the translated description of the menu item.
     *
     * @return string
     */
    public function description();

    /**
     * Set the description of the menu item (automatically translated).
     *
     * @param string $description
     *
     * @return static
     */
    public function withDescription($description);

    /**
     * Get the icon associated with the menu item.
     *
     * @return string|null
     */
    public function icon();

    /**
     * Set the icon for the menu item.
     *
     * @param string|null $icon
     *
     * @return static
     */
    public function withIcon($icon);

    /**
     * Get the resolved URL of the menu item.
     *
     * @return string
     */
    public function url();

    /**
     * Set the URL or a dynamic URL resolver for the menu item.
     *
     * @param string|Closure $url
     *
     * @return static
     */
    public function withUrl($url);

    /**
     * Get the named route of the menu item.
     *
     * @return string
     */
    public function route();

    /**
     * Set the named route for the menu item.
     *
     * @param string $route
     *
     * @return static
     */
    public function withRoute($route);

    /**
     * Get the permissions required to view this menu item.
     *
     * @return array|bool
     */
    public function permissions();

    /**
     * Add a single permission to the menu item.
     *
     * @param string $permission
     *
     * @return static
     */
    public function withPermission($permission);

    /**
     * Set multiple permissions or disable permission checks.
     *
     * @param string|array|bool $permissions
     *
     * @return static
     */
    public function withPermissions($permissions);
}
