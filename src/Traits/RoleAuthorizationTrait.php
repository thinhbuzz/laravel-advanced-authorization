<?php


namespace Buzz\Authorization\Traits;


trait RoleAuthorizationTrait
{
    /**
     * @param $permissions
     */
    public function attachPermission($permissions)
    {
        $this->permissions()->attach($permissions);
    }

    /**
     * @param $permissions
     */
    public function detachPermission($permissions = [])
    {
        $this->permissions()->detach($permissions);
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
     * The users that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(app('config')->get('authorization.model_user'));
    }
}
