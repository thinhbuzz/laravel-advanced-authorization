<?php


namespace Buzz\Authorization\Events;



use App\Models\PermissionRole;

class RebuildPermissionRoleEvent
{
    /**
     * @var \App\Models\PermissionRole $permissionRole
     */
    public $permissionRole;

    /**
     * RebuildPermissionRoleEvent constructor.
     * @param \App\Models\PermissionRole $permissionRole
     */
    public function __construct(PermissionRole $permissionRole)
    {
        $this->permissionRole = $permissionRole;
    }
}