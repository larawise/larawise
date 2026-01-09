<?php

namespace Larawise\Dashboard\Search;

use ReturnTypeWillChange;
use Larawise\Contracts\Dashboard\Search\SearchResultContract;

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
class SearchResult implements SearchResultContract
{
    /**
     * Create a new search result instance.
     *
     * @param string $icon
     * @param string $title
     * @param string $description
     * @param string $url
     * @param array $parents
     * @param string $target
     *
     * @return void
     */
    public function __construct(
        protected $icon,
        protected $title,
        protected $description = '',
        protected $url = '',
        protected $parents = [],
        protected $target = "_blank",
    ) { }

    /**
     * Returns the optional description text.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Generates a unique identifier for this result.
     *
     * @return string
     */
    public function identifier()
    {
        return "{$this->title()}-{$this->url()}";
    }

    /**
     * Returns the icon identifier for this result.
     *
     * @return string
     */
    public function icon()
    {
        return $this->icon;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Returns the parent breadcrumb trail for this result.
     *
     * @return array
     */
    public function parents()
    {
        return $this->parents;
    }

    /**
     * Returns the target attribute value for this result.
     *
     * @return string
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * Returns the display title of the result.
     *
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'icon'          => $this->icon,
            'title'         => $this->title,
            'description'   => $this->description,
            'parents'       => $this->parents,
            'url'           => $this->url,
            'target'        => $this->target,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Returns the target URL for this result.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }
}
