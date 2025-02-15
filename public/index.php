<?php define('LARAWISE_START', microtime(true));

/*
|--------------------------------------------------------------------------
| ℹ️ Application (Maintenance)
|--------------------------------------------------------------------------
|
| We will need this file in cases such as putting the application into
| "Maintenance" mode with the "larawise:down" command. Thus, we can
| enable the user to view a custom template that you have created.
|
*/
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| ℹ️ Application (Composer)
|--------------------------------------------------------------------------
|
| "Composer" provides a useful, automatically generated classloader for
| this application. By including the script here in our application, we
| can ensure that all available classes are loaded automatically.
|
*/
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| ℹ️ Application (Bootstrap)
|--------------------------------------------------------------------------
|
| "Bootstrap" is a utility class that allows you to boot any existing
| scripts such as services and services of your application and framework.
| It helps us with app centralizing and caching.
|
*/
(require_once __DIR__.'/../bootstrap/larawise.php')
    ->handleRequest(Illuminate\Http\Request::capture());
