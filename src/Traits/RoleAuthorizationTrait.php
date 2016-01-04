<?php


namespace Buzz\Authorization\Traits;


trait RoleAuthorizationTrait
{
    use GetListKeyObject;
    /**
     * @param $permissions
     */
    public function attachPermission($permissions)
    {
        $this->permissions()->attach($this->getListKey($permissions));
    }

    /**
     * @param $permissions
     */
    public function detachPermission($permissions = [])
    {
        $this->permissions()->detach($this->getListKey($permissions));
    }

    /**
     * The permissions that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(app('config')->get('authorization.model_permission'));
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

        return $res;
    }

    /**
     * The users that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(app('config')->get('authorization.model_user'));
    }
}
