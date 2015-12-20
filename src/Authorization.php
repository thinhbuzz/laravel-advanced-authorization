<?php


namespace Buzz\Authorization;


use Illuminate\Foundation\Application;

/* *
 * @method boolean can($permission)
 * @method boolean canAny($permission)
 * @method boolean is($role)
 * @method boolean isAny($role)
 * */

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
            $this->user = $app['auth']->user();
        } else {
            $this->isLogin = false;
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

    public function user()
    {
        if ($this->isLogin === true) {
            return $this->user;
        }
    }
}
