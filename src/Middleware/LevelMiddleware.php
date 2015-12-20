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
        $levelException = $this->config->get('authorization.level_exception');
        if (starts_with($level, 'max')) {
            $level = substr($level, 3);
            $userLevel = app('authorization')->maxLevel();
        } else {
            $userLevel = app('authorization')->level();
        }
        /*Compare smallest level of user ===  $level*/
        if (strlen($level) === 1 && $userLevel !== intval($level)) {
            throw new $levelException();
        }
        /*Check: number <= user level <= number*/
        if (strpos($level, '<=>') !== false) {
            $middLevel = explode('<=>', $level);
            if ($userLevel < intval($middLevel[0]) || $userLevel > intval($middLevel[1])) {
                throw new $levelException();
            }
        } else {
            /*Check: user level < number*/
            if (strpos($level, '<') !== false) {
                if ($userLevel >= intval(substr($level, 1))) {
                    throw new $levelException();
                }
            }
            /*Check: user level < number*/
            if (strpos($level, '>') !== false) {
                if ($userLevel <= intval(substr($level, 1))) {
                    throw new $levelException();
                }
            }
        }
        /*Check: user has all levels*/
        if (strpos($level, '&') !== false) {
            if (app('authorization')->matchLevel(explode('&', trim($level, '&'))) === false) {
                throw new $levelException();
            }
        } elseif (strpos($level, '|') !== false) {
            /*Check user has one in any levels*/
            if (app('authorization')->matchAnyLevel(explode('|', trim($level, '|'))) === false) {
                throw new $levelException();
            }
        }

        return $next($request);
    }
}
