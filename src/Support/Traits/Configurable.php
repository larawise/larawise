<?php

namespace Larawise\Support\Traits;

use Illuminate\Contracts\Config\Repository;

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
trait Configurable
{
    /**
     * The config repository instance.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Get / set the specified configuration value.
     *
     * @param array<string, mixed>|string|null $key
     * @param mixed $default
     *
     * @return ($key is null ? Repository : ($key is string ? mixed : null))
     */
    protected function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->getRepository();
        }

        if (is_array($key)) {
            return $this->getRepository()->set($key);
        }

        return $this->getRepository()->get($key, $default);
    }

    /**
     * Get the current event dispatcher instance.
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->config;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param Repository $repository
     *
     * @return void
     */
    public function setRepository($repository)
    {
        $this->config = $repository;
    }
}
