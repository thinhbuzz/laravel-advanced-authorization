<?php


namespace Buzz\Authorization\Traits;


use Buzz\Authorization\LoadData\CacheAuthorization;
use Buzz\Authorization\LoadData\WithoutCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait UserAuthorizationTrait
{
    use GetListKeyObject;
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
     * @var CacheAuthorization|WithoutCache
     */
    public $dataAuthorzation;

    /**
     * Append new roles to user
     *
     * @param string|array $role
     */
    public function attachRoles($roles)
    {
        $this->roles()->attach($this->getListKey($roles));
        app('events')->fire('roles.attached', $this);
    }

    /**
     * Return true if user has all permissions
     *
     * @param string|array $permission
     * @param bool $any
     * @return bool
     */
    public function can($permission, $any = false, $prefix = false)
    {
        $this->loadDataClass()->getPermission();
        if ($permission instanceof Model) {
            $permission = $permission->slug;
        }
        if ($prefix)
            $allSlug = $this->slugPermissions->toArray();
        if (is_array($permission)) {
            if ($prefix) {
                foreach ($permission as $item) {
                    if ($this->checkCanWithPrefix($allSlug, $item) === true) {
                        return true;
                    }
                }
                return false;
            } else {
                foreach ($permission as $item) {
                    if ($this->slugPermissions->search($item) === false) {
                        return false;
                    } elseif ($any === true) {
                        return true;
                    }
                }
                return true;
            }
        }
        if ($prefix)
            return $this->checkCanWithPrefix($allSlug, $permission) !== false;
        else
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
     * Return true if user has one in any permissions start with permission input
     *
     * @param string|array $permission
     * @return bool
     */
    public function canWithPrefix($permissions)
    {
        return $this->can($permissions, false, true);
    }

    protected function checkCanWithPrefix($allSlug, $permission)
    {
        foreach ($allSlug as $slug) {
            if (strpos($slug, $permission) === 0)
                return true;
        }
        return false;
    }

    /**
     * Remove roles from users
     *
     * @param string|array $roles
     * @return int
     */
    public function detachRoles($roles = [])
    {
        $res = $this->roles()->detach($this->getListKey($roles));
        app('events')->fire('roles.detached', $this);

        return $res;
    }

    public function forceUpdateCache()
    {
        if ((app('config')->get('authorization.cache.enable')) === true) {
            $this->loadDataClass()->forceUpdateCache();
        }
    }

    public function forgetCache()
    {
        if ((app('config')->get('authorization.cache.enable')) === true) {
            $this->loadDataClass()->forgetCache();
        }
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
        $this->loadDataClass()->getRoles();
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

    protected function loadDataClass()
    {
        if (is_null($this->dataAuthorzation)) {
            $this->dataAuthorzation = (app('config')->get('authorization.cache.enable')) === true ?
                (new CacheAuthorization($this)) : (new WithoutCache($this));
        }
        return $this->dataAuthorzation;
    }

    /**
     * Check status load roles
     * @return bool
     */
    public function isLoadRoles()
    {
        return isset($this->relations['roles']);
    }

    /**
     * Check status load permission relations
     * @return bool
     */
    public function isLoadPermissions()
    {
        if (!$this->isLoadRoles())
            return false;

        return is_object(($firstRole = $this->roles->first())) && isset($firstRole->relations['permissions']);
    }

    /**
     * Load roles of user if not exist
     */
    public function loadRoles()
    {
        if (!$this->isLoadRoles()) {
            $this->load('roles');
        }
    }

    /**
     * Return permissions of user
     *
     * @return Collection
     */
    public function permissions()
    {
        (new WithoutCache($this))->getPermission();

        return $this->permissions;
    }

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(app('config')->get('authorization.model.role'));
    }

    /**
     * Sync roles of user
     *
     * @param  \Illuminate\Database\Eloquent\Collection|array $roles
     *
     * @return array
     */
    public function syncRoles($roles)
    {
        $res = $this->roles()->sync($this->getListKey($roles));
        app('events')->fire('roles.synced', $this);

        return $res;
    }
}
