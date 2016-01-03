<?php


namespace Buzz\Authorization\LoadData;


use Buzz\Authorization\Interfaces\GetDataInterface;
use Illuminate\Support\Collection;

class WithoutCache implements GetDataInterface
{
    private $model;

    /**
     * WithoutCache constructor.
     * @param $model
     */
    public function __construct($model)
    {

        $this->model = $model;
    }

    public function getPermission()
    {
        if (is_null($this->model->permissions)) {
            if ($this->model->isLoadRoles()) {
                $this->loadPermission();
            } else {
                $this->loadRoleAndPermission();
            }
        }
        if (is_null($this->model->permissions)) {
            $slugPermissions = new Collection();
            $permissions = new Collection();
            $this->model->roles->each(function ($item, $key) use (&$permissions, &$slugPermissions) {
                $slugPermissions = $slugPermissions->merge($item->permissions->lists('slug'));
                $item->permissions->each(function ($v, $k) use (&$permissions) {
                    $tmpSlug = $permissions->lists('slug');
                    if ($tmpSlug->search($v->slug) === false) {
                        $permissions->push($v);
                    }
                });
            });
            $this->model->permissions = $permissions;
            $this->model->slugPermissions = $slugPermissions->unique();
        }

        return $this->model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getRoles()
    {
        if (is_null($this->model->slugRoles)) {
            $this->loadRoles();
            $this->model->slugRoles = $this->model->roles->lists('slug');
        }
    }

    public function getLevels()
    {
        if (is_null($this->model->levels)) {
            $this->loadRoles();
            $this->model->levels = $this->model->roles->lists('level');
        }

        return $this->model;
    }


    protected function loadRoles()
    {
        if (!$this->model->isLoadRoles()) {
            $this->model->load('roles');
        }
    }

    protected function loadPermission()
    {
        if (!$this->model->isLoadPermissions()) {
            $this->model->roles->load('permissions');
        }
    }

    protected function loadRoleAndPermission()
    {
        $this->model->load('roles.permissions');
    }
}