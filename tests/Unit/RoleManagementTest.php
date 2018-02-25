<?php

namespace Tests\Unit;

use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleManagementTest extends RoleTestCase
{
    use RefreshDatabase;

    /**
     * RoleManagementTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->refreshDatabase();
    }

    public function testCreateRole()
    {
        $this->createRole();
    }

    public function testAttachPermissionString()
    {
        $role = $this->createRole();
        $permissions = ['create_role'];
        $role->attachPermissions($permissions);
        $role->refresh();
        $this->assertPermission($role, $permissions);
    }

    public function testAttachPermissionObject()
    {
        $role = $this->createRole();
        $permissions = [new PermissionRole(['permission' => 'create_role'])];
        $role->attachPermissions($permissions);
        $role->refresh();
        $this->assertPermission($role, $permissions);
    }

    public function testDetachPermissionString()
    {
        $role = $this->createRole();
        $permissions = ['create_role', 'delete_role'];
        $role->attachPermissions($permissions);
        $role->refresh();
        $this->assertPermission($role, $permissions);
        try {
            $role->detachPermissions(['create_role']);
        } catch (\Exception $e) {
        }
        // before reload permission
        $this->assertPermission($role, $permissions);
        // reload permissions
        $role->refresh();
        /**
         * @var Collection $rolePermissions
         */
        $rolePermissions = $role->permissions->map(function (PermissionRole $permission) {
            return $permission->permission;
        });
        $this->assertTrue($rolePermissions->contains('delete_role'));
        $this->assertNotTrue($rolePermissions->contains('create_role'));
    }

    public function testDetachPermissionObject()
    {
        $role = $this->createRole();
        $permissions = [
            new PermissionRole(['permission' => 'create_role']),
            new PermissionRole(['permission' => 'delete_role']),
        ];
        $role->attachPermissions($permissions);
        $role->refresh();
        $this->assertPermission($role, $permissions);
        try {
            $role->detachPermissions([new PermissionRole(['permission' => 'create_role'])]);
        } catch (\Exception $e) {
        }
        // before reload permission
        $this->assertPermission($role, $permissions);
        // reload permissions
        $role->refresh();
        /**
         * @var Collection $rolePermissions
         */
        $rolePermissions = $role->permissions->map(function (PermissionRole $permission) {
            return $permission->permission;
        });
        $this->assertTrue($rolePermissions->contains('delete_role'));
        $this->assertNotTrue($rolePermissions->contains('create_role'));
    }

    public function testDetachAllPermissions()
    {
        $role = $this->createRole();
        $permissions = ['create_role'];
        $role->attachPermissions($permissions);
        $role->refresh();
        $this->assertPermission($role, $permissions);
        try {
            $role->detachPermissions();
        } catch (\Exception $e) {
        }
        // before reload permission
        $this->assertPermission($role, $permissions);
        // reload permissions
        $role->refresh();
        $this->assertEmpty($role->permissions);
    }

    /**
     * @param \App\Models\Role $role
     * @param string[] $permissions
     */
    protected function assertPermission(Role $role, array $permissions)
    {
        /**
         * @var Collection $rolePermissions
         */
        $rolePermissions = $role->permissions->map(function (PermissionRole $permission) {
            return $permission->permission;
        });
        foreach ($permissions as $permission) {
            $this->assertTrue($rolePermissions->contains($permission instanceof PermissionRole ? $permission->permission : $permission));
        }
    }

}