<?php


namespace Buzz\Authorization\Traits;


use Illuminate\Database\Eloquent\Model;

trait RoleAuthorizationTrait
{
    use GetListKeyObject;
    /**
     * @param $permissions
     */
    public function attachPermissions($permissions)
    {

        $this->permissions()->attach($this->getListKey($permissions));
        app('events')->fire('permissions.attached', $this);
    }

    /**
     * Remove permissions from roles
     *
     * @param string|array $permissions
     * @return int
     */
    public function detachPermissions($permissions = [])
    {
        $res = $this->permissions()->detach($this->getListKey($permissions));
        app('events')->fire('permissions.detached', $this);

        return $res;
    }

    /**
     * The permissions that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(app('config')->get('authorization.model.permission'));
    }

    /**
     * Sync permissions of roles
     *
     * @param  \Illuminate\Database\Eloquent\Collection|array $permissions
     *
     * @return array
     */
    public function syncPermissions($permissions)
    {
        $res = $this->permissions()->sync($this->getListKey($permissions));
        app('events')->fire('permissions.synced', $this);

        return $res;
    }

    /**
     * The users that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(app('config')->get('authorization.model.user'));
    }
}
