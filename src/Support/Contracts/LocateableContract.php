<?php

namespace Larawise\Support\Contracts;

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
interface LocateableContract
{
    /**
     * Add a new location path to the stack.
     *
     * @param string $location
     *
     * @return void
     */
    public function addLocation($location);

    /**
     * Add one or more path hints for a given namespace.
     *
     * @param string $namespace
     * @param string|string[] $hints
     *
     * @return void
     */
    public function addNamespace($namespace, $hints);

    /**
     * Remove a location path from the stack.
     *
     * @param string $location
     *
     * @return void
     */
    public function forgetLocation($location);

    /**
     * Remove all path hints for the given namespace.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function forgetNamespace($namespace);

    /**
     * Get all registered namespace hints.
     *
     * @return array<string, array>
     */
    public function getHints();

    /**
     * Get the currently active paths.
     *
     * @return string[]
     */
    public function getPaths();

    /**
     * Determine if the given location exists in the stack.
     *
     * @param string $location
     *
     * @return bool
     */
    public function hasLocation($location);

    /**
     * Determine if the given namespace has any registered hints.
     *
     * @param string $namespace
     *
     * @return bool
     */
    public function hasNamespace($namespace);

    /**
     * Prepend a location path to the beginning of the stack.
     *
     * @param string $location
     *
     * @return void
     */
    public function prependLocation($location);

    /**
     * Prepend one or more path hints to the beginning of the namespace stack.
     *
     * @param string $namespace
     * @param string|string[] $hints
     *
     * @return void
     */
    public function prependNamespace($namespace, $hints);

    /**
     * Replace all path hints for the given namespace.
     *
     * @param string $namespace
     * @param string|string[] $hints
     *
     * @return void
     */
    public function replaceNamespace($namespace, $hints);

    /**
     * Replace all namespace hints with the given set.
     *
     * @param array<string, array> $hints
     *
     * @return $this
     */
    public function setHints($hints);

    /**
     * Set the active paths.
     *
     * @param string[] $paths
     *
     * @return $this
     */
    public function setPaths($paths);
}
