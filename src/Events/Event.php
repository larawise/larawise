<?php

namespace Larawise\Events;

use Illuminate\Foundation\Events\Dispatchable;

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
abstract class Event
{
    use Dispatchable;

    /**
     * Indicates whether the event should be dispatched via queue.
     *
     * @var bool
     */
    protected $shouldQueue = false;

    /**
     * Dispatch the event with the given arguments.
     *
     * @return mixed
     */
    public static function dispatch()
    {
        $event = new static(...func_get_args());

        return $event->shouldQueue()
            ? dispatch($event)->onQueue($event->viaQueue())
            : event($event);
    }

    /**
     * Dispatch the event with the given arguments if the given truth test passes.
     *
     * @param bool $boolean
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public static function dispatchIf($boolean, ...$arguments)
    {
        if ($boolean) {
            $event = new static(...$arguments);

            return $event->shouldQueue()
                ? dispatch($event)->onQueue($event->viaQueue())
                : event($event);
        }
    }

    /**
     * Dispatch the event with the given arguments unless the given truth test passes.
     *
     * @param bool $boolean
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public static function dispatchUnless($boolean, ...$arguments)
    {
        if (! $boolean) {
            $event = new static(...$arguments);

            return $event->shouldQueue()
                ? dispatch($event)->onQueue($event->viaQueue())
                : event($event);
        }
    }

    /**
     * Determines whether the event should be queued.
     *
     * @return bool
     */
    public function shouldQueue()
    {
        return $this->shouldQueue;
    }

    /**
     * Defines which queue this event should be dispatched to.
     *
     * @return string
     */
    public function viaQueue()
    {
        return 'larawise';
    }
}
