<?php


namespace Buzz\Authorization\Traits;


trait GenerateCacheKey
{


    /**
     * Get cache key
     *
     * @return string
     */
    protected function generateKey($model = false)
    {
        if ($model)
            return 'authorization.cache.' . $model->getKey();
        return 'authorization.cache.' . $this->model->getKey();
    }
}