<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Change the default name of the Artisan binary.
 *
 * @var string
 */
const ARTISAN_BINARY = 'sevenity';

/*
|--------------------------------------------------------------------------
| ℹ️ Application (Configure)
|--------------------------------------------------------------------------
|
| Larawise includes a special builder that supports configuring the application
| end-to-end and allows you to style the application in a customized manner.
| You can review the documentation to easily configure all areas of the application.
|
*/
return Application::configure(basePath: dirname(__DIR__))
    // Register an array of container bindings to be bound when the application is booting.
    ->withBindings([
        // ...
    ])
    // Register an array of singleton container bindings to be bound when the application is booting.
    ->withSingletons([
        // ...
    ])
    // Register additional service providers.
    ->withProviders([
        // ...
    ])
    // Register the routing services for the application.
    ->withRouting(
        // ...
    )
    // Register the global middleware, middleware groups, and middleware aliases for the application
    ->withMiddleware(function (Middleware $middleware) {
        // ...
    })
    // Register and configure the application's exception handler.
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })
    // Register additional Sevenity commands with the application.
    ->withCommands([
        // ...
    ])
    // Register the scheduled tasks for the application.
    ->withSchedule(function (Schedule $schedule) {
        // ...
    })
    // Create the "Larawise" application instance.
    ->create()
    // Move the application's language files directory to the resources directory.
    ->useLangPath(resource_path('languages'));
