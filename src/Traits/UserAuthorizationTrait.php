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
     * The array slug permissions of user.
     *
     * @var \Illuminate\Support\Collection
     */
    public $slugPermissions;
    /**
     * The array slug roles of user.
     *
     * @var \Illuminate\Support\Collection
     */
    public $slugRoles;

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
                if ($this->slugPermissions->search($item) === false) {
                    return false;
                } elseif ($any === true) {
                    return true;
                }
            }

            return true;
        }

        return $this->slugPermissions->search($permission) !== false;
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
     * Remove roles from users
     *
     * @param string|array $roles
     * @return int
     */
    public function detachRole($roles = [])
    {
        return $this->roles()->detach($roles);
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
        if (is_null($this->slugRoles)) {
            $this->slugRoles = new Collection();
            $this->slugRoles = $this->roles->lists('slug');
        }
        if ($role instanceof Model) {
            $role = $role->slug;
        }
        if (is_array($role)) {
            foreach ($role as $item) {
                if ($this->slugRoles->search($item) === false) {
                    return false;
                } elseif ($any === true) {
                    return true;
                }
            }

            return true;
        }

        return $this->slugRoles->search($role) !== false;
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
     * Load permissions of user if not exist
     *
     * @return void
     */
    protected function loadPermissions()
    {
        if (is_null($this->slugPermissions)) {
            if (!is_null($this->roles)) {
                if (is_object(($firstRole = $this->roles->first())) && is_null($firstRole->slugPermissions)) {
                    $this->roles->load('permissions');
                }
            } else {
                $this->load(['roles.permissions']);
            }
            $slugPermissions = new Collection();
            $permissions = new Collection();
            $this->roles->each(function ($item, $key) use (&$permissions, &$slugPermissions) {
                $slugPermissions = $slugPermissions->merge($item->permissions->lists('slug'));
                $item->permissions->each(function ($v, $k) use (&$permissions) {
                    $tmpSlug = $permissions->lists('slug');
                    if ($tmpSlug->search($v->slug) === false) {
                        $permissions->push($v);
                    }
                });
            });
            $this->permissions = $permissions;
            $this->slugPermissions = $slugPermissions->unique();
        }
    }

    /**
     * Return permissions of user
     *
     * @return Collection
     */
    public function permissions()
    {
        $this->loadPermissions();

        return $this->permissions;
    }

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(app('config')->get('authorization.model_role'));
    }

}
