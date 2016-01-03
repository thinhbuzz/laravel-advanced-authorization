<?php


namespace Buzz\Authorization\Events;


use Buzz\Authorization\Traits\GenerateCacheKey;

class UpdateUserEvent
{
    use GenerateCacheKey;
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
        if (app('config')->get('authorization.cache.auto_update')) {
            $this->model->forceUpdateCache();
        } else {
            $this->model->forgetCache();
        }
    }
}