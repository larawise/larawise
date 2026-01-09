<?php

namespace Larawise\Contracts\Dashboard\Section;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

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
interface SectionItemContract extends Htmlable, Renderable, Arrayable
{
    /**
     * Checks whether the current user has permission to view this item.
     *
     * @return bool
     */
    public function checkPermissions();

    /**
     * Returns the item description.
     *
     * @return string
     */
    public function description();

    /**
     * Returns the icon for this item.
     *
     * @return string
     */
    public function icon();

    /**
     * Returns the unique identifier for this item.
     *
     * @return string
     */
    public function identifier();

    /**
     * Checks whether this item has any permission constraints.
     *
     * @return bool
     */
    public function hasPermissions();

    /**
     * Creates a new section item instance with the given ID.
     *
     * @param string $id
     *
     * @return static
     */
    public static function make($id);

    /**
     * Returns the list of permissions required to view this item.
     *
     * @return array<string>
     */
    public function permissions();

    /**
     * Returns the priority value for this item.
     *
     * @return int
     */
    public function priority();

    /**
     * Renders the section item using its assigned view.
     *
     * @return string
     */
    public function render();

    /**
     * Returns the section ID this item belongs to.
     *
     * @return string
     */
    public function section();

    /**
     * Returns the HTML target attribute value for this item.
     *
     * @return string
     */
    public function target();

    /**
     * Returns the display title of the item.
     *
     * @return string
     */
    public function title();

    /**
     * Returns the item's target URL.
     *
     * @return string
     */
    public function url();

    /**
     * Returns the view path used to render this item.
     *
     * @return string
     */
    public function view();

    /**
     * Removes all permission constraints from this item.
     *
     * @return static
     */
    public function withoutPermission();

    /**
     * Sets the description text for this item.
     *
     * @param string $description
     *
     * @return static
     */
    public function withDescription($description);

    /**
     * Sets the icon class for this item.
     *
     * @param string $icon
     *
     * @return static
     */
    public function withIcon($icon);

    /**
     * Sets the unique identifier for this item.
     *
     * @param string $id
     *
     * @return static
     */
    public function withIdentifier($id);

    /**
     * Adds a single permission requirement to this item.
     *
     * @param string $permission
     *
     * @return static
     */
    public function withPermission($permission);

    /**
     * Sets multiple permission requirements for this item.
     *
     * @param array<string> $permissions
     *
     * @return static
     */
    public function withPermissions($permissions);

    /**
     * Sets the priority value for this item.
     *
     * @param int $priority
     *
     * @return static
     */
    public function withPriority($priority);

    /**
     * Sets the item's URL using a named route.
     *
     * @param string $route
     * @param array $parameters
     * @param bool $absolute
     *
     * @return static
     */
    public function withRoute($route, $parameters = [], $absolute = true);

    /**
     * Sets the section ID this item belongs to.
     *
     * @param string $section
     *
     * @return static
     */
    public function withSection($section);

    /**
     * Configures whether the item's URL should open in a new browser tab.
     *
     * @param string $target
     *
     * @return static
     */
    public function withTarget($target = "_self");

    /**
     * Sets the display title for this item.
     *
     * @param string $title
     *
     * @return static
     */
    public function withTitle($title);

    /**
     * Sets the target URL for this item.
     *
     * @param string $url
     *
     * @return static
     */
    public function withUrl($url);

    /**
     * Sets the Blade view used to render this item.
     *
     * @param string $view
     *
     * @return static
     */
    public function withView($view);
}
