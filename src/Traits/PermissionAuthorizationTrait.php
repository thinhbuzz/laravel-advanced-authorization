<?php


namespace Buzz\Authorization\Traits;


trait PermissionAuthorizationTrait
{

    /**
     * The roles that belong to the permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(\Config::get('authorization.model_role'));
    }
}