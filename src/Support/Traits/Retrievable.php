<?php

namespace Larawise\Support\Traits;

use Closure;
use Illuminate\Support\Collection;

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
trait Retrievable
{
    /**
     * Stores callbacks to be executed before retrieving items for each group.
     *
     * @var array
     */
    protected array $beforeRetrieving = [];

    /**
     * Stores callbacks to be executed after items have been retrieved for each group.
     *
     * @var array
     */
    protected array $afterRetrieved = [];

    /**
     * Registers a callback to run after items have been retrieved for the current group.
     *
     * @param Closure $callback
     *
     * @return static
     */
    public function afterRetrieved(Closure $callback)
    {
        // Bind the callback to the current group context
        $this->afterRetrieved[$this->group][] = $callback;

        // Reset group context to default
        $this->default();

        return $this;
    }

    /**
     * Registers a callback to run before retrieving items for the current group.
     *
     * @param Closure $callback
     *
     * @return static
     */
    public function beforeRetrieving(Closure $callback)
    {
        // Bind the callback to the current group context
        $this->beforeRetrieving[$this->group][] = $callback;

        // Reset group context to default
        $this->default();

        return $this;
    }

    /**
     * Executes all registered "after retrieved" callbacks for the current group.
     *
     * @param Collection $menu
     *
     * @return void
     */
    protected function dispatchAfterRetrieved(Collection $menu)
    {
        if (empty($this->afterRetrieved[$this->group])) {
            return;
        }

        foreach ($this->afterRetrieved[$this->group] as $callback) {
            call_user_func($callback, $this, $menu);
        }
    }

    /**
     * Executes all registered "before retrieving" callbacks for the current group.
     *
     * @return void
     */
    protected function dispatchBeforeRetrieving()
    {
        if (empty($this->beforeRetrieving[$this->group])) {
            return;
        }

        foreach ($this->beforeRetrieving[$this->group] as $callback) {
            call_user_func($callback, $this);
        }
    }
}
