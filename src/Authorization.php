<?php


namespace Buzz\Authorization;


use Illuminate\Foundation\Application;

class Authorization
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var boolean
     */
    protected $isLogin;
    protected $user;
    /*
     * Config of package
     * @var array
     * */
    protected $config;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->isLogin = $app['auth']->check();
        $this->config = $app->config->get('authorization');
        if ($app['auth']->check() === true) {
            $this->isLogin = true;
            if ($this->config['eager_type'] === 1) {
                $this->user = $app['auth']->user()->load(['roles']);
            } else {
                $this->user = $app['auth']->user()->load(['roles.permissions']);
            }
        } else {
            $this->isLogin = false;
        }
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name) === false) {
            if ($this->isLogin === false) {
                return false;
            }

            return $this->user->{$name}($arguments);
        }

        return $this->{$name}($arguments);
    }

    public function can($permission)
    {
        return $this->authorization('can', $permission);
    }

    public function canAny($permission)
    {
        return $this->authorization('canAny', $permission);
    }

    protected function authorization($method, $value)
    {
        if (empty($value)) {
            return false;
        }
        if ($this->isLogin === false) {
            return false;
        }
        if (in_array($method, ['can', 'canAny']) && $this->config['eager_type'] === 1 && is_null($this->user->permissions)) {
            $this->app['auth']->user()->load(['roles.permissions']);
        }

        return $this->user->{$method}($value);
    }
}