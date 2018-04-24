<?php

namespace Buzz\Authorization\Traits;

use App\Models\PermissionRole;
use App\Models\Role;

/**
 * @property \Illuminate\Database\Eloquent\Collection $roles
 */
trait PermissionForUserTrait
{
    /**
     * The permissions of user.
     *
     * @var \Illuminate\Support\Collection
     */
    public $permissions;

    /**
     * Check is super user
     *
     * @var boolean
     */
    private $_isSuper;

    /**
     * Return permissions of user
     *
     * @return \Illuminate\Support\Collection
     */
    public function permissions()
    {
        $this->loadPermissions();

        return $this->permissions;
    }

    /**
     * Load roles of user if not exist
     */
    protected function loadPermissions()
    {
        if ($this->permissions) {
            return;
        }
        if (!$this->isLoadedRole()) {
            $this->load('roles');
        }
        $permissions = collect([]);
        $roleLevels = collect([]);
        /**
         * @var Role $role
         */
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge(
                $role->permissions->map(function (PermissionRole $permissionRole) {
                    return $permissionRole->permission;
                })
            );
            $roleLevels->push($role->level);
        }

        $permissionGroups = config('authorization.groupKeys');

        $this->permissions = $permissions->map(function ($permission) use ($permissionGroups) {
            if (isset($permissionGroups[$permission])) {
                return $permissionGroups[$permission];
            }
            return $permission;
        })
            ->flatten()
            ->unique();

        $this->roleLevels = $roleLevels->unique()->sort();
    }

    /**
     * @param string[] $permissions
     *
     * @return bool
     */
    public function can(array $permissions)
    {
        if ($this->isSuper()) {
            return true;
        }
        return collect($permissions)->every(function ($permission) {
            return $this->permissions->contains($permission);
        });
    }

    /**
     * @param string[] $permissions
     *
     * @return bool
     */
    public function canAny(array $permissions)
    {
        if ($this->isSuper()) {
            return true;
        }
        return (bool)collect($permissions)->first(function ($permission) {
            return $this->permissions->contains($permission);
        });
    }

    /**
     * @param string[] $permissions
     *
     * @return bool
     */
    public function cantNot(array $permissions)
    {
        if ($this->isSuper()) {
            return false;
        }
        return collect($permissions)->every(function ($permission) {
            return !$this->permissions->contains($permission);
        });
    }

    /**
     * @param string[] $permissions
     *
     * @return bool
     */
    public function cantNotAny(array $permissions)
    {
        if ($this->isSuper()) {
            return false;
        }
        $this->loadPermissions();
        return (bool)collect($permissions)->first(function ($permission) {
            return !$this->permissions->contains($permission);
        });
    }

    /**
     * Check is super user
     *
     * @return bool
     */
    public function isSuper()
    {
        $this->loadPermissions();
        if (is_bool($this->_isSuper)) {
            return $this->_isSuper;
        }

        $this->_isSuper = $this->permissions->contains(config('authorization.super_user_key'));
        return $this->_isSuper;
    }
}