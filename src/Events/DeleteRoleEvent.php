<?php


namespace Buzz\Authorization\Events;


use Buzz\Authorization\Traits\CacheAction;
use Buzz\Authorization\Traits\GenerateCacheKey;

class DeleteRoleEvent
{
    use GenerateCacheKey, CacheAction;
    protected $userIds;

    /**
     * UpdateUserEvent constructor.
     * @param $model
     */
    public function __construct($userIds)
    {

        $this->userIds = $userIds;
    }

    public function boot()
    {
        $userClass = app('config')->get('authorization.model.user');
        $obj = new $userClass();
        $users = $obj->with('roles.permissions')->whereIn($obj->getKeyName(), $this->userIds)->get();
        $this->bootCacheAction($users);
    }
}