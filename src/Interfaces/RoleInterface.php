<?php


namespace Buzz\Authorization\Interfaces;


interface RoleInterface
{

    /**
     * @param $permissions
     */
    public function attachPermission($permissions);

    /**
     * @param $permissions
     */
    public function detachPermission($permissions = []);
}