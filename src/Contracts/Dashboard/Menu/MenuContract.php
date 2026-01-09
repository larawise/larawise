<?php

namespace Larawise\Contracts\Dashboard\Menu;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

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
interface MenuContract
{
    /**
     * Create a new menu instance.
     *
     * @return $this
     */
    public function make();

    /**
     * Sets the current menu group context.
     *
     * @param string $id
     *
     * @return $this
     */
    public function for($id);

    /**
     * Resets the group context to the default group.
     *
     * @return $this
     */
    public function default();

    /**
     * Temporarily sets the group context, executes a callback, and resets to default.
     *
     * @param string $id
     * @param Closure $callback
     *
     * @return $this
     */
    public function group($id, Closure $callback);

    /**
     * Sets the internal group ID directly.
     *
     * @param string $id
     *
     * @return $this
     */
    public function withGroupId($id);

    /**
     * Retrieves the current group ID.
     *
     * @return string
     */
    public function groupId();

    /**
     * Registers a new menu item for the current group.
     *
     * @param Arrayable|array $options
     *
     * @return $this
     */
    public function registerItem($options);

    /**
     * Marks one or more menu items as removed for the current group.
     *
     * @param string|array $id
     *
     * @return $this
     */
    public function removeItem($id);

    /**
     * Checks whether a menu item exists in the current group.
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasItem($id);

    /**
     * Retrieves the full dashboard menu tree for the given group.
     *
     * @param string|null $id
     *
     * @return Collection
     */
    public function all($id = null);

    /**
     * Retrieves a single menu item by its ID within the current group.
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getItemById($id);

    /**
     * Retrieves all menu items that belong to a given parent ID within the current group.
     *
     * @param string $id
     *
     * @return Collection|null
     */
    public function getItemsByParentId($id);
}
