<?php


namespace Buzz\Authorization;

use Buzz\Authorization\Events\DeletePermissionEvent;
use Buzz\Authorization\Events\DeleteRoleEvent;
use Buzz\Authorization\Events\UpdateRoleLevelEvent;
use Buzz\Authorization\Events\UpdateRolePermissionEvent;
use Buzz\Authorization\Events\UpdateUserEvent;
use Illuminate\Support\Collection;
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
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'authorization');
        $this->registerAlias();
        $this->registerBladeShortcut();
        $this->registerEvents();
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

    protected function registerAlias()
    {
        $config = $this->app->config->get('authorization');
        if ($config['auto_alias'] === true) {
            \Illuminate\Foundation\AliasLoader::getInstance()
                ->alias($config['alias'], AuthorizationFacade::class);
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
                $blade->directive('greaterLevel', function ($expression) {
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
                $blade->directive('endGreaterLevel', function ($expression) {
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

    protected function registerEvents()
    {
        $config = $this->app->config->get('authorization');
        if (array_get($config, 'cache.event')) {
            $event = $this->app->events;
            $event->listen(['roles.attached', 'roles.detached', 'roles.synced'], function ($user) {
                (new UpdateUserEvent($user))->boot();
            });
            $event->listen(['permissions.attached', 'permissions.detached', 'permissions.synced'], function ($role) {
                (new UpdateRolePermissionEvent($role))->boot();
            });

            $event->listen(sprintf('eloquent.updated: %s', array_get($config, 'model.role')), function ($role) {
                if ($role->isDirty('level')) {
                    (new UpdateRoleLevelEvent($role))->boot();
                }
            });
            $event->listen(sprintf('eloquent.deleting: %s', array_get($config, 'model.role')), function ($role) {
                $userIds = $role->users()->get()->lists('id');
                $this->app->session->put('authorization.user_id', $userIds);
            });

            $event->listen(sprintf('eloquent.deleted: %s', array_get($config, 'model.role')), function ($role) {
                $userIds = $this->app->session->pull('authorization.user_id');
                (new DeleteRoleEvent($userIds))->boot();
            });
            $event->listen(sprintf('eloquent.deleting: %s', array_get($config, 'model.permission')), function ($permission) {
                $roles = $permission->roles()->with('users')->get();
                $this->app->session->put('authorization.roles', $roles);
            });
            $event->listen(sprintf('eloquent.deleted: %s', array_get($config, 'model.permission')), function ($permission) {
                $roles = $this->app->session->pull('authorization.roles');
                (new DeletePermissionEvent($roles))->boot();
            });
        }
    }
}
