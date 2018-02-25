<?php

namespace Buzz\Authorization\Listeners;

use Buzz\Authorization\Events\RebuildPermissionRoleEvent;
use App\Models\PermissionRole;
use App\Models\Role;

class RebuildPermissionRoleListener
{
    /**
     * Handle the event.
     *
     * @param  RebuildPermissionRoleEvent $event
     * @return void
     * @throws \Exception
     */
    public function handle(RebuildPermissionRoleEvent $event)
    {
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        $query = PermissionRole::where('role_id', '=', $event->permissionRole->role_id);
        /**
         * @var \Illuminate\Database\Eloquent\Collection $permissions
         */
        $permissions = $query->get(['permission']);
        /**
         * @var Role $role
         */
        $role = Role::find($event->permissionRole->role_id);
        if ($role) {
            $role->permissions = $permissions->map(function ($permission) {
                return array_only(
                    $permission instanceof PermissionRole ? $permission->toArray() : $permission,
                    ['id', 'permission']
                );
            })
                ->unique();
            $role->save();
        } else {
            $query->delete();
        }
    }
}