<?php

namespace Larawise\Dashboard\Menu;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Larawise\Support\Traits\Retrievable;
use RuntimeException;
use Larawise\Authify\Contracts\HasPermissions;
use Larawise\Contracts\Dashboard\Menu\MenuContract;
use Larawise\Events\Dashboard\Menu\MenuRetrieved;
use Larawise\Events\Dashboard\Menu\MenuRetrieving;
use Larawise\Facades\Srylius;
use Larawise\Service\CacheService;

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
class Menu implements MenuContract
{
    // Concerns
    use Retrievable, Conditionable, Tappable;

    /**
     * Stores all registered menu items, grouped by ID.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Tracks removed menu items, useful for diffing or audit purposes.
     *
     * @var array
     */
    protected $removed = [];

    /**
     * The active group identifier used for scoping menu operations.
     *
     * @var string
     */
    protected $group = 'srylius';

    /**
     * Indicates whether caching is enabled for the menu.
     *
     * @var bool
     */
    protected $caching;

    /**
     * Create a new menu instance.
     *
     * @param Application $app
     * @param Request $request
     * @param CacheService $cache
     *
     * @return void
     */
    public function __construct(
        protected Application $app,
        protected Request $request,
        protected CacheService $cache
    ) {
        $this->caching = (bool) setting('cache_admin_menu_enable', false);
    }

    /**
     * Create a new menu instance.
     *
     * @return $this
     */
    public function make()
    {
        return $this;
    }

    /**
     * Sets the current menu group context.
     *
     * @param string $id
     *
     * @return $this
     */
    public function for($id)
    {
        return $this->withGroupId($id);
    }

    /**
     * Resets the group context to the default group.
     *
     * @return $this
     */
    public function default()
    {
        return $this->for('srylius');
    }

    /**
     * Temporarily sets the group context, executes a callback, and resets to default.
     *
     * @param string $id
     * @param Closure $callback
     *
     * @return $this
     */
    public function group($id, Closure $callback)
    {
        // Set group context
        $this->for($id);

        // Execute logic within that context
        $callback($this);

        // Reset to default group
        $this->default();

        return $this;
    }

    /**
     * Sets the internal group ID directly.
     *
     * @param string $id
     *
     * @return $this
     */
    public function withGroupId($id)
    {
        $this->group = $id;

        return $this;
    }

    /**
     * Retrieves the current group ID.
     *
     * @return string
     */
    public function groupId()
    {
        return $this->group;
    }

    /**
     * Registers a new menu item for the current group.
     *
     * @param Arrayable|array $options
     *
     * @return $this
     */
    public function registerItem($options)
    {
        // Skip registration if cached output already exists
        if ($this->hasCache()) {
            return $this;
        }

        // Convert Arrayable input to array
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        // Remove children from input — they are handled separately
        if (isset($options['children'])) {
            unset($options['children']);
        }

        // Default values for all menu fields
        $defaultOptions = [
            'id' => '',
            'priority' => 99,
            'parent' => null,
            'name' => '',
            'description' => '',
            'icon' => null,
            'url' => '',
            'route' => '',
            'permissions' => [],
            'children' => [],
            'active' => false,
        ];

        // Merge defaults with provided options
        $options = [...$defaultOptions, ...$options];

        // If no URL is provided but a route is defined, resolve it dynamically
        if (! $options['url'] && $options['route']) {
            $options['url'] = fn () => route($options['route']);

            // If permissions are not set (and not explicitly disabled), infer from route
            if (! $options['permissions'] && $options['permissions'] !== false) {
                $options['permissions'] = [$options['route']];
            }
        }

        $id = $options['id'];

        // Throw if ID is missing in local context (e.g. during development)
        throw_if(! $id && $this->isLocal(), new RuntimeException(sprintf('Menu id not specified on class %s', $this->getPreviousCalledClass())));

        // Throw if duplicate ID exists with empty name in local context
        throw_if(isset($this->items[$this->group][$id]) && empty($this->items[$this->group][$id]['name']) && $this->isLocal(), new RuntimeException(sprintf('Menu id already exists: %s on class %s', $id, $this->getPreviousCalledClass())));

        // Register the item under the current group
        $this->items[$this->group][$id] = $options;

        return $this;
    }

    /**
     * Marks one or more menu items as removed for the current group.
     *
     * @param string|array $id
     *
     * @return $this
     */
    public function removeItem($id)
    {
        // If an array of IDs is passed, recursively remove each one
        if (is_array($id)) {
            foreach ($id as $item) {
                $this->removeItem($item);
            }

            return $this;
        }

        // If multiple arguments are passed (e.g. removeItem('a', 'b', 'c')),
        // treat them as an array and recurse
        if (($args = func_get_args()) && count($args) > 1) {
            return $this->removeItem($args);
        }

        // Add the single ID to the list of removed items for the current group
        $this->removed[$this->group][] = $id;

        return $this;
    }

    /**
     * Checks whether a menu item exists in the current group.
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasItem($id)
    {
        // Check if the item ID exists in the links array for the current group
        return isset($this->items[$this->group][$id]);
    }

    /**
     * Retrieves the full dashboard menu tree for the given group.
     *
     * @param string|null $id
     *
     * @return Collection
     */
    public function all($id = null)
    {
        // If a group ID is provided, override the current group context
        if ($id !== null) {
            $this->withGroupId($id);
        }

        // Dispatch Laravel event before menu retrieval begins
        MenuRetrieving::dispatch($this);

        // Trigger WordPress-style action hook for external listeners
        do_action('render_dashboard_menu', $this, $id);

        // Define the menu-building logic as a closure (used for caching or direct execution)
        $value = function () {
            // Dispatch internal pre-retrieval hook
            $this->dispatchBeforeRetrieving();

            // Build the menu tree for the current group
            $items = $this->getItemsByGroup();

            // Apply external filters and dispatch post-retrieval hook
            return tap(apply_filters('dashboard_menu', $items, $this), function ($menu): void {
                $this->dispatchAfterRetrieved($menu);
            });
        };

        // Use cache if enabled, otherwise execute the closure directly
        if ($this->caching) {
            $items = $this->cache->remember($this->cacheKey(), Carbon::now()->addHours(3), $value);
        } else {
            $items = value($value);
        }

        // Apply active state detection and dispatch final events
        return tap($this->applyActive($items), function (Collection $items): void {
            MenuRetrieved::dispatch($this, $items);

            do_action('rendered_dashboard_menu', $this, $items);

            $this->default(); // Finalize default state if needed
        });
    }

    /**
     * Retrieves a single menu item by its ID within the current group.
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getItemById($id)
    {
        // If the item doesn't exist in the current group, return null
        if (! $this->hasItem($id)) {
            return null;
        }

        // Return the item and reset group context to default
        return tap($this->items[$this->group][$id], fn () => $this->default());
    }

    /**
     * Retrieves all menu items that belong to a given parent ID within the current group.
     *
     * @param string $id
     *
     * @return Collection|null
     */
    public function getItemsByParentId($id)
    {
        return collect($this->items[$this->group] ?? [])
            // Filter items whose parent_id matches the given ID
            ->filter(fn ($item) => $item['parent'] === $id)
            // Reset group context to default after filtering
            ->tap(fn () => $this->default());
    }





    public function clearCachesForCurrentUser(): void
    {
        $this->cache->forget($this->cacheKey());

        $this->default();
    }

    public function clearCaches(): void
    {
        $this->cache->flush();
    }

    protected function parseUrl(string|callable|Closure|null $link): string
    {
        if (empty($link)) {
            return '';
        }

        if (is_string($link)) {
            return $link;
        }

        return call_user_func($link);
    }

    protected function isLocal(): bool
    {
        return ! $this->app->runningInConsole() && $this->app->isLocal();
    }

    protected function getPreviousCalledClass(): string
    {
        return isset(debug_backtrace()[1])
            ? debug_backtrace()[1]['class'] . '@' . debug_backtrace()[1]['function']
            : '[undefined]';
    }

    protected function cacheKey(): string
    {
        $userType = 'undefined';
        $userKey = 'guest';
        $locale = $this->app->getLocale();

        if ($user = $this->request->user()) {
            $userType = $user::class;
            $userKey = $user->getKey();
        }

        return sprintf('dashboard_menu:%s:%s:%s:%s', $this->group, $userType, $userKey, $locale);
    }

    public function hasCache(): bool
    {
        if (! $this->caching) {
            return false;
        }

        return $this->cache->has($this->cacheKey());
    }

    /**
     * Builds the final nested menu tree for the current group.
     *
     * @return Collection
     */
    protected function getItemsByGroup()
    {
        // Get all menu items grouped by parent_id
        $groupedItems = $this->getGroupedItemsByGroup();

        // Start mapping from root-level items (parent === null → grouped under key '')
        return $this->getMappedItems($groupedItems[''] ?? collect(), $groupedItems);
    }

    /**
     * Builds a grouped collection of menu items for the current group.
     *
     * @return Collection
     */
    protected function getGroupedItemsByGroup()
    {
        // Get the list of removed item IDs for the current group
        $removed = $this->removed[$this->group] ?? [];

        // Collect all menu links for the current group
        $items = collect($this->items[$this->group] ?? [])->values()
            // Reject items that are explicitly removed or whose parent is removed
            ->reject(fn ($link) => isset($link['id']) && (in_array($link['id'], $removed) || in_array($link['parent_id'], $removed)))
            // Filter out items the current user doesn't have permission to see
            ->filter(function ($link) {
                $user = $this->request->user();

                if (! empty($link['permissions']) && $user instanceof HasPermissions && !$user->hasAnyPermission($link['permissions'])) {
                    return false;
                }

                return true;
            });

        // Get all valid item IDs for reference
        $existsIds = $items->pluck('id')->all();

        // Normalize each item and fix parent references
        return $items->mapWithKeys(function ($item) use ($existsIds) {
                // Parse and normalize the item's URL
                $item['url'] = $this->parseUrl($item['url'] ?? null);

                // Resolve dynamic name if it's a Closure
                $item['name'] = $item['name'] instanceof Closure
                    ? call_user_func($item['name'])
                    : $item['name'];

                // Fix parent references:
                // - Remove parent_id if it points to a non-existent item
                if (! empty($item['parent'])) {
                    if (! in_array($item['parent'], $existsIds)) {
                        $item['parent'] = null;
                    }
                }

                // Return the item keyed by its ID
                return [$item['id'] => $item];
            })
            // Sort items by priority (lower = higher priority)
            ->sortBy('priority')
            // Group items by parent id to prepare for tree mapping
            ->groupBy('parent');
    }

    /**
     * Recursively maps a flat list of menu items into a nested tree structure.
     *
     * @param Collection $items
     * @param Collection $groupedItems
     *
     * @return Collection
     */
    protected function getMappedItems($items, $groupedItems)
    {
        // Remove items that are not valid links and have no children
        return $items->reject(function ($item) use ($groupedItems) {
                return (empty($item['url']) || $item['url'] === '#' || Str::startsWith($item['url'], 'javascript:void(0)')) && ! $groupedItems->get($item['id']);
            })
            // Map each item into a keyed structure with its children attached
            ->mapWithKeys(function ($item) use ($groupedItems) {
                $groupedItem = $groupedItems->get($item['id']);

                // If this item has children, recursively attach them
                if ($groupedItem instanceof Collection && $groupedItem->isNotEmpty()) {
                    $item['children'] = $this->getMappedItems($groupedItem, $groupedItems);
                } else {
                    // Otherwise, set children to an empty collection
                    $item['children'] = collect();
                }

                // Return the item keyed by its ID
                return [$item['id'] => $item];
            });
    }

    /**
     * Applies active state detection to a menu collection.
     *
     * @param Collection $menu
     *
     * @return Collection
     */
    protected function applyActive($menu)
    {
        foreach ($menu as $key => $item) {
            // Recursively apply active detection to this item and its children
            $menu[$key] = $this->applyActiveRecursive($item);

            // If this item is active, stop further iteration (only one active branch is needed)
            if ($menu[$key]['active']) {
                break;
            }
        }

        // Return the updated menu collection
        return $menu;
    }

    /**
     * Recursively marks a menu item and its children as "active" based on the current URL.
     *
     * @param array $item
     *
     * @return array
     */
    protected function applyActiveRecursive($item)
    {
        // Get the full current request URL (e.g. https://example.com/admin/posts)
        $current = $this->request->fullUrl();

        // Get the item's target URL
        $url = $item['url'];

        // Determine if this item is active:
        // - Exact match with current URL
        // - OR partial match, excluding the admin root URL
        $item['active'] = $current === $url || (Str::contains($current, $url) && $url !== url(Srylius::prefix()));

        // If the item has no children, return it as-is
        if ($item['children']->isEmpty()) {
            return $item;
        }

        // Convert children collection to array for iteration
        $children = $item['children']->toArray();

        // Recursively apply active logic to each child
        foreach ($children as &$child) {
            $child = $this->applyActiveRecursive($child);

            // If any child is active, mark the parent as active too
            if ($child['active']) {
                $item['active'] = true;
                break;
            }
        }

        // Re-wrap children as a collection
        $item['children'] = collect($children);

        return $item;
    }
}
