<?php

namespace Larawise\Dashboard\Section;

use Larawise\Contracts\Dashboard\Section\SectionContract;
use Larawise\Contracts\Dashboard\Section\SectionManagerContract;
use Larawise\Events\Dashboard\Section\SectionsRendered;
use Larawise\Events\Dashboard\Section\SectionsRendering;
use Closure;
use Illuminate\Support\Arr;

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
class SectionManager implements SectionManagerContract
{
    /**
     * Create a new section manager instance.
     *
     * @param string $group
     * @param array $groups
     * @param array $names
     * @param array $sections
     * @param array $items
     * @param array $ignored
     * @param array $moved
     *
     * @return void
     */
    public function __construct(
        protected $group = 'settings',
        protected $groups = [],
        protected $names = [],
        protected $sections = [],
        protected $items = [],
        protected $ignored = [],
        protected $moved = []
    ) {
        $this->default();
    }

    /**
     * Registers a callback to run after rendering.
     *
     * @param Closure|callable $callback
     * @param int $priority
     *
     * @return static
     */
    public function afterRendering($callback, $priority = 100)
    {
        push_action($this->filterPrefix() . '_rendered', $callback, $priority);

        $this->default();

        return $this;
    }

    /**
     * Returns all sections across all registered groups.
     *
     * @return array<string, array<SectionContract>>
     */
    public function all()
    {
        $groups = array_keys($this->groups);
        $sections = [];

        foreach ($groups as $group) {
            $this->withGroup($group);
            $this->dispatchBeforeRendering();

            $currentSections = $sections[$group] ?? [];
            $sections[$group] = [...$currentSections, ...$this->sections()];

            $this->dispatchAfterRendering();
        }

        $this->default();

        return $sections;
    }

    /**
     * Registers a callback to run before rendering.
     *
     * @param Closure|callable $callback
     * @param int $priority
     *
     * @return static
     */
    public function beforeRendering($callback, $priority = 100)
    {
        push_action($this->filterPrefix() . '_rendering', $callback, $priority);

        $this->default();

        return $this;
    }

    /**
     * Resets the group context to the default ("settings").
     *
     * @return static
     */
    public function default()
    {
        return $this->withGroup('settings');
    }

    /**
     * Dispatches all pre-rendering events and hooks for the current group.
     *
     * @return void
     */
    protected function dispatchBeforeRendering()
    {
        // Global hook for all section rendering
        do_action('srylius_sections_rendering', $this);

        // Group-specific hook (e.g. "srylius_sections_settings_rendering")
        do_action($this->filterPrefix() . '_rendering', $this);

        // Laravel event for listeners
        SectionsRendering::dispatch($this);
    }

    /**
     * Dispatches all post-rendering events and hooks for the current group.
     *
     * @return void
     */
    protected function dispatchAfterRendering()
    {
        // Global hook for all section rendering
        do_action('srylius_sections_rendered', $this);

        // Group-specific hook (e.g. "srylius_sections_settings_rendered")
        do_action($this->filterPrefix() . '_rendered', $this);

        // Laravel event for listeners
        SectionsRendered::dispatch($this);
    }

    /**
     * Returns the filter prefix for the current group.
     *
     * @return string
     */
    protected function filterPrefix()
    {
        return 'srylius_sections_' . $this->group;
    }

    /**
     * Returns the current active group ID.
     *
     * @return string
     */
    public function group()
    {
        return $this->group;
    }

    /**
     * Returns the display name of the current group.
     *
     * @return string
     */
    public function groupName()
    {
        return $this->names[$this->group] ?? '';
    }

    /**
     * Checks whether the given item ID is marked as ignored.
     *
     * @param string $id
     *
     * @return bool
     */
    public function isIgnoredItem($id)
    {
        return in_array($id, $this->ignored[$this->group] ?? []);
    }

    /**
     * Marks single or multiple item IDs to be ignored.
     *
     * @param string|array<string> $items
     *
     * @return static
     */
    public function ignoreItem($items)
    {
        $items = is_array($items) ? $items : [$items];

        $this->ignored[$this->group] = array_merge(
            $this->ignored[$this->group] ?? [],
            $items
        );

        return $this;
    }

    /**
     * Returns all registered item closures for a section.
     *
     * @param string $section
     *
     * @return array<Closure>
     */
    public function items($section)
    {
        return $this->items[$this->group][$section] ?? [];
    }

    /**
     * Moves sections from one group into another.
     *
     * @param string $from
     * @param string $to
     *
     * @return static
     */
    public function moveGroup($from, $to)
    {
        $this->moved[$to][] = $from;

        return $this;
    }

    /**
     * Registers one or more panel section definitions.
     *
     * @param array|string|Closure $sections
     *
     * @return static
     */
    public function register($sections)
    {
        foreach (Arr::wrap($sections) as $section) {
            $this->sections[$this->group][] = $section;
        }

        return $this;
    }

    /**
     * Registers one or more item closures for a section.
     *
     * @param string $section
     * @param Closure $items
     *
     * @return static
     */
    public function registerItem($section, Closure $items)
    {
        $this->items[$this->group][$section][] = $items;

        return $this;
    }

    /**
     * Marks an item ID to be ignored during rendering.
     *
     * @param string $section
     * @param string $id
     *
     * @return static
     */
    public function removeItem($section, $id)
    {
        if (isset($this->items[$this->group][$section])) {
            $this->ignoreItem($id);
        }

        return $this;
    }

    /**
     * Renders all sections for the current group.
     *
     * @return string
     */
    public function render()
    {
        $this->dispatchBeforeRendering();

        $sections = apply_filters('srylius_sections', $this->sections(), $this->group, $this);

        $content = '';

        foreach ($sections as $section) {
            $content .= $section->render();
        }

        $content = apply_filters('srylius_sections_content', $content, $this->group, $sections, $this);

        $this->dispatchAfterRendering();

        if (! empty($this->moved[$this->group])) {
            $movedGroups = array_unique($this->moved[$this->group]);

            foreach ($movedGroups as $group) {
                $content .= $this->withGroup($group)->render();
            }
        }

        return $content;
    }

    /**
     * Returns the resolved and permission-checked sections for the current group.
     *
     * @return array<SectionContract>
     */
    public function sections()
    {
        $sections = $this->sections[$this->group] ?? [];

        return collect($sections)
            ->map(fn (string|Closure $section) => is_string($section) ? app($section) : value($section))
            ->filter(fn (object $section) => $section instanceof SectionContract)
            ->filter(fn (SectionContract $section) => $section->checkPermissions())
            ->sortBy(fn (SectionContract $section) => $section->priority())
            ->unique(fn (SectionContract $section) => $section->identifier())
            ->map(function (SectionContract $section) {
                return $section
                    ->withGroup($this->group)
                    ->addItems($this->items($section::class));
            })
            ->each(fn (SectionContract $section) => $section->afterSetup())
            ->tap(fn () => $this->default())
            ->all();
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render();
    }

    /**
     * Sets the current group ID and marks it as registered.
     *
     * @param string $group
     *
     * @return static
     */
    public function withGroup($group)
    {
        $this->group = $group;
        $this->groups[$group] = true;

        return $this;
    }

    /**
     * Assigns a display name to the current group.
     *
     * @param string $name
     *
     * @return static
     */
    public function withGroupName($name)
    {
        $this->names[$this->group] = $name;

        return $this;
    }
}
