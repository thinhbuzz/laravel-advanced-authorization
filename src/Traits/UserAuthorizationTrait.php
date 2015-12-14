<?php


namespace Buzz\Authorization\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait UserAuthorizationTrait
{
    /**
     * The permissions of user.
     *
     * @var \Illuminate\Support\Collection
     */
    public $permissions;

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(\Config::get('authorization.model_role'));
    }

    /**
     * Remove roles from users
     *
     * @param string|array $roles
     * @return int
     */
    public function detachRole($roles)
    {
        return $this->roles()->detach($roles);
    }

    /**
     * Append new roles to user
     *
     * @param string|array $role
     */
    public function attachRole($roles)
    {
        $this->roles()->attach($roles);
    }

    /**
     * Return true if user has all roles
     *
     * @param string|array $role
     * @param bool $any
     * @return bool
     */
    public function is($role, $any = false)
    {
        if ($role instanceof Model) {
            $role = $role->slug;
        }
        $slugs = $this->roles->lists('slug');
        if (is_array($role)) {
            foreach ($role as $item) {
                if ($slugs->search($item) === false) {
                    return false;
                } elseif ($any === true) {
                    return true;
                }
            }

            return true;
        }

        return $slugs->search($role) !== false;
    }

    /**
     * Return true if user has one in any roles
     *
     * @param string|array $role
     * @return bool
     */
    public function isAny($role)
    {
        return $this->is($role, true);
    }

    /**
     * Return true if user has all permissions
     *
     * @param string|array $permission
     * @param bool $any
     * @return bool
     */
    public function can($permission, $any = false)
    {
        $this->loadPermissions();
        if ($permission instanceof Model) {
            $permission = $permission->slug;
        }
        if (is_array($permission)) {
            foreach ($permission as $item) {
                if ($this->permissions->search($item) === false) {
                    return false;
                } elseif ($any === true) {
                    return true;
                }
            }

            return true;
        }

        return $this->permissions->search($permission) !== false;
    }

    /**
     * Return true if user has one in any permissions
     *
     * @param string|array $permission
     * @return bool
     */
    public function canAny($permissions)
    {
        return $this->can($permissions, true);
    }

    /**
     * Load permissions of user if not exist
     *
     * @return void
     */
    protected function loadPermissions()
    {
        if (is_null($this->permissions)) {
            if (!is_null($this->roles)) {
                if (is_object(($firstRole = $this->roles->first())) && is_null($firstRole->permissions)) {
                    $this->roles->load('permissions');
                }
            } else {
                $this->load(['roles.permissions']);
            }
            $permissions = new Collection();
            $this->roles->each(function ($item, $key) use (&$permissions) {
                $permissions = $permissions->merge($item->permissions->lists('slug'));
            });
            $this->permissions = $permissions->unique();
        }
    }
}
