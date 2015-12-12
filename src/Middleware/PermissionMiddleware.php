<?php


namespace Buzz\Authorization\Middleware;

use Closure;
use Illuminate\Auth\Guard;
use Illuminate\Config\Repository;

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
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $permissionException = $this->config->get('authorization.permission_exception');
		if (strpos('|', $permission) === false) {
			$separator = '&';
			$method = 'can';
		} else {
			$separator = '|';
			$method = 'canAny';
		}
        if (app('authorization')->{$method}(explode($separator, $permission)) === false) {
            throw new $permissionException();
        }

        return $next($request);
    }
}
