<?php


namespace Buzz\Authorization\Middleware;

use Closure;
use Illuminate\Auth\Guard;
use Illuminate\Config\Repository;

class RoleMiddleware
{
    /**
     * @var Guard
     */
    protected $auth;
    /**
     * @var Repository
     */
    private $config;

    /**
     * DI auth
     *
     * @param Guard $auth
     * @param Repository $config
     */
    public function __construct(Guard $auth, Repository $config)
    {
        $this->auth = $auth;
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $roleException = $this->config->get('authorization.role_exception');
        if (strpos($role, '|') !== false) {
            $method = 'isAny';
            $roles = explode('|', $role);
        } elseif (strpos($role, '&') !== false) {
            $method = 'is';
            $roles = explode('&', $role);
        } else {
            $method = 'is';
            $roles = $role;
        }
        if (app('authorization')->{$method}($roles) === false) {
            throw new $roleException();
        }

        return $next($request);
    }
}
