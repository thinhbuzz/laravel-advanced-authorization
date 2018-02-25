<?php


namespace Buzz\Authorization\Middleware;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Guard;

class PermissionMiddleware
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
     * @param $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $permissionException = $this->config->get('authorization.exception.permission_denied');
        if (strpos($permission, '|') !== false) {
            $method = 'canAny';
            $permissions = explode('|', $permission);
        } elseif (strpos($permission, '&') !== false) {
            $method = 'can';
            $permissions = explode('&', $permission);
        } else {
            $method = 'can';
            $permissions = [$permission];
        }
        if (app('authorization')->{$method}($permissions) === false) {
            throw new $permissionException($permissions);
        }

        return $next($request);
    }
}
