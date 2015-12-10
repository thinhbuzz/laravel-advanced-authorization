<?php


namespace Buzz\Authorization;

use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootConfig();
        $this->registerAlias();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('authorization', function ($app) {
            return new Authorization();
        });
    }

    /**
     * Set config file for package
     */
    protected function bootConfig()
    {
        $path = __DIR__ . '/../config/config.php';
        $this->publishes([$path => config_path('authorization.php')]);
        $this->mergeConfigFrom($path, 'authorization');
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

    private function registerAlias()
    {
        $config = $this->app->config->get('authorization');
        if ($config['auto_alias'] === true) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias($config['alias'], AuthorizationFacade::class);
        }
    }

}