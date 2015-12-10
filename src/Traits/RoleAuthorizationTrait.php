<?php


namespace Buzz\Authorization\Traits;


trait RoleAuthorizationTrait
{

    /**
     * The permissions that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(\Config::get('authorization.model_permission'));
    }

    /**
     * The users that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(\Config::get('authorization.model_user'));
    }

    public function detachPermission($permission)
    {
        $this->users()->detach($permission);
    }

    public function attachPermission($permission)
    {
        $this->users()->attach($permission);
    }

    public function syncPermission($permission)
    {
        $this->users()->sync($permission);
    }
}