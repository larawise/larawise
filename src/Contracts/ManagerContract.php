<?php

namespace Larawise\Contracts;

use Closure;
use Illuminate\Contracts\Container\Container;

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
 *
 * @template TDrive
 */
interface ManagerContract
{
    /**
     * Get a driver instance.
     *
     * @param TDrive $driver
     *
     * @return TDrive
     */
    public function driver($driver = null);

    /**
     * Register a custom driver creator Closure.
     *
     * @param string $driver
     * @param Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback);

    /**
     * Get the default driver name.
     *
     * @return string|null
     */
    public function getDefaultDriver();

    /**
     * Set the default settingfy driver name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name);

    /**
     * Get all the created "drivers".
     *
     * @return array
     */
    public function getDrivers();

    /**
     * Get the container instance used by the manager.
     *
     * @return Container
     */
    public function getContainer();

    /**
     * Set the container instance used by the manager.
     *
     * @param Container $container
     *
     * @return $this
     */
    public function setContainer(Container $container);

    /**
     * Forget all the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers();
}
