<?php

namespace Larawise\Dashboard\Section;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Larawise\Facades\Section as SectionFacade;
use Larawise\Authify\Contracts\HasPermissions;
use Larawise\Contracts\Dashboard\Section\SectionContract;
use Larawise\Contracts\Dashboard\Section\SectionItemContract;
use Larawise\Events\Dashboard\Section\Item\SectionItemsRendered;
use Larawise\Events\Dashboard\Section\Item\SectionItemsRendering;
use Larawise\Events\Dashboard\Section\SectionRendered;
use Larawise\Events\Dashboard\Section\SectionRendering;

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
class Section implements SectionContract
{
    /**
     * Whether to show empty state view when no items exist.
     *
     * @var bool
     */
    protected $emptyState = false;

    /**
     * Blade view used when section has no items.
     *
     * @var string
     */
    protected $emptyStateView = 'srylius::sections.empty';

    /**
     * Group ID this section belongs to.
     *
     * @var string
     */
    protected $group;

    /**
     * Unique identifier for this section.
     *
     * @var string
     */
    protected $id;

    /**
     * List of registered items (raw or closures).
     *
     * @var array
     */
    protected $items = [];

    /**
     * Optional partials injected into the section.
     *
     * @var array
     */
    protected $partials = [];

    /**
     * List of permissions required to view this section.
     *
     * @var array|null
     */
    protected $permissions = null;

    /**
     * Sorting priority for this section.
     *
     * @var int
     */
    protected $priority = 0;

    /**
     * Optional description text for the section.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Display title of the section.
     *
     * @var string
     */
    protected $title;

    /**
     * View used to render the section.
     *
     * @var string
     */
    protected $view = 'srylius::sections.section';

    /**
     * Create a new section instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = uniqid('srylius-section-');

        $this->setup();
    }

    /**
     * Create a new section instance.
     *
     * @param string $id
     *
     * @return Section
     */
    public static function make($id)
    {
        return app(static::class)->withIdentifier($id);
    }

    /**
     * Hook called after setup, for subclasses to override.
     *
     * @return void
     */
    public function afterSetup()
    {
        // ...
    }

    public function setup()
    {
        // ...
    }

    /**
     * Checks whether the current user has permission to view this section.
     *
     * @return bool
     */
    public function checkPermissions(): bool
    {
        if (! $this->hasPermissions()) {
            return true;
        }

        $user = Auth::guard()->user();

        if (! $user || ($user instanceof HasPermissions && ! $user->hasAnyPermission($this->permissions))) {
            return false;
        }

        return true;
    }

    /**
     * Sets the group identifier for this section.
     *
     * @param string $group
     *
     * @return static
     */
    public function withGroup($group)
    {
        // Assign the group ID for this section
        $this->group = $group;

        return $this;
    }

    /**
     * Returns the group identifier for this section.
     *
     * @return string
     */
    public function group()
    {
        return $this->group;
    }

    /**
     * Sets the unique identifier for this section.
     *
     * @param string $id
     *
     * @return static
     */
    public function withIdentifier($id)
    {
        // Assign the section's unique identifier
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the unique identifier for this section.
     *
     * @return string
     */
    public function identifier()
    {
        return $this->id;
    }

    /**
     * Sets the display title for this section.
     *
     * @param string $title
     *
     * @return static
     */
    public function withTitle($title)
    {
        // Assign the section title for display
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the display title of the section.
     *
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * Sets the description text for this section.
     *
     * @param string $description
     *
     * @return static
     */
    public function withDescription($description)
    {
        // Assign the description for display in the panel
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the description text for this section.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Sets the sorting priority of the section.
     *
     * @param int $priority
     *
     * @return static
     */
    public function withPriority($priority)
    {
        // Assign priority value for layout sorting
        $this->priority = $priority;

        return $this;
    }

    /**
     * Returns the sorting priority of the section.
     *
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * Sets the permission list required to view this section.
     *
     * @param array $permissions
     *
     * @return static
     */
    public function withPermissions($permissions)
    {
        // Assign required permissions for this section
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Removes all permission constraints from the section.
     *
     * @return static
     */
    public function withoutPermission()
    {
        // Clear permission requirements
        $this->permissions = null;

        return $this;
    }

    /**
     * Returns the list of required permissions for this section.
     *
     * @return array<string>
     */
    public function permissions()
    {
        return $this->permissions ?? [];
    }

    /**
     * Checks whether this section has any permission constraints.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return $this->permissions !== null;
    }

    /**
     * Returns the Levd view used to render this section.
     *
     * @return string
     */
    public function view()
    {
        return $this->view;
    }

    /**
     * Sets the Levd view used to render this section.
     *
     * @param string $view
     *
     * @return static
     */
    public function withView($view)
    {
        // Override the default section view
        $this->view = $view;

        return $this;
    }

    /**
     * Returns the Levd view used when the section has no items.
     *
     * @return string
     */
    public function emptyStateView()
    {
        return $this->emptyStateView;
    }

    /**
     * Enables the empty state view and optionally sets a custom view.
     *
     * @param string|null $view
     *
     * @return static
     */
    public function withEmptyStateView($view = null)
    {
        // Enable empty state rendering
        $this->emptyState = true;

        // Optionally override the default view
        if ($view) {
            $this->emptyStateView = $view;
        }

        return $this;
    }

    /**
     * Disables the empty state view.
     *
     * @return static
     */
    public function withoutEmptyStateView()
    {
        // Disable empty state rendering
        $this->emptyState = false;

        return $this;
    }

    /**
     * Adds one or more items to the section, resolving closures if needed.
     *
     * @param array|Closure $items
     *
     * @return static
     */
    public function addItems($items)
    {
        foreach ($items as $item) {
            // If the item is a closure, resolve it first
            if ($item instanceof Closure) {
                // Execute the closure
                $itemsClosure = $item();
                // Ensure it's an array
                $itemsClosure = Arr::wrap($itemsClosure);

                // Add each resolved item from the closure
                foreach ($itemsClosure as $itemClosure) {
                    $this->items[] = $itemClosure;
                }

                continue;
            }

            // Add direct item (instance or class name)
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * Sets the raw item list for this section.
     *
     * @param array $items
     *
     * @return static
     */
    public function withItems($items)
    {
        // Store raw item definitions (strings, closures, or instances)
        $this->items = $items;

        return $this;
    }

    /**
     * Resolves and returns all valid, permission-checked items for this section.
     *
     * @return array<SectionItemContract>
     */
    public function items()
    {
        return collect($this->items)
            // Resolve string class names via container
            ->map(fn ($item) => is_string($item) ? app($item) : $item)
            // Keep only valid item instances
            ->filter(fn ($item) => $item instanceof SectionItemContract)
            // Apply permission filtering
            ->filter(fn (SectionItemContract $item) => $item->checkPermissions())
            // Sort by priority
            ->sortBy(fn (SectionItemContract $item) => $item->priority())
            // Ensure uniqueness by identifier after injecting section ID
            ->unique(fn (SectionItemContract $item) => $item->withSection($this->identifier())->identifier())
            // Return as array
            ->all();
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        // Abort rendering if user lacks permission
        if (! $this->checkPermissions()) {
            return '';
        }

        // Dispatch global and group-specific pre-rendering hooks
        do_action('srylius_section_rendering', $this);
        SectionRendering::dispatch($this);

        // Prepare view data
        $data = $this->toArray();

        // Inject empty state view if no items and emptyState is enabled
        if ($data['children']->isEmpty() && $this->emptyState) {
            $data['children'] = new HtmlString(
                $this->renderEmptyState()
            );
        }

        // Render the section view if children exist
        $content = $data['children']->isNotEmpty() ? view($this->view, $data, isset($this->mergedDataCallback) ? app()->call($this->mergedDataCallback) : [])->render() : '';

        // Allow external filters to modify the final HTML
        $content = apply_filters('srylius_section_content', $content, $this);

        // Dispatch post-rendering events and return final content
        return tap($content, function (string $content): void {
            SectionRendered::dispatch($this, $content);
            do_action('srylius_section_rendered', $this, $content);
        });
    }

    /**
     * Renders all items in the section into HTML.
     *
     * @return string
     */
    public function renderItems()
    {
        // Allow external filters to modify the item list before rendering
        $items = apply_filters('srylius_section_items', $this->items(), $this);

        $content = '';

        // Dispatch pre-rendering hooks and events
        do_action('srylius_section_items_rendering', $this, $items);
        SectionItemsRendering::dispatch($this, $items);

        // Remove items that are marked as ignored for this group
        $items = collect($items)->reject(
            fn (SectionItemContract $item) => SectionFacade::withGroup($this->group)->isIgnoredItem($item->identifier())
        )->all();

        // Render each item and concatenate output
        foreach ($items as $item) {
            $content .= $item->render();
        }

        // Apply post-render filters to the final content
        $content = apply_filters('srylius_section_items_content', $content, $this);

        // Dispatch post-rendering events and return final HTML
        return tap($content, function (string $content) use ($items): void {
            SectionItemsRendered::dispatch($this, $items, $content);
            do_action('srylius_section_items_rendered', $this, $items, $content);
        });
    }

    /**
     * Renders the empty state view.
     *
     * @return string
     */
    public function renderEmptyState()
    {
        return view($this->emptyStateView())->render();
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
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return [
            'id'            => $this->identifier(),
            'title'         => $this->title(),
            'description'   => $this->description(),
            'priority'      => $this->priority(),
            'group'         => $this->group(),
            'children'      => new HtmlString($this->renderItems()),
        ];
    }
}
