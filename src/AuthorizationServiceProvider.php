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
        $this->registerBladeShortcut();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('authorization', function ($app) {
            return new Authorization($app);
        });
    }

    /**
     * Set config file for package
     */
    protected function bootConfig()
    {
        $path = __DIR__ . '/../config/config.php';
        $this->publishes([$path => config_path('authorization.php')], 'config');
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

    protected function registerAlias()
    {
        $config = $this->app->config->get('authorization');
        if ($config['auto_alias'] === true) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias($config['alias'], AuthorizationFacade::class);
        }
    }

    protected function registerBladeShortcut()
    {
        $config = $this->app->config->get('authorization');
        if ($config['blade_shortcut'] === true) {
            $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();
            /*
             * Blade shortcut fot authorization
             * */
            $blade->directive('role', function ($expression) {
                return "<?php if(app('authorization')->is{$expression}): ?>";
            });
            $blade->directive('anyRole', function ($expression) {
                return "<?php if(app('authorization')->isAny{$expression}): ?>";
            });
            $blade->directive('permission', function ($expression) {
                return "<?php if(app('authorization')->can{$expression}): ?>";
            });
            $blade->directive('anyPermission', function ($expression) {
                return "<?php if(app('authorization')->canAny{$expression}): ?>";
            });

            $blade->directive('endRole', function ($expression) {
                return "<?php endif; ?>";
            });
            $blade->directive('endAnyRole', function ($expression) {
                return "<?php endif; ?>";
            });
            $blade->directive('endPermission', function ($expression) {
                return "<?php endif; ?>";
            });
            $blade->directive('endAnyPermission', function ($expression) {
                return "<?php endif; ?>";
            });
            /*
             * Blade shortcut for user level
             * */
            if ($config['user_level'] === true) {
                $blade->directive('thanLevel', function ($expression) {
                    return "<?php if(app('authorization')->level() > {$expression}): ?>";
                });
                $blade->directive('lessLevel', function ($expression) {
                    return "<?php if(app('authorization')->level() < {$expression}): ?>";
                });
                $blade->directive('betweenLevel', function ($expression) {
                    list($min, $max) = explode(',', str_replace(['(', ')', ' '], '', $expression));

                    return "<?php if(app('authorization')->level() >= {$min} && app('authorization')->level() <= {$max}): ?>";
                });
                $blade->directive('matchLevel', function ($expression) {
                    return "<?php if(app('authorization')->matchLevel{$expression}): ?>";
                });
                $blade->directive('matchAnyLevel', function ($expression) {
                    return "<?php if(app('authorization')->matchAnyLevel{$expression}): ?>";
                });
                $blade->directive('endThanLevel', function ($expression) {
                    return "<?php endif; ?>";
                });
                $blade->directive('endLessLevel', function ($expression) {
                    return "<?php endif; ?>";
                });
                $blade->directive('endBetweenLevel', function ($expression) {
                    return "<?php endif; ?>";
                });
                $blade->directive('endMatchLevel', function ($expression) {
                    return "<?php endif; ?>";
                });
                $blade->directive('endMatchAnyLevel', function ($expression) {
                    return "<?php endif; ?>";
                });
            }
        }
    }

}