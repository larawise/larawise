<?php

namespace Larawise;

use Illuminate\View\Factory;
use Larawise\Console\CacheCommand;
use Larawise\Packagify\Packagify;
use Larawise\Packagify\PackagifyProvider;
use Larawise\Service\CacheService;

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
class LarawiseProvider extends PackagifyProvider
{
    /**
     * Configure the packagify package.
     *
     * @param Packagify $package
     *
     * @return void
     */
    public function configure(Packagify $package)
    {
        // Configure the package name.
        $package->name('larawise');

        // Configure the package description.
        $package->description('Larawise - The most powerful, elegant, and productive way to develop your next Laravel application.');

        $package->hasHelpers();
        $package->hasConfigurations();

        $package->hasTranslations();

        $package->hasSingletons([
            'larawise.cache' => fn ($app) => new CacheService($app['cache'], $app['config']['larawise.cache.service'])
        ]);

        // Configure the package commands.
        $package->hasCommands([
            CacheCommand::class
        ]);
    }

    public function packageRegistering()
    {
        // What needs to be done before the application's view factory is resolved.
        $this->app->afterResolving('view', function (Factory $levd) {
            // Include levd extension in blade view engine.
            $levd->addExtension('levd', 'blade');
        });
    }
}
