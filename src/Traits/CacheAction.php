<?php


namespace Buzz\Authorization\Traits;


use Buzz\Authorization\Jobs\UpdateCacheAuthorization;

trait CacheAction
{
    public function bootCacheAction($users)
    {
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch((new UpdateCacheAuthorization($users)));
    }
}