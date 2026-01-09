<?php

namespace Larawise;

use Closure;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

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
final class Larawise
{
    // Concerns
    use Concerns\InteractsWithFramework;

    /**
     * Get the application name based on the current context.
     *
     * @return string The name of the application.
     */
    public function name()
    {
        // Check if in the admin panel and return the appropriate configuration value
        return $this->isInAdmin()
            ? config('srylius.name', 'Larawise')
            : config('app.name', 'Larawise');
    }

    /**
     * Get the application URL based on the current context.
     *
     * @return string The URL of the application.
     */
    public function url()
    {
        // Check if in the admin panel and return the appropriate configuration value
        return $this->isInAdmin()
            ? config('srylius.url.web', 'https://srylius.com')
            : config('app.url', 'https://srylius.com');
    }

    /**
     * Get the prefix for the admin directory.
     *
     * @return string The admin directory prefix.
     */
    public function prefix()
    {
        // Fetch the prefix from the configuration
        $prefix = config('core.base.general.admin_dir');

        // If prefix is not set and the Theme class exists, return 'admin' as default
        if (! $prefix && class_exists('Theme')) {
            return 'admin';
        }

        // Return the prefix from configuration
        return $prefix;
    }

    /**
     * Get the list of middleware applied to the application.
     *
     * @param array $except
     *
     * @return array The list of middleware.
     */
    public function middlewares($except = [])
    {
        // Return the middleware stack applied to requests
        return Arr::except(['web', 'core', 'auth'], $except);
    }

    /**
     * Get the list of Srylius platforms with their details.
     *
     * @param array $except
     *
     * @return array<int, array<string, string>>
     */
    public function platforms($except = [])
    {
        return collect([
            'academy'  => [
                'name' => "<b>Srylius</b>.".__('general.academy'),
                'href' => 'https://academy.srylius.com/',
                'icon' => 'ph:graduation-cap-duotone'
            ],
            'career'   => [
                'name' => "<b>Srylius</b>.".__('general.career'),
                'href' => 'https://career.srylius.com/',
                'icon' => 'ph:user-circle-plus-duotone'
            ],
            'support'  => [
                'name' => "<b>Srylius</b>.".__('general.support'),
                'href' => 'https://support.srylius.com/',
                'icon' => 'ph:graduation-cap-duotone'
            ],
            'blog'     => [
                'name' => "<b>Srylius</b>.".__('general.blog'),
                'href' => 'https://blog.srylius.com/',
                'icon' => 'ph:newspaper-duotone'
            ],
            'customer' => [
                'name' => "<b>Srylius</b>.".__('general.customer'),
                'href' => 'https://my.srylius.com/',
                'icon' => 'ph:user-circle-plus-duotone'
            ],
        ])->except($except)->values()->toArray();
    }

    /**
     * Register an application routes.
     *
     * @param Closure|callable $closure
     * @param array|null $middleware
     *
     * @return RouteRegistrar
     */
    public function routes($closure, $middleware = null)
    {
        return Route::prefix($this->prefix())
            ->middleware($middleware ?? $this->middlewares())
            ->group(fn () => $closure());
    }

    /**
     * Determines if the current request is within the admin section.
     *
     * @return bool
     */
    public function isInAdmin()
    {
        $prefix = $this->prefix();

        // If no prefix is defined, assume the request is in the admin section
        if (empty($prefix)) {
            return true;
        }

        // Extract the relevant segments from the request's URL
        $segments = array_slice(request()->segments(), 0, count(explode('/', $prefix)));

        // Compare the extracted segments with the defined prefix
        return implode('/', $segments) === $prefix;
    }
}
