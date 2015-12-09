<?php


namespace Buzz\Authorization;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class UtilitieServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register commands
        $this->commands([
            'command.authorization.model',
            'command.authorization.migration',
            'command.authorization.seeder'
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.authorization.model', function ($app) {
            return new ModelCommand();
        });

        $this->app->singleton('command.authorization.migration', function ($app) {
            return new MigrationCommand();
        });

        $this->app->singleton('command.authorization.seeder', function ($app) {
            return new SeedCommand($app['composer']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['authorization'];
    }

}