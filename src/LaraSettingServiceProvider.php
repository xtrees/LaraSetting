<?php

namespace Xtrees\LaraSetting;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Xtrees\LaraSetting\Facades\LaraSetting as LaraSettingFacade;

class LaraSettingServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../lara-setting.php' => config_path('lara-setting.php')
            ], 'lara-setting-config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'setting');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../lara-setting.php', 'lara-setting'
        );
        // Register into container
        $this->app->singleton('lara-setting', function () {
            return new Setting();
        });

        //Register Facades (load from config)
        AliasLoader::getInstance([
            config('lara-setting.facade', 'LaraSetting') => LaraSettingFacade::class
        ])->register();
    }
}
