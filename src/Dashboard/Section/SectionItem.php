<?php

namespace Larawise\Dashboard\Section;

use Larawise\Authify\Contracts\HasPermissions;
use Larawise\Contracts\Dashboard\Section\SectionItemContract;
use Larawise\Events\Dashboard\Section\Item\SectionItemRendered;
use Larawise\Events\Dashboard\Section\Item\SectionItemRendering;

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
class SectionItem implements SectionItemContract
{
    /**
     * Create a new section item instance.
     *
     * @param string $id
     * @param string $section
     * @param string $title
     * @param string $description
     * @param string $icon
     * @param int $priority
     * @param array|null $permissions
     * @param string $view
     * @param string $url
     * @param string $target
     *
     * @return void
     */
    public function __construct(
        protected $id,
        protected $section,
        protected $title,
        protected $description = '',
        protected $icon = 'ti ti-box',
        protected $priority = 0,
        protected $permissions = null,
        protected $view = 'srylius::sections.item',
        protected $url = '',
        protected $target = '_self',
    ) { }

    /**
     * Checks whether the current user has permission to view this item.
     *
     * @return bool
     */
    public function checkPermissions()
    {
        if (! $this->hasPermissions()) {
            return true;
        }

        $user = auth()->guard()->user();

        // Deny access if user is missing or lacks required permissions
        if (! $user || ($user instanceof HasPermissions && ! $user->hasAnyPermission($this->permissions))) {
            return false;
        }

        return true;
    }

    /**
     * Returns the item description.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Returns the icon for this item.
     *
     * @return string
     */
    public function icon()
    {
        return $this->icon;
    }

    /**
     * Returns the unique identifier for this item.
     *
     * @return string
     */
    public function identifier()
    {
        return $this->id;
    }

    /**
     * Checks whether this item has any permission constraints.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return $this->permissions !== null;
    }

    /**
     * Creates a new section item instance with the given ID.
     *
     * @param string $id
     *
     * @return static
     */
    public static function make($id)
    {
        return app(static::class)->withIdentifier($id);
    }

    /**
     * Returns the list of permissions required to view this item.
     *
     * @return array<string>
     */
    public function permissions()
    {
        return $this->permissions ?? [];
    }

    /**
     * Returns the priority value for this item.
     *
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * Renders the section item using its assigned view.
     *
     * @return string
     */
    public function render()
    {
        // Trigger pre-rendering hook for external listeners
        do_action('panel_section_item_rendering', $this);

        // Dispatch internal pre-rendering event
        SectionItemRendering::dispatch($this);

        // Render the item using its Blade view and context array
        $content = view(
            $this->view,
            $this->toArray()
        )->render();

        // Allow external filters to modify the rendered content
        $content = apply_filters('panel_section_item_content', $content, $this);

        // Dispatch internal post-rendering event with final content
        SectionItemRendered::dispatch($this, $content);

        // Trigger post-rendering hook for external listeners
        do_action('panel_sections_rendered', $this, $content);

        // Return the final HTML output
        return $content;
    }

    /**
     * Returns the section ID this item belongs to.
     *
     * @return string
     */
    public function section()
    {
        return $this->section;
    }

    /**
     * Returns the HTML target attribute value for this item.
     *
     * @return string
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * Returns the display title of the item.
     *
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * Returns a normalized context array representing this item.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return [
            'id'            => $this->identifier(),
            'title'         => $this->title(),
            'description'   => $this->description(),
            'icon'          => $this->icon(),
            'url'           => $this->url(),
            'target'        => $this->target(),
            'priority'      => $this->priority(),
            'permissions'   => $this->permissions(),
            'section'     => $this->section(),
        ];
    }

    /**
     * Converts the item to its rendered HTML representation.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render();
    }

    /**
     * Returns the item's target URL.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Returns the view path used to render this item.
     *
     * @return string
     */
    public function view()
    {
        // Return the view used to render this section item
        return $this->view;
    }

    /**
     * Removes all permission constraints from this item.
     *
     * @return static
     */
    public function withoutPermission()
    {
        $this->permissions = null;

        return $this;
    }

    /**
     * Sets the description text for this item.
     *
     * @param string $description
     *
     * @return static
     */
    public function withDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets the icon class for this item.
     *
     * @param string $icon
     *
     * @return static
     */
    public function withIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Sets the unique identifier for this item.
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
     * Adds a single permission requirement to this item.
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
     * Sets multiple permission requirements for this item.
     *
     * @param array<string> $permissions
     *
     * @return static
     */
    public function withPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Sets the priority value for this item.
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
     * Sets the item's URL using a named route.
     *
     * @param string $route
     * @param array $parameters
     * @param bool $absolute
     *
     * @return static
     */
    public function withRoute($route, $parameters = [], $absolute = true)
    {
        return $this
            ->withUrl(route($route, $parameters, $absolute))
            ->withPermission($route);
    }

    /**
     * Sets the section ID this item belongs to.
     *
     * @param string $section
     *
     * @return static
     */
    public function withSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Configures whether the item's URL should open in a new browser tab.
     *
     * @param string $target
     *
     * @return static
     */
    public function withTarget($target = '_self')
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Sets the display title for this item.
     *
     * @param string $title
     *
     * @return static
     */
    public function withTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the target URL for this item.
     *
     * @param string $url
     *
     * @return static
     */
    public function withUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Sets the Blade view used to render this item.
     *
     * @param string $view
     *
     * @return static
     */
    public function withView($view)
    {
        $this->view = $view;

        return $this;
    }
}
