<?php

namespace Larawise\Events\Dashboard\Section\Item;

use Botble\Base\Contracts\PanelSections\PanelSectionItem;
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
class SectionItemRendering extends Event
{
    /**
     * Create a new event instance.
     *
     * @param PanelSectionItem $item
     *
     * @return void
     */
    public function __construct(
        public $item
    ) { }
}
