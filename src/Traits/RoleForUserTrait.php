<?php

namespace Buzz\Authorization\Traits;

use App\Models\Role;

trait RoleForUserTrait
{
    /**
     * The level of user.
     *
     * @var \Illuminate\Support\Collection
     */
    public $roleLevels;

    /**
     * The roles that belong to the user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    /**
     * Check status load roles
     * @return bool
     */
    protected function isLoadedRole()
    {
        return isset($this->relations['roles']);
    }

    /**
     * @example
     * ```php
     * $user->attachRole([$role1, $role2, $role3]);
     * $user->attachRole([1, 2, 3]);
     * $user->attachRole([1 => ['start_time' => Carbon::now()]]);
     * $user->attachRole([1 => ['end_time' => Carbon::Carbon::now()->addDay(5)]]);
     * $user->attachRole([1 => ['start_time' => Carbon::now(), 'end_time' => Carbon::Carbon::now()->addDay(5)]]);
     * ``
     *
     * @param Role[]|int[] $roles
     * @return void
     */
    public function attachRole(array $roles = [])
    {
        $this->roles()->attach($this->prepareRole($roles));
    }

    /**
     * @example
     * ```php
     * $user->syncRole([$role1, $role2, $role3]);
     * $user->syncRole([1, 2, 3]);
     * $user->syncRole([1 => ['start_time' => Carbon::now()], 2, 3]);
     * $user->syncRole([1 => ['end_time' => Carbon::Carbon::now()->addDay(5)], 2, 3]);
     * $user->syncRole([1 => ['start_time' => Carbon::now(), 'end_time' => Carbon::Carbon::now()->addDay(5)], 2, 3]);
     * ``
     *
     * @param Role[]|int[] $roles
     * @return void
     */
    public function syncRole(array $roles = [])
    {
        $this->roles()->sync($this->prepareRole($roles));
    }

    /**
     * @return void
     */
    public function cleanRole()
    {
        $this->roles()->detach();
    }

    /**
     * @param Role[]|int[] $roles
     * @example
     * ```php
     * $user->detachRole([$role1, $role2, $role3]);
     * $user->detachRole([1, 2, 3]);
     * ``
     * @return void
     */
    public function detachRole(array $roles = [])
    {
        $this->roles()->detach($this->prepareRole($roles));
    }

    /**
     * @param Role[]|int[] $roles
     * @return int[]
     */
    private function prepareRole(array $roles = [])
    {
        /**
         * @var int[] $roleIds
         */
        $roleIds = [];
        foreach ($roles as $index => $role) {
            if ($role instanceof Role) {
                array_push($roleIds, $role->id);
            } elseif (is_array($role)) {
                $roleIds = $roleIds + [$index => $role];
            } else {
                array_push($roleIds, $role);
            }
        }

        return $roleIds;
    }
}