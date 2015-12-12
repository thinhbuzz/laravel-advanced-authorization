<?php


namespace Buzz\Authorization\Middleware;

use Closure;
use Illuminate\Auth\Guard;
use Illuminate\Config\Repository;

class LevelMiddleware
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
    public function handle($request, Closure $next, $level)
    {
        dd($level);
        $levelException = $this->config->get('authorization.level_exception');
        $userLevel = app('authorization')->level();
        if (strpos('<=>', $level) !== false) {
            $middLevel = explode('<=>', $level);
            if ($userLevel < intval($middLevel[0]) || $userLevel > intval($middLevel[1])) {
                throw new $levelException();
            }
        }
        if (strpos('<', $level) !== false) {
            if ($userLevel >= intval(substr($level, 1))) {
                throw new $levelException();
            }
        }
        if (strpos('>', $level) !== false) {
            if ($userLevel <= intval(substr($level, 1))) {
                throw new $levelException();
            }
        }
        if (strpos('&', $level) !== false) {
            if (app('authorization')->matchLevel(explode('&', $level)) === false) {
                throw new $levelException();
            }
        }
        if (strpos('|', $level) !== false) {
            if (app('authorization')->matchAnyLevel(explode('|', $level)) === false) {
                throw new $levelException();
            }
        }

        return $next($request);
    }
}
