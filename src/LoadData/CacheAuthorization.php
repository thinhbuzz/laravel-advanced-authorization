<?php


namespace Buzz\Authorization\LoadData;


use Buzz\Authorization\Interfaces\GetDataInterface;
use Buzz\Authorization\Traits\GenerateCacheKey;
use Illuminate\Support\Collection;

class CacheAuthorization implements GetDataInterface
{
    use GenerateCacheKey;
    protected $model;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * CacheAuthorization constructor.
     * @param $model
     */
    public function __construct($model)
    {

        $this->model = $model;
    }

    public function getRoles()
    {
        $this->loadCacheData();
        return $this->collection->get('slugRoles');
    }

    public function getPermission()
    {
        $this->loadCacheData();
        return $this->collection->get('slugPermissions');
    }

    public function getLevels()
    {
        $this->loadCacheData();
        return $this->collection->get('levels');
    }

    private function loadCacheData($force = false)
    {
        $userLevel = app('config')->get('authorization.user_level');
        if (app('cache')->has($this->generateKey()) && $force === false) {
            $this->collection = app('cache')->get($this->generateKey());
            $this->model->slugPermissions = $this->collection->get('slugPermissions');
            $this->model->slugRoles = $this->collection->get('slugRoles');
            if ($userLevel) {
                $this->model->levels = $this->collection->get('levels');
            }
        } else {
            $fromDB = new WithoutCache($this->model);
            $this->collection = new Collection();
            $fromDB->getPermission();$fromDB->getRoles();$fromDB->getLevels();
            $this->collection->put('slugPermissions', $this->model->slugPermissions);
            $this->collection->put('slugRoles', $this->model->slugRoles);
            if ($userLevel) {
                $this->collection->put('levels', $this->model->levels);
            }
            app('cache')->put($this->generateKey(), $this->collection, app('config')->get('authorization.cache.time'));
        }
    }

    public function forceUpdateCache()
    {
        $this->loadCacheData(true);
    }

    public function forgetCache()
    {
        app('cache')->forget($this->generateKey());
    }
}