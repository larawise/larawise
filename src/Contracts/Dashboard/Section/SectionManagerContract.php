<?php

namespace Larawise\Contracts\Dashboard\Section;

use Closure;
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
interface SectionManagerContract extends Htmlable, Renderable
{
    /**
     * Registers a callback to run after rendering.
     *
     * @param Closure|callable $callback
     * @param int $priority
     *
     * @return static
     */
    public function afterRendering($callback, $priority = 100);

    /**
     * Returns all sections across all registered groups.
     *
     * @return array<string, array<SectionContract>>
     */
    public function all();

    /**
     * Registers a callback to run before rendering.
     *
     * @param Closure|callable $callback
     * @param int $priority
     *
     * @return static
     */
    public function beforeRendering($callback, $priority = 100);

    /**
     * Resets the group context to the default ("settings").
     *
     * @return static
     */
    public function default();

    /**
     * Returns the current active group ID.
     *
     * @return string
     */
    public function group();

    /**
     * Returns the display name of the current group.
     *
     * @return string
     */
    public function groupName();

    /**
     * Checks whether the given item ID is marked as ignored.
     *
     * @param string $id
     *
     * @return bool
     */
    public function isIgnoredItem($id);

    /**
     * Marks single or multiple item IDs to be ignored.
     *
     * @param string|array<string> $items
     *
     * @return static
     */
    public function ignoreItem($items);

    /**
     * Returns all registered item closures for a section.
     *
     * @param string $section
     *
     * @return array<Closure>
     */
    public function items($section);

    /**
     * Moves sections from one group into another.
     *
     * @param string $from
     * @param string $to
     *
     * @return static
     */
    public function moveGroup($from, $to);

    /**
     * Registers one or more panel section definitions.
     *
     * @param array|string|Closure $sections
     *
     * @return static
     */
    public function register($sections);

    /**
     * Registers one or more item closures for a section.
     *
     * @param string $section
     * @param Closure $items
     *
     * @return static
     */
    public function registerItem($section, Closure $items);

    /**
     * Marks an item ID to be ignored during rendering.
     *
     * @param string $section
     * @param string $id
     *
     * @return static
     */
    public function removeItem($section, $id);

    /**
     * Returns the resolved and permission-checked sections for the current group.
     *
     * @return array<SectionContract>
     */
    public function sections();

    /**
     * Sets the current group ID and marks it as registered.
     *
     * @param string $group
     *
     * @return static
     */
    public function withGroup($group);

    /**
     * Assigns a display name to the current group.
     *
     * @param string $name
     *
     * @return static
     */
    public function withGroupName($name);
}
