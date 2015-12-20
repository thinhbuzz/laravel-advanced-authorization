<?php


namespace Buzz\Authorization\Interfaces;


use Illuminate\Support\Collection;

interface UserAuthorizationInterface
{
    /**
     * Append new roles to user
     *
     * @param string|array $role
     */
    public function attachRole($roles = []);

    /**
     * Return true if user has all permissions
     *
     * @param string|array $permission
     * @param bool $any
     * @return bool
     */
    public function can($permission, $any = false);

    /**
     * Return true if user has one in any permissions
     *
     * @param string|array $permission
     * @return bool
     */
    public function canAny($permission, $any = false);

    /**
     * Remove roles from users
     *
     * @param string|array $roles
     * @return int
     */
    public function detachRole($roles = []);

    /**
     * Return true if user has all roles
     *
     * @param string|array $role
     * @param bool $any
     * @return bool
     */
    public function is($role, $any = false);

    /**
     * Return true if user has one in any roles
     *
     * @param string|array $role
     * @return bool
     */
    public function isAny($role);

    /**
     * Return permissions of user
     *
     * @return Collection
     */
    public function permissions();
}