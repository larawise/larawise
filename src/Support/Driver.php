<?php

namespace Larawise\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Events\Dispatcher;
use Larawise\Contracts\DriverContract;
use Larawise\Support\Traits\Configurable;
use Larawise\Support\Traits\Dispatchable;
use Larawise\Support\Traits\Encryptable;

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
 */
abstract class Driver implements DriverContract
{
    use Dispatchable, Encryptable, Configurable;

    /**
     * Create a new driver instance.
     *
     * @param Repository $config
     * @param Encrypter $encrypter
     * @param Dispatcher $events
     *
     * @return void
     */
    public function __construct($config, $encrypter, $events)
    {
        $this->config = $config;
        $this->encrypter = $encrypter;
        $this->events = $events;
    }
}
