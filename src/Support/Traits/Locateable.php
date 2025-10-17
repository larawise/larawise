<?php

namespace Larawise\Support\Traits;

use Illuminate\Contracts\Filesystem\Filesystem;

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
trait Locateable
{
    /**
     * The delimiter used to separate namespace segments.
     *
     * @var string
     */
    protected $delimiter = '::';

    /**
     * The namespace-to-path mapping hints.
     *
     * @var array<string, array>
     */
    protected $hints = [];

    /**
     * Optional callback to resolve possible file names.
     *
     * @var callable|null
     */
    protected $fileResolver;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Register a view extension with the finder.
     *
     * @var string[]
     */
    protected $extensions;

    /**
     * Optional callback for resolving namespace segments.
     *
     * @var callable|null
     */
    protected $namespaceResolver;

    /**
     * The array of active paths.
     *
     * @var string[]
     */
    protected $paths;

    /**
     * Add a new location path to the stack.
     *
     * @param string $location
     *
     * @return void
     */
    public function addLocation($location)
    {
        $this->paths[] = $this->resolvePath($location);
    }

    /**
     * Add one or more path hints for a given namespace.
     *
     * @param string $namespace
     * @param string|string[] $hints
     *
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Remove a location path from the stack.
     *
     * @param string $location
     *
     * @return void
     */
    public function forgetLocation($location)
    {
        $location = $this->resolvePath($location);

        $this->paths = array_filter($this->paths, fn ($path) => $path !== $location);
    }

    /**
     * Remove all path hints for the given namespace.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function forgetNamespace($namespace)
    {
        unset($this->hints[$namespace]);
    }

    /**
     * Get the current delimiter used for namespace resolution.
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Get the list of registered file extensions.
     *
     * @return string[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Get all registered namespace hints.
     *
     * @return array<string, array>
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Get the currently active paths.
     *
     * @return string[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Get an array of possible file names for the given identifier.
     *
     * @param string $name
     * @param string[]|null $extensions
     *
     * @return string[]
     */
    protected function getPossibleFiles(string $name, ?array $extensions = null)
    {
        $extensions = $extensions ?? ['php'];

        if (is_callable($this->fileResolver)) {
            return call_user_func($this->fileResolver, $name, $extensions);
        }

        return array_map(
            fn ($ext) => str_replace('.', '/', $name).'.'.$ext,
            $extensions
        );
    }

    /**
     * Returns whether the name has any hint information.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasHintInformation($name)
    {
        return str_contains($name, $this->delimiter);
    }

    /**
     * Determine if the given location exists in the stack.
     *
     * @param string $location
     *
     * @return bool
     */
    public function hasLocation($location)
    {
        $location = $this->resolvePath($location);

        return in_array($location, $this->paths, true);
    }

    /**
     * Determine if the given namespace has any registered hints.
     *
     * @param string $namespace
     *
     * @return bool
     */
    public function hasNamespace($namespace)
    {
        return isset($this->hints[$namespace]);
    }

    /**
     * Prepend a location path to the beginning of the stack.
     *
     * @param string $location
     *
     * @return void
     */
    public function prependLocation($location)
    {
        array_unshift($this->paths, $this->resolvePath($location));
    }

    /**
     * Prepend one or more path hints to the beginning of the namespace stack.
     *
     * @param string $namespace
     * @param string|string[] $hints
     *
     * @return void
     */
    public function prependNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Replace all path hints for the given namespace.
     *
     * @param string $namespace
     * @param string|string[] $hints
     *
     * @return void
     */
    public function replaceNamespace($namespace, $hints)
    {
        $this->hints[$namespace] = (array) $hints;
    }

    /**
     * Resolve the namespace segments using the configured callback or default logic.
     *
     * @param string $name
     *
     * @return array
     */
    protected function resolveNamespace($name)
    {
        return is_callable($this->namespaceResolver)
            ? call_user_func($this->namespaceResolver, $name)
            : explode($this->delimiter, $name);
    }

    /**
     * Normalize and resolve the given path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function resolvePath($path)
    {
        return realpath($path) ?: $path;
    }

    /**
     * Set the delimiter used for namespace resolution.
     *
     * @param string $delimiter
     *
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Set the list of file extensions to search for.
     *
     * @param string[] $extensions
     *
     * @return $this
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * Set a custom file resolver callback.
     *
     * @param callable $resolver
     *
     * @return void
     */
    public function setFileResolver($resolver)
    {
        $this->fileResolver = $resolver;
    }

    /**
     * Replace all namespace hints with the given set.
     *
     * @param array<string, array> $hints
     *
     * @return $this
     */
    public function setHints($hints)
    {
        $this->hints = $hints;

        return $this;
    }

    /**
     * Set a custom namespace resolver callback.
     *
     * @param callable $resolver
     *
     * @return void
     */
    public function setNamespaceResolver($resolver)
    {
        $this->namespaceResolver = $resolver;
    }

    /**
     * Set the active paths.
     *
     * @param string[] $paths
     *
     * @return $this
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;

        return $this;
    }
}
