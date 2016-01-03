<?php


namespace Buzz\Authorization\Events;


use Buzz\Authorization\Traits\CacheAction;
use Buzz\Authorization\Traits\GenerateCacheKey;

class DeletePermissionEvent
{
    use GenerateCacheKey, CacheAction;
    private $roles;

    /**
     * UpdateUserEvent constructor.
     * @param $model
     */
    public function __construct($roles)
    {

        $this->roles = $roles;
    }

    public function boot()
    {
        foreach ($this->roles as $role) {
            $this->bootCacheAction($role->users);
        }
    }
}