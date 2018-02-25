<?php

namespace Tests\Unit;

use App\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class RoleForUserTraitTest extends RoleTestCase
{

    /**
     * RoleManagementTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function testAttachRoleObject()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [$role, $role2];
        $user->attachRole($roles);
        $this->assertRole($user, $roles, false);
    }

    public function testAttachRoleId()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [$role->id, $role2->id];
        $user->attachRole($roles);
        $this->assertRole($user, $roles, false);
    }

    /**
     * @param \App\User $user
     * @param array $roles
     * @param bool $useKey
     */
    protected function assertRole(User $user, array $roles, bool $useKey = true)
    {
        if ($useKey) {
            $roles = array_keys($roles);
        }
        /**
         * @var Collection $userRoles
         */
        $userRoles = $user->roles->map(function (Role $role) {
            return $role->id;
        });
        foreach ($roles as $role) {
            $this->assertTrue($userRoles->contains($role instanceof Role ? $role->id : $role));
        }
    }

    public function testAttachRoleWithStartTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now()],
            $role2->id => ['start_time' => Carbon::now()->addDay(1)],
        ];
        $user->attachRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testAttachRoleWithEndTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['end_time' => Carbon::now()->addDay(3)],
            $role2->id => ['end_time' => Carbon::now()->addDay(4)],
        ];
        $user->attachRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testAttachRoleWithStartTimeEndTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now(), 'end_time' => Carbon::now()->addDay(3)],
            $role2->id => ['start_time' => Carbon::now()->addDay(1), 'end_time' => Carbon::now()->addDay(4)],
        ];
        $user->attachRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testSyncRoleId()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [$role->id, $role2->id];
        $user->syncRole($roles);
        $this->assertRole($user, $roles, false);
    }

    public function testSyncRoleIdWithRemoveOldRole()
    {
        // sync role first time
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [$role->id, $role2->id];
        $user->syncRole($roles);
        // assert first time
        $this->assertRole($user, $roles, false);

        // sync next time
        $role3 = $this->createRole();
        $role4 = $this->createRole();
        $user = $this->createUser();
        $roles2 = [$role3->id, $role4->id];
        $user->syncRole($roles2);
        // assert remove role of first time
        foreach ($user->roles as $role) {
            $this->assertNotTrue(in_array($role->id, $roles));
        }
        // assert role of next time
        $this->assertRole($user, $roles2, false);
    }

    public function testSyncRoleWithStartTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now()],
            $role2->id => ['start_time' => Carbon::now()->addDay(1)],
        ];
        $user->syncRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testSyncRoleWithSpecialStartTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now()],
            $role2->id,
        ];
        $user->syncRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testSyncRoleWithEndTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['end_time' => Carbon::now()->addDay(3)],
            $role2->id => ['end_time' => Carbon::now()->addDay(4)],
        ];
        $user->syncRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testSyncRoleWithStartTimeEndTime()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now(), 'end_time' => Carbon::now()->addDay(3)],
            $role2->id => ['start_time' => Carbon::now()->addDay(1), 'end_time' => Carbon::now()->addDay(4)],
        ];
        $user->syncRole($roles);
        $this->assertRole($user, $roles);
    }

    public function testCleanRole()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now(), 'end_time' => Carbon::now()->addDay(3)],
            $role2->id => ['start_time' => Carbon::now()->addDay(1), 'end_time' => Carbon::now()->addDay(4)],
        ];
        $user->syncRole($roles);
        $this->assertRole($user, $roles);
        // clean role
        $user->cleanRole();
        // assert before reload roles
        $this->assertTrue($user->roles instanceof Collection);
        $this->assertNotTrue($user->roles->isEmpty());
        // reload roles
        $user->load('roles');

        $this->assertTrue($user->roles instanceof Collection);
        $this->assertTrue($user->roles->isEmpty());
    }

    public function testDetachRoleObject()
    {
        $role = $this->createRole();
        $role2 = $this->createRole();
        $user = $this->createUser();
        $roles = [
            $role->id => ['start_time' => Carbon::now(), 'end_time' => Carbon::now()->addDay(3)],
            $role2->id => ['start_time' => Carbon::now()->addDay(1), 'end_time' => Carbon::now()->addDay(4)],
        ];
        $user->syncRole($roles);
        $this->assertRole($user, $roles);
        // detach role
        $user->detachRole([$role]);
        // assert before reload roles
        $this->assertRole($user, $roles);
        // reload roles
        $user->load('roles');

        // $role has been removed
        $this->assertNotTrue($user->roles->contains($role->id));
        // $role2
        $this->assertTrue($user->roles->contains($role2->id));
    }
}