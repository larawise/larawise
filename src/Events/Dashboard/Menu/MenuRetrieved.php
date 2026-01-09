<?php

namespace Larawise\Events\Dashboard\Menu;

use Illuminate\Support\Collection;
use Larawise\Dashboard\Menu\Menu;
use Larawise\Events\Event;

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
class MenuRetrieved extends Event
{
    /**
     * Create a new event instance.
     *
     * @param Menu $menu
     * @param Collection $items
     *
     * @return void
     */
    public function __construct(
        public Menu $menu,
        public Collection $items
    ) { }
}
