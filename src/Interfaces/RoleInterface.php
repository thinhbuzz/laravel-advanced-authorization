<?php


namespace Buzz\Authorization\Interfaces;


interface RoleInterface
{

    /**
     * @param $permissions
     */
    public function attachPermissions($permissions);

    /**
     * @param $permissions
     */
    public function detachPermissions($permissions = []);
}