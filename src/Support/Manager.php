<?php

namespace Larawise\Support;

use Illuminate\Support\Manager as IlluminateManager;
use Larawise\Contracts\ManagerContract;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v0.0.1
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 *
 * @template TDriver
 */
abstract class Manager extends IlluminateManager implements ManagerContract
{
    /**
     * Call a custom driver creator.
     *
     * @param TDriver $driver
     *
     * @return TDriver
     */
    protected function callCustomCreator($driver)
    {
        if (method_exists($this, 'build')) {
            return $this->build(parent::callCustomCreator($driver));
        }

        return parent::callCustomCreator($driver);
    }
}
