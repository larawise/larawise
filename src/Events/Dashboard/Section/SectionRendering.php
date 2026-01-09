<?php

namespace Larawise\Events\Dashboard\Section;

use Botble\Base\Contracts\PanelSections\PanelSection;
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
class SectionRendering extends Event
{
    /**
     * Create a new event instance.
     *
     * @param PanelSection $section
     *
     * @return void
     */
    public function __construct(
        public $section
    ) { }
}
