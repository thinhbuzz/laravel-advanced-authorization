<?php


namespace Buzz\Authorization;

use Buzz\Authorization\Events\RebuildPermissionRoleEvent;
use Buzz\Authorization\Listeners\RebuildPermissionRoleListener;
use Buzz\Authorization\Middleware\PermissionMiddleware;
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
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'authorization');
        $this->registerBladeShortcut();
        $this->registerRebuildRoleEvent();
        $this->registerMiddleware();/*publish migration and config*/
        $this->publishes([
            __DIR__ . '/../migrations/' => base_path('/database/migrations'),
            __DIR__ . '/Models/' => app_path('/Models'),
            __DIR__ . '/../config.php' => config_path('authorization.php')
        ]);
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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['authorization'];
    }

    /**
     * Register blade short cut
     *
     * @return void
     */
    protected function registerBladeShortcut()
    {
        /**
         * @var \Illuminate\Config\Repository $appConfig
         */
        $appConfig = $this->app['config'];
        $config = $appConfig->get('authorization');
        if ($config['blade_shortcut'] === true) {
            /**
             * @var \Illuminate\View\Factory $view
             */
            $view = $this->app['view'];
            /**
             * @var \Illuminate\View\Engines\CompilerEngine $engine
             */
            $engine = $view->getEngineResolver()->resolve('blade');
            /**
             * @var \Illuminate\View\Compilers\BladeCompiler $bladeCompiler
             */
            $bladeCompiler = $engine->getCompiler();
            /*
             * Blade shortcut fot authorization
             * */
            $bladeCompiler->directive('permission', function ($expression) {
                return "<?php if(app('authorization')->can{$expression}): ?>";
            });
            $bladeCompiler->directive('anyPermission', function ($expression) {
                return "<?php if(app('authorization')->canAny{$expression}): ?>";
            });

            $bladeCompiler->directive('endPermission', function () {
                return "<?php endif; ?>";
            });
            $bladeCompiler->directive('endAnyPermission', function () {
                return "<?php endif; ?>";
            });
        }
    }

    protected function registerRebuildRoleEvent()
    {
        /**
         * @var \Illuminate\Contracts\Events\Dispatcher $events
         */
        $events = $this->app['events'];
        $events->listen(RebuildPermissionRoleEvent::class, RebuildPermissionRoleListener::class);
    }

    protected function registerMiddleware()
    {
        /**
         * @var \Illuminate\Routing\Router $router
         */
        $router = $this->app['router'];
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
    }
}
