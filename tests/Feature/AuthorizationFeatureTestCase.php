<?php

namespace Tests\Feature;

use App\User;
use App\Models\PermissionRole;
use App\Models\Role;
use Faker\Factory;
use Tests\TestCase;

class AuthorizationFeatureTestCase extends TestCase
{
    public $faker;

    /**
     * RoleManagementTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    /**
     * @return array
     */
    public function createUserAndAttach()
    {
        $user = $this->createUser();
        /**
         * @var Role[] $roles
         */
        $roles = [$this->createRole(), $this->createRole()];
        $authorization = config('authorization.groupKeys.authorization');
        $roles[0] = $this->attachPermissions($roles[0], [$authorization[0]]);
        $roles[1] = $this->attachPermissions($roles[1], [$authorization[1]]);
        $user = $this->attachRole($user, $roles);
        return [$user, $roles];
    }

    /**
     * @return User
     */
    protected function createUser()
    {
        return User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ]);
    }

    /**
     * @return Role
     */
    protected function createRole()
    {
        $role = new Role();
        $role->name = $this->faker->name;
        $this->assertNotTrue(is_int($role->id));
        $this->assertTrue($role->save());
        $this->assertTrue(is_int($role->id));
        return $role;
    }

    /**
     * @param \App\Models\Role $role
     * @param array $permissions
     * @return \App\Models\Role
     */
    public function attachPermissions(Role $role, array $permissions)
    {
        $role->attachPermissions($permissions);
        $role->refresh();
        return $role;
    }

    /**
     * @param \App\User $user
     * @param array $roles
     * @return \App\User
     */
    public function attachRole(User $user, array $roles)
    {
        $user->attachRole($roles);
        return $user;
    }

    /**
     * @param string $uri
     * @param array $middleware
     * @param null $callback
     */
    public function createRouter(string $uri, array $middleware = [], $callback = null)
    {
        $this->app['router']->get($uri, [
            'middleware' => $middleware,
            'uses' => is_callable($callback) ? $callback : $this->defaultCallable(),
        ]);
    }

    /**
     * @return \Closure
     */
    public function defaultCallable()
    {
        return function () {
            return response([]);
        };
    }

    /**
     * @param array $roles
     * @return string|null
     */
    public function getDeniedPermission(array $roles)
    {
        /**
         * @var string[] $authorization
         */
        $authorization = config('authorization.groupKeys.authorization');
        /**
         * @var \Illuminate\Support\Collection $permissions
         */
        $permissions = collect($roles)->map(function (Role $role) {
            return $role->permissions->map(function (PermissionRole $permissionRole) {
                return $permissionRole->permission;
            });
        })
            ->flatten()
            ->unique();
        return collect($authorization)->diff($permissions)->first();
    }

    /**
     * @param string $methodName
     * @return string
     */
    public function getTestUri(string $methodName)
    {
        return '/' . $methodName;
    }
}