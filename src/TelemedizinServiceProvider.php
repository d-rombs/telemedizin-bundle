<?php

namespace Telemedizin\TelemedizinBundle;

use Illuminate\Support\ServiceProvider;
use Telemedizin\TelemedizinBundle\Console\Commands\SeedDatabaseCommand;

class TelemedizinServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        // publish migrations
        $this->publishes([
            __DIR__.'/Database/Migrations/' => database_path('migrations'),
        ], 'telemedizin-migrations');

        // publish config
        $this->publishes([
            __DIR__.'/config/telemedizin.php' => config_path('telemedizin.php'),
        ], 'telemedizin-config');

        // publish seeders
        $this->publishes([
            __DIR__.'/Database/Seeders/' => database_path('seeders/telemedizin'),
        ], 'telemedizin-seeders');

        // publish email templates
        $this->publishes([
            __DIR__.'/resources/views/emails/' => resource_path('views/vendor/telemedizin/emails'),
        ], 'telemedizin-email-templates');

        // publish assets
        $this->publishes([
            __DIR__.'/resources/assets/' => public_path('vendor/telemedizin'),
        ], 'telemedizin-assets');

        // load routes
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');

        // load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'telemedizin');

        // load migrations
        // $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // merge config
        $this->mergeConfigFrom(
            __DIR__.'/config/telemedizin.php', 'telemedizin'
        );

        // register artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedDatabaseCommand::class,
            ]);
        }
    }
} 