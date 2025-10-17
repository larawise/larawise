<?php

namespace Larawise\Support\Traits;

use Illuminate\Contracts\Events\Dispatcher;

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
trait Dispatchable
{
    /**
     * The event dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * Dispatch an event and call the listeners.
     *
     * @param string|object $event
     * @param mixed $payload
     * @param bool $halt
     *
     * @return array|null
     */
    protected function dispatch($event, $payload = [], $halt = false)
    {
        return $this->getDispatcher()->dispatch($event, $payload, $halt);
    }

    /**
     * Get the current event dispatcher instance.
     *
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param Dispatcher $dispatcher
     *
     * @return void
     */
    public function setDispatcher($dispatcher)
    {
        $this->events = $dispatcher;
    }
}
