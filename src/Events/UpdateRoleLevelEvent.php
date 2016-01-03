<?php


namespace Buzz\Authorization\Events;


use Buzz\Authorization\Traits\CacheAction;
use Buzz\Authorization\Traits\GenerateCacheKey;

class UpdateRoleLevelEvent
{
    use GenerateCacheKey, CacheAction;
    protected $model;

    /**
     * UpdateUserEvent constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function boot()
    {
        $users = $this->model->users()->with('roles.permissions')->get();
        $this->bootCacheAction($users);
    }
}