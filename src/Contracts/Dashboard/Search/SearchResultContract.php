<?php

namespace Larawise\Contracts\Dashboard\Search;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

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
interface SearchResultContract extends JsonSerializable, Arrayable, Jsonable
{
    /**
     * Returns the optional description text.
     *
     * @return string
     */
    public function description();

    /**
     * Generates a unique identifier for this result.
     *
     * @return string
     */
    public function identifier();

    /**
     * Returns the icon identifier for this result.
     *
     * @return string
     */
    public function icon();

    /**
     * Returns the parent breadcrumb trail for this result.
     *
     * @return array
     */
    public function parents();

    /**
     * Returns the target attribute value for this result.
     *
     * @return string
     */
    public function target();

    /**
     * Returns the display title of the result.
     *
     * @return string
     */
    public function title();

    /**
     * Returns the target URL for this result.
     *
     * @return string
     */
    public function url();
}
