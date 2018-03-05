<?php

namespace JasonXt\LaraSetting;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use JasonXt\LaraSetting\Facades\LaraSetting as LaraSettingFacade;

class LaraSettingServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/lara-setting.php', 'lara-setting'
        );

        // Register into container
        $this->app->singleton('lara-setting', function () {
            return new Setting();
        });

        //Register Facades (load from config)
        $facadeName = config('lara-setting.facade', 'LaraSetting');

        $loader = AliasLoader::getInstance();
        $loader->alias($facadeName, LaraSettingFacade::class);


        $this->app->make('lara-setting');

        //
        $this->publishes([
            __DIR__ . '/lara-setting.php' => config_path('lara-setting.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}