<?php

namespace Larawise\Dashboard\Menu;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Larawise\Contracts\Dashboard\Menu\MenuItemContract;

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
class MenuItem implements MenuItemContract
{
    // Concerns
    use Conditionable, Tappable;

    /**
     * The identifier of the menu item.
     *
     * @var string
     */
    protected $id;

    /**
     * The priority of the menu item.
     *
     * @var string
     */
    protected $priority = 99;

    /**
     * The parent id of the menu item.
     *
     * @var string
     */
    protected $parent = null;

    /**
     * The name of the menu item.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The description of the menu item.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The icon of the menu item.
     *
     * @var string
     */
    protected $icon = null;

    /**
     * The dynamic URL of the menu item.
     *
     * @var string
     */
    protected $url = '';

    /**
     * The route of the menu item.
     *
     * @var string
     */
    protected $route = '';

    /**
     * The permissions of the menu item.
     *
     * @var string|array|bool
     */
    protected $permissions = [];

    /**
     * Create a new menu item instance.
     *
     * @return static
     */
    public static function make()
    {
        return new self();
    }

    /**
     * Get the unique ID of the menu item.
     *
     * @return string
     */
    public function identifier()
    {
        return $this->id;
    }

    /**
     * Set the unique ID of the menu item.
     *
     * @param string $id
     *
     * @return static
     */
    public function withIdentifier($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the display priority of the menu item.
     *
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * Set the display priority of the menu item.
     *
     * @param int $priority
     *
     * @return static
     */
    public function withPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the parent ID for nested menu structures.
     *
     * @return string|null
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Set the parent ID for nested menu structures.
     *
     * @param string|null $parent
     *
     * @return static
     */
    public function withParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the translated name of the menu item.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Set the name of the menu item (automatically translated).
     *
     * @param string $name
     *
     * @return static
     */
    public function withName($name)
    {
        $this->name = __($name);

        return $this;
    }

    /**
     * Get the translated description of the menu item.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Set the description of the menu item (automatically translated).
     *
     * @param string $description
     *
     * @return static
     */
    public function withDescription($description)
    {
        $this->description = __($description);

        return $this;
    }

    /**
     * Get the icon associated with the menu item.
     *
     * @return string|null
     */
    public function icon()
    {
        return $this->icon;
    }

    /**
     * Set the icon for the menu item.
     *
     * @param string|null $icon
     *
     * @return static
     */
    public function withIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the resolved URL of the menu item.
     *
     * @return string
     */
    public function url()
    {
        return $this->url instanceof Closure ? call_user_func($this->url) : $this->url;
    }

    /**
     * Set the URL or a dynamic URL resolver for the menu item.
     *
     * @param string|Closure $url
     *
     * @return static
     */
    public function withUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the named route of the menu item.
     *
     * @return string
     */
    public function route()
    {
        return $this->route;
    }

    /**
     * Set the named route for the menu item.
     *
     * @param string $route
     *
     * @return static
     */
    public function withRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get the permissions required to view this menu item.
     *
     * @return array|bool
     */
    public function permissions()
    {
        return $this->permissions;
    }

    /**
     * Add a single permission to the menu item.
     *
     * @param string $permission
     *
     * @return static
     */
    public function withPermission($permission)
    {
        $this->permissions[] = $permission;

        return $this;
    }

    /**
     * Set multiple permissions or disable permission checks.
     *
     * @param string|array|bool $permissions
     *
     * @return static
     */
    public function withPermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return [
            'id'            => $this->identifier(),
            'priority'      => $this->priority(),
            'parent'        => $this->parent(),
            'name'          => $this->name(),
            'description'   => $this->description(),
            'icon'          => $this->icon(),
            'url'           => $this->url(),
            'route'         => $this->route(),
            'permissions'   => $this->permissions(),
        ];
    }
}
