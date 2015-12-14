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
            'command.authorization.seeder'
        ]);
        /*publish migration*/
        $this->publishes([
            realpath(__DIR__ . '/../migrations/') => base_path('/database/migrations')
        ], 'migrations');
        /*publish config*/
        $path = __DIR__ . '/../config/config.php';
        $this->publishes([$path => config_path('authorization.php')], 'config');
        $this->mergeConfigFrom($path, 'authorization');
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
