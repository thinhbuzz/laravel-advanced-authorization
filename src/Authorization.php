<?php

namespace Buzz\Authorization;

use App\User;
use Illuminate\Foundation\Application;

/**
 * Class Authorization
 * @package Buzz\Authorization
 * @method boolean can($permissions)
 * @method boolean canAny($permissions)
 * @method boolean cantAny($permissions)
 * @method boolean cantNotAny($permissions)
 */
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
    /**
     * @var User
     */
    protected $user;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->isLogin = $app['auth']->check();
        if ($app['auth']->check() === true) {
            $this->isLogin = true;
            $this->user = $app['auth']->user();
        }
    }

    /**
     * Call all method of user object
     *
     * @param $name
     * @param $arguments
     * @return bool
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name) === false) {
            if ($this->isLogin === true) {
                return call_user_func_array([$this->user, $name], $arguments);
            }

            return false;
        }

        return call_user_func_array([$this, $name], $arguments);
    }

    /**
     * @return \App\User|null
     */
    public function user()
    {
        if ($this->isLogin === true) {
            return $this->user;
        }
        return null;
    }
}
