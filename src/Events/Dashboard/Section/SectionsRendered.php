<?php

namespace Larawise\Events\Dashboard\Section;

use Botble\Base\Contracts\PanelSections\Manager;
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
class SectionsRendered extends Event
{
    /**
     * Create a new event instance.
     *
     * @param Manager $manager
     *
     * @return void
     */
    public function __construct(
        public $manager
    ) { }
}
