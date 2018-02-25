<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property  int $id
 * @property string $name
 * @property string $description
 * @property int $level
 * @property \Illuminate\Database\Eloquent\Collection $permissions
 * @property string|null $all_permissions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Role extends Model
{
    public $table = 'roles';

    public function relationPermissions()
    {
        return $this->hasMany(PermissionRole::class, 'role_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles', 'role_id', 'user_id');
    }

    /**
     * @param \App\Models\PermissionRole[]|string[] $permissions
     */
    public function attachPermissions(array $permissions)
    {
        $this->relationPermissions()->saveMany($this->preparePermissions($permissions));
    }

    /**
     * @param \App\Models\PermissionRole[]|string[] [$permissions=[]]
     * @return void
     * @throws \Exception
     */
    public function detachPermissions(array $permissions = [])
    {
        $permission = new PermissionRole();
        $permission->setAttribute(
            $this->relationPermissions()->getForeignKeyName(),
            $this->relationPermissions()->getParentKey()
        );
        /**
         * @var \Illuminate\Database\Eloquent\Builder $permission
         */
        if (empty($permissions)) {
            $permission->withoutGlobalScopes()->delete();
        } else {
            $permission->withoutGlobalScopes()
                ->whereIn('permission', array_map(function ($permission) {
                    if ($permission instanceof PermissionRole) {
                        return $permission->permission;
                    }
                    return $permission;
                }, $permissions))
                ->delete();
        }
        $permission->fireModelEvent('deleted', false);
    }

    /**
     * @param \App\Models\PermissionRole[]|string[] $permissions
     * @return  \App\Models\PermissionRole[]
     */
    public function preparePermissions(array $permissions)
    {
        return array_map(function ($permission) {
            if (is_string($permission)) {
                return new PermissionRole(['permission' => $permission]);
            }
            return $permission;
        }, $permissions);
    }

    public function getPermissionsAttribute($permissions)
    {
        return collect(json_decode($permissions, true))
            ->map(function ($permissionRole) {
                return new PermissionRole($permissionRole);
            });
    }

    public function setPermissionsAttribute($permissions)
    {
        $this->attributes['permissions'] = collect($permissions)->toJson();
    }
}
